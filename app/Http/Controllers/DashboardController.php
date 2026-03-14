<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first(); // Spatie role
        $data = [];

        if ($user->hasRole('admin')) {
            $data['totalUsers'] = \App\Models\User::count();
            $data['activeAssignments'] = \App\Models\Assignment::whereNotIn('status', ['published', 'canceled'])->whereNotIn('status', ['completed'])->count();
            $data['pendingLeaves'] = \App\Models\PermohonanIzin::where('status', 'pending')->count();

            // Chart Data: Assignment Status
            $assignmentStats = \App\Models\Assignment::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $data['chart'] = [
                'labels' => array_keys($assignmentStats),
                'series' => array_values($assignmentStats)
            ];

            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $data['serverStatus'] = 'Online';
            } catch (\Exception $e) {
                $data['serverStatus'] = 'DB Error';
            }

        } elseif ($user->hasRole('direktur')) {
            $data['totalKerjasama'] = \App\Models\Kerjasama::where('status', 'active')->count();
            $data['kerjasamaPending'] = \App\Models\Kerjasama::where('status', 'pending')->count();
            $data['permohonanIzinPending'] = \App\Models\PermohonanIzin::where('status', 'pending')->count();

            // Chart Data: Kerjasama Status
            $kerjasamaStats = \App\Models\Kerjasama::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $data['chart'] = [
                'labels' => array_keys($kerjasamaStats),
                'series' => array_values($kerjasamaStats)
            ];

            // Real Performance Avg (All Wartawan)
            $totalAssignments = \App\Models\Assignment::whereHas('reporter', function ($q) {
                $q->role('wartawan');
            })->count();

            $completedAssignments = \App\Models\Assignment::whereHas('reporter', function ($q) {
                $q->role('wartawan');
            })->whereIn('status', ['submitted', 'published'])->count();

            $data['performanceAvg'] = $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100, 1) : 0;

        } elseif ($user->hasRole('wartawan')) {
            $data['activeTasks'] = \App\Models\Assignment::where('reporter_id', $user->id)
                ->whereIn('status', ['assigned', 'accepted', 'on_site'])->count();

            $data['completedTasks'] = \App\Models\Assignment::where('reporter_id', $user->id)
                ->whereIn('status', ['submitted', 'published'])
                ->whereMonth('updated_at', now()->month)
                ->count();

            $data['myAssignments'] = \App\Models\Assignment::where('reporter_id', $user->id)
                ->whereIn('status', ['assigned', 'accepted', 'on_site', 'revision'])
                ->latest()
                ->take(5)
                ->get();

            // Chart Data: Monthly Completed Tasks (Last 6 Months)
            $labels = [];
            $series = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');
                $series[] = \App\Models\Assignment::where('reporter_id', $user->id)
                    ->whereIn('status', ['submitted', 'published'])
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->count();
            }
            $data['chart'] = ['labels' => $labels, 'series' => $series];

        } elseif ($user->hasRole(['editor', 'pegawai'])) {
            $data['pendingReviews'] = \App\Models\Assignment::where('editor_id', $user->id)
                ->whereIn('status', ['submitted', 'revision'])
                ->count();

            $data['publishedMonth'] = \App\Models\Assignment::where('editor_id', $user->id)
                ->where('status', 'published')
                ->whereMonth('updated_at', now()->month)
                ->count();

            // Chart Data: Monthly Published (Last 6 Months)
            $labels = [];
            $series = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');
                $series[] = \App\Models\Assignment::where('editor_id', $user->id)
                    ->where('status', 'published')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->count();
            }
            $data['chart'] = ['labels' => $labels, 'series' => $series];


        }

        // Attendance (Common for all non-admin usually, but good to have)
        if (!$user->hasRole('admin')) {
            $todayAttendance = \App\Models\Absensi::where('user_id', $user->id)
                ->whereDate('created_at', now()->today())
                ->first();
            $data['todayAttendance'] = $todayAttendance;
        }

        return view('dashboard.index', compact('role', 'data'));
    }
}
