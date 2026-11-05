<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * لون الترويسة الموحّد في كل ملفات التصدير
     */
    protected string $accent = '3699FF';

    /**
     * Export users to Excel
     */
    public function exportUsers(): StreamedResponse
    {
        $users = User::with('roles', 'employee')->get();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'ID' => $user->id,
                'الاسم' => $user->name,
                'البريد الإلكتروني' => $user->email,
                'رقم الموظف' => $user->employee_number ?? '',
                'الأدوار' => $user->roles->pluck('name')->map(fn ($r) => role_name_ar($r))->implode('، '),
                'الحالة' => $user->employee ? ($user->employee->status ?? 'غير معرف') : 'بدون موظف',
                'تاريخ الإنشاء' => optional($user->created_at)->format('Y-m-d H:i'),
            ];
        }

        return $this->download($data, 'users.xlsx', 'المستخدمون');
    }

    /**
     * Export roles to Excel
     */
    public function exportRoles(): StreamedResponse
    {
        $roles = Role::with('permissions')->withCount(['users', 'permissions'])->get();

        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'ID' => $role->id,
                'الدور' => role_name_ar($role->name),
                'الاسم الإنجليزي' => $role->name,
                'عدد الصلاحيات' => (int) $role->permissions_count,
                'عدد المستخدمين' => (int) $role->users_count,
                'تاريخ الإنشاء' => optional($role->created_at)->format('d/m/Y'),
            ];
        }

        return $this->download($data, 'roles.xlsx', 'الأدوار');
    }

    /**
     * Export permissions to Excel
     */
    public function exportPermissions(): StreamedResponse
    {
        $permissions = Permission::withCount('roles')->get();

        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'ID' => $permission->id,
                'الصلاحية' => perm_name_ar($permission->name),
                'الاسم الإنجليزي' => $permission->name,
                'عدد الأدوار' => (int) $permission->roles_count,
                'تاريخ الإنشاء' => optional($permission->created_at)->format('d/m/Y'),
            ];
        }

        return $this->download($data, 'permissions.xlsx', 'الصلاحيات');
    }

    /**
     * Export permissions matrix (roles vs permissions)
     */
    public function exportPermissionMatrix(): StreamedResponse
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        $data = [];
        foreach ($roles as $role) {
            $row = ['الدور' => role_name_ar($role->name)];
            $assigned = $role->permissions->pluck('name')->flip();

            foreach ($permissions as $permission) {
                $row[$permission->name] = $assigned->has($permission->name) ? '✓' : '✗';
            }
            $data[] = $row;
        }

        return $this->download(
            $data,
            'permission_matrix.xlsx',
            'مصفوفة الصلاحيات',
            array_merge(['الدور'], $permissions->pluck('name')->all())
        );
    }

    /**
     * Export database backup using PHP
     * Reads database credentials from .env file
     */
    public function exportDatabase(): StreamedResponse
    {
        try {
            // Get database credentials from .env
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUsername = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            // Validate required settings
            if (empty($dbName)) {
                return response()->streamDownload(function () {
                    echo 'Database name not configured in .env file';
                }, 'error.txt', ['Content-Type' => 'text/plain']);
            }

            // Generate backup filename with timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "database_backup_{$timestamp}.sql";

            // Stream the SQL dump directly
            return response()->streamDownload(function () use ($dbHost, $dbPort, $dbUsername, $dbPassword, $dbName) {
                // Connect to MySQL using PDO
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                $pdo = new \PDO($dsn, $dbUsername, $dbPassword, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]);

                // Set SQL mode for consistent export
                $pdo->exec("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");

                // Output headers
                echo "-- Database Backup\n";
                echo "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
                echo "-- Database: {$dbName}\n";
                echo "-- Host: {$dbHost}\n";
                echo "-- --------------------------------------------------------\n\n";

                // Disable foreign key checks
                echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

                // Get all tables
                $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

                foreach ($tables as $table) {
                    echo "-- Table structure for table `{$table}`\n";
                    echo "DROP TABLE IF EXISTS `{$table}`;\n";

                    // Get CREATE TABLE statement
                    $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                    echo $createTable['Create Table'] . ";\n\n";

                    // Get table data
                    $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll();
                    $columnNames = array_map(fn($col) => "`{$col['Field']}`", $columns);

                    echo "-- Dumping data for table `{$table}`\n";

                    $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll();
                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            $values = array_map(function($value) {
                                if ($value === null) {
                                    return 'NULL';
                                }
                                return "'" . addslashes($value) . "'";
                            }, $row);

                            echo "INSERT INTO `{$table}` (" . implode(', ', $columnNames) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                    }
                    echo "\n";
                }

                // Re-enable foreign key checks
                echo "SET FOREIGN_KEY_CHECKS=1;\n";

                // Close connection
                $pdo = null;
            }, $filename, [
                'Content-Type' => 'application/sql',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            Log::error('Database export exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->streamDownload(function () use ($e) {
                echo "Error: " . $e->getMessage();
            }, 'error.txt', ['Content-Type' => 'text/plain']);
        }
    }

    /**
     * بناء ملف xlsx وتنزيله باستخدام PhpSpreadsheet مباشرة
     * (حزمة maatwebsite/excel غير مثبّتة في هذا المشروع، لذلك نعتمد على phpoffice/phpspreadsheet المثبّتة).
     */
    protected function download(array $data, string $filename, string $sheetTitle, array $preferredHeadings = []): StreamedResponse
    {
        $headings = $preferredHeadings ?: array_keys($data[0] ?? []);

        if (empty($headings)) {
            $headings = ['لا توجد بيانات'];
            $data = [['لا توجد بيانات']];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($sheetTitle, 0, 31));
        $sheet->setRightToLeft(true);

        // صف العناوين
        $sheet->fromArray($headings, null, 'A1');
        $lastColumn = $sheet->getHighestColumn();
        $this->styleHeader($sheet, $lastColumn);

        // صفوف البيانات
        if (! empty($data)) {
            $rows = [];
            foreach ($data as $row) {
                $rows[] = array_values($row);
            }
            $sheet->fromArray($rows, null, 'A2');
        }

        $highestRow = max(1, $sheet->getHighestRow());
        $this->styleBody($sheet, $lastColumn, $highestRow);

        $highestColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($index = 1; $index <= $highestColumnIndex; $index++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer, $spreadsheet) {
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * تنسيق صف العناوين: خلفية بلون النظام + خط أبيض عريض + إطار
     */
    protected function styleHeader(Worksheet $sheet, string $lastColumn): void
    {
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->accent],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D3E0']],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(26);
    }

    /**
     * تنسيق صفوف البيانات: توسيط + إطار كامل
     */
    protected function styleBody(Worksheet $sheet, string $lastColumn, int $highestRow): void
    {
        if ($highestRow < 2) {
            return;
        }

        $sheet->getStyle('A2:' . $lastColumn . $highestRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'EBEDF3']],
            ],
        ]);
    }
}
