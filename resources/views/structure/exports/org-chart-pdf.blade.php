<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ __('org_chart') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            direction: rtl;
            line-height: 1.4;
        }
        .org-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        .org-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .org-subtitle {
            font-size: 16px;
            color: #7f8c8d;
        }
        .department {
            border: 2px solid #e74c3c;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #fdf2f2;
            page-break-inside: avoid;
        }
        .department-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .section {
            border: 1px solid #3498db;
            border-radius: 8px;
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 8px;
        }
        .branch {
            border: 1px solid #f39c12;
            border-radius: 6px;
            margin: 8px 0;
            padding: 8px;
            background: #fef9e7;
        }
        .branch-title {
            font-size: 14px;
            font-weight: bold;
            color: #f39c12;
            margin-bottom: 5px;
        }
        .employee-list {
            margin: 5px 0;
            padding-right: 20px;
        }
        .employee-item {
            margin: 3px 0;
            font-size: 13px;
        }
        .director {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-print {
            display: none;
        }
        
        @media screen {
            .print-only {
                display: none;
            }
            body {
                max-width: 1200px;
                margin: 0 auto;
            }
        }
        
        @media print {
            .print-only {
                display: block !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: left; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> {{ __('print') }}
        </button>
        <button onclick="window.close()" class="btn btn-secondary ml-2">
            <i class="fas fa-times"></i> {{ __('close') }}
        </button>
    </div>
    
    <div class="org-header">
        <div class="org-title">{{ __('Municipality of Jabalia Al-Nazlah') }}</div>
        <div class="org-subtitle">{{ __('Organizational Chart') }} - {{ date('Y-m-d') }}</div>
    </div>

    @if($departments->isEmpty())
        <div style="text-align: center; padding: 50px;">
            <h3>{{ __('no_records') }}</h3>
        </div>
    @else
        @foreach($departments as $index => $department)
            @if($index > 0)
                <div class="page-break"></div>
            @endif
            
            @php
                $deptEmps = $departmentEmployeesByDepartment->get($department->id, collect());
            @endphp
            
            <div class="department">
                <div class="department-title">{{ $department->name }}</div>
                <div class="director">
                    {{ __('department director') }}: {{ $department->director_label }}
                </div>
                
                @if($deptEmps->isNotEmpty())
                    <div class="employee-list">
                        <strong>{{ __('employees') }} ({{ __('department') }}):</strong>
                        @foreach($deptEmps as $emp)
                            <div class="employee-item">
                                {{ $emp->name }} @if($emp->job) - {{ $emp->job->name }} @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @forelse($department->sections as $section)
                    @php
                        $secEmps = $sectionEmployeesBySection->get($section->id, collect());
                    @endphp
                    
                    <div class="section">
                        <div class="section-title">{{ $section->name }}</div>
                        <div class="director">
                            {{ __('section director') }}: {{ $section->director_label }}
                        </div>
                        
                        @if($secEmps->isNotEmpty())
                            <div class="employee-list">
                                <strong>{{ __('employees') }} ({{ __('section') }}):</strong>
                                @foreach($secEmps as $emp)
                                    <div class="employee-item">
                                        {{ $emp->name }} @if($emp->job) - {{ $emp->job->name }} @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($section->branches->isNotEmpty())
                            @foreach($section->branches as $branch)
                                @php
                                    $brEmps = $branchEmployeesByBranch->get($branch->id, collect());
                                @endphp
                                
                                <div class="branch">
                                    <div class="branch-title">{{ $branch->name }}</div>
                                    <div class="director">
                                        {{ __('branch director') }}: {{ $branch->director_label }}
                                    </div>
                                    
                                    @if($brEmps->isNotEmpty())
                                        <div class="employee-list">
                                            <strong>{{ __('employees') }}:</strong>
                                            @foreach($brEmps as $emp)
                                                <div class="employee-item">
                                                    {{ $emp->name }} @if($emp->job) - {{ $emp->job->name }} @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @empty
                    <div style="color: #7f8c8d; font-style: italic;">{{ __('no_records') }} ({{ __('sections') }})</div>
                @endforelse
            </div>
        @endforeach
    @endif

    <div style="margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px;">
        {{ __('Generated on') }}: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
