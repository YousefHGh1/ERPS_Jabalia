<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل نظام إدارة البلدية المتكامل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .presentation-container {
            min-height: 100vh;
            position: relative;
        }

        .slide {
            min-height: 100vh;
            display: none;
            padding: 60px 20px;
            position: relative;
            background: white;
            overflow: hidden;
        }

        .slide.active {
            display: flex;
            animation: slideIn 0.6s ease-in-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-content {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            z-index: 2;
            position: relative;
        }

        .slide-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .slide-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--primary-color);
            margin-bottom: 20px;
            position: relative;
        }

        .slide-subtitle {
            font-size: 1.5rem;
            color: var(--text-light);
            font-weight: 400;
        }

        .slide-number {
            position: absolute;
            top: 30px;
            left: 30px;
            font-size: 4rem;
            font-weight: 900;
            color: rgba(59, 130, 246, 0.1);
        }

        .navigation {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: white;
            padding: 20px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Tajawal', sans-serif;
        }

        .nav-btn.primary {
            background: var(--primary-color);
            color: white;
        }

        .nav-btn.primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .nav-btn.secondary {
            background: var(--bg-light);
            color: var(--text-dark);
        }

        .nav-btn.secondary:hover {
            background: var(--border-color);
        }

        .slide-indicators {
            display: flex;
            gap: 10px;
        }

        .indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background: var(--primary-color);
            width: 30px;
            border-radius: 5px;
        }

        .organization-chart {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            margin: 50px 0;
        }

        .org-level {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .org-item {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
            min-width: 200px;
        }

        .org-item:hover {
            transform: translateY(-5px);
        }

        .org-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .org-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .org-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .process-flow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 50px 0;
            position: relative;
        }

        .process-flow::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            z-index: 0;
        }

        .process-step {
            background: white;
            border: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 120px;
            height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 1;
            position: relative;
            transition: all 0.3s ease;
        }

        .process-step:hover {
            transform: scale(1.1);
            background: var(--primary-color);
            color: white;
        }

        .step-number {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .step-text {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 2px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-color);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .feature-description {
            color: var(--text-light);
            line-height: 1.8;
        }

        .workflow-diagram {
            background: var(--bg-light);
            border-radius: 20px;
            padding: 40px;
            margin: 50px 0;
        }

        .workflow-step {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-right: 5px solid var(--primary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .workflow-step:hover {
            transform: translateX(10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .workflow-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .workflow-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .workflow-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .workflow-content {
            color: var(--text-light);
            line-height: 1.8;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 5px;
        }

        .status-waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .financial-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin: 50px 0;
        }

        .financial-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .value-proposition {
            background: linear-gradient(135deg, var(--accent-color), var(--danger-color));
            color: white;
            border-radius: 20px;
            padding: 50px;
            margin: 50px 0;
            text-align: center;
        }

        .value-title {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 30px;
        }

        .value-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .value-item {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            border: 2px solid rgba(255,255,255,0.2);
            transition: transform 0.3s ease;
        }

        .value-item:hover {
            transform: scale(1.05);
        }

        .value-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .value-item-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .value-description {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .highlight-box {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
            font-size: 1.3rem;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .arrow-animation {
            display: inline-block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @media (max-width: 768px) {
            .slide-title {
                font-size: 2.5rem;
            }
            
            .slide-subtitle {
                font-size: 1.2rem;
            }
            
            .process-flow {
                flex-direction: column;
                gap: 20px;
            }
            
            .process-flow::before {
                display: none;
            }
            
            .navigation {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="presentation-container">
        <!-- Slide 1: Title -->
        <div class="slide active" id="slide-1">
            <div class="slide-number">01</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-city"></i> 
                        دليل نظام إدارة البلدية المتكامل
                    </h1>
                    <p class="slide-subtitle">الحل الشامل للإدارة البلدية الحديثة</p>
                </div>
                
                <div class="highlight-box">
                    <i class="fas fa-rocket"></i> 
                    ثورة في الإدارة البلدية من خلال التكنولوجيا
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="feature-title">الإدارة الإدارية</h3>
                        <p class="feature-description">
                            هيكل تنظيمي كامل مع دوائر وأقسام وشعب
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="feature-title">الطلبات المركزية</h3>
                        <p class="feature-description">
                            معالجة تلقائية للطلبات مع توجيه ذكي
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3 class="feature-title">الإدارة المالية</h3>
                        <p class="feature-description">
                            فوترة موحدة ومتابعة شاملة للمدفوعات
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2: Structure Administrative -->
        <div class="slide" id="slide-2">
            <div class="slide-number">02</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-sitemap"></i> 
                        الهيكل الإداري
                    </h1>
                    <p class="slide-subtitle">الهيكل التنظيمي الكامل</p>
                </div>

                <div class="organization-chart">
                    <div class="org-level">
                        <div class="org-item">
                            <div class="org-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="org-title">الدوائر</div>
                            <div class="org-subtitle">الإدارة العليا</div>
                        </div>
                    </div>
                    
                    <div class="arrow-animation">
                        <i class="fas fa-arrow-down fa-3x"></i>
                    </div>
                    
                    <div class="org-level">
                        <div class="org-item">
                            <div class="org-icon">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <div class="org-title">الأقسام</div>
                            <div class="org-subtitle">الأقسام المتخصصة</div>
                        </div>
                    </div>
                    
                    <div class="arrow-animation">
                        <i class="fas fa-arrow-down fa-3x"></i>
                    </div>
                    
                    <div class="org-level">
                        <div class="org-item">
                            <div class="org-icon">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div class="org-title">الشعب</div>
                            <div class="org-subtitle">الوحدات التشغيلية</div>
                        </div>
                    </div>
                    
                    <div class="arrow-animation">
                        <i class="fas fa-arrow-down fa-3x"></i>
                    </div>
                    
                    <div class="org-level">
                        <div class="org-item">
                            <div class="org-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="org-title">الموظفين</div>
                            <div class="org-subtitle">الموظفون التشغيليون</div>
                        </div>
                    </div>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-link"></i> 
                    كل موظف مربوط بالوظيفة والمركز الهرمي
                </div>
            </div>
        </div>

        <!-- Slide 3: Public Office -->
        <div class="slide" id="slide-3">
            <div class="slide-number">03</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-user-tie"></i> 
                        قلم الجمهور
                    </h1>
                    <p class="slide-subtitle">نقطة الدخول الموحدة لجميع المواطنين</p>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="workflow-title">استقبال المواطن</div>
                        </div>
                        <div class="workflow-content">
                            استقبال المواطن وتحديد احتياجاته
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-folder-plus"></i>
                            </div>
                            <div class="workflow-title">إنشاء الملف الموحد</div>
                        </div>
                        <div class="workflow-content">
                            يتم إنشاء ملف كامل لكل مواطن يحتوي على:
                        </div>
                        <ul style="text-align: right; margin-top: 15px;">
                            <li>جميع الطلبات المقدمة</li>
                            <li>جميع الرخص الصادرة</li>
                            <li>ملفات الهندسة والتنظيم</li>
                            <li>الحجوزات والإنذارات</li>
                            <li>أوامر الحبس</li>
                        </ul>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="workflow-title">مركزية البيانات</div>
                        </div>
                        <div class="workflow-content">
                            جميع معلومات المواطن مركزية في ملف واحد
                        </div>
                    </div>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-equals"></i> 
                    1 مواطن = 1 ملف موحد شامل
                </div>
            </div>
        </div>

        <!-- Slide 4: System of Requests -->
        <div class="slide" id="slide-4">
            <div class="slide-number">04</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-clipboard-list"></i> 
                        نظام الطلبات
                    </h1>
                    <p class="slide-subtitle">دورة معالجة الطلبات الكاملة</p>
                </div>

                <div class="process-flow">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-text">الإدخال</div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-text">النوع</div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-text">البيانات</div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <div class="step-text">المستندات</div>
                    </div>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-keyboard"></i>
                            </div>
                            <div class="workflow-title">1. إدخال الطلب</div>
                        </div>
                        <div class="workflow-content">
                            إدخال كامل لمعلومات الطلب في النظام
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="workflow-title">2. نوع الطلب</div>
                        </div>
                        <div class="workflow-content">
                            <span class="status-badge status-waiting">ترخيص حرفة</span>
                            <span class="status-badge status-processing">ترخيص بناء</span>
                            <span class="status-badge status-completed">شكوى</span>
                            <span class="status-badge status-rejected">أخرى</span>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="workflow-title">3. ربط بيانات المواطن</div>
                        </div>
                        <div class="workflow-content">
                            ربط مع ملف المواطن وبيانات الموقع
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="workflow-title">4. المرفقات</div>
                        </div>
                        <div class="workflow-content">
                            رفع المستندات المطلوبة للمعالجة
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 5: Automatic Routing -->
        <div class="slide" id="slide-5">
            <div class="slide-number">05</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-route"></i> 
                        التوجيه التلقائي
                    </h1>
                    <p class="slide-subtitle">التوزيع الذكي للطلبات</p>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="workflow-title">نظام التوجيه الذكي</div>
                        </div>
                        <div class="workflow-content">
                            النظام يحلل تلقائياً نوع الطلب ويوجهه للقسم المختص
                        </div>
                    </div>

                    <div class="highlight-box">
                        <i class="fas fa-exchange-alt"></i> 
                        التوزيع التلقائي بناءً على نوع الطلب
                    </div>

                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-hammer"></i>
                            </div>
                            <h3 class="feature-title">ترخيص حرفة</h3>
                            <p class="feature-description">
                                <i class="fas fa-arrow-left"></i> قسم الحرف والصناعات
                            </p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="feature-title">ترخيص بناء</h3>
                            <p class="feature-description">
                                <i class="fas fa-arrow-left"></i> قسم الهندسة والتنظيم
                            </p>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="workflow-title">الإشعار التلقائي</div>
                        </div>
                        <div class="workflow-content">
                            موظفو القسم المعني يستلمون إشعاراً فورياً
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 6: Approval System -->
        <div class="slide" id="slide-6">
            <div class="slide-number">06</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-check-circle"></i> 
                        نظام الموافقات
                    </h1>
                    <p class="slide-subtitle">متابعة كاملة لحالات الطلب</p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <h3 class="feature-title">
                            <span class="status-badge status-waiting">قيد الانتظار</span>
                        </h3>
                        <p class="feature-description">
                            الطلب تم تقديمه وينتظر المعالجة
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="feature-title">
                            <span class="status-badge status-processing">قيد المعالجة</span>
                        </h3>
                        <p class="feature-description">
                            الطلب قيد المعالجة من قبل الأقسام
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h3 class="feature-title">
                            <span class="status-badge status-completed">مكتمل</span>
                        </h3>
                        <p class="feature-description">
                            الطلب تمت الموافقة عليه ومعالجته بنجاح
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h3 class="feature-title">
                            <span class="status-badge status-rejected">مرفوض</span>
                        </h3>
                        <p class="feature-description">
                            الطلب تم رفضه مع التبرير
                        </p>
                    </div>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="workflow-title">متابعة الردود</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li>ردود كل قسم</li>
                                <li>موافقات بشروط</li>
                                <li>رفوض مع أسباب مفصلة</li>
                                <li>سجل كامل للتعديلات</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 7: Specialized Files -->
        <div class="slide" id="slide-7">
            <div class="slide-number">07</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-folder-open"></i> 
                        الملفات المتخصصة
                    </h1>
                    <p class="slide-subtitle">الإنشاء التلقائي بعد الموافقة</p>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-magic"></i> 
                   بعد الموافقة يتم ارسال اشعار عاجل للموظفين المعنيين لانشاء ملف
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hammer"></i>
                        </div>
                        <h3 class="feature-title">ملف الحرفة</h3>
                        <p class="feature-description">
                            <strong>المحتوى:</strong><br>
                            <i class="fas fa-user"></i> بيانات المالك<br>
                            <i class="fas fa-map"></i> بيانات الموقع<br>
                            <i class="fas fa-tags"></i> نوع النشاط<br>
                            <i class="fas fa-coins"></i> بيانات مالية<br>
                            <i class="fas fa-file-invoice"></i> رقم الملف
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="feature-title">ملف الهندسة</h3>
                        <p class="feature-description">
                            <strong>المحتوى:</strong><br>
                            <i class="fas fa-user"></i> بيانات المالك<br>
                            <i class="fas fa-map"></i> بيانات الموقع<br>
                            <i class="fas fa-ruler-combined"></i> مواصفات فنية<br>
                            <i class="fas fa-drafting-compass"></i> مخططات معتمدة<br>
                            <i class="fas fa-certificate"></i> رخص صادرة
                        </p>
                    </div>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="workflow-title">الربط بالطلب الأصلي</div>
                        </div>
                        <div class="workflow-content">
                            كل ملف متخصص يبقى مرتبطاً بالطلب الأصلي للمتابعة الكاملة
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 8: Financial System -->
        <div class="slide" id="slide-8">
            <div class="slide-number">08</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-coins"></i> 
                        النظام المالي
                    </h1>
                    <p class="slide-subtitle">إدارة شاملة للمدفوعات والرزم</p>
                </div>

                <div class="financial-section">
                    <div class="financial-card">
                        <h3 style="font-size: 1.5rem; margin-bottom: 20px;">
                            <i class="fas fa-play-circle"></i> عند أول دفعة
                        </h3>
                        <ul style="font-size: 1.1rem; line-height: 2;">
                            <li>رزمة تفتح تلقائياً في الصندوق المركزي</li>
                            <li>الدفعة تسجل تحت القسم المحصل</li>
                            <li>رقم تسلسلي فريد يعطى</li>
                        </ul>
                    </div>

                    <div class="financial-card">
                        <h3 style="font-size: 1.5rem; margin-bottom: 20px;">
                            <i class="fas fa-layer-group"></i> الرزم المالية
                        </h3>
                        <ul style="font-size: 1.1rem; line-height: 2;">
                            <li><strong>رزمة موحدة</strong> - لجميع الأقسام</li>
                            <li><strong>رزم الأقسام</strong> - واحدة لكل قسم</li>
                            <li><strong>متابعة يومية</strong> - حسب تاريخ الرزمة</li>
                        </ul>
                    </div>

                    <div class="financial-card">
                        <h3 style="font-size: 1.5rem; margin-bottom: 20px;">
                            <i class="fas fa-chart-line"></i> الأهمية للإدارة
                        </h3>
                        <ul style="font-size: 1.1rem; line-height: 2;">
                            <li>معرفة دقيقة للتحصيل لكل قسم</li>
                            <li>تقارير مالية دقيقة وفي الوقت الفعلي</li>
                            <li>رقابة كاملة على الحركات المالية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 9: Control and Audit -->
        <div class="slide" id="slide-9">
            <div class="slide-number">09</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-shield-alt"></i> 
                        الرقابة والتدقيق
                    </h1>
                    <p class="slide-subtitle">الشفافية الكاملة للعمليات</p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <h3 class="feature-title">الحركات المالية</h3>
                        <p class="feature-description">
                            <ul style="text-align: right;">
                                <li><i class="fas fa-plus-circle text-success"></i> دفع</li>
                                <li><i class="fas fa-times-circle text-danger"></i> إلغاء وصل</li>
                                <li><i class="fas fa-edit text-warning"></i> تعديل مبلغ</li>
                                <li><i class="fas fa-redo text-info"></i> استرداد</li>
                            </ul>
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="feature-title">الحركات غير المالية</h3>
                        <p class="feature-description">
                            <ul style="text-align: right;">
                                <li><i class="fas fa-edit"></i> تعديل بيانات</li>
                                <li><i class="fas fa-trash"></i> حذف</li>
                                <li><i class="fas fa-exchange-alt"></i> تغيير حالة</li>
                                <li><i class="fas fa-history"></i> سجل كامل</li>
                            </ul>
                        </p>
                    </div>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-eye"></i> 
                    الشفافية الكاملة - جميع العمليات مسجلة وقابلة للتتبع
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="workflow-title">الأمان والتتبع</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li>سجل تدقيق كامل لجميع العمليات</li>
                                <li>تحديد المستخدم لكل إجراء</li>
                                <li>طابع زمني دقيق لكل تعديل</li>
                                <li>أرشفة تلقائية للبيانات التاريخية</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 10: Unified Invoice -->
        <div class="slide" id="slide-10">
            <div class="slide-number">10</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-file-invoice-dollar"></i> 
                        الفاتورة الموحدة
                    </h1>
                    <p class="slide-subtitle">تجميع جميع رسوم البلدية</p>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-compress-alt"></i>
                            </div>
                            <div class="workflow-title">تجميع جميع الرسوم</div>
                        </div>
                        <div class="workflow-content">
                            النظام يجمع تلقائياً جميع الرسوم من جميع أقسام البلدية
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="workflow-title">إصدار فاتورة واحدة</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li><strong>إجمالي المستحقات</strong> - مجموع كل الرسوم</li>
                                <li><strong>المدفوعات</strong> - سجل كامل للدفعات</li>
                                <li><strong>المتبقي</strong> - المبلغ المتبقي للدفع</li>
                                <li><strong>تفصيل حسب القسم</strong> - تفصيل كامل</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-database"></i> 
                    قاعدة بيانات موحدة - فاتورة واحدة لكل البلدية
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="feature-title">للمواطن</h3>
                        <p class="feature-description">
                            فاتورة واحدة لجميع التزاماته البلدية
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-city"></i>
                        </div>
                        <h3 class="feature-title">للبلدية</h3>
                        <p class="feature-description">
                            رؤية كاملة وموحدة لجميع المعاملات
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 11: Notifications -->
        <div class="slide" id="slide-11">
            <div class="slide-number">11</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-bell"></i> 
                        نظام الإشعارات
                    </h1>
                    <p class="slide-subtitle">تواصل فوري وفعال</p>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="workflow-title">الإشعارات التلقائية</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li><i class="fas fa-plus-circle text-success"></i> إنشاء طلب جديد</li>
                                <li><i class="fas fa-check-circle text-info"></i> موافقة على طلب</li>
                                <li><i class="fas fa-exclamation-triangle text-warning"></i> طلبات عاجلة</li>
                                <li><i class="fas fa-clock text-danger"></i> مواعيد استحقاق قريبة</li>
                            </ul>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="workflow-title">الاستهداف الذكي</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li>موظفو القسم المعني</li>
                                <li>المسؤولون الهرميون</li>
                                <li>مديرو النظام</li>
                                <li>تخصيص حسب الدور</li>
                            </ul>
                        </div>
                    </div>

                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="workflow-title">الوصول المباشر</div>
                        </div>
                        <div class="workflow-content">
                            كل إشعار يتضمن رابطاً مباشراً للطلب أو الوثيقة المعنية
                        </div>
                    </div>
                </div>

                <div class="highlight-box">
                    <i class="fas fa-tachometer-alt"></i> 
                    تحسين سرعة الاستجابة والكفاءة التشغيلية
                </div>
            </div>
        </div>

        <!-- Slide 12: Reports -->
        <div class="slide" id="slide-12">
            <div class="slide-number">12</div>
            <div class="slide-content">
                <div class="slide-header">
                    <h1 class="slide-title">
                        <i class="fas fa-chart-bar"></i> 
                        التقارير والتحليلات
                    </h1>
                    <p class="slide-subtitle">لوحة تحكم شاملة لاتخاذ القرار</p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3 class="feature-title">التقارير المالية</h3>
                        <p class="feature-description">
                            <ul style="text-align: right;">
                                <li>التحصيل اليومي حسب القسم</li>
                                <li>متابعة الرزم المالية</li>
                                <li>تحليل اتجاهات الدفع</li>
                                <li>توقعات مالية</li>
                            </ul>
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="feature-title">التقارير الإدارية</h3>
                        <p class="feature-description">
                            <ul style="text-align: right;">
                                <li>توزيع الموظفين حسب القسم</li>
                                <li>عبء العمل حسب القسم</li>
                                <li>أداء الموظفين</li>
                                <li>هيكل تنظيمي ديناميكي</li>
                            </ul>
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="feature-title">التقارير التشغيلية</h3>
                        <p class="feature-description">
                            <ul style="text-align: right;">
                                <li>حجم الطلبات حسب النوع</li>
                                <li>متوسط وقت المعالجة</li>
                                <li>معدلات الموافقة</li>
                                <li>تحديد نقاط الاختناق</li>
                            </ul>
                        </p>
                    </div>
                </div>

                <div class="workflow-diagram">
                    <div class="workflow-step">
                        <div class="workflow-header">
                            <div class="workflow-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="workflow-title">التصدير والمشاركة</div>
                        </div>
                        <div class="workflow-content">
                            <ul style="text-align: right;">
                                <li>تصدير PDF, Excel, Word</li>
                                <li>جدولة تقارير تلقائية</li>
                                <li>لوحات تحكم تفاعلية</li>
                                <li>وصول محلي للتقارير</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 13: Value Proposition -->
        <div class="slide" id="slide-13">
            <div class="slide-number">13</div>
            <div class="slide-content">
                <div class="value-proposition">
                    <h1 class="value-title">
                        <i class="fas fa-star"></i> 
                        قيمة النظام للبلدية
                    </h1>
                    
                    <div class="value-grid">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3 class="value-item-title">تحسين الخدمة</h3>
                            <p class="value-description">
                                معالجة سريعة وفعالة لطلبات المواطنين
                            </p>
                        </div>
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h3 class="value-item-title">تسريع الإجراءات</h3>
                            <p class="value-description">
                                أتمتة كاملة لسير عمل المعالجة
                            </p>
                        </div>
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3 class="value-item-title">زيادة الشفافية</h3>
                            <p class="value-description">
                                تتبع كامل لجميع العمليات
                            </p>
                        </div>
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h3 class="value-item-title">توحيد البيانات</h3>
                            <p class="value-description">
                                قاعدة بيانات موحدة ومركزية لكل البلدية
                            </p>
                        </div>
                    </div>

                    <div style="margin-top: 50px; font-size: 1.5rem; font-weight: 700;">
                        <i class="fas fa-trophy"></i> 
                        التحول الرقمي الكامل للإدارة البلدية
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="navigation">
        <button class="nav-btn secondary" onclick="previousSlide()">
            <i class="fas fa-chevron-right"></i> السابق
        </button>
        
        <div class="slide-indicators">
            <div class="indicator active" onclick="goToSlide(1)"></div>
            <div class="indicator" onclick="goToSlide(2)"></div>
            <div class="indicator" onclick="goToSlide(3)"></div>
            <div class="indicator" onclick="goToSlide(4)"></div>
            <div class="indicator" onclick="goToSlide(5)"></div>
            <div class="indicator" onclick="goToSlide(6)"></div>
            <div class="indicator" onclick="goToSlide(7)"></div>
            <div class="indicator" onclick="goToSlide(8)"></div>
            <div class="indicator" onclick="goToSlide(9)"></div>
            <div class="indicator" onclick="goToSlide(10)"></div>
            <div class="indicator" onclick="goToSlide(11)"></div>
            <div class="indicator" onclick="goToSlide(12)"></div>
            <div class="indicator" onclick="goToSlide(13)"></div>
        </div>
        
        <button class="nav-btn secondary" onclick="nextSlide()">
            التالي <i class="fas fa-chevron-left"></i>
        </button>
        
        <button class="nav-btn primary" onclick="toggleFullscreen()">
            <i class="fas fa-expand"></i> ملء الشاشة
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSlide = 1;
        const totalSlides = 13;

        function showSlide(slideNumber) {
            // Hide all slides
            document.querySelectorAll('.slide').forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Show current slide
            const currentSlideElement = document.getElementById(`slide-${slideNumber}`);
            if (currentSlideElement) {
                currentSlideElement.classList.add('active');
            }
            
            // Update indicators
            document.querySelectorAll('.indicator').forEach((indicator, index) => {
                indicator.classList.toggle('active', index === slideNumber - 1);
            });
            
            currentSlide = slideNumber;
        }

        function nextSlide() {
            if (currentSlide < totalSlides) {
                showSlide(currentSlide + 1);
            }
        }

        function previousSlide() {
            if (currentSlide > 1) {
                showSlide(currentSlide - 1);
            }
        }

        function goToSlide(slideNumber) {
            showSlide(slideNumber);
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            switch(event.key) {
                case 'ArrowRight':
                    previousSlide();
                    break;
                case 'ArrowLeft':
                    nextSlide();
                    break;
                case 'Home':
                    goToSlide(1);
                    break;
                case 'End':
                    goToSlide(totalSlides);
                    break;
                case 'F11':
                    event.preventDefault();
                    toggleFullscreen();
                    break;
            }
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', function(event) {
            touchStartX = event.changedTouches[0].screenX;
        });

        document.addEventListener('touchend', function(event) {
            touchEndX = event.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            if (touchEndX < touchStartX - 50) {
                nextSlide();
            }
            if (touchEndX > touchStartX + 50) {
                previousSlide();
            }
        }

        // Auto-advance (optional - comment out if not wanted)
        // setInterval(nextSlide, 30000); // Advance every 30 seconds
    </script>
</body>
</html>
