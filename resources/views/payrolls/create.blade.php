@extends('layouts.app')

@section('title', 'Input Payroll Baru')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('payrolls.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Input Gaji Baru</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('payrolls.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                {{-- Karyawan --}}
                <div>
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Karyawan</label>
                    <select name="user_id" id="user_id" required
                        class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} - {{ $employee->getRoleNames()->first() }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Bulan --}}
                    <div>
                        <label for="month" class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                        <select name="month" id="month" required
                            class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div>
                        <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                        <select name="year" id="year" required
                            class="w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20">
                            @for($y = 2024; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Gaji Pokok --}}
                <div>
                    <label for="base_salary" class="block text-sm font-semibold text-gray-700 mb-2">Gaji Pokok (Base
                        Salary)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary') }}" required
                            min="0"
                            class="w-full pl-10 rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500/20"
                            placeholder="Contoh: 5000000">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Gaji pokok sebelum bonus dan potongan.</p>
                    @error('base_salary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 bg-orange-600 text-white font-medium rounded-xl hover:bg-orange-700 transition-colors shadow-lg shadow-orange-500/20">
                        <i class="fas fa-save mr-2"></i> Simpan & Lanjut Detail
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection