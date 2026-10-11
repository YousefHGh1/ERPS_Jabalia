<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa; }
        .error-card { max-width: 420px; }
        .error-code { font-size: 5rem; font-weight: 700; color: #6c757d; line-height: 1; }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="error-card mx-auto p-4">
            <div class="error-code">503</div>
            <h1 class="h4 mt-3 mb-2">{{ __('Service Unavailable') }}</h1>
            <p class="text-muted mb-4">{{ __('We are currently performing maintenance. Please try again in a few minutes.') }}</p>
            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('home') }}</a>
        </div>
    </div>
</body>
</html>
