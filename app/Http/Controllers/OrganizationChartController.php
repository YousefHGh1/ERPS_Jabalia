<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use App\Models\BranchDirector;
use App\Models\Department;
use App\Models\DepartmentDirector;
use App\Models\Employee;
use App\Models\OrgChartLayout;
use App\Models\Section;
use App\Models\SectionDirector;
use ArPHP\I18N\Arabic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class OrganizationChartController extends Controller
{public function __construct()
    {$this->middleware('permission:index departments', ['only' => ['index', 'builder',
        'saveBuilder', 'hierarchy']]);}

    public function index()
    {
        $departments = Department::query()
            ->with([
                'directors.employee',
                'sections'          => fn($q)          => $q->orderBy('name'),
                'sections.directors.employee',
                'sections.branches' => fn($q) => $q->orderBy('name'),
                'sections.branches.directors.employee',
            ])
            ->get()
            ->sortBy(function ($department) {
                // Priority ordering: Municipality Presidency (1), Manager (2), Administrative Affairs (3), then others

                $priorityDepartments = [
                    'Municipality Presidency' => 1,
                    'Manager'                 => 2,
                    'Administrative Affairs'  => 3,
                ];

                $departmentName = $department->name;
                return $priorityDepartments[$departmentName] ?? 100;
            })
            ->values();

        $departmentIds = $departments->pluck('id')->filter()->unique()->values();
        $sectionIds    = $departments->pluck('sections')->flatten()->pluck('id')->filter()->unique()->values();
        $branchIds     =
        $departments->pluck('sections')->flatten()->pluck('branches')->flatten()->pluck('id')->filter()->unique()->values();

        $departmentEmployeesByDepartment = $departmentIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('department_id', $departmentIds)
            ->whereNull('section_id')
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('department_id')
            ->map(fn(Collection $group) => $group->values());

        $sectionEmployeesBySection = $sectionIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('section_id', $sectionIds)
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('section_id')
            ->map(fn(Collection $group) => $group->values());

        $branchEmployeesByBranch = $branchIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('branch_id', $branchIds)
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('branch_id')
            ->map(fn(Collection $group) => $group->values());

        return view('structure.org-chart', compact(
            'departments',
            'departmentEmployeesByDepartment',
            'sectionEmployeesBySection',
            'branchEmployeesByBranch'
        ));
    }

    public function builder()
    {
        $departments = Department::query()
            ->with([
                'sections'          => fn($q)          => $q->orderBy('name'),
                'sections.branches' => fn($q) => $q->orderBy('name'),
            ])
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->active()
            ->with(['job', 'department', 'section', 'branch'])
            ->orderBy('name')
            ->get();

        $layout = OrgChartLayout::query()->firstOrCreate(
            ['key' => 'manual_builder'],
            [
                'name'       => 'Manual organizational chart',
                'layout'     => ['nodes' => []],
                'created_by' => auth()->id(),
            ]
        );

        $employeeGroups = [
            'department' => $employees
                ->whereNotNull('department_id')
                ->whereNull('section_id')
                ->whereNull('branch_id')
                ->groupBy('department_id'),
            'section'    => $employees
                ->whereNotNull('section_id')
                ->whereNull('branch_id')
                ->groupBy('section_id'),
            'branch'     => $employees
                ->whereNotNull('branch_id')
                ->groupBy('branch_id'),
        ];

        return view('structure.org-chart-builder', compact(
            'departments',
            'employees',
            'employeeGroups',
            'layout'
        ));
    }

    public function saveBuilder(Request $request)
    {
        $validated = $request->validate([
            'nodes'              => ['present', 'array'],
            'nodes.*.uid'        => ['required', 'string', 'max:80'],
            'nodes.*.type'       => ['required', 'string', 'in:root,department,section,branch,employee'],
            'nodes.*.entity_id'  => ['nullable', 'integer'],
            'nodes.*.title'      => ['required', 'string', 'max:255'],
            'nodes.*.subtitle'   => ['nullable', 'string', 'max:255'],
            'nodes.*.parent_uid' => ['nullable', 'string', 'max:80'],
            'nodes.*.x'          => ['required', 'numeric'],
            'nodes.*.y'          => ['required', 'numeric'],
        ]);

        $layout = OrgChartLayout::query()->updateOrCreate(
            ['key' => 'manual_builder'],
            [
                'name'       => 'Manual organizational chart',
                'layout'     => ['nodes' => array_values($validated['nodes'])],
                'updated_by' => auth()->id(),
            ]
        );

        return response()->json([
            'ok'         => true,
            'message'    => __('saved_successfully'),
            'updated_at' => optional($layout->updated_at)->format('Y-m-d H:i'),
        ]);
    }

    public function exportWord()
    {
        $departments = Department::query()
            ->with([
                'directors.employee',
                'sections'          => fn($q)          => $q->orderBy('name'),
                'sections.directors.employee',
                'sections.branches' => fn($q) => $q->orderBy('name'),
                'sections.branches.directors.employee',
            ])
            ->get()
            ->sortBy(function ($department) {
                // Priority ordering: Municipality Presidency (1), Manager (2), Administrative Affairs (3), then others

                $priorityDepartments = [
                    'Municipality Presidency' => 1,
                    'Manager'                 => 2,
                    'Administrative Affairs'  => 3,
                ];

                $departmentName = $department->name;
                return $priorityDepartments[$departmentName] ?? 100;
            })
            ->values();

        $departmentIds = $departments->pluck('id')->filter()->unique()->values();
        $sectionIds    = $departments->pluck('sections')->flatten()->pluck('id')->filter()->unique()->values();
        $branchIds     =
        $departments->pluck('sections')->flatten()->pluck('branches')->flatten()->pluck('id')->filter()->unique()->values();

        $departmentEmployeesByDepartment = $departmentIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('department_id', $departmentIds)
            ->whereNull('section_id')
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('department_id')
            ->map(fn(Collection $group) => $group->values());

        $sectionEmployeesBySection = $sectionIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('section_id', $sectionIds)
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('section_id')
            ->map(fn(Collection $group) => $group->values());

        $branchEmployeesByBranch = $branchIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('branch_id', $branchIds)
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('branch_id')
            ->map(fn(Collection $group) => $group->values());

        $html = view('structure.exports.org-chart-word', compact(
            'departments',
            'departmentEmployeesByDepartment',
            'sectionEmployeesBySection',
            'branchEmployeesByBranch'
        ))->render();

        $filename = 'org-chart-' . date('Y-m-d') . '.doc';

        return Response::make($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($html));
    }

    public function exportPdfA3()
    {
        $departments = Department::query()
            ->with([
                'directors.employee',
                'sections'          => fn($q)          => $q->orderBy('name'),
                'sections.directors.employee',
                'sections.branches' => fn($q) => $q->orderBy('name'),
                'sections.branches.directors.employee',
            ])
            ->get()
            ->sortBy(function ($department) {
                // Priority ordering: Municipality Presidency (1), Manager (2), Administrative Affairs (3), then others

                $priorityDepartments = [
                    'Municipality Presidency' => 1,
                    'Manager'                 => 2,
                    'Administrative Affairs'  => 3,
                ];

                $departmentName = $department->name;
                return $priorityDepartments[$departmentName] ?? 100;
            })
            ->values();

        $departmentIds = $departments->pluck('id')->filter()->unique()->values();
        $sectionIds    = $departments->pluck('sections')->flatten()->pluck('id')->filter()->unique()->values();
        $branchIds     =
        $departments->pluck('sections')->flatten()->pluck('branches')->flatten()->pluck('id')->filter()->unique()->values();

        $departmentEmployeesByDepartment = $departmentIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('department_id', $departmentIds)
            ->whereNull('section_id')
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('department_id')
            ->map(fn(Collection $group) => $group->values());

        $sectionEmployeesBySection = $sectionIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('section_id', $sectionIds)
            ->whereNull('branch_id')
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('section_id')
            ->map(fn(Collection $group) => $group->values());

        $branchEmployeesByBranch = $branchIds->isEmpty()
            ? collect()
            : Employee::query()
            ->active()
            ->whereIn('branch_id', $branchIds)
            ->with('job')
            ->orderBy('name')
            ->get()
            ->groupBy('branch_id')
            ->map(fn(Collection $group) => $group->values());

        $arabic = static fn($text): string => (new Arabic())->utf8Glyphs(
            e((string) $text),
            100,
            false,
            true
        );

        $html = view('structure.exports.org-chart-pdf-print', compact(
            'departments',
            'departmentEmployeesByDepartment',
            'sectionEmployeesBySection',
            'branchEmployeesByBranch',
            'arabic'
        ))->render();

        $filename = 'org-chart-a3-' . date('Y-m-d') . '.pdf';

        return Pdf::loadHTML($html)
            ->setPaper('a3', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->download($filename);
    }

    /**
     * صفحة طباعة هيكلية دائرة / قسم / شعبة على حدة — بنفس شكل البطاقات + الترويسة الرسمية.
     */
    public function printUnit(string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);
        abort_unless(auth()->check() && auth()->user()->can('index ' . $config['permission']), 403);

        $hierarchy = match ($type) {
            'department' => $this->buildDepartmentHierarchy(
                Department::query()
                    ->with([
                        'directors.employee',
                        'sections'          => fn($q)          => $q->orderBy('name'),
                        'sections.directors.employee',
                        'sections.branches' => fn($q) => $q->orderBy('name'),
                        'sections.branches.directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
            'section'    => $this->buildSectionHierarchy(
                Section::query()
                    ->with([
                        'department.directors.employee',
                        'directors.employee',
                        'branches' => fn($q) => $q->orderBy('name'),
                        'branches.directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
            default      => $this->buildBranchHierarchy(
                Branch::query()
                    ->with([
                        'section.department.directors.employee',
                        'section.directors.employee',
                        'directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
        };

        $node       = $hierarchy['node'] ?? [];
        $children   = $hierarchy['children'] ?? ['kind' => '', 'items' => []];
        $typeLabel  = $type === 'department' ? 'الدائرة' : ($type === 'section' ? 'القسم' : 'الشعبة');
        $nodeAccent = $type === 'department' ? '#d92d20' : ($type === 'section' ? '#1b84ff' : '#d99a00');

        $eyeIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="#1b84ff" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
        <circle cx="12" cy="12" r="3" />
    </svg>';
        $userIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
        <circle cx="12" cy="7" r="4" />
    </svg>';

        $letterheadPath    = public_path('images/للتقارير.jpg');
        $letterheadDataUri = null;
        if (is_file($letterheadPath)) {
            $mime = function_exists('mime_content_type')
                ? (mime_content_type($letterheadPath) ?: 'image/jpeg')
                : 'image/jpeg';
            $letterheadDataUri = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($letterheadPath));
        }

        return view('structure.org-chart-print', [
            'hierarchy'         => $hierarchy,
            'node'              => $node,
            'children'          => $children,
            'type'              => $type,
            'typeLabel'         => $typeLabel,
            'nodeAccent'        => $nodeAccent,
            'eyeIcon'           => $eyeIcon,
            'userIcon'          => $userIcon,
            'letterheadDataUri' => $letterheadDataUri,
        ]);
    }

    public function exportExcel(string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);
        abort_unless(auth()->check() && auth()->user()->can('index ' . $config['permission']), 403);

        $hierarchy = match ($type) {
            'department' => $this->buildDepartmentHierarchy(
                Department::query()
                    ->with([
                        'directors.employee',
                        'sections'          => fn($q)          => $q->orderBy('name'),
                        'sections.directors.employee',
                        'sections.branches' => fn($q) => $q->orderBy('name'),
                        'sections.branches.directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
            'section'    => $this->buildSectionHierarchy(
                Section::query()
                    ->with([
                        'department.directors.employee',
                        'directors.employee',
                        'branches' => fn($q) => $q->orderBy('name'),
                        'branches.directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
            default      => $this->buildBranchHierarchy(
                Branch::query()
                    ->with([
                        'section.department.directors.employee',
                        'section.directors.employee',
                        'directors.employee',
                    ])
                    ->findOrFail((int) $id)
            ),
        };

        $employees = [];
        $this->collectEmployeesFromHierarchy($hierarchy, $employees, $type);
        $this->resolvePermanentVsActing($employees);

        usort($employees, fn($a, $b) => strcmp($a['unit_type'] ?? '', $b['unit_type'] ?? '') ?: strcmp($a['name'] ?? '',
            $b['name'] ?? ''));

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setTitle(mb_substr('هيكلية ' . ($hierarchy['node']['name'] ?? ''), 0, 31));

        $headers = [
            'نوع الوحدة',
            'اسم الوحدة',
            'اسم الموظف',
            'المسمى الوظيفي',
            'نوع التعيين',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B03A2E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $rowIndex = 2;
        foreach ($employees as $emp) {
            $sheet->fromArray([
                $emp['unit_type_label'] ?? '',
                $emp['unit_name'] ?? '',
                $emp['name'] ?? '',
                $emp['job'] ?? '',
                $emp['appointment_type_label'] ?? '',
            ], null, 'A' . $rowIndex);
            $rowIndex++;
        }

        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $unitName = str_replace(['/', '\\', ' '], '_', $hierarchy['node']['name'] ?? 'org-chart');
        $filename = 'org-chart-' . $unitName . '-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function settleEmployees()
    {
        abort_unless(auth()->check() && auth()->user()->can('index departments'), 403);

        $updated   = 0;
        $skipped   = 0;
        $details   = [];

        // 1. تسكين مدراء الدوائر
        $departments = Department::query()
            ->with(['directors.employee'])
            ->get();

        foreach ($departments as $department) {
            $director = $department->directors
                ->firstWhere('director_type', 'permanent')
                ?: $department->directors->firstWhere('director_type', 'acting');

            if (!$director || !$director->employee) {
                $skipped++;
                $details[] = [
                    'unit'    => $department->name,
                    'type'    => 'department',
                    'status'  => 'no_director',
                    'message' => 'لا يوجد مدير مسموح به',
                ];
                continue;
            }

            $employee = $director->employee;
            $current  = [
                'department_id' => $employee->department_id,
                'section_id'    => $employee->section_id,
                'branch_id'     => $employee->branch_id,
            ];
            $desired = [
                'department_id' => (int) $department->id,
                'section_id'    => null,
                'branch_id'     => null,
            ];

            if ($current === $desired) {
                $skipped++;
                $details[] = [
                    'unit'    => $department->name,
                    'type'    => 'department',
                    'status'  => 'already_settled',
                    'message' => 'الموظف مسكّن بالفعل',
                ];
                continue;
            }

            $employee->update($desired);
            $this->updateEmployeeJobTitle($employee, 'department', $director->director_type);
            $updated++;
            $details[] = [
                'unit'    => $department->name,
                'type'    => 'department',
                'status'  => 'updated',
                'message' => 'تم التحديث',
            ];
        }

        // 2. تسكين مدراء الأقسام
        $sections = Section::query()
            ->with(['department', 'directors.employee'])
            ->get();

        foreach ($sections as $section) {
            $director = $section->directors
                ->firstWhere('director_type', 'permanent')
                ?: $section->directors->firstWhere('director_type', 'acting');

            if (!$director || !$director->employee) {
                $skipped++;
                $details[] = [
                    'unit'    => $section->name,
                    'type'    => 'section',
                    'status'  => 'no_director',
                    'message' => 'لا يوجد مدير مسموح به',
                ];
                continue;
            }

            $employee = $director->employee;
            $current  = [
                'department_id' => $employee->department_id,
                'section_id'    => $employee->section_id,
                'branch_id'     => $employee->branch_id,
            ];
            $desired = [
                'department_id' => $section->department_id ? (int) $section->department_id : null,
                'section_id'    => (int) $section->id,
                'branch_id'     => null,
            ];

            if ($current === $desired) {
                $skipped++;
                $details[] = [
                    'unit'    => $section->name,
                    'type'    => 'section',
                    'status'  => 'already_settled',
                    'message' => 'الموظف مسكّن بالفعل',
                ];
                continue;
            }

            $employee->update($desired);
            $this->updateEmployeeJobTitle($employee, 'section', $director->director_type);
            $updated++;
            $details[] = [
                'unit'    => $section->name,
                'type'    => 'section',
                'status'  => 'updated',
                'message' => 'تم التحديث',
            ];
        }

        // 3. تسكين مدراء الشعب
        $branches = Branch::query()
            ->with(['section.department', 'directors.employee'])
            ->get();

        foreach ($branches as $branch) {
            $director = $branch->directors
                ->firstWhere('director_type', 'permanent')
                ?: $branch->directors->firstWhere('director_type', 'acting');

            if (!$director || !$director->employee) {
                $skipped++;
                $details[] = [
                    'unit'    => $branch->name,
                    'type'    => 'branch',
                    'status'  => 'no_director',
                    'message' => 'لا يوجد مدير مسموح به',
                ];
                continue;
            }

            $employee = $director->employee;
            $current  = [
                'department_id' => $employee->department_id,
                'section_id'    => $employee->section_id,
                'branch_id'     => $employee->branch_id,
            ];
            $desired = [
                'department_id' => $branch->section && $branch->section->department_id
                    ? (int) $branch->section->department_id
                    : null,
                'section_id'    => $branch->section_id ? (int) $branch->section_id : null,
                'branch_id'     => (int) $branch->id,
            ];

            if ($current === $desired) {
                $skipped++;
                $details[] = [
                    'unit'    => $branch->name,
                    'type'    => 'branch',
                    'status'  => 'already_settled',
                    'message' => 'الموظف مسكّن بالفعل',
                ];
                continue;
            }

            $employee->update($desired);
            $this->updateEmployeeJobTitle($employee, 'branch', $director->director_type);
            $updated++;
            $details[] = [
                'unit'    => $branch->name,
                'type'    => 'branch',
                'status'  => 'updated',
                'message' => 'تم التحديث',
            ];
        }

        return response()->json([
            'ok'      => true,
            'message' => __('تمت المزامنة بنجاح: تم تحديث :updated موظف، وتخطي :skipped', [
                'updated' => $updated,
                'skipped' => $skipped,
            ]),
            'updated' => $updated,
            'skipped' => $skipped,
            'details' => $details,
        ]);
    }

    private function updateEmployeeJobTitle(Employee $employee, string $unitType, string $directorType, bool $isActing = false): void
    {
        $unitTypeLabel = $unitType === 'department' ? 'مدير دائرة' : ($unitType === 'section' ? 'رئيس قسم' : 'رئيس شعبة');
        $jobTitle = $directorType === 'permanent' ? $unitTypeLabel : 'مكلف - ' . $unitTypeLabel;

        if (!$employee->EmployeeJobInfo) {
            $employee->EmployeeJobInfo()->create(['current_job' => $jobTitle]);
        } else {
            $employee->EmployeeJobInfo->update(['current_job' => $jobTitle]);
        }
    }

    private function collectEmployeesFromHierarchy(array $hierarchy, array &$employees, string $unitType): void
    {
        $node          = $hierarchy['node'] ?? [];
        $nodeName      = $node['name'] ?? '';
        $nodeTypeLabel = $unitType === 'department' ? 'دائرة' : ($unitType === 'section' ? 'قسم' : 'شعبة');

        $directEmployees = $hierarchy['children']['employees'] ?? [];
        foreach ($directEmployees as $emp) {
            $employees[] = [
                'id'                     => $emp['id'],
                'name'                   => $emp['name'],
                'job'                    => $emp['job'] ?? '',
                'unit_name'              => $nodeName,
                'unit_type'              => $unitType,
                'unit_type_label'        => $nodeTypeLabel,
                'appointment_type'       => 'permanent',
                'appointment_type_label' => 'مسكن',
            ];
        }

        $directors = $node['directors'] ?? [];
        if (! empty($directors['permanent'])) {
            $employees[] = [
                'id'                     => $this->extractEmployeeIdFromName($directors['permanent'], $hierarchy),
                'name'                   => $directors['permanent'],
                'job'                    => '',
                'unit_name'              => $nodeName,
                'unit_type'              => $unitType,
                'unit_type_label'        => $nodeTypeLabel,
                'appointment_type'       => 'permanent',
                'appointment_type_label' => 'مسكن',
            ];
        }
        if (! empty($directors['acting'])) {
            $employees[] = [
                'id'                     => $this->extractEmployeeIdFromName($directors['acting'], $hierarchy),
                'name'                   => $directors['acting'],
                'job'                    => '',
                'unit_name'              => $nodeName,
                'unit_type'              => $unitType,
                'unit_type_label'        => $nodeTypeLabel,
                'appointment_type'       => 'acting',
                'appointment_type_label' => 'مكلف',
            ];
        }

        $items = $hierarchy['children']['items'] ?? [];
        foreach ($items as $child) {
            $childType = $child['type'] ?? 'section';
            $this->collectEmployeesFromHierarchy($child, $employees, $childType);
        }
    }

    private function resolvePermanentVsActing(array &$employees): void
    {
        // تجميع حسب (اسم الموظف + اسم الوحدة) للتأكد من أن المسكن والمكلف في نفس الوحدة فقط
        $byKey = [];
        foreach ($employees as $index => $emp) {
            $key = $emp['name'] . '|' . ($emp['unit_name'] ?? '');
            if (! isset($byKey[$key])) {
                $byKey[$key] = [];
            }
            $byKey[$key][] = &$employees[$index];
        }

        foreach ($byKey as $key => $entries) {
            $hasPermanent = false;
            $hasActing    = false;
            foreach ($entries as $entry) {
                if ($entry['appointment_type'] === 'permanent') {
                    $hasPermanent = true;
                }
                if ($entry['appointment_type'] === 'acting') {
                    $hasActing = true;
                }
            }

            if ($hasPermanent && $hasActing) {
                foreach ($entries as $entryKey => $entry) {
                    if ($entry['appointment_type'] === 'permanent') {
                        unset($entries[$entryKey]);
                    } else {
                        $entries[$entryKey]['appointment_type_label'] = 'مكلف (تكليف فقط)';
                    }
                }
            }
        }
    }

    private function extractEmployeeIdFromName(string $name, array $hierarchy): int
    {
        $id = 0;
        $this->findEmployeeIdByName($hierarchy, $name, $id);
        return $id;
    }

    private function findEmployeeIdByName(array $hierarchy, string $name, int &$foundId): void
    {
        if ($foundId !== 0) {
            return;
        }

        $directEmployees = $hierarchy['children']['employees'] ?? [];
        foreach ($directEmployees as $emp) {
            if ($emp['name'] === $name) {
                $foundId = $emp['id'];
                return;
            }
        }

        $items = $hierarchy['children']['items'] ?? [];
        foreach ($items as $child) {
            $this->findEmployeeIdByName($child, $name, $foundId);
        }
    }

    /**
     * نفس منطق الشاشات الأصلية: فصل الموظفين ثم الحذف، وبنفس صلاحية الحذف.
     */
    public function destroy(Request $request, string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);
        abort_unless(in_array($type, ['section', 'branch']), 404);
        abort_unless(auth()->check() && auth()->user()->can('delete ' . $config['permission']), 403);

        $node = $config['model']::query()->findOrFail((int) $id);

        if ($type === 'section') {
            // حذف الشعب التابعة للقسم بعد فصل موظفيها حتى لا تبقى شعب يتيمة
            foreach ($node->branches as $branch) {
                $branch->employees()->update(['branch_id' => null]);
                $branch->delete();
            }
        }

        // فصل موظفي الوحدة قبل الحذف (نفس منطق شاشة الحذف الأصلية)
        $node->employees()->update([$type . '_id' => null]);

        $typeLabel = $type === 'section' ? 'قسم' : 'شعبة';
        activity()
            ->performedOn($node)
            ->causedBy(auth()->user())
            ->log('تم حذف ' . $typeLabel . ': ' . $node->name);

        $node->delete();

        return response()->json([
            'ok'      => true,
            'message' => __('deleted successfully'),
        ]);
    }

    /**
     * سجل أنواع عقد الهيكل التنظيمي المستخدمة في النوافذ المنبثقة.
     * model: الموديل المرتبط بالنوع | permission: صلاحية التعديل (نفس صلاحيات الشاشة الأصلية) | children_kind: نوع
    العناصر
    التابعة
     */
    private const ORG_NODE_TYPES = [
        'department' => [
            'model'         => Department::class,
            'permission'    => 'departments',
            'children_kind' => 'sections',
        ],
        'section'    => [
            'model'         => Section::class,
            'permission'    => 'sections',
            'children_kind' => 'branches',
        ],
        'branch'     => [
            'model'         => Branch::class,
            'permission'    => 'branches',
            'children_kind' => 'employees',
        ],
    ];

    /**
     * التحقق من نوع العقدة وإرجاع إعداداتها، أو إيقاف الطلب بخطأ 404.
     */
    private function resolveOrgNodeType(string $type): array
    {
        abort_unless(array_key_exists($type, self::ORG_NODE_TYPES), 404);

        return self::ORG_NODE_TYPES[$type];
    }

    /**
     * إرجاع بيانات الهيكلية الخاصة بأي عقدة (دائرة/قسم/شعبة) بصيغة JSON.
     * تُستخدم في النافذة المنبثقة عند الضغط على عقدة داخل مخطط الهيكل التنظيمي.
     * محمية بـ permission:index departments (نفس صلاحية شاشة المخطط نفسها).
     */
    public function hierarchy(string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);
        $node   = $config['model']::query()->findOrFail((int) $id);

        switch ($type) {
            case 'department':
                $payload = $this->buildDepartmentHierarchy($node);
                break;
            case 'section':
                $payload = $this->buildSectionHierarchy($node);
                break;
            default:
                $payload = $this->buildBranchHierarchy($node);
                break;
        }

        return response()->json(['ok' => true] + $payload);
    }

    /**
     * هيكلية الدائرة كاملة: الأقسام التابعة، وكل قسم مع شعبه وموظفيه المباشرين،
     * بالإضافة إلى موظفي الدائرة المباشرين — كل المستويات في نافذة واحدة.
     * نفس دلالات العد المستخدمة في شاشة المخطط (الموظفون النشطون فقط) وبعدد استعلامات محدود.
     */
    private function buildDepartmentHierarchy(Department $department): array
    {
        $department->load([
            'directors.employee',
            'employees' => fn($q) =>
            $q->active()
                ->whereNull('section_id')
                ->whereNull('branch_id')
                ->whereNotIn('id', function ($sub) use ($department) {
                    $sub->select('employee_id')
                        ->from('department_directors')
                        ->where('director_type', 'permanent')
                        ->orWhere(function ($or) {
                            $or->select('employee_id')
                                ->from('department_directors')
                                ->where('director_type', 'acting');
                        });
                })
                ->with('job')
                ->orderBy('name'),
            'sections'  => fn($q)  => $q->orderBy('name')->with([
                'directors.employee',
                'employees'          => fn($sq)          => $sq->active()
                    ->whereNull('branch_id')
                    ->whereNotIn('id', function ($sub) use ($department) {
                        $sub->select('employee_id')
                            ->from('section_directors')
                            ->where('director_type', 'permanent')
                            ->orWhere(function ($or) {
                                $or->select('employee_id')
                                    ->from('section_directors')
                                    ->where('director_type', 'acting');
                            });
                    })
                    ->with('job')
                    ->orderBy('name'),
                'branches'           => fn($bq)           => $bq->orderBy('name'),
                'branches.directors.employee',
                'branches.employees' => fn($beq) => $beq->active()->with('job')->orderBy('name'),
            ]),
        ]);

        $mapEmployees = fn(Collection $employees) => $employees->map(fn(Employee $employee) => [
            'id'   => $employee->id,
            'name' => $employee->name,
            'job'  => $employee->job?->name,
        ])->values();

        $items = $department->sections->map(function (Section $section) use ($mapEmployees) {
            return [
                'id'        => $section->id,
                'type'      => 'section',
                'name'      => $section->name,
                'manager'   => $section->director_label,
                'directors' => $section->directors_meta,
                'meta'      => [
                    ['key' => 'branches', 'value' => $section->branches->count(), 'label' => 'الشعب: ' . $section->branches->count()],
                    ['key' => 'direct_employees', 'value' => $section->employees->count(), 'label' => 'الموظفون المباشرون: ' .
                        $section->employees->count()],
                ],
                'employees' => $mapEmployees($section->employees),
                'branches'  => $section->branches->map(fn(Branch $branch) => [
                    'id'        => $branch->id,
                    'type'      => 'branch',
                    'name'      => $branch->name,
                    'manager'   => $branch->director_label,
                    'directors' => $branch->directors_meta,
                    'meta'      => [
                        ['key' => 'direct_employees', 'value' => $branch->employees->count(), 'label' => 'الموظفون: ' .
                            $branch->employees->count()],
                    ],
                    'employees' => $mapEmployees($branch->employees),
                ])->values(),
            ];
        })->values();

        $departmentDirectEmployees = $mapEmployees($department->employees);

        $total = $departmentDirectEmployees->count()
         + $items->sum(fn($section) => count($section['employees']) + collect($section['branches'])->sum(fn($branch) =>
            count($branch['employees'])));

        return [
            'node'     => [
                'type'      => 'department',
                'id'        => $department->id,
                'name'      => $department->name,
                'manager'   => $department->director_label,
                'directors' => $department->directors_meta,
            ],
            'parent'   => null,
            'stats'    => [
                'total_employees'  => $total,
                'direct_employees' => $departmentDirectEmployees->count(),
            ],
            'children' => [
                'kind'      => 'tree',
                'label'     => __('sections'),
                'items'     => $items,
                'employees' => $departmentDirectEmployees,
            ],
        ];
    }

    /**
     * هيكلية القسم كاملة: الشعب التابعة وكل شعبة مع موظفيها،
     * بالإضافة إلى موظفي القسم المباشرين (غير المنتمين لشعبة).
     */
    private function buildSectionHierarchy(Section $section): array
    {
        $section->load([
            'directors.employee',
            'department',
            'employees'          => fn($sq)          => $sq->active()
                ->whereNull('branch_id')
                ->whereNotIn('id', function ($sub) use ($section) {
                    $sub->select('employee_id')
                        ->from('section_directors')
                        ->where('director_type', 'permanent')
                        ->orWhere(function ($or) {
                            $or->select('employee_id')
                                ->from('section_directors')
                                ->where('director_type', 'acting');
                        });
                })
                ->with('job')
                ->orderBy('name'),
            'branches'           => fn($bq)           => $bq->orderBy('name'),
            'branches.directors.employee',
            'branches.employees' => fn($beq) => $beq->active()->with('job')->orderBy('name'),
        ]);

        $mapEmployees = fn(Collection $employees) => $employees->map(fn(Employee $employee) => [
            'id'   => $employee->id,
            'name' => $employee->name,
            'job'  => $employee->job?->name,
        ])->values();

        $items = $section->branches->map(fn(Branch $branch) => [
            'id'        => $branch->id,
            'type'      => 'branch',
            'name'      => $branch->name,
            'manager'   => $branch->director_label,
            'directors' => $branch->directors_meta,
            'meta'      => [
                ['key' => 'direct_employees', 'value' => $branch->employees->count(), 'label' => 'الموظفون: ' .
                    $branch->employees->count()],
            ],
            'employees' => $mapEmployees($branch->employees),
        ])->values();

        $sectionDirectEmployees = $mapEmployees($section->employees);

        return [
            'node'     => [
                'type'      => 'section',
                'id'        => $section->id,
                'name'      => $section->name,
                'manager'   => $section->director_label,
                'directors' => $section->directors_meta,
            ],
            'parent'   => $section->department ? [
                'type' => 'department',
                'id'   => $section->department->id,
                'name' => $section->department->name,
            ] : null,
            'stats'    => [
                'total_employees'  => $sectionDirectEmployees->count() + $items->sum(fn($branch) => count($branch['employees'])),
                'direct_employees' => $sectionDirectEmployees->count(),
            ],
            'children' => [
                'kind'      => 'tree',
                'label'     => __('branches'),
                'items'     => $items,
                'employees' => $sectionDirectEmployees,
            ],
        ];
    }

    /**
     * هيكلية الشعبة: الموظفون المرتبطون بها مباشرة (لا يوجد مستوى أدنى).
     */
    private function buildBranchHierarchy(Branch $branch): array
    {
        $branch->load(['directors.employee', 'section']);

        $employees = Employee::query()
            ->active()
            ->where('branch_id', $branch->id)
            ->whereNotIn('id', function ($sub) use ($branch) {
                $sub->select('employee_id')
                    ->from('branch_directors')
                    ->where('director_type', 'permanent')
                    ->orWhere(function ($or) {
                        $or->select('employee_id')
                            ->from('branch_directors')
                            ->where('director_type', 'acting');
                    });
            })
            ->with('job')
            ->orderBy('name')
            ->get(['id', 'name', 'job_id']);

        return [
            'node'     => [
                'type'      => 'branch',
                'id'        => $branch->id,
                'name'      => $branch->name,
                'manager'   => $branch->director_label,
                'directors' => $branch->directors_meta,
            ],
            'parent'   => $branch->section ? [
                'type' => 'section',
                'id'   => $branch->section->id,
                'name' => $branch->section->name,
            ] : null,
            'stats'    => [
                'total_employees'  => $employees->count(),
                'direct_employees' => $employees->count(),
            ],
            'children' => [
                'kind'  => 'employees',
                'label' => __('employees'),
                'items' => $employees->map(fn(Employee $employee) => [
                    'id'   => $employee->id,
                    'name' => $employee->name,
                    'job'  => $employee->job?->name,
                ])->values(),
            ],
        ];
    }

    /**
     * جلب بيانات عنصر واحد لتعبئة نافذة التعديل (JSON).
     * محمي بنفس صلاحية التحديث الخاصة بشاشة العنصر الأصلية.
     */
    public function editData(string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);

        abort_unless(auth()->check() && auth()->user()->can('update ' . $config['permission']), 403);

        $node = $config['model']::query()->findOrFail((int) $id);

        $parents = null;

        if ($type === 'section') {
            $parents = [
                'field' => 'department_id',
                'label' => __('departments'),
                'items' => Department::query()->orderBy('name')->get(['id', 'name']),
            ];
        } elseif ($type === 'branch') {
            $parents = [
                'field' => 'section_id',
                'label' => __('sections'),
                'items' => Section::query()->orderBy('name')->get(['id', 'name']),
            ];
        }

        return response()->json([
            'ok'      => true,
            'node'    => [
                'type'      => $type,
                'id'        => $node->id,
                'name'      => $node->name,
                'parent_id' => $type === 'section'
                    ? $node->department_id
                    : ($type === 'branch' ? $node->section_id : null),
            ],
            'parents' => $parents,
        ]);
    }

    /**
     * تحديث بيانات العنصر (الاسم والعنصر الأب إن وجد) وحفظه فعلياً بقاعدة البيانات.
     * محمي بنفس صلاحية التحديث الخاصة بشاشة العنصر الأصلية، مع نفس قواعد التحقق المستخدمة هناك.
     */
    public function update(Request $request, string $type, $id)
    {
        $config = $this->resolveOrgNodeType($type);

        abort_unless(auth()->check() && auth()->user()->can('update ' . $config['permission']), 403);

        $node = $config['model']::query()->findOrFail((int) $id);

        $rules = [
            'name' => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
        ];

        if ($type === 'section') {
            $rules['department_id'] = 'required|integer|exists:departments,id';
        } elseif ($type === 'branch') {
            $rules['section_id'] = 'required|integer|exists:sections,id';
        }

        $validatedData = $request->validate($rules, [
            'required' => __('required'),
            'string'   => __('string'),
            'regex'    => __('regex'),
            'numeric'  => __('numeric'),
        ]);

        $node->update($validatedData);

        // Log activity (بنفس نمط الشاشات الحالية)
        $typeLabel = $type === 'department' ? 'إدارة' : ($type === 'section' ? 'قسم' : 'شعبة');
        activity()
            ->performedOn($node)
            ->causedBy(auth()->user())
            ->log('تم تعديل ' . $typeLabel . ': ' . $node->name);

        return response()->json([
            'ok'      => true,
            'message' => __('updated successfully'),
            'node'    => [
                'type' => $type,
                'id'   => $node->id,
                'name' => $node->name,
            ],
        ]);
    }

    /**
     * خريطة أنواع الإضافة المستخدمة من داخل نافذة الهيكلية.
     * permission: نفس صلاحية الإضافة الخاصة بالشاشة الأصلية لكل نوع.
     */
    private const ORG_STORE_TYPES = [
        'section'  => ['permission' => 'store sections'],
        'branch'   => ['permission' => 'store branches'],
        // إلحاق موظف موجود: يكفي أحد الصلاحيتين (إنشاء أو تعديل)
        'employee' => ['permission' => ['store employees', 'update employees']],
    ];

    /**
     * فحص صلاحية الإضافة لنوع معين (القيمة قد تكون صلاحية واحدة أو مجموعة صلاحيات).
     */
    private function canStoreType(string $type): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $permission = self::ORG_STORE_TYPES[$type]['permission'] ?? null;

        foreach ((array) $permission as $permissionName) {
            if (auth()->user()->can($permissionName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * بيانات نموذج الإضافة: العنصر الأب الثابت (الوحدة المفتوحة) + قائمة الموظفين
     * الموجودين وتبعيتهم الكاملة عند إضافة موظف (دائرة / قسم+دائرة / شعبة+قسم+دائرة).
     */
    public function createData(string $target, string $unitType, $id)
    {
        abort_unless(isset(self::ORG_STORE_TYPES[$target]), 404);

        $unitConfig = $this->resolveOrgNodeType($unitType);
        $unit       = $unitConfig['model']::query()->findOrFail((int) $id);

        abort_unless($this->canStoreType($target), 403);

        // ربط العنصر الجديد بالوحدة المفتوحة: قسم تحت دائرة / شعبة تحت قسم / موظف تحت أي وحدة
        if ($target === 'section') {
            abort_unless($unitType === 'department', 404);
            $parent = ['field' => 'department_id', 'value' => $unit->id, 'label' => __('departments'), 'unit_type' =>
                'department',
                'unit_name'        => $unit->name];
        } elseif ($target === 'branch') {
            abort_unless($unitType === 'section', 404);
            $parent = ['field' => 'section_id', 'value' => $unit->id, 'label' => __('sections'), 'unit_type' => 'section',
                'unit_name'        => $unit->name];
        } else {
            $fields = [
                'department' => ['field' => 'department_id', 'label' => __('departments')],
                'section'    => ['field' => 'section_id', 'label' => __('sections')],
                'branch'     => ['field' => 'branch_id', 'label' => __('branches')],
            ];
            $parent = $fields[$unitType] + ['value' => $unit->id, 'unit_type' => $unitType, 'unit_name' => $unit->name];
        }

        return response()->json([
            'ok'         => true,
            'target'     => $target,
            'parent'     => $parent,
            'assignment' => $target === 'employee' ? $this->resolveEmployeeAssignment($unitType, $unit) : null,
            'employees'  => $target === 'employee'
                ? Employee::query()->orderBy('name')->get(['id', 'name', 'department_id', 'section_id', 'branch_id'])
                : [],
        ]);
    }

    /**
     * حساب التبعية الكاملة للموظف حسب الوحدة المختارة:
     * - من الدائرة: department_id فقط (ويُصفَّر القسم والشعبة)
     * - من القسم: section_id + department_id الأب (وتُصفَّر الشعبة)
     * - من الشعبة: branch_id + section_id الأب + department_id الجد.
     * تُعاد القيم كمعرفات (للاعتماد في الحفظ) + كائنات فيها الأسماء (لعرضها في النموذج).
     */
    private function resolveEmployeeAssignment(string $unitType, $unit): array
    {
        $assignment = ['department_id' => null, 'section_id' => null, 'branch_id' => null];

        $branchModel     = null;
        $sectionModel    = null;
        $departmentModel = null;

        if ($unitType === 'department') {
            $assignment['department_id'] = (int) $unit->id;
            $departmentModel             = $unit;
        } elseif ($unitType === 'section') {
            $sectionModel = $unit instanceof Section ? $unit : Section::query()->findOrFail((int) $unit->id);
            $sectionModel->loadMissing('department');
            $assignment['section_id']    = (int) $sectionModel->id;
            $assignment['department_id'] = $sectionModel->department_id ? (int) $sectionModel->department_id : null;
            $departmentModel             = $sectionModel->department;
        } elseif ($unitType === 'branch') {
            $branchModel = $unit instanceof Branch
                ? $unit->loadMissing('section.department')
                : Branch::query()->with('section.department')->findOrFail((int) $unit->id);
            $assignment['branch_id']     = (int) $branchModel->id;
            $assignment['section_id']    = $branchModel->section_id ? (int) $branchModel->section_id : null;
            $assignment['department_id'] = $branchModel->section && $branchModel->section->department_id
                ? (int) $branchModel->section->department_id
                : null;
            $sectionModel    = $branchModel->section;
            $departmentModel = $sectionModel?->department;
        }

        // أسماء الوحدات لعرضها في نموذج الإضافة (شعبة ← قسم ← دائرة)
        $assignment['branch'] = $branchModel
            ? ['id' => (int) $branchModel->id, 'name' => $branchModel->name]
            : null;
        $assignment['section'] = $sectionModel
            ? ['id' => (int) $sectionModel->id, 'name' => $sectionModel->name]
            : null;
        $assignment['department'] = $departmentModel
            ? ['id' => (int) $departmentModel->id, 'name' => $departmentModel->name]
            : null;

        return $assignment;
    }

    /**
     * إلحاق موظف موجود بوحدة (دائرة/قسم/شعبة) من داخل نافذة الهيكلية مع احتساب
     * التبعية الكاملة على الخادم — محمية بصلاحيتَي الإضافة/التعديل.
     */
    public function store(Request $request, string $type)
    {
        abort_unless(isset(self::ORG_STORE_TYPES[$type]), 404);
        abort_unless($this->canStoreType($type), 403);

        if ($type === 'section') {
            $validatedData = $request->validate([
                'name'          => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
                'department_id' => 'required|integer|exists:departments,id',
            ], [
                'required' => __('required'),
                'string'   => __('string'),
                'regex'    => __('regex'),
                'numeric'  => __('numeric'),
            ]);

            $created = Section::create($validatedData);
            activity()->causedBy(auth()->user())->log('تم إضافة قسم: ' . $created->name);
        } elseif ($type === 'branch') {
            $validatedData = $request->validate([
                'name'       => 'required|string|regex:/^[\p{L}\s]+$/u|max:255',
                'section_id' => 'required|integer|exists:sections,id',
            ], [
                'required' => __('required'),
                'string'   => __('string'),
                'regex'    => __('regex'),
                'numeric'  => __('numeric'),
            ]);

            $created = Branch::create($validatedData);
            activity()->causedBy(auth()->user())->log('تم إضافة شعبة: ' . $created->name);
        } else {
            // موظف: اختيار موظف موجود وإلحاقه بالوحدة مع تبعيتها الكاملة
            // (دائرة فقط / قسم+دائرة / شعبة+قسم+دائرة) — تُحسب من الخادم ولا يُعتمد على المُدخلات.
            $assignData = $request->validate([
                'employee_id'   => ['required', 'integer', 'exists:employees,id'],
                'department_id' => ['nullable', 'integer', 'exists:departments,id'],
                'section_id'    => ['nullable', 'integer', 'exists:sections,id'],
                'branch_id'     => ['nullable', 'integer', 'exists:branches,id'],
            ], [
                'required' => __('required'),
                'exists'   => __('regex'),
            ]);

            if (empty($assignData['department_id']) && empty($assignData['section_id']) && empty($assignData['branch_id'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assignment' => [__('employee_assignment_required')],
                ]);
            }

            $employee = Employee::query()->findOrFail((int) $assignData['employee_id']);

            $assignment = ['department_id' => null, 'section_id' => null, 'branch_id' => null];
            if (! empty($assignData['branch_id'])) {
                $branch                      = Branch::query()->with('section')->findOrFail((int) $assignData['branch_id']);
                $assignment['branch_id']     = (int) $branch->id;
                $assignment['section_id']    = $branch->section_id ? (int) $branch->section_id : null;
                $assignment['department_id'] = $branch->section && $branch->section->department_id
                    ? (int) $branch->section->department_id
                    : null;
            } elseif (! empty($assignData['section_id'])) {
                $section                     = Section::query()->findOrFail((int) $assignData['section_id']);
                $assignment['section_id']    = (int) $section->id;
                $assignment['department_id'] = $section->department_id ? (int) $section->department_id : null;
            } else {
                $assignment['department_id'] = (int) $assignData['department_id'];
            }

            $employee->update($assignment);
            $created = $employee->fresh();

            activity()->performedOn($created)->causedBy(auth()->user())->log('تم إلحاق موظف بالهيكلية: ' . $created->name);
        }

        return response()->json([
            'ok'      => true,
            'message' => __('saved successfully'),
            'created' => [
                'type' => $type,
                'id'   => $created->id,
                'name' => $created->name,
            ],
        ]);
    }

    /**
     * خريطة أنواع تعيين المدير (دائرة/قسم/شعبة) من داخل نافذة الهيكلية.
     * director: الموديل | unit_field: عمود ربط الوحدة | store/update: نفس صلاحيات شاشات المديرين الأصلية.
     */
    private const ORG_DIRECTOR_TYPES = [
        'department' => [
            'director'   => DepartmentDirector::class,
            'unit_field' => 'department_id',
            'store'      => 'store departmentdirector',
            'update'     => 'update departmentdirector',
            'delete'     => 'delete departmentdirector',
        ],
        'section'    => [
            'director'   => SectionDirector::class,
            'unit_field' => 'section_id',
            'store'      => 'store sectiondirector',
            'update'     => 'update sectiondirector',
            'delete'     => 'delete sectiondirector',
        ],
        'branch'     => [
            'director'   => BranchDirector::class,
            'unit_field' => 'branch_id',
            'store'      => 'store branchdirector',
            'update'     => 'update branchdirector',
            'delete'     => 'delete branchdirector',
        ],
    ];

    /**
     * بيانات نموذج تعيين المدير: المدير الحالي (إن وجد) + قائمة الموظفين للاختيار.
     * الصلاحية المطلوبة: store عند عدم وجود مدير، وupdate عند وجود مدير (تغيير) — مثل الشاشات الأصلية.
     */
    public function directorData(string $type, $id)
    {
        abort_unless(isset(self::ORG_DIRECTOR_TYPES[$type]), 404);

        $unitConfig = $this->resolveOrgNodeType($type);
        $unit       = $unitConfig['model']::query()->findOrFail((int) $id);

        $directorConfig = self::ORG_DIRECTOR_TYPES[$type];
        $existing       = $unit->directors()->where('director_type', 'permanent')->first();
        $acting         = $unit->directors()->where('director_type', 'acting')->first();

        $hasExistingDirector = $existing || $acting;
        $canAccess           = auth()->check() && ($hasExistingDirector
                ? auth()->user()->can($directorConfig['update']) || auth()->user()->can($directorConfig['delete'])
                : auth()->user()->can($directorConfig['store']));
        abort_unless($canAccess, 403);

        return response()->json([
            'ok'        => true,
            'node'      => [
                'type' => $type,
                'id'   => $unit->id,
                'name' => $unit->name,
            ],
            'current'   => $existing ? [
                'employee_id'   => $existing->employee_id,
                'employee_name' => optional($existing->employee)->name ?? __('vacant'),
            ] : null,
            'acting'    => $acting ? [
                'employee_id'   => $acting->employee_id,
                'employee_name' => optional($acting->employee)->name ?? __('vacant'),
            ] : null,
            // نفس تحميل قائمة الموظفين المستخدم في شاشات المديرين الأصلية
            'employees' => Employee::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * تعيين مدير جديد أو تغيير المدير الحالي للوحدة (دائرة/قسم/شعبة) وحفظه بقاعدة البيانات.
     * الموظف يمكنه إدارة أكثر من وحدة، مع تعيين مسكن ومكلف كحد أقصى لكل وحدة.
     */
    public function storeDirector(Request $request, string $type, $id)
    {
        abort_unless(isset(self::ORG_DIRECTOR_TYPES[$type]), 404);

        $unitConfig = $this->resolveOrgNodeType($type);
        $unit       = $unitConfig['model']::query()->findOrFail((int) $id);

        $directorConfig = self::ORG_DIRECTOR_TYPES[$type];
        $validatedData  = $request->validate([
            'employee_id'      => ['nullable', 'integer', 'exists:employees,id'],
            'director_type'    => ['required', 'in:permanent,acting'],
            'convert_existing' => ['nullable', 'boolean'],
        ], [
            'required' => __('required'),
            'exists'   => __('regex'),
            'unique'   => __('unique'),
        ]);

        $existing      = $unit->directors()->where('director_type', $validatedData['director_type'])->first();
        $oppositeType  = $validatedData['director_type'] === 'permanent' ? 'acting' : 'permanent';
        $opposite      = $unit->directors()->where('director_type', $oppositeType)->first();
        $shouldConvert = ! $existing && $opposite && $request->boolean('convert_existing');
        abort_unless($shouldConvert || $validatedData['employee_id'], 422);
        $permission = $existing ? $directorConfig['update'] : $directorConfig['store'];
        if ($shouldConvert) {
            $permission = $directorConfig['update'];
        }
        abort_unless(auth()->check() && auth()->user()->can($permission), 403);

        $employee = $shouldConvert
            ? $opposite->employee
            : Employee::query()->findOrFail($validatedData['employee_id']);

        if ($shouldConvert) {
            $opposite->update([
                'employee_id'   => $employee->id,
                'director_type' => $validatedData['director_type'],
            ]);
            $message = __('updated successfully');
        } elseif ($existing) {
            $existing->update(['employee_id' => $employee->id]);
            $message = __('updated successfully');
        } else {
            $directorConfig['director']::create([
                $directorConfig['unit_field'] => $unit->id,
                'employee_id'                 => $employee->id,
                'director_type'               => $validatedData['director_type'],
            ]);
            $message = __('saved successfully');
        }

        /* === تسكين الموظف في الوحدة عند تعيينه مديراً لها === */
        $employee->loadMissing(['section.department']);
        $settlement = [
            'department_id' => null,
            'section_id'    => null,
            'branch_id'     => null,
        ];
        if ($type === 'department') {
            $settlement['department_id'] = (int) $unit->id;
        } elseif ($type === 'section') {
            $settlement['section_id']    = (int) $unit->id;
            $settlement['department_id'] = $unit->department_id ? (int) $unit->department_id : null;
        } elseif ($type === 'branch') {
            $settlement['branch_id']     = (int) $unit->id;
            $settlement['section_id']    = $unit->section_id ? (int) $unit->section_id : null;
            $settlement['department_id'] = $unit->section && $unit->section->department_id
                ? (int) $unit->section->department_id
                : null;
        }
        $employee->update($settlement);

        $this->updateEmployeeJobTitle($employee, $type, $validatedData['director_type'], $validatedData['director_type'] === 'acting');

        $unitTypeLabel = $type === 'department' ? 'مدير دائرة' : ($type === 'section' ? 'رئيس قسم' : 'رئيس شعبة');
        activity()->causedBy(auth()->user())->log('تم تعيين ' . $unitTypeLabel . ': ' . $unit->name . ' → ' .
            $employee->name);

        return response()->json([
            'ok'      => true,
            'message' => $message,
            'manager' => [
                'type'          => $type,
                'id'            => $unit->id,
                'employee_id'   => $employee->id,
                'employee_name' => $employee->name,
                'director_type' => $validatedData['director_type'],
            ],
        ]);
    }

    public function deleteDirector(Request $request, string $type, $id)
    {
        abort_unless(isset(self::ORG_DIRECTOR_TYPES[$type]), 404);

        $unitConfig     = $this->resolveOrgNodeType($type);
        $unit           = $unitConfig['model']::query()->findOrFail((int) $id);
        $directorConfig = self::ORG_DIRECTOR_TYPES[$type];

        abort_unless(auth()->check() && auth()->user()->can($directorConfig['delete']), 403);

        $validatedData = $request->validate([
            'director_type' => ['required', 'in:permanent,acting'],
        ]);

        $director = $unit->directors()
            ->where('director_type', $validatedData['director_type'])
            ->firstOrFail();
        $director->delete();

        return response()->json([
            'ok'      => true,
            'message' => __('deleted successfully'),
        ]);
    }}
