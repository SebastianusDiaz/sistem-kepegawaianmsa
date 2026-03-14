@extends('layouts.app')

@section('title', 'Berita Terbit')
@section('page_title', 'Berita Terbit')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Berita Terbit (Published)
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Daftar penugasan liputan yang telah disetujui dan diterbitkan.
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('assignments.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Penugasan
                </a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($assignments as $task)
                <article
                    class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-green-200 ring-1 ring-green-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 overflow-hidden">

                    {{-- Status Banner --}}
                    <div class="absolute top-0 right-0 p-0">
                        <div class="bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                            Published
                        </div>
                    </div>

                    <div class="p-5 flex-1">
                        <div class="mb-2 text-xs font-semibold text-green-600 uppercase tracking-wide">
                            {{ $task->created_at->format('d M Y') }}
                        </div>
                        <a href="{{ route('assignments.show', ['assignment' => $task->id, 'from' => 'published']) }}"
                            class="block group-hover:text-green-700 transition-colors">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2">{{ $task->title }}</h3>
                        </a>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $task->description }}</p>

                        <div class="space-y-2 text-sm text-gray-500 mt-auto">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ Str::limit($task->location_name, 30) }}
                            </div>
                            <div class="flex items-center border-t border-gray-100 pt-2 mt-2">
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400">Reporter</p>
                                    <p class="text-xs font-medium text-gray-900">{{ $task->reporter->name ?? 'Unknown' }}</p>
                                </div>
                                @if($task->editor)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-400">Editor</p>
                                        <p class="text-xs font-medium text-gray-900">{{ $task->editor->name ?? '-' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 px-5 py-3 border-t border-green-100 flex justify-between items-center">
                        @if($task->evidence_link)
                            <a href="{{ $task->evidence_link }}" target="_blank"
                                class="text-green-700 hover:text-green-800 text-xs font-semibold flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Link Berita
                            </a>
                        @else
                            <span class="text-gray-400 text-xs italic">Link tidak tersedia</span>
                        @endif

                        <a href="{{ route('assignments.show', ['assignment' => $task->id, 'from' => 'published']) }}"
                            class="text-gray-600 hover:text-gray-900 text-xs font-medium">Detail ></a>
                    </div>
                </article>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-12 text-center bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada berita terbit</h3>
                    <p class="mt-1 text-sm text-gray-500">Penugasan yang telah disetujui akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection