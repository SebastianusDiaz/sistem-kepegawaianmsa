@extends('layouts.app')

@section('title', 'Review Penugasan')
@section('page_title', 'Review Penugasan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Review Penugasan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Daftar penugasan yang menunggu review dan persetujuan Editor.
                </p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($assignments as $task)
                <article
                    class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-orange-200 ring-1 ring-orange-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 overflow-hidden">

                    <div class="absolute top-0 right-0 p-0">
                        <div
                            class="{{ $task->status === 'revision' ? 'bg-yellow-500' : 'bg-orange-600' }} text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                            {{ $task->status === 'revision' ? 'Revisi' : 'Submitted' }}
                        </div>
                    </div>

                    <div class="p-5 flex-1">
                        <div
                            class="mb-2 text-xs font-semibold {{ $task->status === 'revision' ? 'text-yellow-600' : 'text-orange-600' }} uppercase tracking-wide">
                            {{ $task->status === 'revision' ? 'Menunggu Revisi Wartawan' : 'Menunggu Review' }}
                        </div>
                        <a href="{{ route('reviews.show', $task->id) }}"
                            class="block group-hover:text-orange-700 transition-colors">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2">{{ $task->title }}</h3>
                        </a>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $task->description }}</p>

                        <div class="space-y-2 text-sm text-gray-500 mt-auto">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Wartawan: <span
                                    class="font-medium text-gray-900 ml-1">{{ $task->reporter->name ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Diajukan {{ $task->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 px-5 py-3 border-t border-orange-100">
                        <a href="{{ route('reviews.show', $task->id) }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Mulai Review
                        </a>
                    </div>
                </article>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-12 text-center bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Semua aman!</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada penugasan yang menunggu review saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection