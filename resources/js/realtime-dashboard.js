class RealTimeDashboard {
    constructor() {
        this.socket = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.updateInterval = null;
        this.chartInstances = {};

        this.init();
    }

    init() {
        this.connectWebSocket();
        this.startPeriodicUpdates();
        this.setupEventListeners();
    }

    connectWebSocket() {
        // For now, we'll use polling instead of WebSocket
        // Later, this can be replaced with Laravel Reverb or Pusher
        console.log('Real-time dashboard initialized with polling');
    }

    startPeriodicUpdates() {
        // Update every 30 seconds
        this.updateInterval = setInterval(() => {
            this.fetchStats();
        }, 30000);

        // Initial fetch
        this.fetchStats();
    }

    fetchStats() {
        fetch('/dashboard/realtime-stats', { credentials: 'same-origin' })
            .then(response => response.json())

            .then(data => {
                this.updateDashboard(data);
                this.showUpdateIndicator();
            })
            .catch(error => {
                console.error('Error fetching stats:', error);
                this.showError('فشل في تحديث البيانات');
            });
    }

    updateDashboard(data) {
        // Update counters with animation
        this.updateCounters(data);
        this.updateCharts(data);
        this.updateTimeline(data.timeline);
        this.updateAlerts(data.system_alerts || []);
        this.updateBadges(data);
    }

    updateCounters(data) {
        const counters = [
            { id: 'totalEmployees', value: data.employees?.total || 0 },
            { id: 'presentToday', value: data.employees?.present_today || 0 },
            { id: 'totalRequests', value: data.requests?.total || 0 },
            { id: 'pendingRequests', value: data.requests?.pending || 0 },
            { id: 'totalCrafts', value: data.crafts?.total || 0 },
            { id: 'activeCrafts', value: data.crafts?.active || 0 },
            { id: 'totalEngineering', value: data.engineering?.total || 0 },
            { id: 'completedEngineering', value: data.engineering?.completed || 0 },
        ];

        counters.forEach(counter => {
            const element = document.getElementById(counter.id);
            if (element) {
                this.animateNumber(element, counter.value);
            }
        });

        // Update progress bars
        this.updateProgressBar('attendanceProgress', data.employees?.attendance_rate || 0);
        this.updateProgressBar('requestsProgress', data.requests?.completion_rate || 0);
        this.updateProgressBar('craftsProgress', data.crafts?.total ? (data.crafts.active / data.crafts.total) * 100 : 0);
        this.updateProgressBar('engineeringProgress', data.engineering?.total ? (data.engineering.completed / data.engineering.total) * 100 : 0);
    }

    animateNumber(element, targetValue) {
        const currentValue = parseInt(element.textContent) || 0;
        const increment = (targetValue - currentValue) / 20;
        let current = currentValue;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += increment;

            if (step >= 20) {
                element.textContent = targetValue;
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current);
            }
        }, 50);

        // Add highlight effect
        element.classList.add('text-primary');
        setTimeout(() => {
            element.classList.remove('text-primary');
        }, 1000);
    }

    updateProgressBar(id, percentage) {
        const element = document.getElementById(id);
        if (element) {
            element.style.width = percentage + '%';
        }
    }

    updateCharts(data) {
        if (data.heatmap && data.heatmap.length > 0) {
            this.updateHeatmapChart(data.heatmap);
        }
    }

    updateHeatmapChart(heatmapData) {
        const ctx = document.getElementById('heatmapChart');
        if (!ctx) return;

        if (this.chartInstances.heatmap) {
            this.chartInstances.heatmap.destroy();
        }

        this.chartInstances.heatmap = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: heatmapData.map(item => item.area),
                datasets: [{
                    label: 'الكثافة',
                    data: heatmapData.map(item => item.count),
                    backgroundColor: heatmapData.map(item =>
                        `rgba(255, ${255 - item.intensity * 2}, ${255 - item.intensity * 2}, 0.8)`
                    ),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    updateTimeline(timeline) {
        const container = document.getElementById('liveTimeline');
        if (!container) return;

        if (!timeline || timeline.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">لا توجد أنشطة حديثة</p>';
            return;
        }

        const timelineHtml = timeline.map(item => `
            <div class="timeline-item ${item.priority === 'high' ? 'priority-high' : ''} mb-4">
                <div class="d-flex align-items-start">
                    <div class="symbol symbol-40 mr-3">
                        <div class="symbol-label ${item.type === 'request' ? 'bg-primary' : 'bg-warning'}">
                            <i class="fas ${item.type === 'request' ? 'fa-clipboard-list' : 'fa-calendar'} text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">${item.title}</h6>
                                <p class="text-muted mb-1">${item.description}</p>
                                <small class="text-muted">${item.user} • ${item.time}</small>
                            </div>
                            ${item.priority === 'high' ? '<span class="badge badge-danger">عاجل</span>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = timelineHtml;
    }

    updateAlerts(alerts) {
        const container = document.getElementById('systemAlerts');
        if (!container) return;

        if (!alerts || alerts.length === 0) {
            container.innerHTML = '';
            return;
        }

        const alertsHtml = alerts.map(alert => `
            <div class="alert alert-${alert.type} alert-dismissible fade show" role="alert">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-text">
                    ${alert.message}
                </div>
                <div class="alert-actions">
                    <a href="${alert.action_url}" class="btn btn-sm btn-${alert.type}">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        `).join('');

        container.innerHTML = alertsHtml;
    }

    updateBadges(data) {
        const badges = [
            { id: 'pendingHolidaysBadge', value: data.holidays?.pending_holidays || 0 },
            { id: 'renewalPendingBadge', value: data.crafts?.renewal_pending || 0 },
            { id: 'unreadNotificationsBadge', value: data.notifications?.unread || 0 },
        ];

        badges.forEach(badge => {
            const element = document.getElementById(badge.id);
            if (element) {
                element.textContent = badge.value;
                if (badge.value > 0) {
                    element.classList.add('alert-badge');
                } else {
                    element.classList.remove('alert-badge');
                }
            }
        });
    }

    showUpdateIndicator() {
        const indicator = document.querySelector('.live-indicator');
        if (indicator) {
            indicator.style.background = '#10b981';
            setTimeout(() => {
                indicator.style.background = '#10b981';
            }, 1000);
        }
    }

    showError(message) {
        // Show error notification
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-text">
                    ${message}
                </div>
            </div>
        `;

        const container = document.getElementById('systemAlerts');
        if (container) {
            container.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }

    setupEventListeners() {
        // Handle manual refresh
        const refreshBtn = document.querySelector('[onclick="refreshAllData()"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.fetchStats();
            });
        }

        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.fetchStats();
            }
        });
    }

    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }

        // Destroy chart instances
        Object.values(this.chartInstances).forEach(chart => {
            if (chart) chart.destroy();
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.realTimeDashboard = new RealTimeDashboard();
});

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (window.realTimeDashboard) {
        window.realTimeDashboard.destroy();
    }
});
