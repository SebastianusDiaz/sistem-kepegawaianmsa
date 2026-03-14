<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Assignment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    protected $archiveService;

    public function __construct(\App\Services\ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    const OFFICE_LAT = -6.175392;
    const OFFICE_LNG = 106.827153;
    const OFFICE_GEOFENCE_RADIUS_METERS = 30;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole(['admin', 'direktur'])) {
            $absensis = Absensi::with('user', 'assignment')->latest()->get();
        } else {
            $absensis = Absensi::with('user', 'assignment')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        $todayAttendance = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today())
            ->latest()
            ->first();

        $hasOpenAttendance = $todayAttendance && $todayAttendance->status === 'open';
        $activeAssignments = Assignment::whereIn('status', ['assigned', 'accepted', 'on_site'])
            ->where('reporter_id', $user->id)
            ->get();

        return view('absensi.index', compact('absensis', 'todayAttendance', 'hasOpenAttendance', 'activeAssignments'));
    }

    /**
     * Show the form for creating a new resource (Admin Manual Entry).
     */
    public function create()
    {
        if (!Auth::user()->hasRole('admin'))
            abort(403);
        $users = User::all();
        $assignments = Assignment::whereIn('status', ['assigned', 'accepted', 'on_site'])->get();
        return view('absensi.create', compact('users', 'assignments'));
    }

    /**
     * Store a newly created resource in storage (Admin Manual Entry).
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin'))
            abort(403);
        $request->validate([
            'user_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required',
        ]);

        $data = $request->all();
        $data['legacy_status'] = $request->status;
        $data['status'] = 'manual_edit';

        if ($request->jam_masuk && $request->jam_keluar) {
            $start = Carbon::parse($request->timestamp . ' ' . $request->jam_masuk);
            $end = Carbon::parse($request->jam_keluar);
            $data['worked_minutes'] = $end->diffInMinutes($start);
        }

        Absensi::create($data);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil ditambahkan');
    }

    /**
     * Check-In Logic
     */
    public function checkIn(Request $request, \App\Actions\HandleFieldAttendanceAction $handleFieldAttendance)
    {
        $request->validate([
            'attendance_type' => 'required|in:office,field',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
            'assignment_id' => 'nullable|exists:assignments,id',
            'manual_assignment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = Auth::user();

        // Common check for open attendance
        if ($this->hasOpenAttendance($user->id)) {
            return back()->withErrors(['message' => 'Anda sudah check-in sebelumnya.']);
        }

        if ($request->attendance_type === 'field') {
            // FIELD LOGIC

            if ($request->assignment_id) {
                // OPTION 1: Assignment Selection (Existing Logic)
                if (!$this->hasActiveAssignment($user->id, $request->assignment_id)) {
                    return back()->withErrors(['message' => 'Surat Tugas tidak valid atau tidak aktif.']);
                }

                $assignment = Assignment::find($request->assignment_id);

                try {
                    $handleFieldAttendance->execute(
                        $assignment,
                        $request->lat ?? 0,
                        $request->lng ?? 0,
                        $request->accuracy ?? 0
                    );
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return back()->withErrors($e->errors());
                }

            } elseif ($request->hasFile('manual_assignment')) {
                // OPTION 2: Manual Surat Upload
                if (!$request->lat || !$request->lng) {
                    return back()->withErrors(['message' => 'Lokasi diperlukan untuk Absensi Lapangan.']);
                }

                // Store File using ArchiveService
                $archive = $this->archiveService->store(
                    $request->file('manual_assignment'),
                    'Manual Attendance Evidence', // Category
                    $user->id
                );

                Absensi::create([
                    'user_id' => $user->id,
                    'attendance_type' => 'field',
                    'tanggal' => Carbon::today(),
                    'jam_masuk' => Carbon::now()->toTimeString(),
                    'status' => 'open',
                    'legacy_status' => 'hadir',
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'accuracy' => $request->accuracy ?? 0,
                    'note' => 'Manual Upload',
                    'evidence_path' => 'storage/' . $archive->file_path, // Store the path generally used
                ]);

            } else {
                return back()->withErrors(['message' => 'Pilih Surat Tugas atau Upload Bukti Surat Tugas untuk Absensi Lapangan.']);
            }

        } else {
            // OFFICE LOGIC (Keep existing manual)
            if (!$request->lat || !$request->lng || !$request->accuracy) {
                return back()->withErrors(['message' => 'Lokasi diperlukan untuk Absensi Kantor.']);
            }

            if (!$this->validateOfficeGeofence($request->lat, $request->lng, $request->accuracy)) {
                return back()->withErrors(['message' => 'Anda berada di luar jangkauan kantor (Radius ' . self::OFFICE_GEOFENCE_RADIUS_METERS . 'm).']);
            }

            Absensi::create([
                'user_id' => $user->id,
                'attendance_type' => 'office',
                'tanggal' => Carbon::today(),
                'jam_masuk' => Carbon::now()->toTimeString(),
                'status' => 'open',
                'legacy_status' => 'hadir',
                'lat' => $request->lat,
                'lng' => $request->lng,
                'accuracy' => $request->accuracy,
            ]);
        }

        return redirect()->back()->with('success', 'Check-in Berhasil');
    }

    /**
     * Check-Out Logic
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasOpenAttendance($user->id)) {
            return back()->withErrors(['message' => 'Tidak ada absensi yang aktif.']);
        }

        $attendance = Absensi::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $checkInTime = Carbon::createFromFormat('H:i:s', $attendance->jam_masuk);
        $checkOutTime = Carbon::now();

        $attendance->update([
            'jam_keluar' => $checkOutTime->toTimeString(),
            'status' => 'closed',
            'worked_minutes' => $this->calculateWorkedMinutes($checkInTime, $checkOutTime),
            'note' => $attendance->note ? $attendance->note . ' | ' . $request->note : $request->note,
        ]);

        return redirect()->back()->with('success', 'Check-out Berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(Absensi $absensi)
    {
        return view('absensi.show', compact('absensi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absensi $absensi)
    {
        if (!Auth::user()->hasRole('admin'))
            abort(403);
        $users = User::all();
        return view('absensi.edit', compact('absensi', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absensi $absensi)
    {
        if (!Auth::user()->hasRole('admin'))
            abort(403);
        $request->validate([
            'tanggal' => 'required|date',
            // 'status'  => 'required', // Status handling needed
        ]);

        $data = $request->all();
        if ($request->has('status')) {
            $data['legacy_status'] = $request->status; // map old form status
        }
        $data['status'] = 'manual_edit'; // Force manual edit status on update

        $absensi->update($data);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absensi $absensi)
    {
        if (!Auth::user()->hasRole('admin'))
            abort(403);
        $absensi->delete();
        return back()->with('success', 'Absensi berhasil dihapus');
    }

    // ================= PRIVATE HELPERS =================

    private function validateOfficeGeofence($lat, $lng, $accuracy)
    {
        $distance = $this->calculateDistance($lat, $lng, self::OFFICE_LAT, self::OFFICE_LNG);

        // Use accuracy to give some buffer, but strictly must be within range + accuracy check?
        // User says: "accuracy <= 100m (office only)".
        // And "Must be inside office geofence (<= 100m)".

        if ($accuracy > 100) {
            return false;
        }

        return $distance <= self::OFFICE_GEOFENCE_RADIUS_METERS;
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

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function calculateWorkedMinutes($checkIn, $checkOut)
    {
        return $checkOut->diffInMinutes($checkIn);
    }

    private function hasOpenAttendance($userId)
    {
        return Absensi::where('user_id', $userId)
            ->where('status', 'open')
            ->exists();
    }

    private function hasActiveAssignment($userId, $assignmentId = null)
    {
        $query = Assignment::where('reporter_id', $userId)
            ->whereIn('status', ['assigned', 'accepted', 'on_site']);

        if ($assignmentId) {
            $query->where('id', $assignmentId);
        }

        return $query->exists();
    }
}
