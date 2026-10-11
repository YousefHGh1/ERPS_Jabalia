<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب خدمة - {{ $request->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-maroon: #b03a2e;
            --brand-maroon-dark: #8a2d23;
            --brand-gold: #c9962e;
            --brand-gold-light: #f4e6c8;
            --ink: #1a1a1a;
            --muted: #6b6b6b;
            --panel-bg: #faf6ef;
            --border: #e6ddc9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            color: var(--ink);
            margin: 0;
        }

        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-btn button {
            background: var(--brand-maroon);
            color: #fff;
            border: 0;
            padding: 12px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
        }

        .print-btn button:hover {
            background: var(--brand-maroon-dark);
        }

        .request-sheet {
            max-width: 800px;
            min-height: 900px;
            margin: 0 auto;
            background: #fff url('{{ asset('images/image.png') }}') center / 100% 100% no-repeat;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .request-body {
            padding: 18% 12% 25%;
        }

        .print-date {
            text-align: left;
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .title-block {
            text-align: center;
            margin: 10px 0 30px;
        }

        .title {
            color: var(--brand-maroon);
            font-size: 26px;
            font-weight: 800;
            text-decoration: underline;
        }

        .subtitle {
            color: var(--muted);
            font-size: 16px;
            margin-top: 6px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 26px;
        }

        .info-box {
            background: var(--panel-bg);
            padding: 18px;
            border-radius: 8px;
            border-right: 4px solid var(--brand-maroon);
        }

        .info-box h3,
        .details-section h3 {
            margin: 0 0 10px;
            color: var(--brand-maroon);
            font-size: 18px;
            font-weight: 700;
        }

        .info-box p {
            margin: 7px 0;
            color: #333;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .value {
            font-weight: 700;
            color: #000;
            text-align: left;
        }

        .details-section {
            background: var(--brand-gold-light);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 26px;
            border: 1px solid var(--border);
        }

        .request-text-section {
            margin-top: 26px;
        }

        .request-text {
            background: #fff;
            border: 1px solid var(--border);
            border-right: 4px solid var(--brand-gold);
            border-radius: 6px;
            min-height: auto;
            padding: 18px;
            line-height: 2;
            white-space: pre-wrap;
            font-size: 17px;
        }

        .address-detail-inline {
            font-weight: 700;
            color: var(--brand-maroon);
            font-size: 15px;
            padding: 8px 12px;
            background: var(--brand-gold-light);
            border-radius: 4px;
            margin-bottom: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .address-detail-section {
            background: var(--panel-bg);
            padding: 18px;
            border-radius: 8px;
            border-right: 4px solid var(--brand-maroon);
            margin-bottom: 26px;
        }

        .address-detail-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .address-detail-section h3 {
            margin: 0;
            color: var(--brand-maroon);
            font-size: 18px;
            font-weight: 700;
            flex: 0 0 auto;
            width: 130px;
            white-space: nowrap;
        }

        /* عنوان "نص الطلب" بجانب بوكس النص (صف أفقي) بدل أن يكون فوقه */
        .request-text-section .details-section {
            display: flex;
            align-items: stretch;
            gap: 14px;
        }

        .request-text-section .details-section h3 {
            flex: 0 0 auto;
            width: 92px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            white-space: nowrap;
            border-left: 1px solid var(--border);
        }

        .request-text-section .request-text {
            flex: 1 1 auto;
            min-width: 0;
        }

        .routes-section {
            margin-top: 26px;
        }

        .routes-table-wrapper {
            overflow-x: auto;
        }

        .routes-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 13.5px;
        }

        .routes-table th,
        .routes-table td {
            border: 1px solid var(--border);
            padding: 9px 8px;
            text-align: right;
            vertical-align: top;
        }

        .routes-table th {
            background: var(--brand-maroon);
            color: #fff;
            font-weight: 700;
            white-space: nowrap;
        }

        .routes-table td {
            color: #333;
        }

        .routes-table tbody tr:nth-child(even) {
            background: #fcfaf6;
        }

        .routes-empty {
            margin: 0;
            background: #fff;
            border: 1px solid var(--border);
            padding: 12px;
            color: var(--muted);
            font-size: 14px;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 80px;
            text-align: center;
        }

        .signature {
            grid-column: 3;
            font-size: 15px;
            font-weight: 700;
        }

        .signature span {
            display: block;
            margin-top: 48px;
            border-top: 1px solid #777;
            padding-top: 5px;
            font-weight: 400;
        }

        .footer-note {
            text-align: center;
            margin-top: 26px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 12.5px;
        }

        .footer-note p {
            margin: 3px 0;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 0;
            }

            .request-sheet {
                box-shadow: none;
                max-width: 100%;
                width: 210mm;
                height: 297mm;
                margin: 0 auto;
                background-image: url('{{ asset('images/image.png') }}');
                background-size: 100% 100%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .request-body {
                padding: 55mm 20mm 18mm;
                width: 210mm;
                height: 297mm;
            }

            /* ===== ضغط المحتوى في صفحة واحدة (A4) =====
               السبب: المسافات الكبيرة وبوكس نص الطلب كان يتجاوز ارتفاع 297mm
               فينتقل الجدول والتوقيعات إلى الصفحة الثانية. */
            .request-sheet {
                height: auto;
                min-height: 0;
            }

            .request-body {
                height: auto;
                min-height: 0;
                padding-top: 46mm;
                padding-bottom: 8mm;
            }

            /* تقليص المسافات الرأسية */
            .print-date {
                font-size: 11px;
                margin-bottom: 4px;
            }

            .title-block {
                margin: 4px 0 10px;
            }

            .title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 12px;
                margin-top: 2px;
            }

            .info-section {
                gap: 10px;
                margin-bottom: 10px;
            }

            .info-box {
                padding: 9px 10px;
            }

            .info-box h3,
            .details-section h3 {
                font-size: 14px;
                margin: 0 0 5px;
            }

            .info-box p {
                margin: 3px 0;
                font-size: 12.5px;
            }

            /* تقليص ارتفاع بوكس نص الطلب ليأخذ مسارات أقل */
            .request-text-section {
                margin-top: 0;
            }

            .details-section {
                padding: 8px 10px;
                margin-top: 10px;
            }

            .request-text {
                min-height: auto;
                max-height: none;
                overflow: visible;
                padding: 8px 10px;
                line-height: 1.7;
                font-size: 13px;
            }

            .address-detail-inline {
                font-size: 12px;
                padding: 6px 8px;
                margin-bottom: 8px;
            }

            .address-detail-section {
                padding: 8px 10px;
                margin-bottom: 10px;
            }

            .address-detail-row {
                gap: 10px;
            }

            .address-detail-section h3 {
                font-size: 14px;
                margin: 0;
                width: 100px;
            }

            /* عنوان "نص الطلب" بجانب بوكس النص - ضبط للطباعة */
            .request-text-section .details-section {
                gap: 10px;
            }

            .request-text-section .details-section h3 {
                width: 78px;
                font-size: 13px;
                margin: 0;
            }

            /* جدول المسارات: صفوف أضيق */
            .routes-section {
                margin-top: 10px;
            }

            .routes-table {
                font-size: 11.5px;
            }

            .routes-table th,
            .routes-table td {
                padding: 4px 5px;
            }

            .routes-empty {
                padding: 8px;
                font-size: 11.5px;
            }

            /* التوقيعات: كانت متباعدة جداً (80px) فقلّلناها */
            .signatures {
                gap: 10px;
                margin-top: 26px;
            }

            .signature {
                font-size: 12.5px;
            }

            .signature span {
                margin-top: 26px;
                padding-top: 3px;
            }

            .footer-note {
                margin-top: 10px;
                padding-top: 5px;
                font-size: 10.5px;
            }

            .footer-note p {
                margin: 1px 0;
            }
        }
    </style>
</head>

<body>
    <div class="print-btn no-print">
        <button type="button" onclick="window.print()">طباعة الطلب</button>
    </div>
    <div class="request-sheet">
        <div class="request-body">
            <div class="print-date">تاريخ الطباعة: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="title-block">
                <div class="subtitle">قلم الجمهور - بلدية جباليا النزلة</div>
                <div class="title">طلب خدمة</div>
            </div>
            <div class="info-section">
                <div class="info-box">
                    <h3>معلومات المكلف</h3>
                    <p><span>الاسم:</span> <span class="value">{{ $request->mokalaf->name ?? '-' }}</span></p>
                    <p><span>رقم الهوية:</span> <span
                            class="value">{{ $request->mokalaf->identity_number ?? '-' }}</span></p>
                    <p><span>رقم الجوال:</span> <span
                            class="value">{{ $request->mobile ?: $request->mokalaf->mobile ?? '-' }}</span></p>
                    <p><span>العنوان:</span> <span
                            class="value">{{ $request->address ?: $request->mokalaf->address ?? '-' }}</span></p>
                </div>
                <div class="info-box">
                    <h3>معلومات الطلب</h3>
                    <p><span>رقم الطلب:</span> <span class="value">{{ $request->id }}</span></p>
                    <p><span>نوع الطلب:</span> <span class="value">{{ $request->requestType->name ?? '-' }}</span></p>
                    <p><span>التاريخ:</span> <span
                            class="value">{{ optional($request->created_at)->format('Y-m-d') ?? '-' }}</span></p>
                    <p><span>الحالة:</span> <span class="value">{{ $request->status->name ?? '-' }}</span></p>
                </div>
            </div>
            @if($request->neighborhood || $request->widget || $request->voucher || $request->block)
                <div class="address-detail-section">
                    <div class="address-detail-row">
                        <h3>{{ __('العنوان التفصيلي') }}</h3>
                        <div class="address-detail-inline">
                            @if($request->neighborhood){{ __('الحي') }}: {{ $request->neighborhood }} @endif
                            @if($request->widget) || {{ __('القطعة') }}: {{ $request->widget }} @endif
                            @if($request->voucher) || {{ __('القسيمة') }}: {{ $request->voucher }} @endif
                            @if($request->block) || {{ __('المقسم') }}: {{ $request->block }} @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="request-text-section">
                <div class="details-section" style="margin-bottom: 0;">
                    <h3>نص الطلب</h3>
                    <div class="request-text">{{ $request->text ?: '-' }}</div>
                </div>
            </div>
            <div class="details-section routes-section">
                <h3>جدول مسارات الطلب</h3>
                @if ($request->responses && $request->responses->count() > 0)
                    <div class="routes-table-wrapper">
                        <table class="routes-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الجهة (قسم/دائرة)</th>
                                    <th>الموظف</th>
                                    <th>حالة المسار</th>
                                    <th>تاريخ الرد</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($request->responses as $response)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $response->unit_name }}</td>
                                        <td>{{ $response->responsedBy->name ?? '-' }}</td>
                                        <td>{{ $response->responseType->name ?? '-' }}</td>
                                        <td>
                                            {{ $response->responsed_at ? \Carbon\Carbon::parse($response->responsed_at)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>{{ $response->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="routes-empty">لا توجد مسارات مسجلة لهذا الطلب.</p>
                @endif
            </div>
            <div class="signatures">
                <div class="signature">مقدم الطلب<span>التوقيع</span></div>

            </div>
            <div class="footer-note">
                <p>تم إصدار هذا المستند بواسطة نظام إدارة الوثائق - بلدية جباليا النزلة</p>
            </div>
        </div>
    </div>
</body>

</html>
