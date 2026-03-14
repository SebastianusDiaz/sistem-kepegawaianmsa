@extends('layouts.app')

@section('title', 'Review: ' . $assignment->title)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center justify-between">
            <a href="{{ route('reviews.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Antrian Review
            </a>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- LEFT COLUMN: Assignment Content (Read Only) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Content Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                {{ $assignment->status }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $assignment->created_at->format('d M Y, H:i') }}</span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 leading-tight">
                            {{ $assignment->title }}
                        </h1>

                        <div class="prose prose-orange max-w-none text-gray-600">
                            <h3 class="text-gray-900 text-lg font-semibold">Deskripsi & Brief</h3>
                            <p class="whitespace-pre-line">{{ $assignment->description }}</p>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Lokasi</h4>
                                <p class="text-sm font-medium text-gray-900">{{ $assignment->location_name }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deadline</h4>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $assignment->deadline->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EVIDENCE / HASIL LIPUTAN --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Hasil Liputan (Submitted)</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        @if($assignment->evidence_photo)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Cover</h4>
                                <img src="{{ asset($assignment->evidence_photo) }}"
                                    class="rounded-lg shadow-md max-w-sm max-h-64 object-cover">
                            </div>
                        @endif

                        @if($assignment->evidence_document)
                            <div class="flex items-center p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h5 class="text-sm font-bold text-gray-900">Dokumen / Press Release</h5>
                                    <a href="{{ asset($assignment->evidence_document) }}" target="_blank"
                                        class="text-blue-600 hover:underline text-sm font-medium">Download File</a>
                                </div>
                            </div>
                        @endif

                        @if($assignment->evidence_link)
                            <div class="flex items-center p-4 bg-gray-50 border border-gray-100 rounded-xl">
                                <div class="ml-4 flex-1">
                                    <h5 class="text-sm font-bold text-gray-900">Link Eksternal</h5>
                                    <a href="{{ $assignment->evidence_link }}" target="_blank"
                                        class="text-blue-600 hover:underline text-sm">{{ $assignment->evidence_link }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Document History --}}
                @if($assignment->attachments->count() > 0)
                    <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-bold text-gray-700">Riwayat Versi File</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($assignment->attachments as $attachment)
                                <li class="px-6 py-3 flex justify-between items-center text-sm">
                                    <span class="text-gray-600">{{ $attachment->file_type }} -
                                        {{ $attachment->created_at->format('d/m/Y H:i') }}</span>
                                    <a href="{{ asset($attachment->file_path) }}" target="_blank"
                                        class="text-blue-600 hover:underline">Download</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>

            {{-- RIGHT COLUMN: Review Console --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Review Action Card --}}
                <div class="bg-white rounded-2xl shadow-lg border border-orange-100 overflow-hidden sticky top-6">
                    <div class="bg-black px-6 py-3">
                        <h3 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Review Console
                        </h3>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('reviews.store', $assignment->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tindakan Review</label>
                                <select name="action" id="actionSelect"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                    onchange="toggleReason(this.value)">
                                    <option value="approve">Setujui & Publish (Approve)</option>
                                    <option value="revision">Minta Revisi (Request Revision)</option>
                                    <option value="comment">Kirim Komentar Saja (Comment)</option>
                                </select>
                            </div>

                            <div id="publishOptions"
                                class="hidden space-y-4 mb-4 border-l-4 border-green-500 pl-4 bg-green-50 py-3 pr-2 rounded-r-md">
                                <h4 class="text-xs font-bold text-green-800 uppercase mb-2">Opsi Publikasi Final</h4>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Link Berita Publish
                                    </label>
                                    <input type="url" name="final_link" placeholder="https://..."
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Ganti File Press Release (Final)
                                    </label>
                                    <input type="file" name="final_document" accept=".pdf,.doc,.docx"
                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-100 file:text-green-700 hover:file:bg-green-200">
                                    <p class="text-[10px] text-gray-500 mt-1">Upload ini akan <b>menggantikan</b> file draft
                                        wartawan.</p>
                                </div>
                            </div>

                            <div id="commentSection" class="hidden mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan / Komentar <span
                                        id="reqInfo" class="text-red-500 hidden">*</span></label>
                                <textarea name="message" rows="4"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                    placeholder="Tuliskan catatan untuk wartawan..."></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran (Opsional)</label>
                                <input type="file" name="file"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            </div>

                            <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                Submit Review
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Discussion History --}}
                @if($assignment->discussions->count() > 0)
                    <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-sm font-bold text-gray-700">Riwayat Diskusi</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto p-4 space-y-4">
                            @foreach($assignment->discussions as $msg)
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ substr($msg->user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div
                                        class="flex-1 bg-white p-3 rounded-lg shadow-sm {{ $msg->type === 'revision_request' ? 'border-l-4 border-red-500' : '' }}">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-bold text-gray-900">{{ $msg->user->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $msg->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $msg->message }}</p>
                                        @if($msg->type === 'revision_request')
                                            <p class="text-xs text-red-600 font-semibold mt-1 uppercase">Revisi Diminta</p>
                                        @endif
                                        @if($msg->file_path)
                                            <a href="{{ asset('storage/' . $msg->file_path) }}" target="_blank"
                                                class="block mt-2 text-xs text-blue-600 hover:underline">
                                                <i class="fas fa-paperclip"></i> Lihat Lampiran
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function toggleReason(val) {
            const commentSection = document.getElementById('commentSection');
            const reqInfo = document.getElementById('reqInfo');

            // Show comment section for revision or comment
            if (val === 'revision' || val === 'comment') {
                commentSection.classList.remove('hidden');
            } else {
                // Optional for approve, but maybe hidden by default to streamline
                commentSection.classList.add('hidden');
            }

            // Require message if revision
            if (val === 'revision' || val === 'comment') {
                reqInfo.classList.remove('hidden');
            } else {
                reqInfo.classList.add('hidden');
            }

            // Publish Options default to hidden
            const publishOptions = document.getElementById('publishOptions');
            if (val === 'approve') {
                publishOptions.classList.remove('hidden');
            } else {
                publishOptions.classList.add('hidden');
            }
        }

        // Init
        toggleReason(document.getElementById('actionSelect').value);
    </script>
@endsection