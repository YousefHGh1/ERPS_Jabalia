@props([
    'title' => 'بلدية جباليا النزلة',
    'subtitle' => 'نظام إدارة الشؤون الإدارية',
    'showDate' => true,
    'showWeather' => false,
])

<div class="municipal-banner">
    <div class="banner-content">
        <div class="banner-logo">
            <img src="{{ asset('assets/bg/logo-light.png') }}" alt="{{ $title }}">
        </div>
        <div class="banner-text">
            <h1 class="banner-title">{{ $title }}</h1>
            <p class="banner-subtitle">{{ $subtitle }}</p>
            @if($showDate)
                <div class="banner-date">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    @php
                        try {
                            echo \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، d F Y');
                        } catch (\Exception $e) {
                            echo date('Y-m-d');
                        }
                    @endphp
                </div>
            @endif
        </div>
        @if($showWeather)
            <div class="banner-weather">
                <i class="fas fa-sun fa-2x text-warning"></i>
                <span class="weather-temp">25°C</span>
            </div>
        @endif
    </div>
</div>

<style>
.municipal-banner {
    background: linear-gradient(135deg, #3699FF 0%, #1BC5BD 50%, #FFA800 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 24px rgba(54, 153, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.municipal-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: bannerGlow 15s ease-in-out infinite;
}

@keyframes bannerGlow {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(-20px, 20px); }
}

.banner-content {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.banner-logo img {
    height: 80px;
    width: auto;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.banner-text {
    flex: 1;
}

.banner-title {
    color: white;
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.banner-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    margin: 0 0 1rem 0;
    font-weight: 500;
}

.banner-date {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.banner-weather {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
    background: rgba(255, 255, 255, 0.15);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.weather-temp {
    font-size: 1.5rem;
    font-weight: 700;
}

@media (max-width: 768px) {
    .municipal-banner {
        padding: 1.5rem;
    }
    
    .banner-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .banner-logo img {
        height: 60px;
    }
    
    .banner-title {
        font-size: 1.5rem;
    }
    
    .banner-subtitle {
        font-size: 1rem;
    }
    
    .banner-weather {
        width: 100%;
        justify-content: center;
    }
}
</style>
