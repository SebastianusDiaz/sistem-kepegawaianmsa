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

        // Get Reporters (Wartawan) AND Staff (Pegawai/Admin who edit)
        // We fetch users who have either role.
        $query = User::role(['wartawan', 'pegawai', 'admin'])->with(['profile.division', 'profile.position']);

        // ACCESS CONTROL: Admin & Direktur see all, others only see themselves
        if (!auth()->user()->hasRole(['admin', 'direktur'])) {
            $query->where('id', auth()->id());
        }

        if ($divisionId) {
            $query->whereHas('profile', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $users = $query->get();

        // Prepare Report Data
        $reportData = $users->map(function ($user) use ($startDate, $endDate) {

            // Determine if user is primarily a Reporter or an Editor
            // For this logic, we check if they have 'wartawan' role.
            $isWartawan = $user->hasRole('wartawan');

            if ($isWartawan) {
                // --- WARTAWAN LOGIC (By Reporter ID) ---
                $query = Assignment::where('reporter_id', $user->id)
                    ->whereBetween('start_time', ["$startDate 00:00:00", "$endDate 23:59:59"]);

                $total = (clone $query)->count();

                // Completed: Submitted or Published
                $completed = (clone $query)->whereIn('status', ['submitted', 'published'])->count();

                // On Progress: Active states including Revision
                $pending = (clone $query)->whereIn('status', ['assigned', 'accepted', 'on_site', 'revision'])->count();

                $canceled = (clone $query)->where('status', 'canceled')->count();

            } else {
                // --- PEGAWAI LOGIC (By Editor ID) ---
                // "Kinerja pegawai berdasarkan revisi ... sampai disetujui"
                // We count assignments where they are the EDITOR.
                $query = Assignment::where('editor_id', $user->id)
                    ->whereBetween('updated_at', ["$startDate 00:00:00", "$endDate 23:59:59"]); // Use updated_at or start_time? usually update for approval.

                $total = (clone $query)->count();

                // Approved (Target): Status 'published'
                $completed = (clone $query)->where('status', 'published')->count();

                // Pending Review/Process: Submitted or Revision
                $pending = (clone $query)->whereIn('status', ['submitted', 'revision'])->count();

                $canceled = (clone $query)->where('status', 'canceled')->count();
            }

            $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

            // Only return if there is some activity or user is relevant? 
            // Often better to show 0s than nothing if they are in the list.
            return (object) [
                'user' => $user,
                'role_type' => $isWartawan ? 'Wartawan' : 'Pegawai',
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
