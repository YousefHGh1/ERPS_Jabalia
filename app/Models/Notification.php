<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'is_read',
        'read_at',
        'icon',
        'priority',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * تحويل رابط التنبيه ليظهر دائماً على دومين APP_URL.
     */
    public function getLinkAttribute($value): ?string
    {
        if (empty($value) || $value === '#') {
            return $value;
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        if (str_starts_with($value, '/')) {
            return $appUrl . $value;
        }

        $parsed = parse_url($value);
        if (is_array($parsed) && isset($parsed['host'])) {
            $host = strtolower((string) $parsed['host']);
            if (in_array($host, ['127.0.0.1', 'localhost'], true)) {
                $path = $parsed['path'] ?? '/';
                $query = isset($parsed['query']) ? ('?' . $parsed['query']) : '';
                $fragment = isset($parsed['fragment']) ? ('#' . $parsed['fragment']) : '';
                return $appUrl . $path . $query . $fragment;
            }

            return $value;
        }

        return $appUrl . '/' . ltrim($value, '/');
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديد الأيقونة بناءً على نوع التنبيه
     */
    public function getIconAttribute(): string
    {
        if ($this->attributes['icon']) {
            return $this->attributes['icon'];
        }

        return match($this->type) {
            'holiday' => 'fa-calendar-check',
            'permission' => 'fa-clock',
            'attendance' => 'fa-user-clock',
            'system' => 'fa-bell',
            'payment' => 'fa-money-bill',
            'license' => 'fa-id-card',
            'request' => 'fa-envelope',
            default => 'fa-bell',
        };
    }

    /**
     * تحديد لون الأولوية
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'text-gray-400',
            'normal' => 'text-blue-500',
            'high' => 'text-orange-500',
            'urgent' => 'text-red-500',
            default => 'text-blue-500',
        };
    }

    /**
     * نطاق التنبيهات غير المقروءة
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * نطاق التنبيهات المقروءة
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * نطاق حسب النوع
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * نطاق حسب الأولوية
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * تحديد التنبيه كمقروء
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * تحديد التنبيه كغير مقروء
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
