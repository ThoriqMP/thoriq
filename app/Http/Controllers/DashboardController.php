<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Models\CashTransaction;
use App\Models\OmsetLog;
use App\Models\PayrollDistribution;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeProjectsCount = Project::count();
        
        $todoTasksCount = Task::where('status', 'todo')->count();
        $inProgressTasksCount = Task::where('status', 'in_progress')->count();
        $doneTasksCount = Task::where('status', 'done')->count();
        $totalTasksCount = Task::count();

        $recentDocuments = Document::with('tags')
            ->latest()
            ->take(5)
            ->get();

        $urgentTasks = Task::with('project')
            ->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $totalFilesSize = Document::sum('file_size'); // bytes

        // Financial data additions for the merged dashboard
        $totalIn = CashTransaction::where('tipe', 'in')->sum('nominal');
        $totalOut = CashTransaction::where('tipe', 'out')->sum('nominal');
        $saldoKas = $totalIn - $totalOut;

        $totalB = OmsetLog::where('status', 'approved')->sum('alokasi_gaji');
        $totalGapokPool = OmsetLog::where('status', 'approved')->sum('gaji_pokok_pool');
        $totalTukinPool = OmsetLog::where('status', 'approved')->sum('tukin_pool');

        $payrollPending = PayrollDistribution::where('status_pembayaran', 'pending')->sum(
            \DB::raw('nominal_gapok_diterima + nominal_tukin_diterima')
        );

        return view('dashboard', compact(
            'activeProjectsCount',
            'todoTasksCount',
            'inProgressTasksCount',
            'doneTasksCount',
            'totalTasksCount',
            'recentDocuments',
            'urgentTasks',
            'totalFilesSize',
            'saldoKas',
            'totalB',
            'totalGapokPool',
            'totalTukinPool',
            'payrollPending'
        ));
    }
}
