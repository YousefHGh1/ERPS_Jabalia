<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رخصة حرفة - {{ $license->file_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --maroon: #b03a2e;
            --maroon-dark: #8a2d23;
            --gold-light: #f4e6c8;
            --ink: #1a1a1a;
            --muted: #6b6b6b;
            --panel: #faf6ef;
            --border: #e6ddc9;
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 20px; color: var(--ink); background: #f5f5f5; font-family: 'Tajawal', 'Segoe UI', Tahoma, sans-serif; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .no-print button { margin: 0 4px; padding: 12px 26px; border: 0; border-radius: 6px; color: #fff; background: var(--maroon); cursor: pointer; font: 700 15px 'Tajawal', sans-serif; }
        .no-print button:hover { background: var(--maroon-dark); }
        .no-print .close-button { background: #6b6b6b; }
        .license-sheet { max-width: 800px; min-height: 900px; margin: 0 auto; background: #fff url('{{ asset('images/image.png') }}') center / 100% 100% no-repeat; box-shadow: 0 2px 10px rgba(0, 0, 0, .1); }
        .license-body { padding: 18% 12% 20%; }
        .license-meta { display: flex; justify-content: space-between; margin-bottom: 8px; color: var(--muted); font-size: 12px; }
        .license-meta strong { color: var(--maroon-dark); }
        .license-title-block { margin: 10px 0 22px; text-align: center; }
        .license-title { color: var(--maroon); font-size: 21px; font-weight: 800; text-decoration: underline; }
        .license-intro { margin-top: 5px; color: var(--muted); font-size: 13px; }
        .info-section { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
        .info-box { padding: 13px 14px; border-right: 4px solid var(--maroon); border-radius: 8px; background: var(--panel); }
        .info-box h3, .address-section h3, .notes-section h3, .fees-section h3 { margin: 0 0 8px; color: var(--maroon-dark); font-size: 15px; font-weight: 700; }
        .info-box h3 { color: var(--maroon); font-size: 14px; }
        .info-box p { display: flex; justify-content: space-between; gap: 10px; margin: 0; padding: 5px 0; border-bottom: 1px solid rgba(230, 221, 201, .65); color: #333; font-size: 12px; }
        .info-box p:last-child { padding-bottom: 0; border-bottom: 0; }
        .info-box .value { color: #000; font-weight: 700; text-align: left; }
        .address-section, .notes-section { margin-bottom: 16px; padding: 13px 16px; border: 1px solid var(--border); border-radius: 8px; background: var(--panel); }
        .address-section p, .notes-section p { margin: 0; color: #333; font-size: 12.5px; line-height: 1.7; }
        .details-section { margin-bottom: 16px; padding: 13px 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--gold-light); }
        .details-section h3 { margin: 0 0 8px; color: var(--maroon-dark); font-size: 15px; font-weight: 700; }
        .details-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .detail-box { padding: 8px 6px; border: 1px solid var(--border); border-radius: 6px; background: #fff; text-align: center; }
        .detail-box .label { display: block; margin-bottom: 4px; color: var(--muted); font-size: 10px; }
        .detail-box .value { display: block; color: var(--maroon); font-size: 11.5px; font-weight: 700; }
        .fees-section { margin-bottom: 16px; padding: 14px; border: 1px solid var(--border); border-radius: 8px; background: var(--gold-light); }
        .fees-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 9px; }
        .fee-box { padding: 10px 5px; border: 1px solid var(--border); border-radius: 6px; background: #fff; text-align: center; }
        .fee-box .label { margin-bottom: 5px; color: var(--muted); font-size: 10px; }
        .fee-box .value { color: var(--maroon); font-size: 15px; font-weight: 800; }
        .signature-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 28px; }
        .signature { color: #333; font-size: 12px; text-align: center; }
        .signature-line { margin-top: 34px; border-top: 1px solid #555; }
        .footer-note { margin-top: 22px; padding-top: 9px; border-top: 1px solid var(--border); color: var(--muted); font-size: 10.5px; text-align: center; }
        .footer-note p { margin: 3px 0; }
        @media (max-width: 640px) {
            body { padding: 10px; }
            .license-body { padding: 13% 7% 12%; }
            .info-section { grid-template-columns: 1fr; gap: 10px; }
            .fees-grid { grid-template-columns: repeat(2, 1fr); }
            .signature-row { gap: 8px; }
        }
        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            .no-print { display: none !important; }
            @page { size: A4; margin: 0; }
            .license-sheet { width: 210mm; height: 297mm; max-width: 100%; margin: 0 auto; box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .license-body { width: 210mm; height: 297mm; padding: 48mm 20mm 15mm; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">طباعة الرخصة</button>
        <button class="close-button" onclick="window.close()">إغلاق</button>
    </div>
    <div class="license-sheet">
        <div class="license-body">
            <div class="license-meta">
                <span>تاريخ الإصدار: <strong>{{ date('d/m/Y') }}</strong></span>
                <span>رقم الملف: <strong>{{ $license->file_number }}</strong></span>
            </div>
            <div class="license-title-block">
                <div class="license-title">شهادة رخصة حرفة</div>
                <div class="license-intro">بلدية جباليا النزلة - دائرة الحرف والصناعات</div>
            </div>
            <div class="info-section">
                <div class="info-box">
                    <h3>بيانات صاحب الحرفة</h3>
                    <p><span>الاسم:</span><span class="value">{{ $license->owner_name ?? '---' }}</span></p>
                    <p><span>رقم الهوية:</span><span class="value">{{ $license->id_number ?? '---' }}</span></p>
                    <p><span>مالك العقار:</span><span class="value">{{ $license->property_owner ?? '---' }}</span></p>
                    <p><span>هوية المالك:</span><span class="value">{{ $license->property_owner_id ?? '---' }}</span></p>
                    <p><span>جهة الملكية:</span><span class="value">
                        @switch($license->ownership_entity)
                            @case('citizen') المواطن @break
                            @case('municipality') بلدية جباليا النزلة @break
                            @case('awqaf') وزارة الأوقاف @break
                            @default ---
                        @endswitch
                    </span></p>
                </div>
                <div class="info-box">
                    <h3>بيانات النشاط</h3>
                    <p><span>العلامة التجارية:</span><span class="value">{{ $license->commercial_name ?? '---' }}</span></p>
                    <p><span>تصنيف الحرفة:</span><span class="value">{{ $license->classification ?? '---' }}</span></p>
                    <p><span>نوع الحرفة التفصيلي:</span><span class="value">{{ $license->detailed_craft_type ?? '---' }}</span></p>
                    <p><span>حالة الترخيص:</span><span class="value">{{ $license->is_licensed ? 'مرخّص' : 'غير مرخّص' }}</span></p>
                    <p><span>سنة أول ترخيص:</span><span class="value">{{ $license->first_license_year ?? '---' }}</span></p>
                </div>
            </div>
            <div class="address-section">
                <h3>موقع الحرفة</h3>
                <p>{{ $license->address ?? '---' }} - {{ $license->neighborhood ?? '---' }} - {{ $license->main_street ?? '---' }}</p>
                <p>أقرب مسجد / معلم: {{ $license->nearest_mosque ?? '---' }}</p>
            </div>
            <div class="details-section">
                <h3>تفاصيل الموقع والتشغيل</h3>
                <div class="details-grid">
                    <div class="detail-box"><span class="label">رقم المبنى (GIS)</span><span class="value">{{ $license->gis_building_number ?? '---' }}</span></div>
                    <div class="detail-box"><span class="label">رقم الشارع</span><span class="value">{{ $license->street_number ?? '---' }}</span></div>
                    <div class="detail-box"><span class="label">الطابق</span><span class="value">{{ $license->floor_number ?? '---' }}</span></div>
                    <div class="detail-box"><span class="label">رقم الوحدة / المحل</span><span class="value">{{ $license->unit_number ?? '---' }}</span></div>
                    <div class="detail-box"><span class="label">أيام العطلة</span><span class="value">
                        @switch($license->holiday_days)
                            @case('Saturday') السبت @break
                            @case('Sunday') الأحد @break
                            @case('Monday') الاثنين @break
                            @case('Tuesday') الثلاثاء @break
                            @case('Wednesday') الأربعاء @break
                            @case('Thursday') الخميس @break
                            @case('Friday') الجمعة @break
                            @default ---
                        @endswitch
                    </span></div>
                    <div class="detail-box"><span class="label">المشغل المرخّص</span><span class="value">{{ $license->licensed_operator ?? '---' }}</span></div>
                </div>
            </div>
            <div class="fees-section">
                <h3>البيانات المالية</h3>
                <div class="fees-grid">
                    <div class="fee-box"><div class="label">رسوم الحرفة</div><div class="value">{{ number_format($license->craft_fees ?? 0, 2) }} ₪</div></div>
                    <div class="fee-box"><div class="label">رسوم اليافطة</div><div class="value">{{ number_format($license->signboard_fees ?? 0, 2) }} ₪</div></div>
                    <div class="fee-box"><div class="label">إجمالي الديون</div><div class="value">{{ number_format($license->total_debts ?? 0, 2) }} ₪</div></div>
                    <div class="fee-box"><div class="label">المتبقي</div><div class="value">{{ number_format($license->remaining ?? 0, 2) }} ₪</div></div>
                </div>
            </div>
            <div class="notes-section">
                <h3>ملاحظات</h3>
                <p>{{ $license->notes ?? 'لا توجد ملاحظات إضافية.' }}</p>
            </div>
            <div class="signature-row">
                <div class="signature"><strong>توقيع رئيس القسم</strong><div class="signature-line"></div></div>
                <div class="signature"><strong>ختم البلدية</strong><div class="signature-line"></div></div>
                <div class="signature"><strong>توقيع المدير</strong><div class="signature-line"></div></div>
            </div>
            <div class="footer-note"><p>تم إصدار هذه الرخصة بواسطة نظام إدارة الوثائق - بلدية جباليا النزلة</p></div>
        </div>
    </div>
</body>
</html>
