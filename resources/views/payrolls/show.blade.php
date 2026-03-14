@extends('layouts.app')

@section('title', 'Detail Gaji')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('payrolls.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Payroll</h1>
                    <p class="text-slate-500 text-sm">Periode: {{ $payroll->period_end->translatedFormat('F Y') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($payroll->status == 'paid')
                    <a href="{{ route('payrolls.print', $payroll) }}" target="_blank"
                        class="px-4 py-2 bg-slate-800 text-white font-medium rounded-xl hover:bg-slate-700 transition-colors shadow-lg shadow-slate-800/20">
                        <i class="fas fa-print mr-2"></i> Cetak Slip
                    </a>
                @else
                    <button disabled class="px-4 py-2 bg-gray-100 text-gray-400 font-medium rounded-xl cursor-not-allowed">
                        <i class="fas fa-print mr-2"></i> Cetak (Draft)
                    </button>
                @endif

                @role('direktur')
                @if($payroll->status == 'draft')
                    <form action="{{ route('payrolls.sync', $payroll) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-blue-100 text-blue-600 font-medium rounded-xl hover:bg-blue-200 transition-colors"
                            title="Hitung Ulang Potongan Cuti/Izin">
                            <i class="fas fa-sync-alt mr-2"></i> Sinkronisasi
                        </button>
                    </form>

                    <form action="{{ route('payrolls.markAsPaid', $payroll) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin data sudah benar? Status akan berubah menjadi Paid.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                            <i class="fas fa-check-circle mr-2"></i> Tandai Lunas (Paid)
                        </button>
                    </form>
                @endif
                <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST"
                    onsubmit="return confirm('Hapus data payroll ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-100 text-red-600 font-medium rounded-xl hover:bg-red-200 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endrole
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Detail Card --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Employee Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-3">
                        Informasi Karyawan</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Nama</label>
                            <p class="font-medium text-gray-900">{{ $payroll->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Jabatan</label>
                            <p class="font-medium text-gray-900">{{ $payroll->user->profile->position->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Divisi</label>
                            <p class="font-medium text-gray-900">{{ $payroll->user->profile->division->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Status Gaji</label>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $payroll->status == 'paid' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Salary Breakdown --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <i class="fas fa-coins text-9xl"></i>
                    </div>

                    <h3
                        class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-3 relative z-10">
                        Rincian Penerimaan</h3>

                    <div class="space-y-4 relative z-10">
                        {{-- Base Salary --}}
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div>
                                <p class="font-semiboild text-gray-700">Gaji Pokok</p>
                                @role('direktur')
                                <button onclick="document.getElementById('edit-base-salary').classList.toggle('hidden')"
                                    class="text-xs text-orange-600 hover:underline mt-1">Ubah Gaji Pokok</button>
                                <form id="edit-base-salary" action="{{ route('payrolls.update', $payroll) }}" method="POST"
                                    class="hidden mt-2 flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="{{ $payroll->status }}">
                                    <input type="hidden" name="bonus" value="{{ $payroll->bonus }}">
                                    <input type="hidden" name="deductions" value="{{ $payroll->deductions }}">
                                    <input type="number" name="base_salary" value="{{ $payroll->base_salary }}"
                                        class="w-32 text-xs rounded-lg border-gray-300">
                                    <button type="submit"
                                        class="px-3 py-1 bg-orange-600 text-white rounded-lg text-xs">Simpan</button>
                                </form>
                                @endrole
                            </div>
                            <span class="font-bold text-gray-900">Rp
                                {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                        </div>

                        {{-- Bonuses --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Bonus / Tunjangan</h4>
                            <div class="space-y-2">
                                @forelse($payroll->deduction_details ?? [] as $index => $item)
                                    @if(($item['type'] ?? '') == 'bonus')
                                        <div
                                            class="flex justify-between items-center p-3 rounded-lg border border-green-100 bg-green-50/50">
                                            <div class="flex items-center gap-2">
                                                @role('direktur')
                                                <form action="{{ route('payrolls.update', $payroll) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <!-- Keep required fields -->
                                                    <input type="hidden" name="base_salary" value="{{ $payroll->base_salary }}">
                                                    <input type="hidden" name="status" value="{{ $payroll->status }}">
                                                    <input type="hidden" name="bonus" value="{{ $payroll->bonus }}">
                                                    <input type="hidden" name="deductions" value="{{ $payroll->deductions }}">

                                                    <input type="hidden" name="remove_item_index" value="{{ $index }}">
                                                    <button type="submit" class="text-red-400 hover:text-red-600"><i
                                                            class="fas fa-times-circle"></i></button>
                                                </form>
                                                @endrole
                                                <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                                            </div>
                                            <span class="text-sm font-bold text-green-600">+ Rp
                                                {{ number_format($item['amount'], 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-sm text-slate-400 italic pl-2">Tidak ada bonus.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Deductions --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Potongan</h4>
                            <div class="space-y-2">
                                @forelse($payroll->deduction_details ?? [] as $index => $item)
                                    @if(($item['type'] ?? '') == 'deduction')
                                        <div
                                            class="flex justify-between items-center p-3 rounded-lg border border-red-100 bg-red-50/50">
                                            <div class="flex items-center gap-2">
                                                @role('direktur')
                                                <form action="{{ route('payrolls.update', $payroll) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="base_salary" value="{{ $payroll->base_salary }}">
                                                    <input type="hidden" name="status" value="{{ $payroll->status }}">
                                                    <input type="hidden" name="bonus" value="{{ $payroll->bonus }}">
                                                    <input type="hidden" name="deductions" value="{{ $payroll->deductions }}">

                                                    <input type="hidden" name="remove_item_index" value="{{ $index }}">
                                                    <button type="submit" class="text-red-400 hover:text-red-600"><i
                                                            class="fas fa-times-circle"></i></button>
                                                </form>
                                                @endrole
                                                <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                                            </div>
                                            <span class="text-sm font-bold text-red-600">- Rp
                                                {{ number_format($item['amount'], 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-sm text-slate-400 italic pl-2">Tidak ada potongan.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Total --}}
                        <div
                            class="flex justify-between items-center bg-orange-50 p-6 rounded-2xl border border-orange-100 mt-6">
                            <span class="font-bold text-gray-900">Take Home Pay</span>
                            <span class="font-black text-2xl text-orange-600">Rp
                                {{ number_format($payroll->net_salary, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Add Item Sidebar (Director Only) --}}
            @role('direktur')
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-3">
                        Tambah Rincian</h3>
                    <form action="{{ route('payrolls.update', $payroll) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Hidden required fields to pass validation since we reuse update method --}}
                        <input type="hidden" name="base_salary" value="{{ $payroll->base_salary }}">
                        <input type="hidden" name="status" value="{{ $payroll->status }}">
                        <input type="hidden" name="bonus" value="{{ $payroll->bonus }}">
                        <input type="hidden" name="deductions" value="{{ $payroll->deductions }}">
                        <input type="hidden" name="add_item" value="1">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tipe</label>
                            <select name="item_type"
                                class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500/20">
                                <option value="bonus">Bonus / Tunjangan (+)</option>
                                <option value="deduction">Potongan (-)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                            <input type="text" name="item_name" required placeholder="Contoh: Lembur, Terlambat"
                                class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500/20">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah (Rp)</label>
                            <input type="number" name="item_amount" required min="0" placeholder="0"
                                class="w-full rounded-lg border-gray-200 text-sm focus:border-orange-500 focus:ring-orange-500/20">
                        </div>

                        <button type="submit"
                            class="w-full py-2 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-black transition-colors">
                            Tambahkan
                        </button>
                    </form>
                </div>
            </div>
            @endrole
        </div>
    </div>
@endsection