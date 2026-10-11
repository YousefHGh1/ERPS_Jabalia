<script>
    const CRAFT_FEE_DATA = {
        // رسوم الحرفة - رسوم اللوحة حسب الفئات
        // فئة 1
        "تجارة عامة تصليح وتجميع حمامات شمسية": { craft_fee: 590, signboard_fee: 60 },
        "ورش حدادة": { craft_fee: 390, signboard_fee: 30 },
        "تصليح أجهزة كهربائية": { craft_fee: 390, signboard_fee: 30 },
        "ورش تصليح وتجديد حمامات شمسية": { craft_fee: 390, signboard_fee: 30 },
        "مغاسل آلية": { craft_fee: 690, signboard_fee: 90 },
        "ورش ألمنيوم وصناعات ألمنيوم": { craft_fee: 590, signboard_fee: 60 },
        "مفاتيح وزرافيل": { craft_fee: 290, signboard_fee: 0 },
        "غسيل كي وتجهيز ملابس": { craft_fee: 290, signboard_fee: 0 },
        "تصليح ثلاجات وغسالات": { craft_fee: 290, signboard_fee: 0 },
        "ورش تصليح سيارات والدراجات": { craft_fee: 390, signboard_fee: 30 },
        "مكتب تاكسيات": { craft_fee: 590, signboard_fee: 60 },
        "مركز تعليم كمبيوتر": { craft_fee: 390, signboard_fee: 30 },
        "مكتب سياحة وسفر": { craft_fee: 390, signboard_fee: 30 },
        "محطة وقود فئة أ": { craft_fee: 3090, signboard_fee: 90 },
        "صراف": { craft_fee: 590, signboard_fee: 60 },
        "مسبكة معادن": { craft_fee: 490, signboard_fee: 60 },
        "تصليح تلفزيونات وراديوهات": { craft_fee: 290, signboard_fee: 0 },
        "مركز طبي": { craft_fee: 590, signboard_fee: 60 },
        "صراف آلي خارج البنك": { craft_fee: 5000, signboard_fee: 0 },
        "مصنع ورق": { craft_fee: 590, signboard_fee: 60 },
        "بيع اكسسوارات الزينة": { craft_fee: 290, signboard_fee: 0 },
        "مكتب عرائض": { craft_fee: 290, signboard_fee: 0 },
        "ورشة لسمكرة السيارات": { craft_fee: 390, signboard_fee: 30 },
        "تجارة عامة تجديد سخانات شمسية وكهربائية": { craft_fee: 690, signboard_fee: 90 },
        "مكتب مقاولات عامة واستثمارات": { craft_fee: 690, signboard_fee: 90 },
        "مكاتب العرائض والخدمات العامة": { craft_fee: 390, signboard_fee: 30 },
        "بيع بويات": { craft_fee: 390, signboard_fee: 30 },
        // فئة 2
        "معارض بيع السيارات": { craft_fee: 590, signboard_fee: 60 },
        "السمكرة و دهان سيارات": { craft_fee: 590, signboard_fee: 60 },
        "قطع الغيار": { craft_fee: 590, signboard_fee: 60 },
        "اكسسوارات سيارات": { craft_fee: 390, signboard_fee: 30 },
        "غسيل وتشحيم سيارات": { craft_fee: 390, signboard_fee: 30 },
        "كهربائي سيارات": { craft_fee: 390, signboard_fee: 30 },
        "مصانع خياطة و تجهيز ملابس": { craft_fee: 590, signboard_fee: 60 },
        "زيوت معدنية": { craft_fee: 390, signboard_fee: 30 },
        "مصنع شراب": { craft_fee: 590, signboard_fee: 60 },
        "تجارة عامة - أبقار ومواشي": { craft_fee: 690, signboard_fee: 90 },
        "تجارة عامة - ملابس وأقمشة": { craft_fee: 690, signboard_fee: 90 },
        "تجارة عامة - استيراد وتصدير": { craft_fee: 790, signboard_fee: 90 },
        "معرض لبيع الأثاث والموبيليا": { craft_fee: 490, signboard_fee: 60 },
        "بيع كعك": { craft_fee: 290, signboard_fee: 0 },
        "مدرسة تعليم سياقة سيارات": { craft_fee: 590, signboard_fee: 60 },
        "محل تنجيد كنب وكراسي": { craft_fee: 390, signboard_fee: 30 },
        "مصنع مخللات": { craft_fee: 590, signboard_fee: 60 },
        "601/1000KVA": { craft_fee: 2050, signboard_fee: 0 },
        "مكبس حديد": { craft_fee: 1090, signboard_fee: 90 },
        "بيع كاميرات مراقبة + مستلزمات الكمبيوتر": { craft_fee: 390, signboard_fee: 30 },
        "ملعب خماسي فئة \"أ\" مساحة 2 دونم": { craft_fee: 2000, signboard_fee: 90 },
        // فئة 3
        "رخام": { craft_fee: 690, signboard_fee: 90 },
        "صناعات بلاستيكية ونايلون": { craft_fee: 590, signboard_fee: 60 },
        "صناعي معامل بلوك وباطون وكسارات": { craft_fee: 690, signboard_fee: 90 },
        "مصانع مستحضرات التجميل والمنظفات": { craft_fee: 590, signboard_fee: 60 },
        "مصانع أدوات كهربائية": { craft_fee: 690, signboard_fee: 90 },
        "مطابع": { craft_fee: 690, signboard_fee: 90 },
        "خلط بويات وتعبئة وتصنيع": { craft_fee: 690, signboard_fee: 90 },
        "مصانع المرتديلا والنقانق": { craft_fee: 590, signboard_fee: 60 },
        "تجاري ذهب ومجوهرات": { craft_fee: 590, signboard_fee: 60 },
        "مصنع اسفنج": { craft_fee: 1090, signboard_fee: 90 },
        "مصنع زينكو": { craft_fee: 590, signboard_fee: 60 },
        "ورشة بناشر": { craft_fee: 290, signboard_fee: 0 },
        "مصنع عصير بندورة": { craft_fee: 1090, signboard_fee: 90 },
        "اطارات سيارات كبيرة": { craft_fee: 590, signboard_fee: 60 },
        "مصنع اسفلت": { craft_fee: 1090, signboard_fee: 90 },
        "تصنيع و انتاج بن و بهارات": { craft_fee: 690, signboard_fee: 90 },
        "بيع أجهزة لاب توب وأجهزة خلوية": { craft_fee: 590, signboard_fee: 60 },
        "بيع وصيانة ديجيتال": { craft_fee: 390, signboard_fee: 30 },
        "مخزن مشروبات غازية": { craft_fee: 290, signboard_fee: 0 },
        "اكسسوارات ومستحضرات تجميل": { craft_fee: 390, signboard_fee: 30 },
        "محل لبيع البن": { craft_fee: 390, signboard_fee: 30 },
        "مكبس خردوات": { craft_fee: 1090, signboard_fee: 90 },
        "تجارة عامة - بيع الآلات والمعدات الصناعية": { craft_fee: 690, signboard_fee: 90 },
        "1-300KVA": { craft_fee: 1050, signboard_fee: 0 },
        "301-600KVA": { craft_fee: 1550, signboard_fee: 0 },
        "مصنع باطون": { craft_fee: 1090, signboard_fee: 90 },
        "ورشة صناعية": { craft_fee: 590, signboard_fee: 60 },
        "بيع كلف خياطة": { craft_fee: 390, signboard_fee: 30 },
        "بيع مواد بناء": { craft_fee: 690, signboard_fee: 90 },
        "معصرة زيتون": { craft_fee: 490, signboard_fee: 60 },
        "محل بيع نايلون وبراميل بلاستيك": { craft_fee: 290, signboard_fee: 0 },
        "مصنع مناهل": { craft_fee: 790, signboard_fee: 90 },
        "مصنع انتاج وتعبئة مواد غذائية": { craft_fee: 1090, signboard_fee: 90 },
        "بيع أدوات كهربائية مستعملة": { craft_fee: 290, signboard_fee: 0 },
        "بيع خردة": { craft_fee: 290, signboard_fee: 0 },
        // فئة 4
        "تجاري أدوات صحية وسيراميك": { craft_fee: 690, signboard_fee: 90 },
        "محل بيع أدوات بناء وبويات": { craft_fee: 390, signboard_fee: 30 },
        "خرداوات وملابس مستعملة": { craft_fee: 290, signboard_fee: 0 },
        "مخازن": { craft_fee: 390, signboard_fee: 30 },
        "مكتب تجارة عامة وإستثمارات ومقاولات": { craft_fee: 1090, signboard_fee: 90 },
        "تجارة عامة/ استيراد وتصدير / مقاولات": { craft_fee: 790, signboard_fee: 90 },
        "أحذية وشنط": { craft_fee: 390, signboard_fee: 30 },
        "تجاري ألبسة ونثريات": { craft_fee: 390, signboard_fee: 30 },
        "تجاري أجهزة كهربائية ومنزلية": { craft_fee: 690, signboard_fee: 90 },
        "تأجير كراسي": { craft_fee: 290, signboard_fee: 0 },
        "مكاتب وقرطاسيه": { craft_fee: 290, signboard_fee: 0 },
        "تجاري": { craft_fee: 590, signboard_fee: 60 },
        "تبريد وتكييف": { craft_fee: 390, signboard_fee: 30 },
        "بيع أدوات كهربائية وتنويرات": { craft_fee: 390, signboard_fee: 30 },
        "خلوية _ جوال": { craft_fee: 390, signboard_fee: 30 },
        "تجاري معارض أثاث منزلي": { craft_fee: 690, signboard_fee: 90 },
        "تحف هدايا": { craft_fee: 290, signboard_fee: 0 },
        "ألعاب أطفال": { craft_fee: 390, signboard_fee: 30 },
        "تجاري مواد بناء": { craft_fee: 690, signboard_fee: 90 },
        "حديد وحصمة": { craft_fee: 590, signboard_fee: 60 },
        "تجاري أقمشة وستائر ومفروشات": { craft_fee: 390, signboard_fee: 30 },
        "معارض ثلاجات وغسالات مستعملة": { craft_fee: 390, signboard_fee: 30 },
        "تجاري تأجير وبيع فساتين سهرة": { craft_fee: 590, signboard_fee: 60 },
        "بنوك – وشركات كبرى": { craft_fee: 8090, signboard_fee: 0 },
        "فنادق وإستراحات": { craft_fee: 7090, signboard_fee: 90 },
        "بيع أجهزة حاسوب ومستلزماته": { craft_fee: 390, signboard_fee: 30 },
        "خياط عربي": { craft_fee: 200, signboard_fee: 0 },
        "حضانة": { craft_fee: 390, signboard_fee: 30 },
        "مكتب تجارة عامة و مقاولات": { craft_fee: 740, signboard_fee: 90 },
        "مكتب تجارة عامة استيراد و تصدير": { craft_fee: 590, signboard_fee: 60 },
        "بيع رفوف": { craft_fee: 390, signboard_fee: 30 },
        "مشغل اسفنج": { craft_fee: 590, signboard_fee: 60 },
        "شركة اتصالات": { craft_fee: 3090, signboard_fee: 0 },
        "محل لبيع الأدوات الصحية والسباكة": { craft_fee: 390, signboard_fee: 30 },
        "بوتيك ملابس جاهزة": { craft_fee: 390, signboard_fee: 30 },
        "مكتب تجارة عامة - مكتب تاكسيات": { craft_fee: 690, signboard_fee: 90 },
        "تجارة عامة - قطع غيار": { craft_fee: 690, signboard_fee: 90 },
        "معرض أثاث منزلي مستعمل": { craft_fee: 290, signboard_fee: 0 },
        "مكتب تجارة عامة": { craft_fee: 590, signboard_fee: 60 },
        "تجارة عامة": { craft_fee: 690, signboard_fee: 90 },
        "تصليح ساعات": { craft_fee: 290, signboard_fee: 0 },
        "مكتب محاماة": { craft_fee: 390, signboard_fee: 30 },
        "بيع أدوات منزلية": { craft_fee: 390, signboard_fee: 30 },
        "مجرشة بلاستيك": { craft_fee: 590, signboard_fee: 60 },
        "مخازن فواكه": { craft_fee: 390, signboard_fee: 30 },
        "مصنع لصناعة الحلويات والطحينة والحلاوة": { craft_fee: 690, signboard_fee: 90 },
        "تجاري ذهب ومجوهرات": { craft_fee: 490, signboard_fee: 60 },
        "بيع أدوات بناء": { craft_fee: 490, signboard_fee: 60 },
        "مخزن لبيع الأخشاب": { craft_fee: 390, signboard_fee: 30 },
        "تصليح عربات": { craft_fee: 290, signboard_fee: 0 },
        "محمص وبيع مكسرات": { craft_fee: 390, signboard_fee: 30 },
        "تصنيع كيك و مواد غذائية": { craft_fee: 590, signboard_fee: 60 },
        "مركز الإستشارات والخدمات التنموية": { craft_fee: 390, signboard_fee: 30 },
        "بيع أقمشة": { craft_fee: 390, signboard_fee: 30 },
        "تصليح مكن": { craft_fee: 390, signboard_fee: 30 },
        "محل دهان موبيليا": { craft_fee: 390, signboard_fee: 30 },
        "بيع وتركيب زجاج ومرايا": { craft_fee: 390, signboard_fee: 30 },
        "تجارة عامة ومقاولات": { craft_fee: 790, signboard_fee: 90 },
        "مصنع تجميع ثلاجات": { craft_fee: 390, signboard_fee: 30 },
        "قرية سياحية": { craft_fee: 7000, signboard_fee: 90 },
        "تصنيع وانتاج وبيع البن والبهارات والمكسرات": { craft_fee: 590, signboard_fee: 60 },
        "محطة اذاعة": { craft_fee: 1090, signboard_fee: 90 },
        "مزرعة ارانب": { craft_fee: 490, signboard_fee: 60 },
        "تصليح ولحام صدامات": { craft_fee: 290, signboard_fee: 0 },
        "يافطات واعلانات": { craft_fee: 0, signboard_fee: 0 },
        "مكتب دعاية واعلان": { craft_fee: 490, signboard_fee: 60 },
        "مركز ثقافي او تعليمي": { craft_fee: 490, signboard_fee: 60 },
        "مركز دراسات وابحاث": { craft_fee: 390, signboard_fee: 30 },
        "تجارة عامة وصناعة": { craft_fee: 690, signboard_fee: 90 },
        "دراجات هوائية": { craft_fee: 290, signboard_fee: 0 },
        "مصنع بورجر": { craft_fee: 590, signboard_fee: 60 },
        "محل تنجيد": { craft_fee: 240, signboard_fee: 0 },
        "بيع أمتعة مستعملة": { craft_fee: 190, signboard_fee: 0 },
        "ورشة لثني و تشكيل الحديد": { craft_fee: 590, signboard_fee: 60 },
        "بيع فول وفلافل": { craft_fee: 290, signboard_fee: 0 },
        "محل لبيع حلويات و سكاكر": { craft_fee: 240, signboard_fee: 0 },
        "مصنع مناهل": { craft_fee: 790, signboard_fee: 90 },
        "بنك + صراف داخلي": { craft_fee: 8590, signboard_fee: 90 },
        "بيع دخان": { craft_fee: 290, signboard_fee: 0 },
        "بيع حصر": { craft_fee: 290, signboard_fee: 0 },
        "بيع طيور": { craft_fee: 290, signboard_fee: 0 },
        "منتجع سياحي": { craft_fee: 7090, signboard_fee: 90 },
        "مصنع علف": { craft_fee: 1090, signboard_fee: 90 },
        "مخبز صاج وفراشيح": { craft_fee: 390, signboard_fee: 30 },
        "مخزن مواد بناء": { craft_fee: 490, signboard_fee: 60 },
        "بيع مرطبات": { craft_fee: 290, signboard_fee: 0 },
        "بيع أحذية بلاستيكية": { craft_fee: 290, signboard_fee: 0 },
        "بيع اطارات سيارات": { craft_fee: 490, signboard_fee: 60 },
        "بيع دواجن": { craft_fee: 290, signboard_fee: 0 },
        "بيع بويات": { craft_fee: 390, signboard_fee: 30 },
        "بيع مواد تنظيف": { craft_fee: 390, signboard_fee: 30 },
        "بيع دجاج": { craft_fee: 290, signboard_fee: 0 },
        "مركز تعليمي": { craft_fee: 290, signboard_fee: 0 },
        "تجارة عامة - مواد غذائية": { craft_fee: 690, signboard_fee: 90 },
        "مكتب مقاولات عامة": { craft_fee: 590, signboard_fee: 60 },
        "بيع حلويات": { craft_fee: 390, signboard_fee: 30 },
        "تصميم وبرمجة": { craft_fee: 590, signboard_fee: 60 },
        "تخزين بطاريات": { craft_fee: 390, signboard_fee: 30 },
        "بيع وتركيب العطور": { craft_fee: 390, signboard_fee: 30 },
        "معرض مطابخ ألمنيوم": { craft_fee: 490, signboard_fee: 60 },
        "مخزن لبيع وتخزين البيض": { craft_fee: 290, signboard_fee: 0 },
        "تصليح اجهزة كمبيوتر": { craft_fee: 390, signboard_fee: 30 },
        "منجرة ألية": { craft_fee: 390, signboard_fee: 30 },
        "بيع وصيانة غازات مستعملة": { craft_fee: 290, signboard_fee: 0 },
        "معمل معجنات": { craft_fee: 390, signboard_fee: 30 },
        "ورشة غيار زيوت وفلاتر": { craft_fee: 290, signboard_fee: 0 },
        "تجارة عامة - بيع وخلط بويات": { craft_fee: 790, signboard_fee: 90 },
        "صالتي أفراح": { craft_fee: 3160, signboard_fee: 90 },
        // فئة 5
        "مطاعم كباب وشواء لحوم": { craft_fee: 390, signboard_fee: 30 },
        "مطاعم أكلات شعبية فول + فلافل": { craft_fee: 340, signboard_fee: 30 },
        "مطاعم شاورما": { craft_fee: 590, signboard_fee: 60 },
        "مطابخ مأكولات": { craft_fee: 490, signboard_fee: 60 },
        "مطاعم ساندويتشات كبدة ونقانق وبيتزا": { craft_fee: 590, signboard_fee: 60 },
        "مطاعم أسماك": { craft_fee: 590, signboard_fee: 60 },
        "كافي شوب": { craft_fee: 590, signboard_fee: 60 },
        "مطعم وكوفي شوب سياحي درجة اولي": { craft_fee: 1090, signboard_fee: 90 },
        "مصنع حلويات": { craft_fee: 590, signboard_fee: 60 },
        "محل لبيع حلويات و سكاكر": { craft_fee: 240, signboard_fee: 0 },
        // فئة 6
        "مخبز الى للخبز و المعجنات": { craft_fee: 890, signboard_fee: 90 },
        "بيع كعك ومعجنات وجاتوهات": { craft_fee: 390, signboard_fee: 30 },
        "محطات وقود": { craft_fee: 3090, signboard_fee: 90 },
        "مزارع دواجن": { craft_fee: 490, signboard_fee: 60 },
        "مخبز الى": { craft_fee: 590, signboard_fee: 60 },
        "مطحنة حبوب": { craft_fee: 590, signboard_fee: 60 },
        "مطعم وكوفي شوب سياحي درجة اولي": { craft_fee: 1090, signboard_fee: 90 },
        "ورشة غيار زيوت وفلاتر": { craft_fee: 290, signboard_fee: 0 },
        // فئة 7
        "محطات غاز": { craft_fee: 3090, signboard_fee: 90 },
        "مراكز تعبئة غاز منزلي": { craft_fee: 2090, signboard_fee: 90 },
        "صالات أفراح": { craft_fee: 2090, signboard_fee: 90 },
        "مستودع ادوية": { craft_fee: 1090, signboard_fee: 90 },
        "محطة 1": { craft_fee: 777, signboard_fee: 90 },
        "محطة وقود فئة ب": { craft_fee: 2590, signboard_fee: 90 },
        "محطة وقود فئة ج": { craft_fee: 2090, signboard_fee: 90 },
        "محطة غاز فئة أ": { craft_fee: 3090, signboard_fee: 90 },
        "محطة غاز فئة ب": { craft_fee: 2590, signboard_fee: 90 },
        "محطة غاز فئة ج": { craft_fee: 2090, signboard_fee: 90 },
        "صالة افراح فئة أ": { craft_fee: 2090, signboard_fee: 90 },
        // فئة 8
        "استراحة": { craft_fee: 3590, signboard_fee: 90 },
        "ساحات": { craft_fee: 0, signboard_fee: 0 },
        "ملعب خماسي فئة ج أقل من دونم": { craft_fee: 1000, signboard_fee: 90 },
        "صالة افراح فئة ب": { craft_fee: 1590, signboard_fee: 90 },
        "صالة افراح فئة ج": { craft_fee: 1090, signboard_fee: 90 },
        "شاليه": { craft_fee: 1500, signboard_fee: 90 },
        "عيادة طبيب بيطري": { craft_fee: 490, signboard_fee: 60 },
        "صراف آلي خارج البنك": { craft_fee: 5000, signboard_fee: 0 },
        "مسبح": { craft_fee: 1090, signboard_fee: 90 },
        "ملعب خماسي فئة ب أقل من 2 دونم": { craft_fee: 1500, signboard_fee: 90 },
        "مؤسسة اقراض": { craft_fee: 1690, signboard_fee: 90 },
        "مكتب مقاولات عامة واستثمارات": { craft_fee: 690, signboard_fee: 90 },
        "مكاتب العرائض والخدمات العامة": { craft_fee: 390, signboard_fee: 30 },
        "بيع بويات": { craft_fee: 390, signboard_fee: 30 },
        // فئة 9
        "مكاتب المحاسبة": { craft_fee: 390, signboard_fee: 30 },
        "مكاتب الدعاية والاعلان": { craft_fee: 390, signboard_fee: 30 },
        "مكاتب الحج والعمرة والسياحة": { craft_fee: 590, signboard_fee: 60 },
        "مكتب خدمات جامعية": { craft_fee: 390, signboard_fee: 30 },
        "مكاتب هندسية": { craft_fee: 590, signboard_fee: 60 },
        "مكاتب اتصالات": { craft_fee: 390, signboard_fee: 30 },
        "خدمات انترنت وألعاب أتاري": { craft_fee: 390, signboard_fee: 30 },
        "تيسير زواج": { craft_fee: 390, signboard_fee: 30 },
        "كوافير سيدات": { craft_fee: 390, signboard_fee: 30 },
        "صالون حلاقة": { craft_fee: 290, signboard_fee: 0 },
        "بقـــــــالة": { craft_fee: 290, signboard_fee: 0 },
        "مكتب تجارة عامة - بيع خضار وفواكه": { craft_fee: 590, signboard_fee: 60 },
        "مخزن لبيع وتخزين البيض": { craft_fee: 290, signboard_fee: 0 },
        "مختبر فحص مواد": { craft_fee: 1090, signboard_fee: 90 },
        "ستديو تصوير": { craft_fee: 390, signboard_fee: 30 },
        "منجرة ألية": { craft_fee: 390, signboard_fee: 30 },
        "محل دهان موبيليا": { craft_fee: 390, signboard_fee: 30 },
        "بيع نثريات": { craft_fee: 290, signboard_fee: 0 },
        "ورشة خراطة": { craft_fee: 590, signboard_fee: 60 },
        "شركة اتصالات خلوية": { craft_fee: 15000, signboard_fee: 0 },
        "صالة رياضية": { craft_fee: 590, signboard_fee: 60 },
        "شركة لإنتاج وتوزيع الأعمال الإعلامية": { craft_fee: 690, signboard_fee: 90 },
        "معمل مزيل شعر": { craft_fee: 590, signboard_fee: 60 },
        "مركز ثقافي او تعليمي": { craft_fee: 490, signboard_fee: 60 },
        "مركز دراسات وابحاث": { craft_fee: 390, signboard_fee: 30 },
        "تجارة عامة وصناعة": { craft_fee: 690, signboard_fee: 90 },
        "مكتب شركة باصات": { craft_fee: 590, signboard_fee: 60 },
        "بيع وصيانة غازات مستعملة": { craft_fee: 290, signboard_fee: 0 },
        "معمل معجنات": { craft_fee: 390, signboard_fee: 30 },
        // فئة 10
        "سوبر ماركت": { craft_fee: 590, signboard_fee: 60 },
        "ميني ماركت": { craft_fee: 390, signboard_fee: 30 },
        "بيع مواد تموينية": { craft_fee: 390, signboard_fee: 30 },
        "بيع مشروبات غازية": { craft_fee: 390, signboard_fee: 30 },
        "بيع بوظة ومرطبات وجاتوه": { craft_fee: 390, signboard_fee: 30 },
        "بيع لحوم طازجة وجزارة": { craft_fee: 590, signboard_fee: 60 },
        "بنك فرعي اول": { craft_fee: 8090, signboard_fee: 0 },
        // فئة 11
        "بيع دواجن وحبش": { craft_fee: 390, signboard_fee: 30 },
        "بيع لحوم مجمدة وأسماك": { craft_fee: 340, signboard_fee: 30 },
        "بيع أسماك طازجة": { craft_fee: 390, signboard_fee: 30 },
        "محطات تحلية مياه": { craft_fee: 590, signboard_fee: 60 },
        // فئة 12
        "شركات إستخراج مياه": { craft_fee: 590, signboard_fee: 60 },
        "بيع فلاتر مياه": { craft_fee: 490, signboard_fee: 60 },
        // فئة 13
        "عيادة طبيب متخصص": { craft_fee: 590, signboard_fee: 60 },
        "صيدلية": { craft_fee: 590, signboard_fee: 60 },
        "مختبر": { craft_fee: 390, signboard_fee: 30 },
        "عيادة نفسية": { craft_fee: 490, signboard_fee: 60 },
        "بيع اعشاب و عطارة": { craft_fee: 290, signboard_fee: 0 },
        "نظارات طبية": { craft_fee: 390, signboard_fee: 30 },
        "عيادة أسنان": { craft_fee: 590, signboard_fee: 60 },
        "مزارع أبقار وأغنام": { craft_fee: 790, signboard_fee: 90 },
        "محل لبيع ادوات بيطرية": { craft_fee: 390, signboard_fee: 30 },
        "عيادة طبيب جراح": { craft_fee: 790, signboard_fee: 90 },
        "عيادة طبيب عام": { craft_fee: 490, signboard_fee: 60 },
        "مصنع شيبس": { craft_fee: 590, signboard_fee: 60 },
        "مخبز الى": { craft_fee: 590, signboard_fee: 60 },
        // فئة 14
        "مزارع دواجن لحم": { craft_fee: 490, signboard_fee: 60 },
        "بيع اعلاف": { craft_fee: 390, signboard_fee: 30 },
        "بيع أدوات زراعية وبذور": { craft_fee: 390, signboard_fee: 30 },
        "مشاتل زراعية": { craft_fee: 390, signboard_fee: 30 },
        "بيع خضار وفواكه": { craft_fee: 290, signboard_fee: 0 },
        "تجارة عامة - ملابس وأقمشة": { craft_fee: 690, signboard_fee: 90 },
        "مصانع البان و جبنة": { craft_fee: 590, signboard_fee: 60 },
        "تصليح ولحام صدامات": { craft_fee: 290, signboard_fee: 0 },
        // فئة 16
        "محل خياطة": { craft_fee: 290, signboard_fee: 0 },
        // فئة 17
        "601/1000KVA": { craft_fee: 2050, signboard_fee: 0 },
        "مكبس حديد": { craft_fee: 1090, signboard_fee: 90 },
        // فئة 0
        "مطحنة بن وبهارات ودقة وزعتر": { craft_fee: 490, signboard_fee: 60 },
        "مطحنة ملح": { craft_fee: 290, signboard_fee: 0 },
        "مكتب استثمارات عقارية": { craft_fee: 590, signboard_fee: 60 }
    };

    const CRAFT_DETAILED_TYPES = {
        "مهنية": [
            "تجارة عامة تصليح وتجميع حمامات شمسية",
            "ورش حدادة",
            "تصليح أجهزة كهربائية",
            "ورش تصليح وتجديد حمامات شمسية",
            "مغاسل آلية",
            "ورش ألمنيوم وصناعات ألمنيوم",
            "مفاتيح وزرافيل",
            "غسيل كي وتجهيز ملابس",
            "تصليح ثلاجات وغسالات",
            "ورش تصليح سيارات والدراجات",
            "مكتب تاكسيات",
            "مركز تعليم كمبيوتر",
            "مكتب سياحة وسفر",
            "محطة وقود فئة أ",
            "صراف",
            "مسبكة معادن",
            "تصليح تلفزيونات وراديوهات",
            "مركز طبي",
            "صراف آلي خارج البنك",
            "مصنع ورق"
        ],
        "مهنية مواصلات": [
            "معارض بيع السيارات",
            "السمكرة و دهان سيارات",
            "قطع الغيار",
            "اكسسوارات سيارات",
            "غسيل وتشحيم سيارات",
            "كهربائي سيارات",
            "مصانع خياطة و تجهيز ملابس",
            "زيوت معدنية",
            "مصنع شراب",
            "تجارة عامة - أبقار ومواشي",
            "تجارة عامة - ملابس وأقمشة",
            "تجارة عامة - استيراد وتصدير",
            "معرض لبيع الأثاث والموبيليا",
            "بيع كعك",
            "مدرسة تعليم سياقة سيارات",
            "محل تنجيد كنب وكراسي",
            "مصنع مخللات",
            "601/1000KVA",
            "مكبس حديد"
        ],
        "صناعية": [
            "رخام",
            "صناعات بلاستيكية ونايلون",
            "صناعي معامل بلوك وباطون وكسارات",
            "مصانع مستحضرات التجميل والمنظفات",
            "مصانع أدوات كهربائية",
            "مطابع",
            "خلط بويات وتعبئة وتصنيع",
            "مصانع المرتديلا والنقانق",
            "تجاري ذهب ومجوهرات",
            "مصنع اسفنج",
            "مصنع زينكو",
            "ورشة بناشر",
            "مصنع عصير بندورة",
            "اطارات سيارات كبيرة",
            "مصنع اسفلت",
            "تصنيع و انتاج بن و بهارات",
            "بيع أجهزة لاب توب وأجهزة خلوية",
            "بيع وصيانة ديجيتال",
            "مخزن مشروبات غازية",
            "اكسسوارات ومستحضرات تجميل",
            "محل لبيع البن",
            "مكبس خردوات",
            "تجارة عامة - بيع الآلات والمعدات الصناعية",
            "1-300KVA",
            "301-600KVA",
            "مصنع باطون",
            "ورشة صناعية",
            "بيع كلف خياطة",
            "بيع مواد بناء",
            "معصرة زيتون",
            "محل بيع نايلون وبراميل بلاستيك",
            "مصنع مناهل",
            "مصنع انتاج وتعبئة مواد غذائية"
        ],
        "تجارية": [
            "تجاري أدوات صحية وسيراميك",
            "محل بيع أدوات بناء وبويات",
            "خرداوات وملابس مستعملة",
            "مخازن",
            "مكتب تجارة عامة وإستثمارات ومقاولات",
            "تجارة عامة/ استيراد وتصدير / مقاولات",
            "أحذية وشنط",
            "تجاري ألبسة ونثريات",
            "تجاري أجهزة كهربائية ومنزلية",
            "تأجير كراسي",
            "مكاتب وقرطاسيه",
            "تجاري",
            "تبريد وتكييف",
            "بيع أدوات كهربائية وتنويرات",
            "خلوية _ جوال",
            "تجاري معارض أثاث منزلي",
            "تحف هدايا",
            "ألعاب أطفال",
            "تجاري مواد بناء",
            "حديد وحصمة",
            "تجاري أقمشة وستائر ومفروشات",
            "معارض ثلاجات وغسالات مستعملة",
            "تجاري تأجير وبيع فساتين سهرة",
            "بنوك – وشركات كبرى",
            "فنادق وإستراحات",
            "بيع أجهزة حاسوب ومستلزماته",
            "خياط عربي",
            "تجارة عامة تجديد سخانات شمسية وكهربائية",
            "حضانة",
            "مكتب تجارة عامة و مقاولات",
            "مكتب تجارة عامة استيراد و تصدير",
            "بيع رفوف",
            "مشغل اسفنج",
            "شركة اتصالات",
            "مكتب تجارة عامة - بيع خضار وفواكه",
            "محل لبيع الأدوات الصحية والسباكة",
            "بوتيك ملابس جاهزة",
            "مكتب تجارة عامة - مكتب تاكسيات",
            "تجارة عامة - قطع غيار",
            "معرض أثاث منزلي مستعمل",
            "مكتب تجارة عامة",
            "تجارة عامة",
            "تصليح ساعات",
            "بيع اكسسوارات الزينة",
            "مكتب محاماة",
            "بيع نثريات",
            "بيع أدوات منزلية",
            "مجرشة بلاستيك",
            "مخازن فواكه",
            "مصنع لصناعة الحلويات والطحينة والحلاوة",
            "تجاري ذهب ومجوهرات",
            "بيع أدوات بناء",
            "مخزن لبيع الأخشاب",
            "تصليح عربات",
            "محمص وبيع مكسرات",
            "تصنيع كيك و مواد غذائية",
            "مركز الإستشارات والخدمات التنموية",
            "بيع كاميرات مراقبة + مستلزمات الكمبيوتر",
            "بيع أقمشة",
            "تصليح مكن",
            "مكتب عرائض",
            "محل دهان موبيليا",
            "بيع وتركيب زجاج ومرايا",
            "تجارة عامة ومقاولات",
            "مصنع تجميع ثلاجات",
            "بيع أدوات كهربائية مستعملة",
            "قرية سياحية",
            "تصنيع وانتاج وبيع البن والبهارات والمكسرات",
            "محطة اذاعة",
            "مزرعة ارانب",
            "تصليح ولحام صدامات",
            "يافطات واعلانات",
            "مكتب دعاية واعلان",
            "مركز ثقافي او تعليمي",
            "مركز دراسات وابحاث",
            "تجارة عامة وصناعة",
            "دراجات هوائية",
            "مصنع بورجر",
            "محل تنجيد",
            "بيع أمتعة مستعملة",
            "ورشة لثني و تشكيل الحديد",
            "بيع فول وفلافل",
            "محل لبيع حلويات و سكاكر",
            "بنك + صراف داخلي",
            "بيع دخان",
            "بيع حصر",
            "بيع طيور",
            "منتجع سياحي",
            "مصنع علف",
            "مخبز صاج وفراشيح",
            "مخزن مواد بناء",
            "بيع مرطبات",
            "بيع أحذية بلاستيكية",
            "بيع اطارات سيارات",
            "بيع دواجن",
            "مسبح",
            "بيع بويات",
            "بيع مواد تنظيف",
            "بيع دجاج",
            "مركز تعليمي",
            "تجارة عامة - مواد غذائية",
            "مكتب مقاولات عامة",
            "مكتب مقاولات عامة واستثمارات",
            "بيع حلويات",
            "تصميم وبرمجة",
            "تخزين بطاريات",
            "بيع وتركيب العطور",
            "معرض مطابخ ألمنيوم",
            "مخزن لبيع وتخزين البيض",
            "مختبر فحص مواد",
            "تصليح اجهزة كمبيوتر",
            "ستديو تصوير",
            "منجرة ألية",
            "بيع وصيانة غازات مستعملة",
            "معمل معجنات",
            "ورشة غيار زيوت وفلاتر",
            "بيع رفوف",
            "شركة اتصالات خلوية",
            "تجارة عامة - بيع وخلط بويات",
            "صالة رياضية",
            "شركة لإنتاج وتوزيع الأعمال الإعلامية",
            "معمل مزيل شعر",
            "مكتب استثمارات عقارية",
            "مطحنة ملح",
            "مؤسسة اقراض",
            "ملعب خماسي فئة \"أ\" مساحة 2 دونم"
        ],
        "مطاعم/مطابخ": [
            "مطاعم كباب وشواء لحوم",
            "مطاعم أكلات شعبية فول + فلافل",
            "مطاعم شاورما",
            "مطابخ مأكولات",
            "مطاعم ساندويتشات كبدة ونقانق وبيتزا",
            "مطاعم أسماك",
            "كافي شوب",
            "مطعم وكوفي شوب سياحي درجة اولي",
            "مصنع حلويات"
        ],
        "مخابز": [
            "مخبز الى للخبز و المعجنات",
            "بيع كعك ومعجنات وجاتوهات",
            "محطات وقود",
            "مزارع دواجن",
            "مخبز الى",
            "مطحنة حبوب",
            "مطعم وكوفي شوب سياحي درجة اولي"
        ],
        "محروقات": [
            "محطات غاز",
            "مراكز تعبئة غاز منزلي",
            "صالات أفراح",
            "مستودع ادوية",
            "محطة 1",
            "محطة وقود فئة ب",
            "محطة وقود فئة ج",
            "محطة غاز فئة أ",
            "محطة غاز فئة ب",
            "محطة غاز فئة ج",
            "صالة افراح فئة أ",
            "مزرعة ارانب"
        ],
        "صالات ومناسبات": [
            "استراحة",
            "ساحات",
            "ملعب خماسي فئة \"ج\" أقل من دونم",
            "صالة افراح فئة ب",
            "صالة افراح فئة ج",
            "شاليه",
            "عيادة طبيب بيطري",
            "صراف آلي خارج البنك",
            "مسبح",
            "ملعب خماسي فئة \"ب\" أقل من 2 دونم",
            "مؤسسة اقراض",
            "مكتب مقاولات عامة واستثمارات"
        ],
        "خدماتية": [
            "مكاتب العرائض والخدمات العامة",
            "مكاتب المحاسبة",
            "مكاتب الدعاية والاعلان",
            "مكاتب الحج والعمرة والسياحة",
            "مكتب خدمات جامعية",
            "مكاتب هندسية",
            "مكاتب اتصالات",
            "خدمات انترنت وألعاب أتاري",
            "تيسير زواج",
            "كوافير سيدات",
            "صالون حلاقة",
            "بقـــــــالة",
            "مكتب تجارة عامة - بيع خضار وفواكه",
            "مخزن لبيع وتخزين البيض",
            "مختبر فحص مواد",
            "ستديو تصوير",
            "منجرة ألية",
            "محل دهان موبيليا",
            "بيع نثريات",
            "ورشة خراطة",
            "شركة اتصالات خلوية",
            "صالة رياضية",
            "شركة لإنتاج وتوزيع الأعمال الإعلامية",
            "معمل مزيل شعر",
            "مركز ثقافي او تعليمي",
            "مركز دراسات وابحاث",
            "تجارة عامة وصناعة",
            "مكتب شركة باصات"
        ],
        "غذائية A": [
            "سوبر ماركت",
            "ميني ماركت",
            "بيع مواد تموينية",
            "بيع مشروبات غازية",
            "بيع بوظة ومرطبات وجاتوه",
            "بيع لحوم طازجة وجزارة",
            "بنك فرعي اول"
        ],
        "غذائية B": [
            "بيع دواجن وحبش",
            "بيع لحوم مجمدة وأسماك",
            "بيع أسماك طازجة",
            "محطات تحلية مياه"
        ],
        "تحلية مياه": [
            "شركات إستخراج مياه",
            "بيع فلاتر مياه"
        ],
        "طبية": [
            "عيادة طبيب متخصص",
            "صيدلية",
            "مختبر",
            "عيادة نفسية",
            "بيع اعشاب و عطارة",
            "نظارات طبية",
            "عيادة أسنان",
            "مزارع أبقار وأغنام",
            "محل لبيع ادوات بيطرية",
            "عيادة طبيب جراح",
            "عيادة طبيب عام",
            "مصنع شيبس"
        ],
        "زراعية": [
            "مزارع دواجن لحم",
            "بيع اعلاف",
            "بيع أدوات زراعية وبذور",
            "مشاتل زراعية",
            "بيع خضار وفواكه",
            "تجارة عامة - ملابس وأقمشة",
            "مصانع البان و جبنة",
            "تصليح ولحام صدامات"
        ],
        "مصانع غذائية": [
            "محل خياطة"
        ],
        "يافطات و اعلانات": [
            "601/1000KVA",
            "مكبس حديد"
        ],
        "مولدات": [
            "مطحنة بن وبهارات ودقة وزعتر",
            "مطحنة ملح",
            "مكتب استثمارات عقارية"
        ],
        "مطاحن": []
    };

    function loadDetailedCraftTypes(selectedValue = '') {
        const classif = document.getElementById('classification').value;
        const $detailed = $('#detailed_craft_type');

        console.log('loadDetailedCraftTypes called, classif:', classif, 'selectedValue:', selectedValue);

        if (!$detailed.length) {
            console.log('detailed element not found');
            return;
        }

        // استخراج اسم التصنيف بدون الرقم (مثل "1 - مهنية" -> "مهنية")
        let classifKey = classif;
        if (classif && classif.includes(' - ')) {
            classifKey = classif.split(' - ')[1];
        }

        if (classifKey && CRAFT_DETAILED_TYPES[classifKey]) {
            let options = '<option value="">اختر نوع الحرفة التفصيلي...</option>';
            CRAFT_DETAILED_TYPES[classifKey].forEach(item => {
                const selected = (selectedValue === item) ? ' selected' : '';
                options += `<option value="${item}"${selected}>${item}</option>`;
            });
            $detailed.html(options);

            if ($.fn.selectpicker) {
                $detailed.selectpicker('refresh');
                $detailed.selectpicker('enable');
                if (selectedValue) {
                    $detailed.selectpicker('val', selectedValue);
                }
            } else {
                $detailed[0].disabled = false;
            }
        } else {
            $detailed.html('<option value="">اختر التصنيف أولاً...</option>');

            if ($.fn.selectpicker) {
                $detailed.selectpicker('refresh');
                $detailed.selectpicker('disable');
            } else {
                $detailed[0].disabled = true;
            }
        }
    }

    // دالة لتحديث الرسوم عند اختيار نوع حرف تفصيلي
    function updateFeesFromDetailedType() {
        const detailedSelect = document.getElementById('detailed_craft_type');
        const selectedValue = detailedSelect.value;

        if (!selectedValue) return;

        // البحث عن رسوم النوع التفصيلي مباشرة
        const fees = CRAFT_FEE_DATA[selectedValue];
        if (fees) {
            const craftFeeInput = document.getElementById('craft_fees');
            const signboardFeeInput = document.querySelector('input[name="signboard_fees"]');

            if (craftFeeInput) craftFeeInput.value = fees.craft_fee;
            if (signboardFeeInput) signboardFeeInput.value = fees.signboard_fee;

            // إعادة حساب الرصيد
            calculateRemaining();
        }
    }

    function generateFileNumber() {
        const fileInput = document.getElementById('file_number');
        if (fileInput && !fileInput.readOnly) {
            fileInput.value = "{{ $next_file_number ?? '' }}";
        }
    }

    function handleStatusChange(select) {
        const status = select.value;
        const fileInput = document.getElementById('file_number');
        const selectedOption = select.options[select.selectedIndex];
        const color = selectedOption.getAttribute('data-color');

        if (color) {
            select.style.border = `2px solid ${color}`;
            if (fileInput) {
                fileInput.style.backgroundColor = color;
                fileInput.style.color = 'white';
            }
        } else {
            select.style.border = `1px solid #E4E6EF`;
            if (fileInput) {
                fileInput.style.backgroundColor = '#ffffff';
                fileInput.style.color = '#3F4254';
            }
        }

        // Automatic file number and year generation
        const yearInput = document.querySelector('input[name="first_license_year"]');
        const lastLicenseInput = document.querySelector('input[name="last_license"]');
        const currentYear = new Date().getFullYear();

        if (status === 'new') {
            if (fileInput && !fileInput.value && !fileInput.readOnly) {
                fileInput.value = "{{ $next_file_number ?? '' }}";
            }

            if (yearInput && !yearInput.value) {
                yearInput.value = currentYear;
            }

            if (lastLicenseInput && !lastLicenseInput.value) {
                lastLicenseInput.value = currentYear;
            }
        } else if (status === 'renewal') {
            if (lastLicenseInput && !lastLicenseInput.value) {
                lastLicenseInput.value = currentYear - 1;
            }
        }
    }

    function validateForm() {
        const fileNumber = document.getElementById('file_number')?.value;
        const status = document.getElementById('status')?.value;
        const classification = document.getElementById('classification')?.value;
        const saveBtn = document.getElementById('saveBtn');

        if (saveBtn) {
            if (fileNumber && status && classification) {
                saveBtn.disabled = false;
            } else {
                saveBtn.disabled = true;
            }
        }
    }

    function calculateRemaining() {
        const isEditForm = document.getElementById('editLicenseForm');
        const craftFees = parseFloat(document.getElementById('craft_fees')?.value) || 0;
        const signboardFees = parseFloat(document.querySelector('input[name="signboard_fees"]')?.value) || 0;
        const paymentAmount = parseFloat(document.querySelector('input[name="payment_amount"]')?.value) || 0;
        const discountPercent = parseFloat(document.getElementById('discount_percent')?.value) || 0;

        // المبلغ المستحق للفترة الحالية فقط (رسوم الحرفة + رسوم اللوحة بعد الخصم)
        const currentBase = craftFees + signboardFees;
        const discountAmount = currentBase * (discountPercent / 100);
        const finalAfterDiscount = currentBase - discountAmount;
        const remainingValue = finalAfterDiscount - paymentAmount;

        const remainingInput = document.querySelector('input[name="remaining"]');
        const discountAmountInput = document.getElementById('discount_amount');
        const finalPricePreviewInput = document.getElementById('final_price_preview');

        if (discountAmountInput) {
            discountAmountInput.value = discountAmount.toFixed(2);
        }
        if (finalPricePreviewInput) {
            finalPricePreviewInput.value = finalAfterDiscount.toFixed(2);
        }

        // في صفحة الإضافة فقط نحسب ونظهر "الرصيد المتبقي" للواجهة،
        // ولا نكتب في حقل "إجمالي الديون" حتى لا يُعاد استخدامه ويؤدي لمضاعفة المبلغ بعد الحفظ.
        if (!isEditForm && remainingInput) {
            remainingInput.value = remainingValue.toFixed(2);
        }
    }

    // تعريف الدوال في النطاق العام لتكون متاحة من أي مكان
    window.loadDetailedCraftTypes = loadDetailedCraftTypes;
    window.updateFeesFromDetailedType = updateFeesFromDetailedType;
    window.validateForm = validateForm;
    window.calculateRemaining = calculateRemaining;

    $(document).ready(function() {
        // Initial validation and calculation
        validateForm();
        calculateRemaining();

        // ربط أحداث التصنيف والأنواع التفصيلية مباشرة
        var $classification = $('#classification');
        var $detailedType = $('#detailed_craft_type');

        if ($classification.length) {
            $classification.on('change changed.bs.select', function() {
                loadDetailedCraftTypes();
                validateForm();
                calculateRemaining();
            });
        }

        if ($detailedType.length) {
            $detailedType.on('change changed.bs.select', function() {
                updateFeesFromDetailedType();
                validateForm();
                calculateRemaining();
            });
        }

        // بديل احتياطي: ربط حدث change على الـ select المخفي بواسطة bootstrap-select
        // bootstrap-select يخفي الـ original select ويستخدم نسخته الخاصة
        // الحدث change يشتغل على selectpicker المخصص
        $(document).on('change', 'select.selectpicker', function() {
            var $this = $(this);
            if ($this.attr('id') === 'classification') {
                loadDetailedCraftTypes();
                validateForm();
                calculateRemaining();
            } else if ($this.attr('id') === 'detailed_craft_type') {
                updateFeesFromDetailedType();
                validateForm();
                calculateRemaining();
            }
        });

        // ربط الأحداث المالية
        $(document).on('input change', '#craft_fees, #discount_percent, input[name="signboard_fees"], input[name="payment_amount"], input[name="total_debts"]', function() {
            validateForm();
            calculateRemaining();
        });

        // تحميل الأنواع التفصيلية إذا كان التصنيف محدّداً مسبقاً
        const classifEl = document.getElementById('classification');
        if (classifEl && classifEl.value) {
            const currentDetailed = "{{ $license->detailed_craft_type ?? '' }}";
            setTimeout(function() {
                loadDetailedCraftTypes(currentDetailed || "{{ old('detailed_craft_type') }}");
            }, 1000);
        }

        const statusEl = document.getElementById('status');
        if (statusEl) {
            handleStatusChange(statusEl);
        }

        // Search for existing license by file number
        $('#file_number').on('change', function() {
            const fileNumber = $(this).val();
            const form = $(this).closest('form');

            if (fileNumber && fileNumber.length === 6 && form.attr('id') === 'createLicenseForm') {
                $(this).addClass('spinner spinner-right spinner-primary');

                $.ajax({
                    url: "{{ route('craft_licenses.search') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        file_number: fileNumber
                    },
                    success: function(response) {
                        // Populate fields
                        if (response.status) $('#status').val(response.status).trigger('change');
                        if (response.classification) {
                            $('#classification').selectpicker('val', response.classification);
                        }

                        // Populate other inputs by name
                        const fields = [
                            'tax_number', 'last_license', 'holiday_days', 'id_number', 'phone_number',
                            'owner_name', 'property_owner', 'property_owner_id', 'ownership_entity',
                            'first_license_year', 'commercial_name', 'neighborhood',
                            'main_street', 'address', 'nearest_mosque', 'gis_building_number',
                            'street_number', 'floor_number', 'unit_number', 'total_debts',
                            'craft_fees', 'signboard_fees', 'remaining', 'remaining_years',
                            'advertisement_board', 'number_of_stores', 'board_in_meters',
                            'final_price', 'notes', 'licensed_operator', 'detailed_craft_type'
                        ];

                        fields.forEach(field => {
                            if (response[field] !== undefined) {
                                form.find(`[name="${field}"]`).val(response[field]);
                            }
                        });

                        // تفعيل تحميل الأنواع التفصيلية بعد جلب التصنيف
                        if (response.classification) {
                            loadDetailedCraftTypes(response.detailed_craft_type || '');
                        }

                        // Show notice that data was loaded
                        toastr.success('تم جلب بيانات الملف السابق بنجاح');
                    },
                    error: function() {
                        // Reset if not found or other error?
                        // Usually we just let the user continue creating
                    },
                    complete: function() {
                        $('#file_number').removeClass('spinner spinner-right spinner-primary');
                    }
                });
            }
        });

        // Fetch citizen data by ID number (Owner)
        $(document).on('input', 'input[name="id_number"]', function() {
            const czn_id = $(this).val();
            const ownerInput = $('input[name="owner_name"]');

            if (czn_id.length === 9) {
                ownerInput.addClass('spinner spinner-right spinner-primary').prop('placeholder', 'جاري جلب الاسم...');

                $.ajax({
                    url: "{{ route('getCzn') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        czn_id: czn_id
                    },
                    success: function(response) {
                        if (response.FULLNAME) {
                            ownerInput.val(response.FULLNAME);
                        } else if (response.FULL_NAME_AR) {
                            ownerInput.val(response.FULL_NAME_AR);
                        } else {
                            toastr.warning('لم يتم العثور على اسم لهذا الرقم');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching citizen data');
                        toastr.error('فشل الاتصال بخدمة جلب الأسماء');
                    },
                    complete: function() {
                        ownerInput.removeClass('spinner spinner-right spinner-primary').prop('placeholder', '');
                    }
                });
            }
        });

        // Fetch citizen data by ID number (Property Owner)
        $(document).on('input', 'input[name="property_owner_id"]', function() {
            const czn_id = $(this).val();
            const ownerInput = $('input[name="property_owner"]');

            if (czn_id.length === 9) {
                ownerInput.addClass('spinner spinner-right spinner-primary').prop('placeholder', 'جاري جلب الاسم...');

                $.ajax({
                    url: "{{ route('getCzn') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        czn_id: czn_id
                    },
                    success: function(response) {
                        if (response.FULLNAME) {
                            ownerInput.val(response.FULLNAME);
                        } else if (response.FULL_NAME_AR) {
                            ownerInput.val(response.FULL_NAME_AR);
                        } else {
                            toastr.warning('لم يتم العثور على اسم لهذا الرقم');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching property owner data');
                    },
                    complete: function() {
                        ownerInput.removeClass('spinner spinner-right spinner-primary').prop('placeholder', '');
                    }
                });
            }
        });
    });
</script>
