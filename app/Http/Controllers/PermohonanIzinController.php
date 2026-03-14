<?php

namespace App\Http\Controllers;

use App\Models\PermohonanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanIzinController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole(['admin', 'direktur'])) {
            $permohonanIzins = PermohonanIzin::with('user', 'approver')->latest()->get();
        } else {
            $permohonanIzins = PermohonanIzin::with('user', 'approver')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('permohonan_izin.index', compact('permohonanIzins'));
    }

    public function create()
    {
        return view('permohonan_izin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_izin' => 'required|in:cuti,izin,sakit',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
        ]);

        PermohonanIzin::create([
            'user_id' => Auth::id(),
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return redirect()->route('permohonan-izin.index')
            ->with('success', 'Permohonan izin berhasil diajukan dan menunggu persetujuan.');
    }

    public function show(PermohonanIzin $permohonanIzin)
    {
        $permohonanIzin->load('user', 'approver');
        return view('permohonan_izin.show', compact('permohonanIzin'));
    }

    public function edit(PermohonanIzin $permohonanIzin)
    {
        if ($permohonanIzin->status !== 'pending') {
            return redirect()->route('permohonan-izin.index')
                ->withErrors(['message' => 'Permohonan yang sudah diproses tidak dapat diedit.']);
        }

        if ($permohonanIzin->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return view('permohonan_izin.edit', compact('permohonanIzin'));
    }

    public function update(Request $request, PermohonanIzin $permohonanIzin)
    {
        if ($permohonanIzin->status !== 'pending') {
            return redirect()->route('permohonan-izin.index')
                ->withErrors(['message' => 'Permohonan yang sudah diproses tidak dapat diedit.']);
        }

        if ($permohonanIzin->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'jenis_izin' => 'required|in:cuti,izin,sakit',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
        ]);

        $permohonanIzin->update($request->only([
            'jenis_izin',
            'tanggal_mulai',
            'tanggal_selesai',
            'alasan',
        ]));

        return redirect()->route('permohonan-izin.index')
            ->with('success', 'Permohonan izin berhasil diperbarui.');
    }

    public function destroy(PermohonanIzin $permohonanIzin)
    {
        if ($permohonanIzin->status !== 'pending') {
            return redirect()->route('permohonan-izin.index')
                ->withErrors(['message' => 'Permohonan yang sudah diproses tidak dapat dihapus.']);
        }

        if ($permohonanIzin->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $permohonanIzin->delete();

        return redirect()->route('permohonan-izin.index')
            ->with('success', 'Permohonan izin berhasil dihapus.');
    }

    public function approve(PermohonanIzin $permohonanIzin)
    {
        if (!Auth::user()->hasRole('direktur')) {
            abort(403, 'Hanya Direktur yang dapat menyetujui permohonan.');
        }

        if ($permohonanIzin->status !== 'pending') {
            return redirect()->route('permohonan-izin.show', $permohonanIzin)
                ->withErrors(['message' => 'Permohonan ini sudah diproses sebelumnya.']);
        }

        $permohonanIzin->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('permohonan-izin.show', $permohonanIzin)
            ->with('success', 'Permohonan izin berhasil disetujui.');
    }

    public function reject(Request $request, PermohonanIzin $permohonanIzin)
    {
        if (!Auth::user()->hasRole('direktur')) {
            abort(403, 'Hanya Direktur yang dapat menolak permohonan.');
        }

        if ($permohonanIzin->status !== 'pending') {
            return redirect()->route('permohonan-izin.show', $permohonanIzin)
                ->withErrors(['message' => 'Permohonan ini sudah diproses sebelumnya.']);
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|min:10',
        ]);

        $permohonanIzin->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()->route('permohonan-izin.show', $permohonanIzin)
            ->with('success', 'Permohonan izin berhasil ditolak.');
    }
}
