@props(['title', 'stats', 'quickActions', 'recentItems'])

<div class="field-dashboard" x-data="fieldDashboard()">
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="dashboard-title">{{ $title }}</h4>
            <button class="btn btn-sm btn-outline-primary" @click="refreshData()">
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
            </button>
        </div>
        
        <!-- Quick Stats -->
        <div class="quick-stats-grid">
            @foreach($stats as $stat)
                <div class="stat-card" @click="navigateToStat('{{ $stat['route'] ?? '#' }}')">
                    <div class="stat-icon">
                        <i class="{{ $stat['icon'] ?? 'fas fa-chart-bar' }}"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" x-text="{{ $stat['value'] ?? 0 }}">{{ $stat['value'] ?? 0 }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                    @if(isset($stat['trend']))
                        <div class="stat-trend {{ $stat['trend'] > 0 ? 'positive' : 'negative' }}">
                            <i class="fas fa-{{ $stat['trend'] > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            <span>{{ abs($stat['trend']) }}%</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Actions -->
    @if($quickActions)
        <div class="quick-actions-section">
            <h5 class="section-title">{{ __('Quick Actions') }}</h5>
            <div class="quick-actions-grid">
                @foreach($quickActions as $action)
                    <a href="{{ $action['route'] }}" 
                       class="action-card"
                       @touchstart="$el.classList.add('action-touch')"
                       @touchend="$el.classList.remove('action-touch')">
                        <div class="action-icon">
                            <i class="{{ $action['icon'] }}"></i>
                        </div>
                        <div class="action-label">{{ $action['label'] }}</div>
                        @if(isset($action['badge']))
                            <span class="action-badge">{{ $action['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Items -->
    @if($recentItems)
        <div class="recent-items-section">
            <h5 class="section-title">{{ __('Recent Activity') }}</h5>
            <div class="recent-items-list">
                @foreach($recentItems as $item)
                    <div class="recent-item" @click="navigateToItem('{{ $item['route'] }}')">
                        <div class="item-icon">
                            <i class="{{ $item['icon'] ?? 'fas fa-file' }}"></i>
                        </div>
                        <div class="item-content">
                            <div class="item-title">{{ $item['title'] }}</div>
                            <div class="item-meta">
                                <span class="item-time">{{ $item['time'] }}</span>
                                <span class="item-status status-{{ $item['status'] ?? 'pending' }}">
                                    {{ $item['status'] ?? 'Pending' }}
                                </span>
                            </div>
                        </div>
                        <div class="item-arrow">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Floating Action Button -->
    <div class="fab-container">
        <button class="fab-main" @click="toggleFab()">
            <i class="fas fa-plus" x-show="!fabOpen"></i>
            <i class="fas fa-times" x-show="fabOpen"></i>
        </button>
        
        <div class="fab-options" x-show="fabOpen" x-transition:enter="transition ease-out duration-200">
            @foreach($quickActions ?? [] as $index => $action)
                <button class="fab-option" 
                        style="animation-delay: {{ $index * 50 }}ms"
                        @click="navigateToFabAction('{{ $action['route'] }}')"
                        @touchstart="$el.classList.add('fab-touch')"
                        @touchend="$el.classList.remove('fab-touch')">
                    <i class="{{ $action['icon'] }}"></i>
                    <span class="fab-tooltip">{{ $action['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Offline Indicator -->
    <div class="offline-indicator" x-show="!isOnline" x-transition:enter="transition ease-out duration-200">
        <div class="alert alert-warning">
            <i class="fas fa-wifi"></i>
            {{ __('You are offline. Some features may be limited.') }}
        </div>
    </div>
</div>

<script>
function fieldDashboard() {
    return {
        loading: false,
        fabOpen: false,
        isOnline: navigator.onLine,
        
        init() {
            // Monitor online/offline status
            window.addEventListener('online', () => {
                this.isOnline = true;
                this.syncOfflineData();
            });
            
            window.addEventListener('offline', () => {
                this.isOnline = false;
            });
            
            // Initialize PWA features
            this.initPWA();
            
            // Check for updates
            this.checkForUpdates();
        },
        
        refreshData() {
            this.loading = true;
            
            // Simulate data refresh
            setTimeout(() => {
                this.loading = false;
                this.showNotification('Dashboard refreshed', 'success');
            }, 1000);
        },
        
        toggleFab() {
            this.fabOpen = !this.fabOpen;
        },
        
        navigateToStat(route) {
            if (route && route !== '#') {
                window.location.href = route;
            }
        },
        
        navigateToItem(route) {
            window.location.href = route;
        },
        
        navigateToFabAction(route) {
            this.fabOpen = false;
            window.location.href = route;
        },
        
        initPWA() {
            // Register service worker if available
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered:', registration);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            }
            
            // Handle install prompt
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Show install button after some delay
                setTimeout(() => {
                    this.showInstallPrompt();
                }, 3000);
            });
            
            window.showInstallPrompt = () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                    });
                }
            };
        },
        
        checkForUpdates() {
            // Check for app updates
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                this.showUpdateAvailable();
                            }
                        });
                    });
                });
            }
        },
        
        showUpdateAvailable() {
            if (confirm('A new version is available. Would you like to update?')) {
                window.location.reload();
            }
        },
        
        showInstallPrompt() {
            if (confirm('Would you like to install this app for offline access?')) {
                window.showInstallPrompt();
            }
        },
        
        syncOfflineData() {
            // Sync any offline data when coming back online
            const offlineData = localStorage.getItem('offline_data');
            if (offlineData) {
                try {
                    const data = JSON.parse(offlineData);
                    this.syncDataToServer(data);
                    localStorage.removeItem('offline_data');
                } catch (e) {
                    console.error('Failed to sync offline data:', e);
                }
            }
        },
        
        syncDataToServer(data) {
            // Implement server sync logic
            fetch('/api/sync-offline-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    this.showNotification('Data synced successfully', 'success');
                }
            })
            .catch(error => {
                console.error('Sync failed:', error);
            });
        },
        
        showNotification(message, type = 'info') {
            // Create a simple notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification-toast`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                left: 20px;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    }
}
</script>

<style>
.field-dashboard {
    padding-bottom: 80px; /* Space for FAB */
    min-height: 100vh;
    background: #f8f9fa;
}

.mobile-header {
    background: white;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 100;
}

.dashboard-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
}

.quick-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card:active {
    transform: translateY(0);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e3f2fd;
    color: #1976d2;
    font-size: 1.2rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
    margin-top: 0.25rem;
}

.stat-trend {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.stat-trend.positive {
    color: #28a745;
}

.stat-trend.negative {
    color: #dc3545;
}

.quick-actions-section, .recent-items-section {
    background: white;
    margin: 1rem;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.75rem;
}

.action-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem 0.75rem;
    text-align: center;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
    position: relative;
}

.action-card:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #333;
}

.action-card:active {
    transform: scale(0.95);
}

.action-touch {
    transform: scale(0.95);
    opacity: 0.8;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #007bff;
    color: white;
    margin: 0 auto 0.5rem;
    font-size: 1.1rem;
}

.action-label {
    font-size: 0.8rem;
    font-weight: 500;
}

.action-badge {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 0.125rem 0.375rem;
    font-size: 0.7rem;
    font-weight: 600;
}

.recent-items-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.recent-item:hover {
    background: #e9ecef;
}

.item-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e3f2fd;
    color: #1976d2;
    font-size: 0.9rem;
}

.item-content {
    flex: 1;
}

.item-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
}

.item-meta {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.item-time {
    font-size: 0.75rem;
    color: #666;
}

.item-status {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.125rem 0.375rem;
    border-radius: 10px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-urgent {
    background: #f8d7da;
    color: #721c24;
}

.item-arrow {
    color: #666;
    font-size: 0.8rem;
}

.fab-container {
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    z-index: 1000;
}

.fab-main {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0,123,255,0.5);
}

.fab-main:active {
    transform: scale(0.95);
}

.fab-options {
    position: absolute;
    bottom: 70px;
    right: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.fab-option {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: white;
    color: #333;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    opacity: 0;
    transform: scale(0);
    animation: fabOptionIn 0.3s ease forwards;
}

.fab-option:hover {
    background: #f8f9fa;
    transform: scale(1.1);
}

.fab-option:active {
    transform: scale(0.95);
}

.fab-tooltip {
    position: absolute;
    right: 60px;
    background: #333;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.fab-option:hover .fab-tooltip {
    opacity: 1;
}

.fab-touch {
    transform: scale(0.9);
}

.offline-indicator {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 2000;
}

.offline-indicator .alert {
    margin: 0;
    border-radius: 0;
    text-align: center;
}

.notification-toast {
    animation: slideIn 0.3s ease-out;
}

/* Animations */
@keyframes slideIn {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(-100%);
        opacity: 0;
    }
}

@keyframes fabOptionIn {
    from {
        opacity: 0;
        transform: scale(0);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .quick-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .action-icon {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
    }
    
    .action-label {
        font-size: 0.7rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .field-dashboard {
        background: #1a1a1a;
    }
    
    .mobile-header,
    .quick-actions-section,
    .recent-items-section {
        background: #2d2d2d;
        border-color: #444;
    }
    
    .dashboard-title,
    .section-title {
        color: #fff;
    }
    
    .stat-card,
    .action-card,
    .recent-item {
        background: #3d3d3d;
        color: #fff;
    }
    
    .stat-card:hover,
    .action-card:hover,
    .recent-item:hover {
        background: #4d4d4d;
    }
    
    .stat-number,
    .item-title {
        color: #fff;
    }
    
    .stat-label,
    .item-time {
        color: #aaa;
    }
}
</style>
