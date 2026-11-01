<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class TweetSmsController extends Controller
{
    public function index(): View
    {
        $config = config('services.tweetsms');

        return view('settings.tweetsms.index', [
            'enabled' => (bool) $config['enabled'],
            'baseUrl' => $config['base_url'],
            'sender' => $config['sender'],
            'hasUsername' => filled($config['username']),
            'hasPassword' => filled($config['password']),
            'hasSender' => filled($config['sender']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'username' => ['nullable', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'max:255'],
            'sender' => ['nullable', 'string', 'max:20'],
            'base_url' => ['required', 'url', 'max:255'],
        ]);

        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            return back()->withErrors(['settings' => 'تعذر حفظ الإعدادات في ملف البيئة. تحقق من صلاحية الكتابة على ملف .env.'])->withInput($request->except('password'));
        }

        $current = file_get_contents($envPath);
        $values = [
            'TWEETSMS_ENABLED' => $request->boolean('enabled') ? 'true' : 'false',
            'TWEETSMS_BASE_URL' => $validated['base_url'],
            'TWEETSMS_SENDER' => $validated['sender'] ?? '',
        ];
        if (filled($validated['username'] ?? null)) {
            $values['TWEETSMS_USERNAME'] = $validated['username'];
        }
        if (filled($validated['password'] ?? null)) {
            $values['TWEETSMS_PASSWORD'] = $validated['password'];
        }

        foreach ($values as $key => $value) {
            $line = $key . '=' . $this->envValue((string) $value);
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            if (preg_match($pattern, $current)) {
                $current = preg_replace($pattern, $line, $current, 1);
            } else {
                $current = rtrim($current) . PHP_EOL . $line . PHP_EOL;
            }
        }

        if (file_put_contents($envPath, $current, LOCK_EX) === false) {
            return back()->withErrors(['settings' => 'فشل حفظ الإعدادات.'])->withInput($request->except('password'));
        }

        \Artisan::call('config:clear');

        return back()->with('success', 'تم حفظ إعدادات نظام الرسائل.');
    }

    public function balance(): RedirectResponse
    {
        $config = config('services.tweetsms');
        if (!$this->hasCredentials($config)) {
            return back()->withErrors(['sms' => 'أدخل اسم المستخدم وكلمة المرور واسم المرسل أولاً.']);
        }

        try {
            $response = Http::timeout((int) ($config['timeout'] ?? 10))->get($config['base_url'], [
                'comm' => 'chk_balance',
                'user' => $config['username'],
                'pass' => $config['password'],
            ]);

            if (!$response->successful()) {
                return back()->withErrors(['sms' => 'تعذر الاتصال بمزود الرسائل (HTTP ' . $response->status() . ').']);
            }

            return back()->with('sms_result', 'رد مزود الرسائل: ' . trim($response->body()));
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['sms' => 'تعذر الاتصال بمزود الرسائل. تحقق من الاتصال وإعدادات الخادم.']);
        }
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate(['phone' => ['required', 'string', 'max:30']]);
        $config = config('services.tweetsms');
        if (!$this->hasCredentials($config)) {
            return back()->withErrors(['sms' => 'أدخل اسم المستخدم وكلمة المرور واسم المرسل أولاً.']);
        }

        try {
            $response = Http::asForm()->timeout((int) ($config['timeout'] ?? 10))->get($config['base_url'], [
                'comm' => 'sendsms',
                'user' => $config['username'],
                'pass' => $config['password'],
                'to' => $validated['phone'],
                'message' => 'رسالة تجريبية من نظام بلدية جباليا.',
                'sender' => $config['sender'],
            ]);

            $body = trim($response->body());
            if (!$response->successful() || !preg_match('/(?:^|\s)1(?:\s|$)/', $body)) {
                return back()->withErrors(['sms' => 'لم يؤكد مزود الرسائل نجاح الإرسال. رد المزود: ' . $body]);
            }

            return back()->with('sms_result', 'تم قبول الرسالة التجريبية للإرسال. رقم الرسالة: ' . $body);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['sms' => 'تعذر الاتصال بمزود الرسائل. تحقق من الاتصال وإعدادات الخادم.']);
        }
    }

    private function hasCredentials(array $config): bool
    {
        return filled($config['username']) && filled($config['password']) && filled($config['sender']);
    }

    private function envValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return '"' . str_replace(['\\', '"', "\r", "\n"], ['\\\\', '\\"', '', ''], $value) . '"';
    }
}
