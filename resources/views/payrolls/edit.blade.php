@extends('layouts.app')

@section('title', 'Edit Payroll')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('payrolls.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 font-medium flex items-center">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Edit Gaji: {{ $payroll->user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">Periode: {{ $payroll->period_start->format('d M Y') }} -
                        {{ $payroll->period_end->format('d M Y') }}</p>
                </div>
                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $payroll->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                    {{ strtoupper($payroll->status) }}
                </span>
            </div>

            <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Base Salary --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gaji Dasar</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="base_salary" value="{{ old('base_salary', $payroll->base_salary) }}"
                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md"
                                placeholder="0">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                        <select name="status"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="draft" {{ $payroll->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="paid" {{ $payroll->status === 'paid' ? 'selected' : '' }}>Paid / Selesai</option>
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Bonus --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bonus / Tambahan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 sm:text-sm font-bold">+ Rp</span>
                            </div>
                            <input type="number" name="bonus" value="{{ old('bonus', $payroll->bonus) }}"
                                class="focus:ring-green-500 focus:border-green-500 block w-full pl-16 sm:text-sm border-gray-300 rounded-md"
                                placeholder="0">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Insentif atau bonus kinerja.</p>
                    </div>

                    {{-- Deductions --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Potongan (Manual Adjust)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-red-500 sm:text-sm font-bold">- Rp</span>
                            </div>
                            <input type="number" name="deductions" value="{{ old('deductions', $payroll->deductions) }}"
                                class="focus:ring-red-500 focus:border-red-500 block w-full pl-16 sm:text-sm border-gray-300 rounded-md"
                                placeholder="0">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Total potongan (termasuk absensi).</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center mt-6">
                    <span class="font-medium text-gray-700">Estimasi Net Salary:</span>
                    <span class="text-xl font-bold text-gray-900">
                        Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                    </span>
                </div>

                <div class="pt-6 flex justify-end space-x-3">
                    <a href="{{ route('payrolls.index') }}"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 rounded-lg text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection