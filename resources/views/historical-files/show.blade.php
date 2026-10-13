@extends('layout.master')

@section('title', 'إفادة حرفة تاريخية — ' . ($fileNumber ?: $craft->id))

@section('page_title')
    الاستعلام التاريخي للحرف
@endsection

@section('sub_title')
    إفادة حرفة
@endsection

@section('content')
    <div class="container-fluid historical-craft-page" dir="rtl">
        <div class="card card-custom shadow-sm border-0 mb-6 historical-craft-toolbar">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="mb-1">إفادة حرفة تاريخية</h3>
                    <span class="text-muted">رقم الملف: {{ $fileNumber ?: '—' }}</span>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('historical-files.index', request()->query()) }}" class="btn btn-light-primary ml-2">
                        <i class="fas fa-arrow-right"></i> العودة للاستعلام
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> طباعة الإفادة
                    </button>
                </div>
            </div>
        </div>

        <div class="historical-craft-print-area">
            <div class="historical-craft-sheet">
                <div class="historical-craft-document" dir="rtl">
                    <div class="historical-craft-title">إفادة حرفة</div>

                    <div class="historical-craft-body">
                        <p>
                            تفيد بلدية جباليا النزلة بأن السيد /
                            <strong><u>{{ $ownerName ?: '..........................' }}</u></strong>
                            هوية رقم /
                            <strong><u>{{ !empty($data['id_card']) ? $data['id_card'] : '..........................' }}</u></strong>
                            له ملف حرفة رقم /
                            <strong><u>{{ $fileNumber ?: '..........................' }}</u></strong>
                            لمزاولة حرفة
                            <strong><u>{{ $data['detailed_type'] ?? '..........................' }}</u></strong>
                            @if (!empty($data['trade_mark_name']) && $data['trade_mark_name'] !== $ownerName)
                                باسم تجاري
                                <strong><u>{{ $data['trade_mark_name'] }}</u></strong>
                            @endif
                            والكائنة في
                            <strong><u>{{ $data['address'] ?? '..........................' }}</u></strong>.
                        </p>

                        @if (!empty($data['first_license_year']))
                            <p>
                                وتشير السجلات التاريخية إلى أن أول ترخيص للحرفة كان سنة
                                <strong><u>{{ $data['first_license_year'] }}</u></strong>.
                            </p>
                        @endif

                        <p class="historical-craft-purpose">
                            وقد أعطيت هذه الإفادة بناءً على طلبه لتقديمها لمن يهمه الأمر.
                        </p>
                        <p class="historical-craft-regards">*** بمزيد الاحترام ***</p>
                    </div>

                    <table class="historical-craft-signatures">
                        <tr>
                            <td>
                                <div class="historical-craft-stamp"></div>
                                <strong>رئيس قسم الحرف والصناعات</strong>
                                <div>بلدية جباليا النزلة</div>
                            </td>
                            <td>
                                <div class="historical-craft-sign-space"></div>
                                <strong>ختم البلدية</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .historical-craft-page {
            --craft-maroon: #8a2c22;
            --craft-line: #e7e0d6;
            color: #2a2420;
            font-family: 'Tajawal', 'Traditional Arabic', 'Segoe UI', Tahoma, sans-serif;
        }
        .historical-craft-sheet {
            width: 100%;
            max-width: 800px;
            min-height: 900px;
            margin: 0 auto;
            background: #fff url('{{ asset('images/image.png') }}') center / 100% 100% no-repeat;
            box-shadow: 0 8px 24px -14px rgba(42, 36, 32, .4);
        }
        .historical-craft-document {
            min-height: 900px;
            padding: 18% 12% 12%;
            line-height: 1.9;
        }
        .historical-craft-title {
            margin: 15px 0 25px;
            padding: 8px 0;
            background: #f2f2f2;
            font-size: 20px;
            font-weight: 800;
            text-align: center;
        }
        .historical-craft-body {
            font-family: 'Traditional Arabic', 'Arial', sans-serif;
            font-size: 17px;
            text-align: justify;
        }
        .historical-craft-body p { margin: 0 0 20px; }
        .historical-craft-purpose,
        .historical-craft-regards { margin: 28px 0 !important; text-align: center; }
        .historical-craft-signatures {
            width: 100%;
            margin-top: 70px;
            border-collapse: collapse;
            text-align: center;
        }
        .historical-craft-signatures td { width: 50%; vertical-align: top; }
        .historical-craft-stamp { height: 75px; }
        .historical-craft-sign-space { height: 75px; }

        @media print {
            body * { visibility: hidden !important; }
            .historical-craft-print-area,
            .historical-craft-print-area * { visibility: visible !important; }
            .historical-craft-toolbar { display: none !important; }
            .historical-craft-print-area {
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .historical-craft-sheet {
                width: 210mm;
                height: 297mm;
                max-width: 100%;
                min-height: 0;
                margin: 0;
                box-shadow: none;
                page-break-after: auto;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .historical-craft-document {
                width: 210mm;
                height: 297mm;
                min-height: 0;
                padding: 55mm 20mm 18mm;
                box-sizing: border-box;
            }
            .historical-craft-signatures { margin-top: 45px; }
            .historical-craft-stamp,
            .historical-craft-sign-space { height: 55px; }
            @page { size: A4; margin: 0; }
            body { margin: 0; }
        }
    </style>
@endsection
