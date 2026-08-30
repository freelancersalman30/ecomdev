<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemToolController extends Controller
{
    public function index()
    {
        $serverInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI / Built-in Server',
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time').'s',
            'db_driver' => config('database.default'),
            'queue_driver' => config('queue.default'),
            'cache_driver' => config('cache.default'),
        ];

        return view('admin.system.tools', compact('serverInfo'));
    }

    public function clearCache(Request $request)
    {
        $action = $request->action;

        switch ($action) {
            case 'app_cache':
                Artisan::call('cache:clear');
                $msg = 'Application Cache cleared successfully.';
                break;
            case 'route_cache':
                Artisan::call('route:clear');
                $msg = 'Route Cache cleared.';
                break;
            case 'config_cache':
                Artisan::call('config:clear');
                $msg = 'Configuration Cache cleared.';
                break;
            case 'view_cache':
                Artisan::call('view:clear');
                $msg = 'Compiled Blade Views cleared.';
                break;
            case 'optimize':
                Artisan::call('optimize:clear');
                $msg = 'Full system optimization cache cleared and rebuilt.';
                break;
            default:
                $msg = 'No action performed.';
        }

        return redirect()->back()->with('success', $msg);
    }
}
