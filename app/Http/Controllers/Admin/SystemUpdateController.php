<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class SystemUpdateController extends Controller
{
    public function index()
    {
        $currentCommit = 'Unknown';
        $currentDate = 'Unknown';
        $currentBranch = 'main';

        try {
            if (function_exists('exec')) {
                $currentCommit = trim(shell_exec('git log -1 --format="%h - %s"') ?? 'Not a git repo / git command unavailable');
                $currentDate = trim(shell_exec('git log -1 --format="%cd" --date=relative') ?? '');
                $currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD') ?? 'main');
            }
        } catch (\Exception $e) {
            // fallback
        }

        return view('admin.settings.system_update', compact('currentCommit', 'currentDate', 'currentBranch'));
    }

    public function pull(Request $request)
    {
        $output = [];
        $success = true;

        try {
            $basePath = base_path();

            // Run git pull / git reset via Process or shell_exec
            $commands = [
                'git fetch origin main 2>&1',
                'git reset --hard origin/main 2>&1',
            ];

            foreach ($commands as $cmd) {
                if (function_exists('shell_exec')) {
                    $res = shell_exec("cd {$basePath} && {$cmd}");
                    $output[] = "$ {$cmd}\n".($res ?: '(no output)');
                }
            }

            // Run artisan optimizations
            Artisan::call('optimize:clear');
            $output[] = '$ php artisan optimize:clear'."\n".Artisan::output();

            Artisan::call('migrate', ['--force' => true]);
            $output[] = '$ php artisan migrate --force'."\n".Artisan::output();

        } catch (\Exception $e) {
            $success = false;
            $output[] = 'Error during update: '.$e->getMessage();
        }

        $logOutput = implode("\n\n", $output);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $success,
                'log' => $logOutput,
            ]);
        }

        return redirect()->route('admin.settings.system_update')
            ->with($success ? 'success' : 'error', 'Update executed. Check log below.')
            ->with('update_log', $logOutput);
    }
}
