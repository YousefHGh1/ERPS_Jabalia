 <!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>هيكلية {{ $typeLabel }} : {{ $node['name'] ?? '' }} — بلدية جباليا النزلة</title>
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

            /* ===== نظام الموصلات والأسهم ===== */
            --line-w: 2px;
            /* سماكة الخط */
            --drop: 20px;
            /* طول النزلة العمودية */
            --elbow: 9px;
            /* نصف قطر انحناء الزاوية */
            --arrow-w: 5px;
            /* نصف عرض رأس السهم */
            --arrow-h: 6px;
            /* ارتفاع رأس السهم */
            --c: var(--brand-maroon);
            /* لون الموصل الحالي */
        }

        * {
            box-sizing: border-box;
        }

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
            font-family: inherit;
            margin: 0 4px;
        }

        .print-btn .btn-back {
            background: #eef0f6;
            color: #3a3f52;
        }

        .print-btn .print-hint {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        .invoice-sheet {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sheet-header img,
        .sheet-footer img {
            display: block;
            width: 100%;
            height: auto;
        }

        .invoice-body {
            padding: 3% 3% 1%;
        }

        .invoice-print-date {
            text-align: left;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .invoice-title-block {
            text-align: center;
            margin: 4px 0 12px;
        }

        .invoice-cert-title {
            font-size: 17px;
            font-weight: 800;
            text-decoration: underline;
            color: var(--brand-maroon);
        }

        .invoice-intro {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .top-chain {
            background: var(--panel-bg);
            padding: 10px;
            border-radius: 6px;
            border-right: 4px solid var(--brand-maroon);
            margin-bottom: 10px;
        }

        .top-chain-title {
            text-align: center;
            font-weight: 800;
            font-size: 11px;
            color: var(--brand-maroon);
            margin-bottom: 6px;
        }

        .chain-node {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 6px;
            padding: 5px 8px;
            text-align: center;
            font-weight: 800;
            font-size: 11px;
        }

        .chain-node.council {
            border-right: 4px solid var(--brand-maroon);
            color: var(--brand-maroon-dark);
        }

        .chain-node.side {
            border-right: 4px solid var(--brand-gold);
            color: #1f2937;
            font-size: 10px;
        }

        .chain-node.mayor {
            border-right: 4px solid var(--brand-maroon);
            color: var(--brand-maroon-dark);
        }

        .chain-node.manager {
            border-right: 4px solid #4b5563;
            color: #374151;
        }

        /* ============================================================
           الموصلات: خط نازل + مسطرة أفقية بزوايا منحنية + رأس سهم مرسوم
           ============================================================ */

        /* رأس السهم (مثلث نظيف بـ clip-path يرث لون الموصل) */
        .arrow-head {
            position: absolute;
            top: calc(var(--drop) - var(--arrow-h));
            right: 50%;
            transform: translateX(50%);
            width: calc(var(--arrow-w) * 2);
            height: var(--arrow-h);
            background: var(--c);
            clip-path: polygon(50% 100%, 0 0, 100% 0);
            z-index: 2;
        }

        .arrow-head.maroon {
            --c: var(--brand-maroon);
        }

        .arrow-head.gold {
            --c: var(--brand-gold);
        }

        /* موصل مفرد: عقدة واحدة تنزل لما تحتها */
        .org-connector {
            position: relative;
            height: var(--drop);
        }

        .org-connector::before {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            transform: translateX(50%);
            width: var(--line-w);
            height: var(--drop);
            background: var(--c);
            border-radius: var(--line-w);
        }

        .org-connector.maroon {
            --c: var(--brand-maroon);
        }

        .org-connector.gold {
            --c: var(--brand-gold);
        }

        .org-connector.small {
            --drop: 14px;
            --elbow: 7px;
            --arrow-h: 5px;
            --arrow-w: 4px;
        }

        /* صف الأبناء */
        .org-row {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            gap: 8px;
        }

        .org-row.maroon {
            --c: var(--brand-maroon);
        }

        .org-row.gold {
            --c: var(--brand-gold);
        }

        .org-row.small {
            --drop: 14px;
            --elbow: 7px;
            --arrow-h: 5px;
            --arrow-w: 4px;
            gap: 5px;
        }

        .org-item {
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            padding-top: var(--drop);
        }

        /* وزن العرض: يُضبط من الـ inline style ‏(flex-grow) حسب عدد الشعب داخل كل قسم */
        .org-row.weighted .org-item {
            flex-basis: 0;
        }

        /* النزلة العمودية لكل بطاقة */
        .org-item::before {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            transform: translateX(50%);
            width: var(--line-w);
            height: var(--drop);
            background: var(--c);
            border-radius: var(--line-w);
        }

        /* المسطرة الأفقية: قطعة داخل كل عنصر، مع زاوية منحنية في الطرفين */
        .org-item::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: var(--elbow);
            border-top: var(--line-w) solid var(--c);
        }

        .org-item:first-child::after {
            right: calc(50% - (var(--line-w) / 2));
            border-right: var(--line-w) solid var(--c);
            border-top-right-radius: var(--elbow);
        }

        .org-item:last-child::after {
            left: calc(50% - (var(--line-w) / 2));
            border-left: var(--line-w) solid var(--c);
            border-top-left-radius: var(--elbow);
        }

        /* الزاوية المنحنية ترسم أعلى النزلة، فتبدأ النزلة بعدها */
        .org-item:first-child::before,
        .org-item:last-child::before {
            top: var(--elbow);
            height: calc(var(--drop) - var(--elbow));
        }

        /* ابن وحيد: خط مستقيم بلا مسطرة */
        .org-row.single-item .org-item::after {
            display: none;
        }

        .org-row.single-item .org-item::before {
            top: 0;
            height: var(--drop);
        }

        .unit-root {
            background: #fff;
            border: 1px solid var(--border);
            border-top: 4px solid var(--brand-maroon);
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(176, 58, 46, .08);
            page-break-inside: avoid;
        }

        /* صف بطاقة الدائرة (غلاف بسيط) */
        .unit-root-row {
            display: block;
        }

        /* منطقة النزلة تحت الدائرة: تحمل النزلة العمودية + الفرع الجانبي للموظفين المباشرين */
        .dept-trunk {
            position: relative;
        }

        /* عند وجود موظفين مباشرين: نطيل النزلة لتتسع لبطاقتهم الجانبية
           (لا يتم التطويل عند إخفاء الموظفين من لوحة إظهار/إخفاء) */
        #print-scale-wrapper:not(.hide-employees) .dept-trunk.has-direct-emps .org-connector {
            --drop: 100px;
        }

        /* بطاقة الموظفين المباشرين كفرع جانبي يخرج من النزلة العمودية */
        .dept-trunk .direct-emps-side {
            position: absolute;
            top: 26px;
            right: calc(50% + 52px);
            width: 30%;
            max-width: 300px;
            z-index: 3;
        }

        /* الذراع الأفقية التي تربط النزلة ببطاقة الموظفين */
        .dept-trunk .direct-emps-side::before {
            content: '';
            position: absolute;
            top: 10px;
            right: -52px;
            width: 52px;
            height: var(--line-w);
            background: var(--brand-maroon);
        }

        /* رأس السهم الأفقي (يتجه نحو بطاقة الموظفين) */
        .dept-trunk .direct-emps-side::after {
            content: '';
            position: absolute;
            top: calc(10px - var(--arrow-w));
            right: -6px;
            width: var(--arrow-h);
            height: calc(var(--arrow-w) * 2);
            background: var(--brand-maroon);
            clip-path: polygon(0 50%, 100% 0, 100% 100%);
        }

        /* البطاقة نفسها داخل الفرع الجانبي */
        .dept-trunk .direct-emps-side .direct-emps-card {
            margin: 0;
            max-width: 100%;
        }

        .unit-root-label {
            font-size: 10px;
            color: var(--muted);
            font-weight: 700;
            margin-bottom: 2px;
        }

        .unit-root-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--brand-maroon);
        }

        .meta-row {
            margin-top: 6px;
            text-align: center;
        }

        .meta-chip {
            display: inline-block;
            font-size: 9.5px;
            background: var(--panel-bg);
            border: 1px solid var(--border);
            color: #5b6478;
            border-radius: 999px;
            padding: 2px 9px;
            margin: 1px;
        }

        .root-manager {
            font-size: 11.5px;
            color: #1a1a1a;
            font-weight: 800;
            margin-top: 5px;
            text-align: center;
        }

        .root-vacant {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            color: var(--brand-maroon-dark);
            background: #fdeceb;
            border: 1px solid #f5c6c0;
            border-radius: 999px;
            padding: 2px 10px;
            margin-top: 5px;
        }

        /* ===== شرائح المسكن والمكلف: كل نوع بلون مختلف ===== */
        .dir-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            justify-content: center;
            margin-top: 4px;
        }

        .dir-chip {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 999px;
            padding: 2px 9px;
            line-height: 1.6;
            page-break-inside: avoid;
        }

        .dir-chip small {
            font-weight: 800;
        }

        /* المسكن: أخضر */
        .dir-chip.is-permanent {
            color: #0f5132;
            background: #e8f7ee;
            border: 1px solid #9fd8b4;
        }

        /* المكلف: أزرق */
        .dir-chip.is-acting {
            color: #0b5ed7;
            background: #e9f2fe;
            border: 1px solid #b3d1f7;
        }

        .br-card .dir-chip {
            font-size: 8.5px;
            padding: 1px 7px;
        }

        /* ===== لوحة إظهار/إخفاء العناصر قبل الطباعة ===== */
        .print-toggles {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .print-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 700;
            font-family: inherit;
            color: #3a3f52;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 12px;
            cursor: pointer;
            user-select: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .print-toggle:hover {
            border-color: var(--brand-maroon);
        }

        .print-toggle input {
            margin: 0;
            cursor: pointer;
            accent-color: var(--brand-maroon);
            width: 14px;
            height: 14px;
        }

        .print-toggle .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .print-toggle .dot-permanent {
            background: #22a55e;
        }

        .print-toggle .dot-acting {
            background: #0b5ed7;
        }

        .print-toggle .dot-employees {
            background: #8a5a00;
        }

        .print-toggle .dot-branches {
            background: var(--brand-gold);
        }

        .print-toggle .dot-meta {
            background: #6b7280;
        }

        .print-toggle .dot-top-chain {
            background: var(--brand-maroon);
        }

        .btn-reset-toggles {
            background: #eef0f6;
            color: #3a3f52;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
        }

        .btn-reset-toggles:hover {
            border-color: var(--brand-maroon);
        }

        /* ===== قواعد الإخفاء: تُفعّل بإضافة الفئة على #print-scale-wrapper ===== */

        /* إخفاء شرائح المسكن */
        .hide-permanent .dir-chip.is-permanent {
            display: none !important;
        }

        /* إخفاء شرائح المكلف */
        .hide-acting .dir-chip.is-acting {
            display: none !important;
        }

        /* إخفاء كتل الموظفين */
        .hide-employees .emp-block,
        .hide-employees .direct-emps-card,
        .hide-employees .direct-emps-side {
            display: none !important;
        }

        /* إخفاء الشعب التابعة (العنوان + الموصلات + البطاقات) */
        .hide-branches .br-wrap {
            display: none !important;
        }

        /* إخفاء بطاقات المعلومات (عدد الشعب/الموظفين) */
        .hide-meta .meta-row {
            display: none !important;
        }

        /* إخفاء القيادة العليا (المجلس البلدي، التدقيق الخارجي، لجان المجلس،
           الرقابة الداخلية، رئيس البلدية، مدير البلدية) */
        .hide-top-chain .top-chain {
            display: none !important;
        }

        .sec-card {
            background: #fff;
            border: 1px solid var(--border);
            border-top: 4px solid #1b84ff;
            border-radius: 8px;
            padding: 8px;
            page-break-inside: avoid;
            height: 100%;
        }

        .sec-name {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: #0f6cdd;
        }

        .sec-manager {
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            color: #333;
            margin-top: 3px;
        }

        /* ===== الشعب التابعة: نفس نظام الموصلات بلون ذهبي وقياس أصغر ===== */
        .br-title {
            text-align: center;
            font-size: 9.5px;
            color: var(--muted);
            font-weight: 700;
            margin: 8px 0 0;
        }

        .br-wrap {
            --c: var(--brand-gold);
            --drop: 13px;
            --elbow: 6px;
            --arrow-h: 5px;
            --arrow-w: 4px;
        }

        /* نزلة قصيرة من بطاقة القسم إلى مسطرة الشعب */
        .br-entry {
            position: relative;
            height: var(--drop);
        }

        .br-entry::before {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            transform: translateX(50%);
            width: var(--line-w);
            height: var(--drop);
            background: var(--c);
            border-radius: var(--line-w);
        }

        .br-list {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            gap: 5px;
        }

        .br-list-item {
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            padding-top: var(--drop);
        }

        .br-list-item::before {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            transform: translateX(50%);
            width: var(--line-w);
            height: var(--drop);
            background: var(--c);
            border-radius: var(--line-w);
        }

        .br-list-item::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: var(--elbow);
            border-top: var(--line-w) solid var(--c);
        }

        .br-list-item:first-child::after {
            right: calc(50% - (var(--line-w) / 2));
            border-right: var(--line-w) solid var(--c);
            border-top-right-radius: var(--elbow);
        }

        .br-list-item:last-child::after {
            left: calc(50% - (var(--line-w) / 2));
            border-left: var(--line-w) solid var(--c);
            border-top-left-radius: var(--elbow);
        }

        .br-list-item:first-child::before,
        .br-list-item:last-child::before {
            top: var(--elbow);
            height: calc(var(--drop) - var(--elbow));
        }

        .br-list.single-branch .br-list-item::after {
            display: none;
        }

        .br-list.single-branch .br-list-item::before {
            top: 0;
            height: var(--drop);
        }

        .br-card {
            background: #fffdf4;
            border: 1px solid var(--border);
            border-top: 3px solid var(--brand-gold);
            border-radius: 6px;
            padding: 6px 8px;
            text-align: center;
            page-break-inside: avoid;
        }

        .br-name {
            font-size: 10px;
            font-weight: 800;
            color: #92600a;
            line-height: 1.3;
            word-break: normal;
            overflow-wrap: break-word;
        }

        .br-manager {
            font-size: 9px;
            color: #333;
            font-weight: 700;
            margin-top: 3px;
        }

        .br-manager.is-vacant {
            display: inline-block;
            color: var(--brand-maroon-dark);
            background: #fdeceb;
            border: 1px solid #f5c6c0;
            border-radius: 999px;
            padding: 1px 8px;
            margin-top: 3px;
        }

        .emp-block {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed var(--border);
        }

        .emp-block-title {
            font-size: 8.5px;
            color: var(--muted);
            font-weight: 700;
            margin-bottom: 4px;
            text-align: center;
        }

        .emp-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            justify-content: center;
        }

        .emp-chip {
            font-size: 8.5px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 2px 6px;
            color: #333;
        }

        .emp-chip small {
            color: var(--muted);
        }

        /* بطاقة الموظفين المباشرين للوحدة (تحت المدير مباشرة) */
        .direct-emps-card {
            max-width: 560px;
            margin: 0 auto;
            border: 1px solid var(--border);
            border-top: 3px solid var(--brand-maroon);
            border-radius: 6px;
            background: var(--panel-bg);
            padding: 6px 8px;
            text-align: center;
        }

        .direct-emps-title {
            font-size: 10px;
            font-weight: 800;
            color: var(--brand-maroon);
            margin-bottom: 4px;
        }

        .footer-note {
            text-align: center;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 9px;
        }

        .footer-note p {
            margin: 2px 0;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 6mm;
            }

            html,
            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .print-btn {
                display: none !important;
            }

            #print-scale-wrapper {
                transform: none !important;
                width: auto !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            .invoice-sheet {
                box-shadow: none;
                max-width: none;
                width: 100% !important;
                margin: 0 !important;
            }

            .invoice-body {
                padding: 0 !important;
            }

            .unit-root,
            .direct-emps-card,
            .sec-card,
            .br-card,
            .chain-node {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            /* ضمان طباعة ألوان الخطوط والأسهم */
            .arrow-head,
            .dir-chip,
            .org-connector::before,
            .org-item::before,
            .org-item::after,
            .br-entry::before,
            .br-list-item::before,
            .br-list-item::after {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="print-btn no-print">
        <div>
            <button onclick="window.print()">🖨 طباعة الهيكلية</button>
            <button class="btn-back" onclick="window.close()">رجوع</button>
        </div>
        <div class="print-toggles" id="print-toggles">
            <span style="font-size:12px;font-weight:800;color:var(--muted);align-self:center;">إظهار/إخفاء قبل الطباعة:</span>
            <label class="print-toggle" title="شرائح المسكن الخضراء">
                <input type="checkbox" data-toggle-class="hide-permanent" checked>
                <span class="dot dot-permanent"></span> المسكن
            </label>
            <label class="print-toggle" title="شرائح المكلف الزرقاء">
                <input type="checkbox" data-toggle-class="hide-acting" checked>
                <span class="dot dot-acting"></span> المكلف
            </label>
            <label class="print-toggle" title="كتل الموظفين داخل البطاقات">
                <input type="checkbox" data-toggle-class="hide-employees" checked>
                <span class="dot dot-employees"></span> الموظفون
            </label>
            <label class="print-toggle" title="الشعب التابعة للأقسام">
                <input type="checkbox" data-toggle-class="hide-branches" checked>
                <span class="dot dot-branches"></span> الشعب
            </label>
            <label class="print-toggle" title="بطاقات عدد الشعب والموظفين">
                <input type="checkbox" data-toggle-class="hide-meta" checked>
                <span class="dot dot-meta"></span> بطاقات المعلومات
            </label>
            <label class="print-toggle" title="إخفاء بلدية جباليا النزلة — الهيكلية، المجلس البلدي، التدقيق الخارجي، قسم لجان المجلس البلدي، وحدة الرقابة الداخلية، رئيس البلدية ومدير البلدية">
                <input type="checkbox" data-toggle-class="hide-top-chain" checked>
                <span class="dot dot-top-chain"></span> القيادة العليا
            </label>
            <button type="button" class="btn-reset-toggles" id="btn-reset-toggles">↺ الافتراضي</button>
        </div>
        <span class="print-hint">إذا لم تظهر الصفحة أفقية تلقائياً، اختر "المزيد من الإعدادات ← الاتجاه ← أفقي" في نافذة
            الطباعة</span>
    </div>

    <div id="print-scale-wrapper">
        <div class="invoice-sheet">

            <div class="invoice-body">


                <div class="invoice-title-block">
                    <div class="invoice-cert-title">هيكلية {{ $typeLabel }} : {{ $node['name'] ?? '' }}</div>
                    {{-- <div class="invoice-intro">بلدية جباليا النزلة — الهيكل التنظيمي</div> --}}
                </div>

                <div class="top-chain">
                    <div class="top-chain-title">بلدية جباليا النزلة — الهيكلية</div>
                    <div class="chain-node council">المجلس البلدي</div>

                    <div class="org-connector maroon small">
                        <span class="arrow-head maroon"></span>
                    </div>
                    <div class="org-row maroon small">
                        <div class="org-item">
                            <span class="arrow-head maroon"></span>
                            <div class="chain-node side">التدقيق الخارجي</div>
                        </div>
                        <div class="org-item">
                            <span class="arrow-head maroon"></span>
                            <div class="chain-node side">قسم لجان المجلس البلدي</div>
                        </div>
                        <div class="org-item">
                            <span class="arrow-head maroon"></span>
                            <div class="chain-node side">وحدة الرقابة الداخلية</div>
                        </div>
                    </div>

                    <div class="org-connector maroon small">
                        <span class="arrow-head maroon"></span>
                    </div>
                    <div class="chain-node mayor">رئيس البلدية</div>

                    <div class="org-connector maroon small">
                        <span class="arrow-head maroon"></span>
                    </div>
                    <div class="chain-node manager">مدير البلدية</div>

                    <div class="org-connector maroon small">
                        <span class="arrow-head maroon"></span>
                    </div>
                </div>

                @php
                    $__directEmps = (($children['kind'] ?? '') === 'employees') ? [] : ($children['employees'] ?? []);
                    $__hasDirectEmps = count($__directEmps) > 0;
                @endphp
                <div class="unit-root">
                    <div class="unit-root-label">بيانات {{ $typeLabel }}</div>
                        <div class="unit-root-name">{{ $node['name'] ?? '' }}</div>
                        @php $visibleNodeMeta = collect($node['meta'] ?? [])->filter(fn($m) => trim($m['label'] ?? '') !== ''); @endphp
                        @if ($visibleNodeMeta->count())
                            <div class="meta-row">
                                @foreach ($visibleNodeMeta as $m)
                                    <span class="meta-chip">{{ $m['label'] }}</span>
                                @endforeach
                            </div>
                        @endif
                        @php $__nodeDirs = $node['directors'] ?? []; @endphp
                        @if (!empty($__nodeDirs['permanent']) || !empty($__nodeDirs['acting']))
                            <div class="dir-chips">
                                @if (!empty($__nodeDirs['permanent']))
                                    <span class="dir-chip is-permanent"><small>{{ __('permanent') }}:</small>
                                        {{ $__nodeDirs['permanent'] }}</span>
                                @endif
                                @if (!empty($__nodeDirs['acting']))
                                    <span class="dir-chip is-acting"><small>{{ __('acting') }}:</small>
                                        {{ $__nodeDirs['acting'] }}</span>
                                @endif
                            </div>
                        @else
                            @if (!empty($node['manager']))
                                <div class="root-manager">مدير {{ $typeLabel }}: {{ $node['manager'] }}</div>
                            @endif
                            @if (!empty($node['vacant']))
                                <div><span class="root-vacant">{{ $node['vacant'] }}</span></div>
                            @endif
                        @endif
                    </div>
                </div>

                @if (($children['kind'] ?? '') === 'employees')
                    <div class="org-connector maroon">
                        <span class="arrow-head maroon"></span>
                    </div>
                    <div class="sec-card">
                        <div class="sec-name">موظفو {{ $typeLabel }}</div>
                        <div class="emp-block">
                            <div class="emp-chips">
                                @foreach ($children['items'] ?? [] as $emp)
                                    <span class="emp-chip">{{ $emp['name'] ?? '' }}@if (!empty($emp['job']))
                                            <small>— {{ $emp['job'] }}</small>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    @php $sectionCount = count($children['items'] ?? []); @endphp
                    @php
                        // وزن عرض كل قسم حسب عدد الشعب داخله (قسم بلا شعب يأخذ وزن 1)
                        $__sectionGrow = collect($children['items'] ?? [])
                            ->map(function ($__child) {
                                return max(1, count($__child['branches'] ?? []));
                            })
                            ->values();
                    @endphp

                    <div class="dept-trunk {{ $__hasDirectEmps ? 'has-direct-emps' : '' }}">
                        <div class="org-connector maroon">
                            <span class="arrow-head maroon"></span>
                        </div>
                        @if ($__hasDirectEmps)
                            <div class="direct-emps-side">
                                <div class="direct-emps-card">
                                    <div class="direct-emps-title">موظفو {{ $typeLabel }} المباشرون</div>
                                    <div class="emp-chips">
                                        @foreach ($__directEmps as $demp)
                                            <span class="emp-chip">{{ $demp['name'] ?? '' }}@if (!empty($demp['job']))
                                                    <small>— {{ $demp['job'] }}</small>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="org-row maroon weighted {{ $sectionCount === 1 ? 'single-item' : '' }}">
                        @foreach ($children['items'] ?? [] as $__idx => $child)
                            @php $__grow = $__sectionGrow[$__idx] ?? 1; @endphp
                            <div class="org-item" style="flex-grow: {{ $__grow }};">
                                @php
                                    $branches = $child['branches'] ?? [];
                                    $hasBranches = count($branches) > 0;

                                    $childEmployees = $child['employees'] ?? [];
                                    $hasEmployees = count($childEmployees) > 0;

                                    $visibleMeta = collect($child['meta'] ?? [])->filter(
                                        fn($m) => trim($m['label'] ?? '') !== '',
                                    );
                                @endphp
                                <span class="arrow-head maroon"></span>
                                <div class="sec-card">
                                    <div class="sec-name">{{ $child['name'] ?? '' }}</div>
                                    @if ($visibleMeta->count())
                                        <div class="meta-row">
                                            @foreach ($visibleMeta as $m)
                                                <span class="meta-chip">{{ $m['label'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @php $__secDirs = $child['directors'] ?? []; @endphp
                                    @if (!empty($__secDirs['permanent']) || !empty($__secDirs['acting']))
                                        <div class="dir-chips">
                                            @if (!empty($__secDirs['permanent']))
                                                <span class="dir-chip is-permanent"><small>{{ __('permanent') }}:</small>
                                                    {{ $__secDirs['permanent'] }}</span>
                                            @endif
                                            @if (!empty($__secDirs['acting']))
                                                <span class="dir-chip is-acting"><small>{{ __('acting') }}:</small>
                                                    {{ $__secDirs['acting'] }}</span>
                                            @endif
                                        </div>
                                    @else
                                        @if (!empty($child['manager']))
                                            <div class="sec-manager">{{ $child['manager'] }}</div>
                                        @endif
                                        @if (!empty($child['vacant']))
                                            <div style="text-align:center"><span
                                                    class="root-vacant">{{ $child['vacant'] }}</span></div>
                                        @endif
                                    @endif
                                    @if ($hasEmployees)
                                        <div class="emp-block">
                                            <div class="emp-block-title">موظفو {{ $child['name'] ?? 'القسم' }}</div>
                                            <div class="emp-chips">
                                                @foreach ($childEmployees as $emp)
                                                    <span class="emp-chip">{{ $emp['name'] ?? '' }}@if (!empty($emp['job']))
                                                            <small>— {{ $emp['job'] }}</small>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($hasBranches)
                                        <div class="br-wrap">
                                            <div class="br-title">الشعب التابعة</div>
                                            <div class="br-entry"></div>
                                            <div class="br-list {{ count($branches) === 1 ? 'single-branch' : '' }}">
                                                @foreach ($branches as $br)
                                                    <div class="br-list-item">
                                                        <span class="arrow-head gold"></span>
                                                        <div class="br-card">
                                                            <div class="br-name">{{ $br['name'] ?? '' }}</div>
                                                            @php $__brVisibleMeta = collect($br['meta'] ?? [])->filter(fn($m) => trim($m['label'] ?? '') !== ''); @endphp
                                                            @if ($__brVisibleMeta->count())
                                                                <div class="meta-row">
                                                                    @foreach ($__brVisibleMeta as $m)
                                                                        <span class="meta-chip">{{ $m['label'] }}</span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                            @php $__brDirs = $br['directors'] ?? []; @endphp
                                                            @if (!empty($__brDirs['permanent']) || !empty($__brDirs['acting']))
                                                                <div class="dir-chips">
                                                                    @if (!empty($__brDirs['permanent']))
                                                                        <span class="dir-chip is-permanent"><small>{{ __('permanent') }}:</small>
                                                                            {{ $__brDirs['permanent'] }}</span>
                                                                    @endif
                                                                    @if (!empty($__brDirs['acting']))
                                                                        <span class="dir-chip is-acting"><small>{{ __('acting') }}:</small>
                                                                            {{ $__brDirs['acting'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                @if (!empty($br['manager']))
                                                                    <div class="br-manager">{{ $br['manager'] }}</div>
                                                                @endif
                                                                @if (!empty($br['vacant']))
                                                                    <div class="br-manager is-vacant">{{ $br['vacant'] }}
                                                                    </div>
                                                                @endif
                                                            @endif
                                                            @if (!empty($br['employees']) && count($br['employees']))
                                                                <div class="emp-block">
                                                                    <div class="emp-chips">
                                                                        @foreach ($br['employees'] as $bemp)
                                                                            <span
                                                                                class="emp-chip">{{ $bemp['name'] ?? '' }}
                                                                                @if (!empty($bemp['job']))
                                                                                    <small>—
                                                                                        {{ $bemp['job'] }}</small>
                                                                                @endif
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                @endif

                <div class="footer-note">
                    <p>تم إصدار هذه الهيكلية بواسطة نظام إدارة الوثائق — بلدية جباليا النزلة
                    <div class="invoice-print-date">تاريخ الطباعة: {{ now()->format('d/m/Y H:i') }}</div>
                    </p>
                </div>

            </div>

        </div>
    </div>

    <script>
        function fitToOnePage() {
            var wrapper = document.getElementById('print-scale-wrapper');
            // إلغاء أي تصغير سابق قبل القياس
            wrapper.style.zoom = '';
            wrapper.style.transform = '';
            wrapper.style.width = '';
            wrapper.style.margin = '';
            // الأبعاد القابلة للطباعة في A4 أفقي بهوامش 6mm بدقة 96dpi ≈ 1031×703 بكسل
            // نترك هامش أمان صغير حتى لا تتسرب سطور لصفحة ثانية
            var pageWidthPx = 1020;
            var pageHeightPx = 690;
            var contentWidth = wrapper.scrollWidth;
            var contentHeight = wrapper.scrollHeight;
            var scale = Math.min(pageWidthPx / contentWidth, pageHeightPx / contentHeight);
            if (scale < 1) {
                // zoom يؤثر على الـ layout الفعلي (بعكس transform) فيحشر المحتوى في صفحة واحدة
                wrapper.style.zoom = scale;
            }
        }

        /* ===== إظهار/إخفاء العناصر قبل الطباعة (مع حفظ التفضيل) ===== */
        (function () {
            var STORAGE_KEY = 'orgChartPrintVisibility';
            var wrapper = document.getElementById('print-scale-wrapper');
            var toggleInputs = document.querySelectorAll('#print-toggles input[data-toggle-class]');
            var resetBtn = document.getElementById('btn-reset-toggles');

            function readSavedState() {
                try {
                    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}') || {};
                } catch (e) {
                    return {};
                }
            }

            function applyState(state) {
                toggleInputs.forEach(function (input) {
                    var cls = input.getAttribute('data-toggle-class');
                    var visible = (typeof state[cls] === 'boolean') ? state[cls] : true;
                    input.checked = visible;
                    if (visible) {
                        wrapper.classList.remove(cls);
                    } else {
                        wrapper.classList.add(cls);
                    }
                });
            }

            function persistState() {
                var state = {};
                toggleInputs.forEach(function (input) {
                    state[input.getAttribute('data-toggle-class')] = input.checked;
                });
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
                } catch (e) { /* تجاهل عجز التخزين */ }
            }

            // استعادة التفضيل المحفوظ عند التحميل (الافتراضي: إظهار الكل)
            applyState(readSavedState());

            toggleInputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    var cls = input.getAttribute('data-toggle-class');
                    if (input.checked) {
                        wrapper.classList.remove(cls);
                    } else {
                        wrapper.classList.add(cls);
                    }
                    persistState();
                });
            });

            resetBtn.addEventListener('click', function () {
                var allVisible = {};
                toggleInputs.forEach(function (input) {
                    allVisible[input.getAttribute('data-toggle-class')] = true;
                });
                applyState(allVisible);
                persistState();
            });
        })();
        window.addEventListener('beforeprint', fitToOnePage);
        window.addEventListener('afterprint', function() {
            var wrapper = document.getElementById('print-scale-wrapper');
            wrapper.style.zoom = '';
            wrapper.style.transform = '';
            wrapper.style.width = '';
            wrapper.style.margin = '';
        });
    </script>
</body>

</html>
