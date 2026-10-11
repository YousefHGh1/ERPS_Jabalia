<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال دفعة</title>
    <style>
        @page { size: A4; margin: 10mm; }
        html, body { 
            min-height: 100%; 
            margin: 0; 
            padding: 0;
            background: #f8f9fa;
        }
        body { 
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif; 
            margin: 0; 
            color: #2c3e50;
            line-height: 1.6;
        }
        .sheet { 
            width: 100%; 
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .copy {
            position: relative;
            border: 2px solid #3498db;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            min-height: calc(50vh - 40px);
            page-break-inside: avoid;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        .copy::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background-size: cover;
            background-position: center;
            opacity: 0.05;
            border-radius: 8px;
        }
        .copy--citizen::before { background-image: url('{{ asset('assets/bg/favicon.png') }}'); }
        .copy--municipality::before { background-image: url('{{ asset('assets/bg/bg-5.jpg') }}'); }
        .copy-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-bottom: 3px solid #3498db;
            margin-bottom: 15px;
            padding-bottom: 15px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border-radius: 8px 8px 0 0;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        }
        .header img { 
            width: 60px; 
            height: 60px;
            margin-bottom: 0; 
            filter: brightness(0) invert(1);
        }
        .header-text { 
            display: flex; 
            flex-direction: column; 
            align-items: center;
        }
        .title { 
            font-size: 24px; 
            margin: 8px 0 4px; 
            font-weight: bold; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }
        .sub { 
            color: rgba(255,255,255,0.9); 
            margin-bottom: 0; 
            font-size: 14px;
            font-weight: 500;
        }
        .receipt-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #90caf9;
        }
        .receipt-info h3 {
            margin: 0 0 10px 0;
            color: #1565c0;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 13px;
            padding: 0 5px;
        }
        .meta div {
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .meta strong {
            color: #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 13px;
            transition: background-color 0.3s ease;
        }
        tr:hover td {
            background-color: #f8f9fa;
        }
        td.label {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            font-weight: bold;
            width: 30%;
            text-align: center;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            text-align: center;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .sign {
            flex: 1;
            text-align: center;
            font-size: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .sign div:first-child {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .line {
            border-top: 2px solid #3498db;
            margin-top: 10px;
            height: 30px;
            position: relative;
        }
        .line::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 2px;
            background: #3498db;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(52, 152, 219, 0.1);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .copy { 
                min-height: calc((297mm - 20mm - 40px) / 2); 
                box-shadow: none;
                border-color: #000;
            }
            body { background: white; }
            .sheet { box-shadow: none; }
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
                <div class="watermark">بلدية جباليا</div>

                <div class="header">
                    <img alt="Logo" src="{{ asset('assets/bg/favicon.png') }}">
                    <div class="header-text">
                        <div class="title">بلدية جباليا النزلة</div>
                        <p class="sub">إيصال دفعة رسمي</p>
                    </div>
                </div>

                <div class="receipt-info">
                    <h3>معلومات الإيصال</h3>
                    <div class="meta">
                        <div><strong>رقم الإيصال:</strong> {{ $paymentNumber }}</div>
                        <div><strong>تاريخ الطباعة:</strong> {{ now()->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <table>
                    <tr>
                        <td class="label">القسم / الجهة</td>
                        <td>{{ $sectionName }}</td>
                        <td class="label">المرجع</td>
                        <td>{{ $reference }}</td>
                    </tr>
                    <tr>
                        <td class="label">اسم المواطن</td>
                        <td>{{ $payerName }}</td>
                        <td class="label">تاريخ الدفع</td>
                        <td>{{ $paymentDate }}</td>
                    </tr>
                    <tr>
                        <td class="label">المبلغ المدفوع</td>
                        <td class="amount">{{ number_format((float) $amount, 2) }} شيكل</td>
                        <td class="label">المبلغ المتبقي</td>
                        <td>{{ number_format((float) $balanceAfter, 2) }} شيكل</td>
                    </tr>
                    <tr>
                        <td class="label">طريقة الدفع</td>
                        <td>{{ $paymentMethod }}</td>
                        <td class="label">المحصل</td>
                        <td>{{ $collector }}</td>
                    </tr>
                    <tr>
                        <td class="label">ملاحظات</td>
                        <td colspan="3">{{ $notes ?: '-' }}</td>
                    </tr>
                </table>

                <div class="footer">
                    <div class="sign">
                        <div>توقيع المحصل</div>
                        <div class="line"></div>
                    </div>
                    <div class="sign">
                        <div>توقيع المواطن</div>
                        <div class="line"></div>
                    </div>
                    <div class="sign">
                        <div>ختم البلدية</div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="no-print" style="margin-top:30px; text-align:center; padding:20px; background:#f8f9fa; border-radius:10px;">
        <button onclick="window.print()" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; padding: 12px 30px; font-size: 16px; border-radius: 25px; cursor: pointer; margin: 0 10px; font-weight: bold; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-print"></i> طباعة الإيصال
        </button>
        <button onclick="window.close()" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; padding: 12px 30px; font-size: 16px; border-radius: 25px; cursor: pointer; margin: 0 10px; font-weight: bold; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-times"></i> إغلاق
        </button>
    </div>
</body>
</html>
