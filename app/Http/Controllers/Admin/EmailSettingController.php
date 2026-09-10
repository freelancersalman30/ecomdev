<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'email')->pluck('value', 'key');
        $emailSettings = $settings;

        return view('admin.settings.email', compact('settings', 'emailSettings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_driver' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|numeric|between:1,65535',
            'mail_encryption' => 'nullable|string|max:50',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, (string) ($value ?? ''), 'email');
        }

        MailConfigService::applyConfig();

        return redirect()->back()->with('success', 'SMTP Email settings saved and activated successfully!');
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        try {
            MailConfigService::applyConfig();

            $driver = config('mail.default', 'smtp');
            if ($driver === 'smtp') {
                Mail::purge('smtp');
            }

            $siteName = Setting::get('site_name', 'DREAMERS PCB');
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            $fromAddress = config('mail.from.address');

            $body = "Hello,\n\nThis is a test notification confirming that your SMTP Email Gateway is successfully configured and working.\n\nConnection Details:\n- Store: {$siteName}\n- Driver: {$driver}\n- Host: {$host}\n- Port: {$port}\n- From: {$fromAddress}\n- Timestamp: ".now()->toDayDateTimeString()."\n\nBest regards,\n{$siteName} System";

            Mail::raw($body, function ($msg) use ($request, $siteName) {
                $msg->to($request->test_email)
                    ->subject("Test Email - {$siteName} SMTP Gateway");
            });

            return redirect()->back()->with('success', "Test email sent successfully to {$request->test_email}!");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'SMTP Connection Error: '.$e->getMessage());
        }
    }
}
