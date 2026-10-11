<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة جباية - {{ $invoice->arabic_name }}</title>
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

        * { box-sizing: border-box; }

        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, sans-serif;
            background-color: #f5f5f5;
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
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            transition: background 0.15s ease;
        }
        .print-btn button:hover {
            background: var(--brand-maroon-dark);
        }

        /* الحاوية: نفس الخلفية الورقية الرسمية المستخدمة في الإفادة وشهادة المنشأة */
        .invoice-sheet {
            max-width: 800px;
            margin: 0 auto;
            background-image: url('{{ asset('images/image.png') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #fff;
            min-height: 900px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* منطقة تعبئة البيانات ضمن هامش الخلفية الورقية */
        .invoice-body {
            padding: 18% 12% 30%;
        }

        .invoice-print-date {
            text-align: left;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .invoice-title-block {
            text-align: center;
            margin: 10px 0 22px;
        }
        .invoice-cert-title {
            font-size: 20px;
            font-weight: 800;
            text-decoration: underline;
            color: var(--brand-maroon);
        }
        .invoice-intro {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }
        .info-box {
            background: var(--panel-bg);
            padding: 14px;
            border-radius: 8px;
            border-right: 4px solid var(--brand-maroon);
        }
        .info-box h3 {
            margin: 0 0 8px;
            color: var(--brand-maroon);
            font-size: 14px;
            font-weight: 700;
        }
        .info-box p {
            margin: 5px 0;
            color: #333;
            font-size: 12.5px;
            display: flex;
            justify-content: space-between;
        }
        .info-box .value {
            font-weight: 700;
            color: #000;
        }

        .amounts-section {
            background: var(--brand-gold-light);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }
        .amounts-section h3 {
            margin: 0 0 12px;
            color: var(--brand-maroon-dark);
            font-size: 15px;
            font-weight: 700;
        }
        .amounts-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .amount-box {
            background: white;
            padding: 12px 6px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid var(--border);
        }
        .amount-box .label {
            font-size: 10.5px;
            color: var(--muted);
            margin-bottom: 5px;
            font-weight: 500;
        }
        .amount-box .value {
            font-size: 17px;
            font-weight: 800;
            color: var(--brand-maroon);
        }
        .amount-box.discount .value {
            color: var(--brand-gold);
        }

        .footer-note {
            text-align: center;
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 10.5px;
        }
        .footer-note p { margin: 3px 0; }

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
            .invoice-sheet {
                box-shadow: none;
                max-width: 100%;
                width: 210mm;
                height: 297mm;
                margin: 0 auto;
                background-image: url('{{ asset('images/image.png') }}');
                background-size: 100% 100%;
                background-repeat: no-repeat;
                background-position: center;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .invoice-body {
                padding: 55mm 20mm 18mm;
                width: 210mm;
                height: 297mm;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="print-btn no-print">
        <button onclick="window.print()">🖨 طباعة الفاتورة</button>
    </div>

    <div class="invoice-sheet">
        <div class="invoice-body">
            <div class="invoice-print-date">
                تاريخ الطباعة: {{ now()->format('d/m/Y H:i') }}
            </div>

            <div class="invoice-title-block">
                <div class="invoice-cert-title">فاتورة جباية</div>
                <div class="invoice-intro">قلم الجمهور</div>
            </div>

            <div class="info-section">
                <div class="info-box">
                    <h3>معلومات المشترك</h3>
                    <p><span>الاسم:</span> <span class="value">{{ $invoice->arabic_name }}</span></p>
                    <p><span>رقم الاشتراك:</span> <span class="value">{{ $invoice->agreement_id ?? '-' }}</span></p>
                    <p><span>رقم الجوال:</span> <span class="value">{{ $invoice->mobile_no ?: '-' }}</span></p>
                </div>
                <div class="info-box">
                    <h3>معلومات الفاتورة</h3>
                    <p><span>رقم السند:</span> <span class="value">{{ $invoice->voucher_no ?? '-' }}</span></p>
                    <p><span>التاريخ:</span> <span class="value">{{ optional($invoice->voucher_date)->format('Y-m-d') ?? '-' }}</span></p>
                    <p><span>الشهر:</span> <span class="value">{{ $invoice->voucher_month ?? '-' }}</span></p>
                </div>
            </div>

            <div class="amounts-section">
                <h3>تفاصيل المبالغ</h3>
                <div class="amounts-grid">
                    <div class="amount-box">
                        <div class="label">المستحق</div>
                        <div class="value">{{ number_format($invoice->voucher_amt ?? 0, 2) }} ₪</div>
                    </div>
                    <div class="amount-box">
                        <div class="label">الصافي</div>
                        <div class="value">{{ number_format($invoice->net_amt ?? 0, 2) }} ₪</div>
                    </div>
                    <div class="amount-box">
                        <div class="label">المتأخرات</div>
                        <div class="value">{{ number_format($invoice->total_late ?? 0, 2) }} ₪</div>
                    </div>
                    <div class="amount-box discount">
                        <div class="label">الحسم</div>
                        <div class="value">{{ number_format($invoice->auto_discount_ratio ?? 0, 2) }} %</div>
                    </div>
                </div>
            </div>

            <div class="footer-note">
                <p>تم إصدار هذه الفاتورة بواسطة نظام إدارة الوثائق — بلدية جباليا النزلة</p>
            </div>
        </div>
    </div>
</body>
</html>