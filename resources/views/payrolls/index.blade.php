@extends('layouts.app')

@section('title', 'Payroll / Penggajian')

@section('content')
<div class="space-y-6">
    {{-- Header & Stats --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Penggajian (Payroll)</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola dan pantau gaji karyawan.</p>
        </div>

        @role('direktur')
        <a href="{{ route('payrolls.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 transition-all hover:-translate-y-0.5 font-medium text-sm">
            <i class="fas fa-plus"></i>
            Input Gaji Baru
        </a>
        @endrole
    </div>

    {{-- Filter Section (For Director) --}}
    @role('direktur')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form action="{{ route('payrolls.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-48">
                <label for="month" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bulan</label>
                <select name="month" id="month" onchange="this.form.submit()" 
                        class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20 text-sm">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div class="w-full md:w-32">
                <label for="year" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tahun</label>
                <select name="year" id="year" onchange="this.form.submit()" 
                        class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20 text-sm">
                    @for($y=2024; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
    @endrole

    {{-- Data Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($payrolls as $payroll)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow relative overflow-hidden group">
            {{-- Status Badge --}}
            <div class="absolute top-4 right-4">
                @if($payroll->status == 'paid')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-50 text-green-600 border border-green-200">
                        <i class="fas fa-check-circle"></i> Paid
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <i class="fas fa-clock"></i> Draft
                    </span>
                @endif
            </div>

            <div class="p-6">
                {{-- User Info --}}
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold border border-slate-200">
                        {{ substr($payroll->user->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 truncate">{{ $payroll->user->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $payroll->user->profile->position->name ?? 'Pegawai' }}</p>
                    </div>
                </div>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Periode</span>
                        <span class="font-medium text-slate-700">{{ $payroll->period_end->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Gaji Pokok</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                    </div>
                    @if($payroll->bonus > 0 || $payroll->deductions > 0)
                    <div class="flex justify-between items-center text-xs text-slate-400 pt-2 border-t border-dashed border-gray-100">
                        <span>Bonus / Potongan</span>
                        <div class="flex gap-2">
                            @if($payroll->bonus > 0) <span class="text-green-600">+{{ number_format($payroll->bonus/1000, 0) }}k</span> @endif
                            @if($payroll->deductions > 0) <span class="text-red-600">-{{ number_format($payroll->deductions/1000, 0) }}k</span> @endif
                        </div>
                    </div>
                    @endif
                    
                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-bold text-gray-900 text-sm">Total Terima</span>
                        <span class="font-bold text-orange-600 text-lg">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div class="flex gap-2 mt-4">
                    @role('direktur')
                        <a href="{{ route('payrolls.show', $payroll) }}" class="flex-1 text-center py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            Manage
                        </a>
                    @else
                        <a href="{{ route('payrolls.show', $payroll) }}" class="flex-1 text-center py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                            Detail
                        </a>
                    @endrole
                    
                    @if($payroll->status == 'paid')
                        <a href="{{ route('payrolls.print', $payroll) }}" target="_blank" class="px-3 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors" title="Print Slip">
                            <i class="fas fa-print"></i>
                        </a>
                    @else
                        <button disabled class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed" title="Draft (Cannot Print)">
                            <i class="fas fa-print"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-slate-400 text-2xl"></i>
            </div>
            <h3 class="text-gray-900 font-medium mb-1">Belum ada data payroll.</h3>
            <p class="text-slate-500 text-sm">Data penggajian akan muncul di sini.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $payrolls->withQueryString()->links() }}
    </div>
</div>
@endsection