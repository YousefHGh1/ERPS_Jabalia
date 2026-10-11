<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('login') }} - بلدية جباليا النزلة</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdL0l9n3+e6HAKqGvQExM5L0G4dXWqCJq4I0B/vMx7F2G6JhNF6U3F4z7gVgDw5IqR4e+K5K3A3L7K4M0N" crossorigin="anonymous">
    
    <style>
        * {
            font-family: 'Cairo', sans-serif !important;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #3699FF;
            --secondary-color: #1BC5BD;
            --accent-color: #FFA800;
            --dark-bg: #1a1a27;
            --light-bg: #ffffff;
            --text-primary: #181C32;
            --text-secondary: #5E6278;
        }
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            transition: background 0.5s ease;
        }
        
        /* Dark mode */
        body.dark-mode {
            background: linear-gradient(135deg, #1a1a27 0%, #252536 50%, #1e1e2d 100%);
        }
        
        /* Animated gradient orbs */
        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: orbFloat 20s ease-in-out infinite;
        }
        
        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(54, 153, 255, 0.4);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }
        
        .orb-2 {
            width: 350px;
            height: 350px;
            background: rgba(27, 197, 189, 0.4);
            bottom: -100px;
            right: -100px;
            animation-delay: 5s;
        }
        
        .orb-3 {
            width: 300px;
            height: 300px;
            background: rgba(255, 168, 0, 0.3);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 10s;
        }
        
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(20px, 30px) scale(1.05); }
        }
        
        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            animation: particleFloat 25s ease-in-out infinite;
        }
        
        @keyframes particleFloat {
            0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.3; }
            25% { transform: translateY(-50px) translateX(20px) rotate(90deg); opacity: 0.5; }
            50% { transform: translateY(-100px) translateX(-20px) rotate(180deg); opacity: 0.4; }
            75% { transform: translateY(-50px) translateX(30px) rotate(270deg); opacity: 0.6; }
        }
        
        .particle:nth-child(1) { width: 15px; height: 15px; left: 10%; top: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 20px; height: 20px; left: 20%; top: 60%; animation-delay: 3s; }
        .particle:nth-child(3) { width: 12px; height: 12px; left: 60%; top: 40%; animation-delay: 6s; }
        .particle:nth-child(4) { width: 18px; height: 18px; left: 80%; top: 70%; animation-delay: 9s; }
        .particle:nth-child(5) { width: 14px; height: 14px; left: 40%; top: 80%; animation-delay: 12s; }
        .particle:nth-child(6) { width: 16px; height: 16px; left: 70%; top: 30%; animation-delay: 15s; }
        .particle:nth-child(7) { width: 10px; height: 10px; left: 30%; top: 50%; animation-delay: 18s; }
        .particle:nth-child(8) { width: 22px; height: 22px; left: 50%; top: 15%; animation-delay: 21s; }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .gradient-orb,
            .particle {
                animation: none;
            }
            
            .login-card,
            .logo-container img,
            .municipality-info {
                animation: none;
            }
        }
        
        /* Glass card effect */
        .login-container {
            position: relative;
            z-index: 10;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.8s ease-out;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));
        }
        
        .login-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: cardShine 8s ease-in-out infinite;
        }
        
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes cardShine {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(10px, 10px); }
        }
        
        /* Dark mode card */
        body.dark-mode .login-card {
            background: rgba(37, 37, 54, 0.85);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Logo and branding */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
            animation: logoEntrance 1s ease-out 0.2s both;
        }
        
        @keyframes logoEntrance {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .logo-container img {
            max-height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }
        
        .logo-container img:hover {
            transform: scale(1.05);
        }
        
        .municipality-info {
            animation: textEntrance 1s ease-out 0.4s both;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }
        
        @keyframes textEntrance {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .municipality-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .municipality-name {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }
        
        .system-name {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }
        
        .welcome-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            margin: 0;
        }
        
        body.dark-mode .municipality-name,
        body.dark-mode .system-name,
        body.dark-mode .welcome-text {
            color: #e4e6ef;
        }
        
        /* Form styling */
        .login-form {
            position: relative;
            z-index: 1;
        }
        
        .form-title {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        body.dark-mode .form-title {
            color: #e4e6ef;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
            transition: color 0.3s ease;
        }
        
        body.dark-mode .form-label {
            color: #e4e6ef;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
            z-index: 2;
            transition: color 0.3s ease;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .input-icon svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        
        [dir="rtl"] .input-icon {
            right: auto;
            left: 1rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.875rem 3rem 0.875rem 1rem;
            font-size: 0.95rem;
            color: #333;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        [dir="rtl"] .form-control {
            padding: 0.875rem 1rem 0.875rem 3rem;
        }
        
        .form-control:focus {
            background: white;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(54, 153, 255, 0.15);
            outline: none;
        }
        
        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }
        
        .form-control::placeholder {
            color: #999;
        }
        
        .form-control.is-invalid {
            border-color: #F64E60;
            background-color: #fff5f5;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Dark mode form controls */
        body.dark-mode .form-control {
            background: rgba(43, 43, 64, 0.9);
            border-color: rgba(255, 255, 255, 0.1);
            color: #e4e6ef;
        }
        
        body.dark-mode .form-control:focus {
            background: rgba(50, 50, 72, 0.95);
            border-color: var(--primary-color);
        }
        
        body.dark-mode .form-control::placeholder {
            color: #a2a5b8;
        }
        
        body.dark-mode .form-control.is-invalid {
            background: rgba(246, 78, 96, 0.1);
            border-color: #F64E60;
        }
        
        body.dark-mode .input-icon {
            color: #a2a5b8;
        }
        
        body.dark-mode .form-control:focus + .input-icon {
            color: var(--primary-color);
        }
        
        /* Password toggle */
        .password-toggle {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            padding: 0.5rem;
            font-size: 0.9rem;
            z-index: 2;
            transition: color 0.3s ease;
        }
        
        [dir="rtl"] .password-toggle {
            left: auto;
            right: 1rem;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        body.dark-mode .password-toggle {
            color: #a2a5b8;
        }
        
        body.dark-mode .password-toggle:hover {
            color: var(--primary-color);
        }
        
        /* Validation feedback */
        .invalid-feedback {
            color: #fff;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            display: none;
        }
        
        .form-control.is-invalid ~ .invalid-feedback {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        body.dark-mode .invalid-feedback {
            color: #F64E60;
            text-shadow: none;
        }
        
        /* Remember me and forgot password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .remember-me input[type="checkbox"] {
            margin-left: 0.5rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        [dir="rtl"] .remember-me input[type="checkbox"] {
            margin-left: 0;
            margin-right: 0.5rem;
        }
        
        body.dark-mode .remember-me {
            color: #e4e6ef;
        }
        
        .forgot-password {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: white;
            text-decoration: underline;
        }
        
        body.dark-mode .forgot-password {
            color: #e4e6ef;
        }
        
        body.dark-mode .forgot-password:hover {
            color: white;
        }
        
        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(54, 153, 255, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(54, 153, 255, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-login .spinner {
            display: none;
        }
        
        .btn-login.loading .spinner {
            display: inline-block;
        }
        
        .btn-login.loading .btn-text {
            opacity: 0.7;
        }
        
        .btn-login .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            border-width: 2px;
        }
        
        /* Dark mode button */
        body.dark-mode .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        /* Footer text */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }
        
        body.dark-mode .login-footer {
            color: #a2a5b8;
        }
        
        /* Theme toggle */
        .theme-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
            color: white;
        }
        
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        body.dark-mode .theme-toggle {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem;
            }
            
            .logo-container img {
                max-height: 60px;
            }
            
            .municipality-header {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .municipality-name {
                font-size: 1.3rem;
            }
            
            .system-name {
                font-size: 0.95rem;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .gradient-orb {
                width: 200px;
                height: 200px;
            }
            
            .particle {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
            }
            
            .municipality-name {
                font-size: 1.3rem;
            }
            
            .btn-login {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }
        
        /* Accessibility improvements */
        .btn-login:focus-visible,
        .form-control:focus-visible,
        .password-toggle:focus-visible,
        .theme-toggle:focus-visible {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        .btn-login:focus:not(:focus-visible),
        .form-control:focus:not(:focus-visible) {
            outline: none;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .login-card {
                background: rgba(255, 255, 255, 0.9);
                border: 2px solid white;
            }
            
            .form-control {
                border-width: 2px;
            }
        }
    </style>
</head>

<body>
    <!-- Animated gradient orbs -->
    <div class="gradient-orb orb-1"></div>
    <div class="gradient-orb orb-2"></div>
    <div class="gradient-orb orb-3"></div>
    
    <!-- Floating particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    
    <!-- Theme toggle -->
    <button type="button" class="theme-toggle" id="themeToggle" aria-label="تبديل الوضع الليلي">
        <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
        <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
    </button>
    
    <div class="login-container">
        <div class="login-card">
            <!-- Logo and branding -->
            <div class="logo-section">
                <div class="logo-container">
                    <img src="{{ asset('assets/bg/logo-light.png') }}" alt="شعار بلدية جباليا النزلة">
                </div>
                <div class="municipality-info">
                    <!-- <h1 class="municipality-name">بلدية جباليا النزلة</h1>
                    <p class="system-name">نظام الإدارة الإلكترونية</p> -->
                    <p class="welcome-text">مرحباً بك في بوابة الموظفين</p>
                </div>
            </div>
            
            <!-- Login form -->
            <form class="login-form" method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                
                <!-- Employee number -->
                <div class="form-group">
                    <label for="employee_number" class="form-label">رقم الموظف</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input 
                            id="employee_number" 
                            type="text" 
                            name="employee_number" 
                            value="{{ old('employee_number') }}" 
                            required 
                            autofocus 
                            placeholder="أدخل رقم الموظف" 
                            autocomplete="username"
                            class="form-control @error('employee_number') is-invalid @enderror"
                            aria-describedby="employee_number_error"
                        >
                        <div class="invalid-feedback" id="employee_number_error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; vertical-align: middle; margin-left: 4px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            {{ $errors->first('employee_number') }}
                        </div>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="أدخل كلمة المرور" 
                            autocomplete="current-password"
                            class="form-control @error('password') is-invalid @enderror"
                            aria-describedby="password_error"
                        >
                        <button 
                            type="button" 
                            class="password-toggle" 
                            id="passwordToggle"
                            aria-label="إظهار/إخفاء كلمة المرور"
                        >
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                        <div class="invalid-feedback" id="password_error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; vertical-align: middle; margin-left: 4px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            {{ $errors->first('password') }}
                        </div>
                    </div>
                </div>
                
                <!-- Remember me and forgot password -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>تذكرني</span>
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="btn btn-login" id="loginButton">
                    <span class="spinner">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        تسجيل الدخول
                    </span>
                </button>
            </form>
            
            <!-- Footer -->
            <div class="login-footer">
                <p>© {{ date('Y') }} بلدية جباليا النزلة - جميع الحقوق محفوظة</p>
            </div>
        </div>
    </div>
    
    <script>
        // Password toggle functionality
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeOffIcon = this.querySelector('.eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
                this.setAttribute('aria-label', 'إخفاء كلمة المرور');
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
                this.setAttribute('aria-label', 'إظهار كلمة المرور');
            }
        });
        
        // Form submission handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            const form = this;
            
            // Prevent double submission
            if (button.disabled) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            button.disabled = true;
            button.classList.add('loading');
            
            // Re-enable after 30 seconds (timeout fallback)
            setTimeout(function() {
                button.disabled = false;
                button.classList.remove('loading');
            }, 30000);
        });
        
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        const moonIcon = themeToggle.querySelector('.moon-icon');
        const sunIcon = themeToggle.querySelector('.sun-icon');
        
        // Check for saved theme preference or system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            body.classList.add('dark-mode');
            moonIcon.style.display = 'none';
            sunIcon.style.display = 'block';
        }
        
        themeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                moonIcon.style.display = 'none';
                sunIcon.style.display = 'block';
                this.setAttribute('aria-label', 'تفعيل الوضع الفاتح');
            } else {
                localStorage.setItem('theme', 'light');
                moonIcon.style.display = 'block';
                sunIcon.style.display = 'none';
                this.setAttribute('aria-label', 'تفعيل الوضع الليلي');
            }
        });
        
        // Remove loading state if there are validation errors
        @if($errors->any())
            document.getElementById('loginButton').disabled = false;
            document.getElementById('loginButton').classList.remove('loading');
        @endif
        
        // Focus management for accessibility
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>

</html>
