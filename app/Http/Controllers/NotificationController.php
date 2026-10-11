<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * إبطال كاش إشعارات المستخدم بعد التحديث ليعود الهيدر محدثاً في الطلب التالي.
     */
    private function forgetNotificationsCache(): void
    {
        if (Auth::check()) {
            Cache::forget('user_notifications_' . Auth::id());
        }
    }

    /**
     * صفحة الإشعارات الموحدة مع ترقيم وتصفية (الكل / غير المقروءة / حسب النوع).
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications()->orderBy('created_at', 'desc');

        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->where('is_read', false);
            } elseif (in_array($request->filter, ['request', 'system', 'holiday', 'permission', 'attendance', 'payment', 'license'], true)) {
                $query->where('type', $request->filter);
            }
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->forgetNotificationsCache();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        $this->forgetNotificationsCache();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        $this->forgetNotificationsCache();

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * للطلب AJAX من الهيدر (تحديث كل 30 ثانية) — كاش قصير لظهور العدد الجديد سريعاً.
     */
    public function getLatest()
    {
        $user = Auth::user();
        $cacheKey = 'user_notifications_' . $user->id;
        $data = Cache::remember($cacheKey, 30, function () use ($user) {
            try {
                $latest = $user->unreadNotifications()->limit(5)->get();
                $count = $user->unreadNotifications()->count();
                return ['count' => $count, 'latest' => $latest];
            } catch (\Throwable $e) {
                return ['count' => 0, 'latest' => collect()];
            }
        });

        return response()->json([
            'count' => $data['count'],
            'latest' => $data['latest']->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'link' => $n->link,
                'icon' => $n->icon,
                'created_at_human' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }
}
