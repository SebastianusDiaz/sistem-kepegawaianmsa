<?php

namespace App\Actions;

use App\Models\Assignment;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class HandleFieldAttendanceAction
{
    /**
     * Handle the attendance logic when a reporter arrives on site.
     *
     * @param Assignment $assignment
     * @param float $currentLat
     * @param float $currentLng
     * @param float $accuracy
     * @return Absensi
     * @throws ValidationException
     */
    public function execute(Assignment $assignment, float $currentLat, float $currentLng, float $accuracy = 0): Absensi
    {
        // 1. Validate Proximity (Geofencing) - DISABLED per user request
        /*
        // Only if assignment has specific coordinates
        if ($assignment->latitude && $assignment->longitude) {
            $distance = $this->calculateDistance(
                $currentLat,
                $currentLng,
                $assignment->latitude,
                $assignment->longitude
            );

            // Allow 50m radius for field coverage
            $allowedRadius = 50;

            if ($distance > $allowedRadius) {
                throw ValidationException::withMessages([
                    'location' => "Anda berada {$distance}m dari lokasi liputan. Harap mendekat ke lokasi ({$allowedRadius}m).",
                ]);
            }
        }
        */

        // 2. Check Existing Attendance
        $todayAttendance = Absensi::where('user_id', $assignment->reporter_id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if ($todayAttendance) {
            // Already checked in (office or other field duty)
            // Log this specific assignment visit if needed, or just update status
            // For now, we just ensure open status is maintained or switch assignment context?
            // The requirement says: "If YES: Update existing record or log".
            // We will just return existing, maybe append note.
            $todayAttendance->update([
                'assignment_id' => $assignment->id, // Switch context to this assignment
                'note' => $todayAttendance->note . " | On Site: " . $assignment->title,
            ]);

            return $todayAttendance;
        }

        return Absensi::create([
            'user_id' => $assignment->reporter_id,
            'attendance_type' => 'field',
            'assignment_id' => $assignment->id,
            'tanggal' => Carbon::today(),
            'jam_masuk' => Carbon::now()->toTimeString(),
            'status' => 'open',
            'legacy_status' => 'hadir',
            'lat' => $currentLat,
            'lng' => $currentLng,
            'accuracy' => $accuracy,
            'note' => 'Auto-checkin via Assignment: ' . $assignment->title
        ]);
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000;
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);
        $dLat = $lat2 - $lat1;
        $dLng = $lng2 - $lng1;
        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c);
    }
}
