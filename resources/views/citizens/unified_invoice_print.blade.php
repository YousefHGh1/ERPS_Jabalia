<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة موحدة للمواطن</title>
    <style>
        @page { size: A4; margin: 8mm; }
        html, body { min-height: 100%; }
        body { font-family: Tahoma, Arial, sans-serif; margin: 0; color: #111; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .sheet { width: 100%; }
        .copy {
            position: relative;
            border: 1px dashed #777;
            padding: 8px 10px;
            margin-bottom: 10px;
            min-height: calc(50vh - 14px);
            page-break-inside: avoid;
            overflow: hidden;
        }
        .copy::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background-size: cover;
            background-position: center;
            opacity: 0.12;
        }
        .copy--citizen::before { background-image: url('{{ asset('assets/bg/favicon.png') }}'); }
        .copy--municipality::before { background-image: url('{{ asset('assets/bg/bg-2.jpg') }}'); }
        .copy-tag {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #444;
            margin-bottom: 4px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            text-align: right;
            border-bottom: 2px double #000;
            padding: 8px 4px;
            margin-bottom: 8px;
            background: transparent;
            color: #111;
        }
        .header img { width: 60px; max-width: 100%; margin-bottom: 0; flex-shrink: 0; }
        .header-text { display: flex; flex-direction: column; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        .header h2 { font-size: 18px; }
        .header h3 { font-size: 14px; }
        .header p { font-size: 12px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; }
        .summary { margin-bottom: 8px; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { border: 1px solid #ddd; padding: 4px 6px; font-size: 11px; }
        .summary td.label { background: #f6f6f6; font-weight: bold; width: 25%; }
        table.payments { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.payments th, table.payments td { border: 1px solid #333; padding: 3px 4px; font-size: 10px; text-align: right; }
        table.payments thead th { background: #efefef; }
        .footer { margin-top: 8px; display: flex; justify-content: space-between; }
        .signature { width: 30%; text-align: center; }
        .line { border-top: 1px solid #222; margin-top: 18px; }
        @media print {
            .no-print { display: none; }
            .copy { min-height: calc((297mm - 16mm - 10px) / 2); }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="sheet">
        @foreach ([
            ['class' => 'copy--citizen', 'tag' => 'نسخة المواطن'],
            ['class' => 'copy--municipality', 'tag' => 'نسخة البلدية'],
        ] as $copy)
            <div class="copy {{ $copy['class'] }}">
                <div class="copy-tag">{{ $copy['tag'] }}</div>
                <div class="header">
                    <img alt="Logo" src="{{ asset('assets/bg/favicon.png') }}">
                    <div class="header-text">
                        <h2>بلدية جباليا النزلة</h2>
                        <h3>فاتورة موحدة للمواطن</h3>
                        <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <div class="meta">
                    <div><strong>اسم المواطن:</strong> {{ $invoiceData['citizen_name'] }}</div>
                    <div><strong>رقم الهوية:</strong> {{ $identityNumber }}</div>
                </div>

                <div class="summary">
                    <table>
                        <tr>
                            <td class="label">إجمالي الديون (قلم الجمهور)</td>
                            <td>{{ number_format($invoiceData['totals']['requests_due'], 2) }} ₪</td>
                            <td class="label">إجمالي المدفوعات (قلم الجمهور)</td>
                            <td>{{ number_format($invoiceData['totals']['requests_paid'], 2) }} ₪</td>
                            <td class="label">المتبقي (قلم الجمهور)</td>
                            <td>{{ number_format((float) $invoiceData['totals']['requests_due'] - (float) $invoiceData['totals']['requests_paid'], 2) }} ₪</td>
                        </tr>
                        <tr>
                            <td class="label">إجمالي الديون (الهندسة والتنظيم)</td>
                            <td>{{ number_format($invoiceData['totals']['engineering_due'], 2) }} ₪</td>
                            <td class="label">إجمالي المدفوعات (الهندسة والتنظيم)</td>
                            <td>{{ number_format($invoiceData['totals']['engineering_paid'], 2) }} ₪</td>
                            <td class="label">المتبقي (الهندسة والتنظيم)</td>
                            <td>{{ number_format((float) $invoiceData['totals']['engineering_due'] - (float) $invoiceData['totals']['engineering_paid'], 2) }} ₪</td>
                        </tr>
                        <tr>
                            <td class="label">إجمالي الديون (الحرف والصناعات)</td>
                            <td>{{ number_format($invoiceData['totals']['craft_due'], 2) }} ₪</td>
                            <td class="label">إجمالي المدفوعات (الحرف والصناعات)</td>
                            <td>{{ number_format($invoiceData['totals']['craft_paid'], 2) }} ₪</td>
                            <td class="label">المتبقي (الحرف والصناعات)</td>
                            <td>{{ number_format((float) $invoiceData['totals']['craft_due'] - (float) $invoiceData['totals']['craft_paid'], 2) }} ₪</td>
                        </tr>
                        <tr>
                            <td class="label">إجمالي الديون الكلي</td>
                            <td><strong>{{ number_format($invoiceData['totals']['all_due'], 2) }} ₪</strong></td>
                            <td class="label">إجمالي المدفوعات الكلي</td>
                            <td><strong>{{ number_format($invoiceData['totals']['all_paid'], 2) }} ₪</strong></td>
                            <td class="label">المتبقي الكلي</td>
                            <td>
                                <strong>
                                    {{ number_format((float) $invoiceData['totals']['all_due'] - (float) $invoiceData['totals']['all_paid'], 2) }} ₪
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="payments">
                    <thead>
                        <tr>
                            <th>القسم / الجهة</th>
                            {{-- <th>المرجع</th> --}}
                            <th>رقم الدفعة</th>
                            <th>أخر الدفعات</th>
                            {{-- <th>طريقة الدفع</th> --}}
                            <th>تاريخ الدفع</th>
                            <th>المحصل</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $latestPaymentsBySection = collect($invoiceData['payments'] ?? [])
                                ->sortByDesc('payment_date')
                                ->unique('section')
                                ->values();
                        @endphp
                        @forelse ($latestPaymentsBySection as $row)
                            <tr>
                                <td>{{ $row['section'] }}</td>
                                {{-- <td>{{ $row['reference'] }}</td> --}}
                                <td>{{ $row['payment_number'] }}</td>
                                <td>{{ number_format((float) $row['amount'], 2) }} ₪</td>
                                {{-- <td>{{ $row['payment_method'] }}</td> --}}
                                <td>{{ $row['payment_date'] }}</td>
                                <td>{{ $row['collector'] }}</td>
                                <td>{{ $row['notes'] ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">لا توجد دفعات مسجلة لهذا المواطن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="footer">
                    <div class="signature">
                        <div>توقيع المحاسب</div>
                        <div class="line"></div>
                    </div>
                    <div class="signature">
                        <div>ختم البلدية</div>
                        <div class="line"></div>
                    </div>
                    <div class="signature">
                        <div>المدير المختص</div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="no-print" style="margin-top:20px; text-align:center;">
        <button onclick="window.print()">طباعة</button>
        <button onclick="window.close()">إغلاق</button>
    </div>
</body>
</html>
