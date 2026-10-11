<!DOCTYPE html>
<html dir="rtl" lang="ar" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
<head>
    <meta charset="UTF-8">
    <title>{{ __('org_chart') }}</title>
    <style>
        /* صفحة أفقية (Landscape) متوافقة مع Word عبر MSO */
        @page WordSection1 {
            size: 29.7cm 21.0cm;
            margin: 1.2cm;
            mso-page-orientation: landscape;
        }
        div.WordSection1 { page: WordSection1; }

        body {
            font-family: Arial, Tahoma, sans-serif;
            direction: rtl;
            margin: 0;
            padding: 10px;
            font-size: 11pt;
        }

        .org-header {
            text-align: center;
            margin-bottom: 12pt;
            padding: 8pt;
            border: 2pt solid #2c3e50;
            background-color: #f8f9fa;
        }
        .org-title {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 4pt;
        }
        .org-subtitle {
            font-size: 11pt;
            color: #7f8c8d;
        }

        /* عقدة الجذر (البلدية) أعلى المخطط */
        .root-node {
            text-align: center;
            margin: 0 auto 4pt;
            padding: 8pt 24pt;
            border: 3pt solid #4b5563;
            background-color: #eceff3;
            font-weight: bold;
            font-size: 14pt;
            width: 42%;
        }
        .root-node .node-meta {
            font-size: 9pt;
            font-weight: normal;
            color: #7f8c8d;
            margin-top: 2pt;
        }
        .connector {
            text-align: center;
            color: #94a3b8;
            font-size: 10pt;
            margin: 2pt 0 8pt;
        }

        /* شجرة أفقية لكل دائرة: الدائرة يميناً ثم الأقسام ثم الشعب ثم الموظفين يساراً */
        .dept-wrap { margin-bottom: 14pt; }
        table.org-tree {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.org-tree td {
            vertical-align: middle;
            text-align: center;
            padding: 5pt 6pt;
            font-size: 10pt;
        }
        .cell-dept {
            border: 2pt solid #d92d20;
            background-color: #fdf2f2;
            width: 20%;
        }
        .cell-section {
            border: 2pt solid #1b84ff;
            background-color: #f0f8ff;
            width: 22%;
        }
        .cell-branch {
            border: 1pt solid #d99a00;
            background-color: #fff9e6;
            width: 24%;
        }
        .cell-emp {
            border: 1pt solid #27ae60;
            background-color: #ffffff;
            width: 34%;
            font-size: 9pt;
        }
        .cell-empty {
            border: 1pt dashed #c8d0dc;
            background-color: #fafbfc;
            color: #b5b5c3;
        }
        .node-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 2pt;
        }
        .cell-dept .node-title { color: #b3241b; }
        .cell-section .node-title { color: #0f6cdd; }
        .cell-branch .node-title { color: #a87900; }
        .node-meta {
            font-size: 8.5pt;
            color: #555555;
            margin-top: 1pt;
        }
        .emp-name {
            font-weight: bold;
            font-size: 9pt;
        }
        .emp-job {
            font-size: 8pt;
            color: #777777;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 9pt;
            margin-top: 14pt;
            border-top: 1pt solid #d5dbe3;
            padding-top: 6pt;
        }
    </style>
</head>
<body>
<div class="WordSection1">
    <div class="org-header">
        <div class="org-title">{{ __('org_chart') }}</div>
        <div class="org-subtitle">{{ __('Municipality of Jabalia Al-Nazlah') }}</div>
    </div>

    <div class="root-node">
        {{ __('Municipality of Jabalia Al-Nazlah') }}
        <div class="node-meta">{{ __('Structural') }}</div>
    </div>
    <div class="connector">▼</div>
@foreach($departments as $department)
    @php
        $deptEmps = $departmentEmployeesByDepartment->get($department->id, collect());

        // بناء صفوف الشجرة الأفقية: كل صف = مسار كامل (دائرة ← قسم ← شعبة ← موظف)
        $rows = [];
        foreach ($department->sections as $section) {
            $secEmps = $sectionEmployeesBySection->get($section->id, collect());

            $branchBlocks = [];
            foreach ($section->branches as $branch) {
                $brEmps = $branchEmployeesByBranch->get($branch->id, collect());
                $branchBlocks[] = ['branch' => $branch, 'emps' => $brEmps, 'rows' => max(1, $brEmps->count())];
            }

            $branchRowsTotal = 0;
            foreach ($branchBlocks as $bb) { $branchRowsTotal += $bb['rows']; }

            $sectionRows = max(1, $secEmps->count() + $branchRowsTotal);
            $emitted = 0;

            // صفوف الشعب وموظفيها
            foreach ($branchBlocks as $bb) {
                for ($i = 1; $i <= $bb['rows']; $i++) {
                    $rows[] = [
                        'section' => $emitted === 0
                            ? ['type' => 'real', 'rows' => $sectionRows, 'model' => $section]
                            : null,
                        'branch' => $i === 1
                            ? ['type' => 'real', 'rows' => $bb['rows'], 'model' => $bb['branch']]
                            : null,
                        'emp' => $bb['emps']->get($i - 1),
                    ];
                    $emitted++;
                }
            }

            // موظفو القسم المباشرون (غير المنتمين لشعبة)
            foreach ($secEmps as $secEmp) {
                $rows[] = [
                    'section' => $emitted === 0
                        ? ['type' => 'real', 'rows' => $sectionRows, 'model' => $section]
                        : null,
                    'branch' => ['type' => 'empty', 'rows' => 1],
                    'emp' => $secEmp,
                ];
                $emitted++;
            }

            // قسم بلا شعب ولا موظفين
            if ($emitted === 0) {
                $rows[] = [
                    'section' => ['type' => 'real', 'rows' => 1, 'model' => $section],
                    'branch' => ['type' => 'empty', 'rows' => 1],
                    'emp' => null,
                ];
            }
        }

        // موظفو الدائرة المباشرون
        foreach ($deptEmps as $deptEmp) {
            $rows[] = [
                'section' => ['type' => 'empty', 'rows' => 1],
                'branch' => ['type' => 'empty', 'rows' => 1],
                'emp' => $deptEmp,
            ];
        }

        // دائرة بلا أقسام ولا موظفين
        if (count($rows) === 0) {
            $rows[] = [
                'section' => ['type' => 'empty', 'rows' => 1],
                'branch' => ['type' => 'empty', 'rows' => 1],
                'emp' => null,
            ];
        }

        $deptRows = count($rows);
    @endphp

    <div class="dept-wrap">
        <table class="org-tree">
            <tr>
                <td class="cell-dept" rowspan="{{ $deptRows }}">
                    <div class="node-title">{{ $department->name }}</div>
                    <div class="node-meta">{{ __('department director') }}: {{ $department->director_label }}</div>
                    <div class="node-meta">{{ __('employees') }}: {{ $deptEmps->count() }}</div>
                </td>
            @foreach($rows as $row)
                @if(!$loop->first)
                    <tr>
                @endif
                @if($row['section'] !== null)
                    @if($row['section']['type'] === 'real')
                        <td class="cell-section" rowspan="{{ $row['section']['rows'] }}">
                            <div class="node-title">{{ $row['section']['model']->name }}</div>
                            <div class="node-meta">{{ __('section director') }}: {{ $row['section']['model']->director_label }}</div>
                        </td>
                    @else
                        <td class="cell-empty" rowspan="{{ $row['section']['rows'] }}"></td>
                    @endif
                @endif
                @if($row['branch'] !== null)
                    @if($row['branch']['type'] === 'real')
                        <td class="cell-branch" rowspan="{{ $row['branch']['rows'] }}">
                            <div class="node-title">{{ $row['branch']['model']->name }}</div>
                            <div class="node-meta">{{ __('branch director') }}: {{ $row['branch']['model']->director_label }}</div>
                        </td>
                    @else
                        <td class="cell-empty" rowspan="{{ $row['branch']['rows'] }}"></td>
                    @endif
                @endif
                @if($row['emp'])
                    <td class="cell-emp">
                        <div class="emp-name">{{ $row['emp']->name }}</div>
                        @if($row['emp']->job)
                            <div class="emp-job">{{ $row['emp']->job->name }}</div>
                        @endif
                    </td>
                @else
                    <td class="cell-emp cell-empty">—</td>
                @endif
                </tr>
            @endforeach
        </table>
    </div>

    @if(!$loop->last)
        <div class="connector">▼</div>
    @endif
@endforeach
    <div class="footer">
        {{ __('Generated on') }}: {{ now()->format('Y-m-d H:i:s') }} — {{ __('Municipality of Jabalia Al-Nazlah') }}
    </div>
</div>
</body>
</html>
