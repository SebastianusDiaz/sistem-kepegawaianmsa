@extends('layouts.app')

@section('title', 'Arsip Digital')

@section('content')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Arsip Digital</h1>
                <p class="text-sm text-gray-500 mt-1">Repository penyimpanan file terpusat</p>
            </div>

            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Upload File Baru</span>
            </button>
        </div>

        <!-- Filters & Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Box -->
            <div class="md:col-span-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <form action="{{ route('archives.index') }}" method="GET" class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama file atau sumber..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- File List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-medium">
                            <th class="px-6 py-4">Nama File</th>
                            <th class="px-6 py-4">Ukuran</th>
                            <th class="px-6 py-4">Diunggah Oleh</th>
                            <th class="px-6 py-4">Sumber</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($archives as $archive)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-lg shrink-0">
                                            @php
                                                $icon = 'fa-file';
                                                if (Str::contains($archive->mime_type, 'image'))
                                                    $icon = 'fa-file-image';
                                                elseif (Str::contains($archive->mime_type, 'pdf'))
                                                    $icon = 'fa-file-pdf';
                                                elseif (Str::contains($archive->mime_type, 'word'))
                                                    $icon = 'fa-file-word';
                                                elseif (Str::contains($archive->mime_type, 'excel') || Str::contains($archive->mime_type, 'spreadsheet'))
                                                    $icon = 'fa-file-excel';
                                            @endphp
                                            <i class="fas {{ $icon }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate max-w-[200px]"
                                                title="{{ $archive->original_name }}">
                                                {{ $archive->original_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">{{ $archive->mime_type }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $archive->human_reable_size }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                            {{ substr($archive->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="truncate max-w-[120px]">{{ $archive->user->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $archive->source }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $archive->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('archives.download', $archive) }}"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if(Auth::user()->id === $archive->user_id || Auth::user()->hasRole('admin'))
                                            <form action="{{ route('archives.destroy', $archive) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" data-confirm="Apakah Anda yakin ingin menghapus file ini?"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-base font-medium text-gray-500">Belum ada file arsip</p>
                                        <p class="text-sm">Silakan upload file baru untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $archives->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal"
        class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity"
        onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Upload File Baru</h3>
                <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('archives.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File</label>
                        <div class="relative group">
                            <input type="file" name="file" required class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                cursor-pointer border border-gray-200 rounded-lg">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Max size: 10MB. Allowed: All types.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm shadow-blue-500/30">
                        Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection