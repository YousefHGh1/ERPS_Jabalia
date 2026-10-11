@extends('layout.master')

@section('title')
    {{ __('Legacy Engineering Files') }} — {{ $file->FILE_NO ?? $file->ID }}
@endsection

@section('page_title')
    {{ __('Legacy Engineering Files') }}
@endsection

@section('sub_title')
    {{ __('View legacy file') }}
@endsection

@section('content')
    <div class="lef-page container-fluid" dir="rtl">
        <div class="card card-custom lef-hero gutter-b">
            <div class="card-header border-0 py-6 lef-hero-header">
                <h3 class="card-title align-items-start flex-column">
                    <span class="lef-hero-eyebrow">{{ __('Legacy Engineering Files') }}</span>
                    <span class="card-label font-weight-bolder text-white lef-hero-title">
                        {{ $file->FILE_NO ?? '#' . $file->ID }}
                    </span>
                    <span class="lef-hero-sub mt-2">
                        {{ __('Historical engineering and organization data (read-only)') }}
                    </span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('legacy-engineering.index') }}" class="lef-btn lef-btn-ghost">
                        <i class="fas fa-arrow-right"></i> {{ __('Back to list') }}
                    </a>
                    <button onclick="printSheet('print-mansha')" class="lef-btn lef-btn-gold ml-2">
                        <i class="fas fa-print"></i> {{ __('طباعة شهادة المنشأة') }}
                    </button>
                    <button onclick="printSheet('print-efada')" class="lef-btn lef-btn-solid ml-2">
                        <i class="fas fa-print"></i> {{ __('طباعة الإفادة') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="row lef-print-area">
            <div class="col-12 mb-6">
                <div class="print-sheet" id="print-mansha">
                    <div class="lef-doc-card">
                        <div class="lef-doc-tab lef-doc-tab-gold">
                            <span class="lef-doc-tab-label">{{ __('شهادة منشأة') }}</span>
                        </div>
                        <div class="lef-doc-body">
                            <div class="print-body">
                                @include('legacy-engineering.shahadat-mansha-print', ['file' => $file])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="print-sheet" id="print-efada">
                    <div class="lef-doc-card">
                        <div class="lef-doc-tab lef-doc-tab-maroon">
                            <span class="lef-doc-tab-label">{{ __('إفادة') }}</span>
                        </div>
                        <div class="lef-doc-body">
                            <div class="print-body">
                                @include('legacy-engineering.efada-print', ['file' => $file])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        /* ============ رموز الهوية البصرية (مستمدة من ترويسة بلدية جباليا النزلة) ============ */
        .lef-page {
            --gov-maroon: #b03a2e;
            --gov-maroon-dark: #8a2c22;
            --gov-navy: #2e5aa8;
            --gov-gold: #c9962e;
            --gov-gold-light: #e8c877;
            --gov-paper: #faf8f5;
            --gov-line: #e7e0d6;
            --gov-ink: #2a2420;
            font-family: 'Tajawal', 'Traditional Arabic', 'Segoe UI', Tahoma, sans-serif;
            color: var(--gov-ink);
        }

        /* ============ رأس الصفحة ============ */
        .lef-hero {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px -12px rgba(138, 44, 34, 0.35);
        }
        .lef-hero-header {
            background: linear-gradient(120deg, var(--gov-maroon) 0%, var(--gov-maroon-dark) 65%, #6f221a 100%);
            position: relative;
            flex-wrap: wrap;
            row-gap: 16px;
        }
        .lef-hero-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.06), transparent 40%);
            pointer-events: none;
        }
        .lef-hero-eyebrow {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--gov-gold-light);
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .lef-hero-title {
            font-size: 1.6rem;
            letter-spacing: .01em;
        }
        .lef-hero-sub {
            display: block;
            color: rgba(255,255,255,0.78);
            font-weight: 500;
            font-size: 13px;
        }

        /* ============ الأزرار ============ */
        .lef-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid transparent;
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
            white-space: nowrap;
        }
        .lef-btn i { font-size: 1rem; line-height: 0; opacity: .9; }
        .lef-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .lef-btn:focus-visible { outline: 2px solid var(--gov-gold-light); outline-offset: 2px; }

        .lef-btn-ghost {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border-color: rgba(255,255,255,0.35);
        }
        .lef-btn-ghost:hover {
            background: rgba(255,255,255,0.18);
            color: #fff;
        }

        .lef-btn-gold {
            background: linear-gradient(135deg, var(--gov-gold-light), var(--gov-gold));
            color: #4a3305;
            box-shadow: 0 6px 16px -6px rgba(201, 150, 46, 0.65);
        }
        .lef-btn-gold:hover {
            color: #4a3305;
            box-shadow: 0 10px 20px -6px rgba(201, 150, 46, 0.75);
        }

        .lef-btn-solid {
            background: #fff;
            color: var(--gov-maroon-dark);
            box-shadow: 0 6px 16px -6px rgba(0,0,0,0.25);
        }
        .lef-btn-solid:hover {
            color: var(--gov-maroon-dark);
            box-shadow: 0 10px 20px -6px rgba(0,0,0,0.3);
        }

        /* ============ بطاقات المستندات ============ */
        .lef-doc-card {
            border-radius: 14px;
            background: #fff;
            border: 1px solid var(--gov-line);
            box-shadow: 0 8px 24px -14px rgba(42, 36, 32, 0.35);
            overflow: hidden;
            transition: box-shadow .2s ease, transform .2s ease;
        }
        .lef-doc-card:hover {
            box-shadow: 0 14px 30px -16px rgba(42, 36, 32, 0.45);
            transform: translateY(-2px);
        }
        .lef-doc-tab {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 22px;
            border-bottom: 1px solid var(--gov-line);
            background: var(--gov-paper);
        }
        .lef-doc-tab::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 3px;
            flex: 0 0 auto;
        }
        .lef-doc-tab-gold::before { background: var(--gov-gold); }
        .lef-doc-tab-maroon::before { background: var(--gov-maroon); }
        .lef-doc-tab-label {
            font-weight: 800;
            font-size: 15px;
            color: var(--gov-ink);
        }
        .lef-doc-body {
            background: #fff;
        }

        /* الشاشة: عرض النموذج بخلفيته الأصلية */
        .print-sheet {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        .lef-doc-body {
            position: relative;
        }
        .lef-doc-body {
            background-image: url('{{ asset('images/image.png') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #fff;
            min-height: 900px;
        }
        /* المنطقة الوسطى التي تُرسم فيها البيانات */
        .print-body {
            position: relative;
            padding: 18% 12% 30%;
        }

        @media (max-width: 576px) {
            .lef-hero-header { text-align: center; justify-content: center; }
            .card-toolbar { width: 100%; justify-content: center; flex-wrap: wrap; row-gap: 8px; }
            .lef-btn { flex: 1 1 auto; justify-content: center; }
        }

        @media print {
            /* اخفِ كل عناصر الصفحة (بما فيها هيدر الموقع والقائمة الجانبية
               والعنوان الكبير القادمين من layout.master) وأظهر منطقة
               المستندات فقط — هذا يعمل بغض النظر عن أسماء الأصناف (classes)
               التي يستخدمها التخطيط الرئيسي، لأننا لا نعدّل ذلك الملف هنا. */
            body * {
                visibility: hidden !important;
            }
            .lef-print-area,
            .lef-print-area * {
                visibility: visible !important;
            }
            .lef-print-area {
                position: absolute !important;
                top: 0 !important;
                right: 0 !important;
                left: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .lef-hero, .lef-doc-tab, .btn, .lef-btn {
                display: none !important;
            }
            .lef-doc-card {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .lef-doc-body {
                padding: 0 !important;
            }
            img {
                max-width: 100% !important;
                page-break-inside: avoid;
            }
            .container-fluid,
            .row,
            .col-12 {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 0;
            }
            body {
                margin: 0;
            }
            .print-sheet {
                max-width: 100%;
                page-break-after: always;
            }
            .print-sheet:last-child {
                page-break-after: auto;
            }
            body.print-mansha .print-sheet:not(#print-mansha),
            body.print-efada .print-sheet:not(#print-efada) {
                display: none !important;
            }
            body.print-mansha #print-mansha,
            body.print-efada #print-efada {
                page-break-after: auto;
            }
            .print-sheet .lef-doc-card {
                width: 210mm;
                height: 297mm;
                display: block;
            }
            .lef-doc-body {
                background-image: url('{{ asset('images/image.png') }}');
                background-size: 100% 100%;
                background-repeat: no-repeat;
                background-position: center;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                width: 210mm;
                height: 297mm;
                padding: 0 !important;
            }
            .print-body {
                padding: 55mm 20mm 18mm;
                width: 210mm;
                height: 297mm;
                box-sizing: border-box;
            }
        }
    </style>

    <script>
        function printSheet(sheetId) {
            document.body.classList.remove('print-mansha', 'print-efada');
            document.body.classList.add(sheetId);
            // تأخير قصير لإعطاء المتصفح (خاصة Chrome/Edge) وقتًا كافيًا لتطبيق
            // الأنماط الجديدة (إخفاء الورقة الأخرى) قبل فتح نافذة الطباعة —
            // بدونه قد تُطبع الورقتان معًا، وتظهر الورقة غير المطلوبة أولًا،
            // كما حدث سابقًا.
            setTimeout(function () {
                window.print();
            }, 60);
        }
        window.onafterprint = function () {
            document.body.classList.remove('print-mansha', 'print-efada');
        };
    </script>
@endsection
