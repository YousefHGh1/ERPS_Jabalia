<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - بلدية جباليا النزلة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif !important;
            box-sizing: border-box
        }

        :root {
            --blue: #3b9dff;
            --ink: #101828;
            --card: #fff;
            --page: #f4f6fb;
            --input: #f8fafc;
            --line: #e6eaf1
        }

        html.dark {
            --ink: #e8ecf4;
            --card: #151b33;
            --page: #0b1026;
            --input: #101736;
            --line: #263056
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--page);
            color: var(--ink)
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: stretch
        }

        .brand {
            flex: 1.15;
            background: linear-gradient(160deg, #0b1026 0%, #141a3d 55%, #241a52 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 44px 46px
        }

        .brand-inner {
            position: relative;
            z-index: 2;
            max-width: 560px;
            margin-inline-start: auto;
            width: 100%;
            text-align: right
        }

        .brand-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px
        }

        .brand-logo img {
            height: 62px;
            background: linear-gradient(160deg, #3b4677 0%, #141a3d 55%, #f8f8f8 100%);
            border-radius: 14px;
            padding: 6px 12px
        }

        .tbtn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .tbtn svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .brand .tbtn {
            border: 1px solid rgba(255, 255, 255, .22);
            background: rgba(255, 255, 255, .08);
            color: #fff
        }

        .sys-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .16);
            padding: 8px 18px;
            border-radius: 999px;
            font-size: .85rem;
            color: #d6dbea;
            margin-bottom: 22px
        }

        .sys-badge .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #22e0a1
        }

        .brand h1 {
            margin: 0 0 14px;
            font-size: 2.2rem;
            line-height: 1.55;
            font-weight: 900;
            text-align: right
        }

        .brand h1 .hl {
            color: var(--blue)
        }

        .brand p.desc {
            margin: 0 0 30px;
            color: #aeb6d3;
            line-height: 2;
            font-size: .97rem;
            text-align: right
        }

        .feat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 24px
        }

        .feat {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 14px;
            padding: 15px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-weight: 700;
            font-size: .92rem
        }

        .feat svg {
            width: 22px;
            height: 22px;
            stroke: var(--blue);
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0
        }

        .brand-foot {
            color: #8b93b5;
            font-size: .82rem;
            text-align: center;
            margin-top: auto;
            position: relative;
            z-index: 2
        }

        .form-side {
            flex: 1;
            background: var(--page);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative
        }

        .form-side .tbtn {
            position: absolute;
            top: 22px;
            left: 22px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--ink)
        }

        .card {
            background: var(--card);
            width: 100%;
            max-width: 480px;
            border-radius: 22px;
            padding: 42px 40px 26px;
            box-shadow: 0 24px 60px rgba(16, 24, 40, .10);
            border: 1px solid var(--line);
            text-align: right
        }

        .card h2 {
            margin: 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800
        }

        .card .sub {
            text-align: center;
            color: #98a2b3;
            margin: 8px 0 28px;
            font-size: .93rem
        }

        label.lbl {
            display: block;
            text-align: right;
            font-weight: 700;
            margin: 0 0 8px;
            font-size: .92rem
        }

        .field {
            position: relative;
            margin-bottom: 20px
        }

        .field input {
            width: 100%;
            border: 1.5px solid var(--line);
            background: var(--input);
            border-radius: 12px;
            padding: 13px 46px;
            font-size: .95rem;
            color: var(--ink);
            text-align: right
        }

        .field input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(59, 157, 255, .15);
            outline: none
        }

        .fic {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            color: #9aa5bb;
            display: flex
        }

        .fic svg,
        .eye svg {
            width: 19px;
            height: 19px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .eye {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9aa5bb;
            cursor: pointer;
            display: flex;
            padding: 4px
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 2px 0 22px;
            font-size: .88rem
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600
        }

        .remember input {
            width: 17px;
            height: 17px;
            accent-color: var(--blue)
        }

        .forgot {
            color: var(--blue);
            text-decoration: none;
            font-weight: 700
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 14px;
            background: linear-gradient(135deg, #3b9dff, #5db4ff);
            color: #fff;
            font-size: 1.03rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px
        }

        .btn svg {
            width: 20px;
            height: 20px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round
        }

        .btn:disabled {
            opacity: .7
        }

        .err {
            background: #fef3f2;
            border: 1px solid #fecdca;
            color: #b42318;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: .85rem;
            margin-bottom: 16px;
            text-align: right
        }

        .field-err {
            color: #d92d20;
            font-size: .8rem;
            margin: -12px 0 14px;
            text-align: right
        }

        .copy {
            text-align: center;
            color: #98a2b3;
            font-size: .8rem;
            margin-top: 24px
        }

        html.dark .sun-i {
            display: none
        }

        html:not(.dark) .moon-i {
            display: none
        }

        @media(max-width:992px) {
            .login-page {
                flex-direction: column
            }

            .brand {
                padding: 30px 22px
            }

            .brand-inner {
                margin: 0 auto
            }

            .brand h1 {
                font-size: 1.5rem
            }

            .card {
                padding: 32px 24px
            }
        }

        @media(max-width:576px) {
            .feat-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="brand">
            <div class="brand-inner">
                <div class="brand-top">
                    <div class="brand-logo"><img src="{{ asset('assets/bg/logo-light.png') }}" alt="شعار البلدية"
                            onerror="this.style.display='none'"></div>
                    <button type="button" class="tbtn" onclick="toggleTheme()" aria-label="تبديل المظهر">
                        <svg class="sun-i" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="4" />
                            <path
                                d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                        </svg>
                        <svg class="moon-i" viewBox="0 0 24 24">
                            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
                        </svg>
                    </button>
                </div>
                <div><span class="sys-badge"><span class="dot"></span> النظام الإداري الموحد</span></div>
                <h1><span class="hl">نظام بلدية جباليا النزلة</span><br>للإدارة والخدمات البلدية</h1>
                <p class="desc">منصة موحدة لإدارة الشؤون الإدارية والموظفين، والصندوق المركزي والتحصيل، وقلم الجمهور
                    والطلبات، والهندسة والحرف والشؤون القانونية.</p>
                <div class="feat-grid">
                    <div class="feat"><span>الشؤون الإدارية</span><svg viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2" />
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                        </svg></div>
                    <div class="feat"><span>الصندوق المركزي</span><svg viewBox="0 0 24 24">
                            <rect x="2" y="5" width="20" height="14" rx="2" />
                            <path d="M2 10h20M6 15h4" />
                        </svg></div>
                    <div class="feat"><span>قلم الجمهور</span><svg viewBox="0 0 24 24">
                            <rect x="8" y="2" width="8" height="4" rx="1" />
                            <path
                                d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M9 12h6M9 16h4" />
                        </svg></div>
                    <div class="feat"><span>الهندسة والحرف</span><svg viewBox="0 0 24 24">
                            <circle cx="6" cy="6" r="3" />
                            <circle cx="6" cy="18" r="3" />
                            <path d="M8.5 8.5 20 20M9 6h11M6 9v11" />
                        </svg></div>
                </div>
            </div>
            <div class="brand-foot">© {{ date('Y') }} بلدية جباليا النزلة — جميع الحقوق محفوظة</div>
        </div>
        <div class="form-side">
            <button type="button" class="tbtn" onclick="toggleTheme()" aria-label="تبديل المظهر">
                <svg class="sun-i" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4" />
                    <path
                        d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                </svg>
                <svg class="moon-i" viewBox="0 0 24 24">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
                </svg>
            </button>
            <div class="card">
                <h2>مرحباً بعودتك 👋</h2>
                <p class="sub">سجل الدخول للمتابعة إلى لوحة التحكم</p>
                @if ($errors->any())
                    <div class="err">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <label class="lbl" for="employee_number">اسم المستخدم</label>
                    <div class="field">
                        <input id="employee_number" type="text" name="employee_number"
                            value="{{ old('employee_number') }}" required autofocus autocomplete="username"
                            placeholder="أدخل اسم المستخدم">
                        <span class="fic"><svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg></span>
                    </div>
                    @error('employee_number')
                        <div class="field-err">{{ $message }}</div>
                    @enderror
                    <label class="lbl" for="password">كلمة المرور</label>
                    <div class="field">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••">
                        <span class="fic"><svg viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg></span>
                        <button type="button" class="eye" id="pwToggle" aria-label="إظهار كلمة المرور"><svg
                                viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg></button>
                    </div>
                    @error('password')
                        <div class="field-err">{{ $message }}</div>
                    @enderror
                    <div class="form-row">
                        <label class="remember"><input type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}> تذكرني</label>
                        @if (Route::has('password.request'))
                            <a class="forgot" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                        @endif
                    </div>
                    <button type="submit" class="btn" id="loginBtn"><svg viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" />
                        </svg> تسجيل الدخول</button>
                </form>
                <p class="copy">بلدية جباليا النزلة · {{ date('Y') }}</p>
            </div>
        </div>
    </div>
    <script>
        (function() {
            // نفس مفاتيح لوحة التحكم حتى يبقى المظهر موحّداً بين صفحة الدخول والنظام
            var KEYS = ['theme', 'municipality_dashboard_theme', 'login-theme'];

            function readTheme() {
                try {
                    for (var i = 0; i < KEYS.length; i++) {
                        var v = localStorage.getItem(KEYS[i]);
                        if (v === 'dark' || v === 'light') {
                            return v;
                        }
                    }
                } catch (e) {}
                return null;
            }

            function saveTheme(mode) {
                try {
                    for (var i = 0; i < KEYS.length; i++) {
                        localStorage.setItem(KEYS[i], mode);
                    }
                } catch (e) {}
            }

            window.toggleTheme = function() {
                var h = document.documentElement;
                h.classList.toggle('dark');
                saveTheme(h.classList.contains('dark') ? 'dark' : 'light');
            };

            var saved = readTheme();
            if (saved === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (saved === 'light') {
                document.documentElement.classList.remove('dark');
            }
        })();

        document.getElementById('pwToggle').addEventListener('click', function() {
            var p = document.getElementById('password');
            p.type = (p.type === 'password') ? 'text' : 'password';
        });
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var b = document.getElementById('loginBtn');
            if (b.disabled) {
                e.preventDefault();
                return false;
            }
            b.disabled = true;
            setTimeout(function() {
                b.disabled = false;
            }, 30000);
        });
    </script>
</body>

</html>
