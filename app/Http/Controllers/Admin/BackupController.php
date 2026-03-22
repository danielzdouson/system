<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });

        $this->backupService = $backupService;
    }

    public function index(Request $request)
    {
        if (!session('backup_access_verified')) {
            return view('admin.backups.verify-password');
        }

        $backups = $this->backupService->listBackups();
        $stats = $this->backupService->getBackupStats();

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }

        session(['backup_access_verified' => true]);
        session(['backup_access_time' => now()]);

        return redirect()->route('admin.backups.index');
    }

    public function revokeAccess()
    {
        session()->forget('backup_access_verified');
        session()->forget('backup_access_time');

        return redirect()->route('admin.backups.index')
            ->with('success', 'Backup access has been revoked. You will need to re-authenticate.');
    }

    public function create(Request $request)
    {
        try {
            $result = $this->backupService->createBackup();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'filename' => $result['filename'],
                    'size' => $result['size']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        try {
            $backupPath = Storage::disk('backups')->path($filename);

            if (!file_exists($backupPath)) {
                $backupPath = $this->backupService->downloadFromS3($filename);
                
                if (!$backupPath) {
                    abort(404, 'Backup file not found');
                }
            }

            return Response::download($backupPath, $filename, [
                'Content-Type' => 'application/zip',
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Failed to download backup: ' . $e->getMessage());
        }
    }

    public function delete($filename)
    {
        try {
            $result = $this->backupService->deleteBackup($filename);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request, $filename)
    {
        $request->validate([
            'confirmation' => 'required|in:RESTORE',
        ]);

        try {
            $result = $this->backupService->restoreBackup($filename);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'pre_restore_backup' => $result['pre_restore_backup']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
