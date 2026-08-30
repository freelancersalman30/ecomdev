<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::where('group', 'email')->pluck('value', 'key');

        return view('admin.settings.email', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, (string) $value, 'email');
        }

        return redirect()->back()->with('success', 'SMTP Email settings saved!');
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test notification from DREAMERS PCB E-Commerce system.', function ($msg) use ($request) {
                $msg->to($request->test_email)->subject('Test Email - DREAMERS PCB');
            });

            return redirect()->back()->with('success', "Test email sent to {$request->test_email}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Mail error: '.$e->getMessage());
        }
    }
}
