<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    /**
     * Dynamically apply mail settings from the database to Laravel config.
     */
    public static function applyConfig(): void
    {
        try {
            $driver = Setting::get('mail_driver');
            $host = Setting::get('mail_host');
            $port = Setting::get('mail_port');
            $username = Setting::get('mail_username');
            $password = Setting::get('mail_password');
            $encryption = Setting::get('mail_encryption');
            $fromAddress = Setting::get('mail_from_address') ?: Setting::get('site_email') ?: config('mail.from.address');
            $fromName = Setting::get('mail_from_name') ?: Setting::get('site_name') ?: config('mail.from.name');

            if (! empty($driver)) {
                Config::set('mail.default', $driver);
            }

            if (! empty($host)) {
                Config::set('mail.mailers.smtp.host', $host);
            }

            if (! empty($port)) {
                Config::set('mail.mailers.smtp.port', (int) $port);
            }

            if ($encryption !== null && $encryption !== '') {
                $enc = strtolower(trim((string) $encryption));
                if (in_array($enc, ['ssl', 'smtps'])) {
                    Config::set('mail.mailers.smtp.scheme', 'smtps');
                    Config::set('mail.mailers.smtp.encryption', 'ssl');
                } elseif (in_array($enc, ['tls', 'starttls'])) {
                    Config::set('mail.mailers.smtp.scheme', null);
                    Config::set('mail.mailers.smtp.encryption', 'tls');
                } elseif ($enc === 'null' || $enc === 'none') {
                    Config::set('mail.mailers.smtp.scheme', null);
                    Config::set('mail.mailers.smtp.encryption', null);
                } else {
                    Config::set('mail.mailers.smtp.encryption', $enc);
                }
            }

            if ($username !== null && $username !== '') {
                Config::set('mail.mailers.smtp.username', $username);
            }

            if ($password !== null && $password !== '') {
                Config::set('mail.mailers.smtp.password', $password);
            }

            if (! empty($fromAddress)) {
                Config::set('mail.from.address', $fromAddress);
            }

            if (! empty($fromName)) {
                Config::set('mail.from.name', $fromName);
            }
        } catch (\Throwable $e) {
            // Silently pass during initial installation or migration without database connection
        }
    }
}
