<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>{!! $arabic(__('org_chart')) !!} - {!! $arabic(__('Municipality of Jabalia Al-Nazlah')) !!}</title>
    <style>
        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html,
        body {
            width: 100% !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            background: #fff;
        }

        .org-chart-page {
            --org-color-root: #4b5563;
            --org-line: #b7c0cd;
            --org-color-dept: #d92d20;
            --org-color-section: #1b84ff;
            --org-color-branch: #d99a00;
            --org-color-council: #0f766e;
            --org-node-border: #e4e8f0;
            --org-soft-bg: #f6f8fb;
            --org-card-shadow: 0 12px 32px rgba(24, 28, 50, 0.08);
            --org-node-shadow: 0 8px 22px rgba(24, 28, 50, 0.07);
            position: static !important;
            width: 100% !important;
            max-width: none !important;
            height: auto !important;
            max-height: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background: #fff !important;
        }

        .org-page-heading {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 2px solid #2c3e50;
            background-color: #f8f9fa;
        }

        .org-page-title {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .org-page-subtitle {
            font-size: 12pt;
            color: #7f8c8d;
        }

        .org-chart-root-node {
            max-width: 420px !important;
            margin: 0 auto 10px;
            border: 1px solid var(--org-node-border);
            border-top: 4px solid var(--org-color-root);
            border-radius: 10px;
            background: #fff;
            box-shadow: var(--org-node-shadow);
            padding: 20px;
            text-align: center;
        }

        .org-chart-root-node .org-node-title {
            font-size: 16pt;
            font-weight: bold;
            color: #181c32;
            margin-bottom: 5px;
        }

        .org-chart-root-node .org-node-sub {
            font-size: 12pt;
            color: #7e8299;
        }

        .org-chart-trunk {
            width: 3px;
            height: 12px;
            margin: 0 auto;
            background: var(--org-line);
            border-radius: 2px;
        }

        .org-side-units {
            display: table;
            table-layout: fixed;
            width: 72%;
            margin: 0 auto 4px;
        }

        .org-side-unit {
            display: table-cell;
            width: 33.333%;
            padding: 0 3px;
            vertical-align: top;
        }

        .org-side-unit .org-node {
            min-height: 24px;
        }

        .org-side-unit .org-node-title {
            font-size: 6pt;
        }

        .org-mayor-node {
            max-width: 180px !important;
            margin-bottom: 4px !important;
        }

        .org-chart-depts {
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            width: 100% !important;
            padding-top: 0 !important;
            margin: 0 auto !important;
            position: relative;
        }

        .org-chart-dept-row {
            display: table !important;
            table-layout: fixed;
            width: 100% !important;
            page-break-inside: avoid;
            margin-bottom: 16px;
        }

        .org-chart-depts::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--org-line) 6%, var(--org-line) 94%, transparent);
            border-radius: 2px;
            display: block !important;
        }

        .org-chart-dept-col {
            page-break-inside: avoid;
            display: table-cell !important;
            visibility: visible !important;
            width: 25% !important;
            max-width: none !important;
            vertical-align: top !important;
            padding: 26px 8px 0 !important;
            position: relative;
        }

        .org-chart-dept-col::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 26px;
            background: var(--org-line);
            border-radius: 2px;
        }

        .org-node {
            border: 1px solid var(--org-node-border);
            border-radius: 10px;
            background: #fff;
            box-shadow: var(--org-node-shadow);
            overflow: hidden;
            margin-bottom: 8px;
        }

        .org-node-dept {
            border-top: 4px solid var(--org-color-dept);
        }

        .org-node-section {
            border-top: 4px solid var(--org-color-section);
        }

        .org-node-branch {
            border-top: 3px solid var(--org-color-branch);
        }

        .org-node-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 8px 10px;
        }

        .org-node-title {
            font-size: 11pt;
            font-weight: 800;
            line-height: 1.15;
            color: #181c32;
            text-align: center !important;
        }

        .org-node-sub {
            font-size: 9.5pt;
            color: #7e8299;
            line-height: 1.25;
            margin-top: 0.15rem;
            text-align: center !important;
        }

        .org-node-body {
            font-size: 9.5pt;
            padding: 10px 12px;
        }

        .org-node-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.2rem;
            margin-top: 0.25rem;
            justify-content: center !important;
        }

        .org-mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.1rem 0.35rem;
            border-radius: 6px;
            background: #f3f6f9;
            color: #5e6278;
            font-size: 8pt;
            font-weight: 700;
        }

        .org-section-stack {
            display: flex !important;
            flex-direction: column;
            gap: 0.3rem;
        }

        .org-branch-stack {
            margin-top: 0.25rem;
            padding-top: 0.25rem;
            border-top: 1px dashed #dfe5ee;
        }

        .org-branch-label {
            font-size: 8pt;
            font-weight: 800;
            color: #7e8299;
            margin-bottom: 0.45rem;
            text-transform: uppercase;
            text-align: center !important;
        }

        .org-employee-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: none !important;
            border: 1px solid #edf0f5;
            border-radius: 8px;
            background: #fbfcfe;
        }

        .org-employee-list li {
            padding: 0.15rem 0.35rem;
            border-bottom: 1px dashed #eff2f5;
            font-size: 8pt;
            color: #3f4254;
            text-align: center !important;
        }

        .org-employee-list li:last-child {
            border-bottom: 0;
        }

        .mt-2 {
            margin-top: 0.2rem !important;
        }

        .mb-2 {
            margin-bottom: 0.2rem !important;
        }

        .mt-3 {
            margin-top: 0.35rem !important;
        }

        .mb-3 {
            margin-bottom: 0.35rem !important;
        }

        .p-3 {
            padding: 0.55rem 0.4rem !important;
        }

        .p-4 {
            padding: 0.55rem 0.4rem !important;
        }

        .text-right {
            text-align: center !important;
        }

        .org-node-head {
            justify-content: center !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 8pt;
            margin-top: 15px;
            border-top: 1px solid #d5dbe3;
            padding-top: 5px;
        }

        /* Dompdf needs a compact fixed layout to keep the complete chart on one A3 page. */
        @media print {
            @page {
                size: A3 landscape;
                margin: 3mm;
            }

            html,
            body {
                width: 100% !important;
                height: 285mm !important;
                overflow: hidden !important;
                font-family: 'DejaVu Sans', sans-serif !important;
                font-size: 6pt !important;
            }

            .org-chart-page,
            .org-chart-depts,
            .org-chart-dept-row {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }

            .org-chart-dept-row {
                table-layout: fixed !important;
            }

            .org-page-heading {
                margin-bottom: 4px !important;
                padding: 3px !important;
            }

            .org-page-title {
                font-size: 10pt !important;
                margin-bottom: 1px !important;
            }

            .org-page-subtitle {
                font-size: 6pt !important;
            }

            .org-chart-root-node {
                max-width: 240px !important;
                margin-bottom: 3px !important;
                padding: 4px 8px !important;
            }

            .org-chart-root-node .org-node-title {
                font-size: 8pt !important;
                margin-bottom: 1px !important;
            }

            .org-chart-root-node .org-node-sub {
                font-size: 5.5pt !important;
            }

            .org-side-units {
                width: 72% !important;
                margin-bottom: 2px !important;
            }

            .org-side-unit {
                padding: 0 2px !important;
            }

            .org-side-unit .org-node {
                min-height: 18px !important;
            }

            .org-side-unit .org-node-title {
                font-size: 4.5pt !important;
            }

            .org-mayor-node {
                max-width: 160px !important;
                margin-bottom: 2px !important;
            }

            .org-chart-trunk {
                height: 3px !important;
            }

            .org-chart-dept-row {
                margin-bottom: 2px !important;
            }

            .org-chart-dept-col {
                box-sizing: border-box !important;
                width: 25% !important;
                max-width: 25% !important;
                min-width: 0 !important;
                padding: 5px 2px 0 !important;
                overflow: hidden !important;
            }

            .org-chart-dept-col::before {
                height: 5px !important;
            }

            .org-node {
                margin-bottom: 1px !important;
                border-radius: 4px !important;
            }

            .org-node-head {
                gap: 2px !important;
                padding: 2px 3px !important;
            }

            .org-node-title {
                font-size: 5.5pt !important;
                line-height: 1 !important;
                overflow-wrap: anywhere !important;
                word-break: break-word !important;
            }

            .org-node-sub,
            .org-node-body,
            .org-mini-badge,
            .org-employee-list li,
            .org-branch-label {
                font-size: 4.5pt !important;
                line-height: 1 !important;
                overflow-wrap: anywhere !important;
                word-break: break-word !important;
            }

            .org-node-body {
                padding: 2px 3px !important;
            }

            .org-node-meta {
                gap: 1px !important;
                margin-top: 1px !important;
            }

            .org-mini-badge {
                padding: 1px 2px !important;
            }

            .org-section-stack,
            .org-branch-stack {
                gap: 1px !important;
                margin-top: 1px !important;
                padding-top: 1px !important;
            }

            .org-branch-label {
                margin-bottom: 1px !important;
            }

            .org-employee-list {
                border-radius: 3px !important;
            }

            .org-employee-list li {
                padding: 1px 2px !important;
            }

            .footer {
                font-size: 4.5pt !important;
                margin-top: 2px !important;
                padding-top: 1px !important;
            }
        }
    </style>
</head>

<body>
    <div class="org-chart-page">
        <div class="org-page-heading">
            <div class="org-page-title">{!! $arabic(__('org_chart')) !!}</div>
            <div class="org-page-subtitle">{!! $arabic(__('Municipality of Jabalia Al-Nazlah')) !!}</div>
        </div>

        <div class="org-chart-root-node">
            <div class="org-node-title">{!! $arabic(__('Municipality of Jabalia Al-Nazlah')) !!}</div>
            <div class="org-node-sub">{!! $arabic(__('Structural')) !!}</div>
        </div>

        <div class="org-chart-trunk"></div>

        <div class="org-chart-root-node"
            style="max-width: 220px; padding: 5px 10px; border-top-color: var(--org-color-council);">
            <div class="org-node-title" style="font-size: 9pt;">{!! $arabic(__('Municipal Council')) !!}</div>
        </div>

        <div class="org-chart-trunk"></div>

        <div class="org-side-units">
            <div class="org-side-unit">
                <div class="org-node org-node-section">
                    <div class="org-node-head">
                        <div class="org-node-title">{!! $arabic(__('External Audit')) !!}</div>
                    </div>
                </div>
            </div>
            <div class="org-side-unit">
                <div class="org-node org-node-section">
                    <div class="org-node-head">
                        <div class="org-node-title">{!! $arabic(__('Municipal Council Committees Section')) !!}</div>
                    </div>
                </div>
            </div>
            <div class="org-side-unit">
                <div class="org-node org-node-section">
                    <div class="org-node-head">
                        <div class="org-node-title">{!! $arabic(__('Internal Control Unit')) !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="org-chart-trunk"></div>

        <div class="org-chart-root-node org-mayor-node"
            style="border-top-color: var(--org-color-branch); padding: 5px 10px;">
            <div class="org-node-title" style="font-size: 9pt; color: var(--org-color-branch);">{!! $arabic(__('Mayor')) !!}
            </div>
        </div>

        <div class="org-chart-trunk"></div>

        <div class="org-chart-depts">
            @foreach ($departments->chunk(4) as $departmentRow)
                <div class="org-chart-dept-row">
                    @foreach ($departmentRow as $department)
                        @php
                            $deptEmps = $departmentEmployeesByDepartment->get($department->id, collect());
                        @endphp

                        <div class="org-chart-dept-col">
                            <div class="org-node org-node-dept">
                                <div class="org-node-head">
                                    <div class="org-node-title">{!! $arabic($department->name) !!}</div>
                                    <div class="org-node-sub">{!! $arabic(__('department director')) !!}:
                                        {!! $arabic($department->director_label) !!}</div>
                                </div>
                                <div class="org-node-body">
                                    <div class="org-node-meta">
                                        <div class="org-mini-badge">
                                            <i class="fas fa-users"></i>
                                            {!! $arabic(__('employees')) !!}: {{ $deptEmps->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($deptEmps->count() > 0)
                                <div class="org-section-stack mt-2">
                                    <ul class="org-employee-list">
                                        @foreach ($deptEmps as $emp)
                                            <li>
                                                <strong>{!! $arabic($emp->name) !!}</strong>
                                                @if ($emp->job)
                                                    <div style="font-size: 8pt; color: #777;">{!! $arabic($emp->job->name) !!}
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @foreach ($department->sections as $section)
                                @php
                                    $secEmps = $sectionEmployeesBySection->get($section->id, collect());
                                @endphp

                                <div class="org-section-stack mt-2">
                                    <div class="org-node org-node-section">
                                        <div class="org-node-head">
                                            <div class="org-node-title">{!! $arabic($section->name) !!}</div>
                                            <div class="org-node-sub">{!! $arabic(__('section director')) !!}:
                                                {!! $arabic($section->director_label) !!}</div>
                                        </div>
                                        <div class="org-node-body">
                                            <div class="org-node-meta">
                                                <div class="org-mini-badge">
                                                    <i class="fas fa-users"></i>
                                                    {!! $arabic(__('employees')) !!}: {{ $secEmps->count() }}
                                                </div>
                                                <div class="org-mini-badge">
                                                    <i class="fas fa-sitemap"></i>
                                                    {!! $arabic(__('branches')) !!}: {{ $section->branches->count() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($secEmps->count() > 0)
                                        <ul class="org-employee-list mt-2">
                                            @foreach ($secEmps as $emp)
                                                <li>
                                                    <strong>{!! $arabic($emp->name) !!}</strong>
                                                    @if ($emp->job)
                                                        <div style="font-size: 8pt; color: #777;">
                                                            {!! $arabic($emp->job->name) !!}</div>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if ($section->branches->count() > 0)
                                        <div class="org-branch-stack mt-2">
                                            @foreach ($section->branches as $branch)
                                                @php
                                                    $brEmps = $branchEmployeesByBranch->get($branch->id, collect());
                                                @endphp

                                                <div class="org-branch-label">{!! $arabic($branch->name) !!}</div>
                                                <div class="org-node org-node-branch">
                                                    <div class="org-node-head">
                                                        <div class="org-node-title">{!! $arabic($branch->name) !!}</div>
                                                        <div class="org-node-sub">{!! $arabic(__('branch director')) !!}:
                                                            {!! $arabic($branch->director_label) !!}</div>
                                                    </div>
                                                    <div class="org-node-body">
                                                        <div class="org-node-meta">
                                                            <div class="org-mini-badge">
                                                                <i class="fas fa-users"></i>
                                                                {!! $arabic(__('employees')) !!}: {{ $brEmps->count() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if ($brEmps->count() > 0)
                                                    <ul class="org-employee-list mt-2">
                                                        @foreach ($brEmps as $emp)
                                                            <li>
                                                                <strong>{!! $arabic($emp->name) !!}</strong>
                                                                @if ($emp->job)
                                                                    <div style="font-size: 8pt; color: #777;">
                                                                        {!! $arabic($emp->job->name) !!}</div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="footer">
            {!! $arabic(__('Generated on')) !!}: {{ now()->format('Y-m-d H:i:s') }} —
            {!! $arabic(__('Municipality of Jabalia Al-Nazlah')) !!}
        </div>
    </div>
</body>

</html>
