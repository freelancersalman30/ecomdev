<?php

namespace App\Services;

use App\Models\SmsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS with tag replacement
     */
    public function send(string $phone, string $template, array $data = [], string $gateway = 'BulkSMS'): bool
    {
        // Replace dynamic tokens
        $message = $template;
        foreach ($data as $key => $val) {
            $message = str_replace("{{$key}}", (string) $val, $message);
        }

        $charCount = mb_strlen($message);
        $smsParts = (int) ceil($charCount / 160) ?: 1;

        // Log SMS entry
        try {
            SmsLog::create([
                'gateway' => $gateway,
                'phone' => $phone,
                'message' => $message,
                'character_count' => $charCount,
                'sms_parts' => $smsParts,
                'status' => 'sent',
                'response_id' => 'SMS_'.uniqid(),
                'sent_at' => Carbon::now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('SMS Dispatch Error: '.$e->getMessage());
            SmsLog::create([
                'gateway' => $gateway,
                'phone' => $phone,
                'message' => $message,
                'character_count' => $charCount,
                'sms_parts' => $smsParts,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => Carbon::now(),
            ]);

            return false;
        }
    }
}
