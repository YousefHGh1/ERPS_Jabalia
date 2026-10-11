<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
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
