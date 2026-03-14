<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class PerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $divisionId = $request->input('division_id');

        // Get Reporters (Users with role 'wartawan')
        $query = User::role('wartawan')->with(['profile.division', 'profile.position']);

        // ACCESS CONTROL: Admin & Direktur see all, others only see themselves
        if (!auth()->user()->hasRole(['admin', 'direktur'])) {
            $query->where('id', auth()->id());
        }

        if ($divisionId) {
            $query->whereHas('profile', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $reporters = $query->get();

        // Prepare Report Data
        $reportData = $reporters->map(function ($reporter) use ($startDate, $endDate) {
            $query = Assignment::where('reporter_id', $reporter->id)
                ->whereBetween('start_time', ["$startDate 00:00:00", "$endDate 23:59:59"]);

            $total = (clone $query)->count();
            $completed = (clone $query)->whereIn('status', ['submitted', 'published'])->count();
            $pending = (clone $query)->whereIn('status', ['assigned', 'accepted', 'on_site'])->count();
            $canceled = (clone $query)->where('status', 'canceled')->count();

            $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

            return (object) [
                'user' => $reporter,
                'total' => $total,
                'completed' => $completed,
                'pending' => $pending,
                'canceled' => $canceled,
                'rate' => $completionRate,
            ];
        });

        // Divisions for filter
        $divisions = \App\Models\Division::all();

        return view('reports.performance', compact('reportData', 'startDate', 'endDate', 'divisions'));
    }
}
