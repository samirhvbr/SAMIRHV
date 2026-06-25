<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'projects' => Project::count(),
            'files' => ProjectFile::count(),
            'downloads_total' => (int) ProjectFile::sum('downloads_count'),
            'downloads_today' => DownloadLog::where('is_bot', false)
                ->whereDate('created_at', today())->count(),
        ];

        $recentDownloads = DownloadLog::with('file.project')
            ->where('is_bot', false)
            ->latest()
            ->limit(10)
            ->get();

        $topFiles = ProjectFile::with('project')
            ->orderByDesc('downloads_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentDownloads', 'topFiles'));
    }
}
