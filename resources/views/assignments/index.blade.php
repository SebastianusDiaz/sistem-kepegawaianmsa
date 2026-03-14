@extends('layouts.app')

@section('title', 'Daftar Penugasan')
@section('page_title', 'Daftar Penugasan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Penugasan Liputan
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan pantau penugasan liputan tim di lapangan.
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('assignments.published') }}" 
                   class="inline-flex items-center px-4 py-2 border border-green-200 rounded-lg shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 mr-3">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Berita Terbit
                </a>

                @if(Auth::user()->hasRole('wartawan'))
                     <a href="{{ route('assignments.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Laporan Mandiri
                    </a>
                @endif

                @if(!Auth::user()->hasRole('wartawan'))
                    <a href="{{ route('assignments.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Penugasan
                    </a>
                @endif
            </div>
        </div>

        {{-- Grid Content --}}

        {{-- OPEN ASSIGNMENTS SECTION (For Reporters) --}}
        @if(isset($openAssignments) && $openAssignments->count() > 0 && Auth::user()->hasRole('wartawan'))
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <span class="bg-green-100 text-green-800 p-1 rounded mr-2">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        Penugasan Tersedia (Ambil Sendiri)
                    </h3>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($openAssignments as $task)
                        @php
                            $priorityColor = match($task->priority) {
                                'urgent' => 'text-red-700 bg-red-50 ring-red-600/20',
                                'high'   => 'text-orange-700 bg-orange-50 ring-orange-600/20',
                                default  => 'text-blue-700 bg-blue-50 ring-blue-600/20',
                            };
                        @endphp
                        <article class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-green-200 ring-1 ring-green-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 overflow-hidden">
                            <div class="absolute top-0 right-0 p-3">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityColor }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            
                            <div class="p-5 flex-1">
                                <div class="mb-2 text-xs font-semibold text-green-600 uppercase tracking-wide">Tersedia</div>
                                <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $task->description }}</p>
                                
                                <div class="space-y-1 text-xs text-gray-500 mt-auto">
                                    <div class="flex items-center">
                                         <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                         {{ Str::limit($task->location_name, 30) }}
                                    </div>
                                    <div class="flex items-center">
                                         <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                         Deadline: {{ $task->deadline->format('d M H:i') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
                                <form action="{{ route('assignments.take', $task->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Ambil Penugasan
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
            <hr class="border-gray-200 mb-8">
        @endif
        
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($assignments as $task)
                {{-- Logic Color untuk Priority --}}
                @php
                    $priorityColor = match($task->priority) {
                        'urgent' => 'text-red-700 bg-red-50 ring-red-600/20',
                        'high'   => 'text-orange-700 bg-orange-50 ring-orange-600/20',
                        default  => 'text-blue-700 bg-blue-50 ring-blue-600/20',
                    };

                    $statusColor = match($task->status) {
                        'completed' => 'bg-green-100 text-green-800',
                        'progress'  => 'bg-yellow-100 text-yellow-800',
                        default     => 'bg-gray-100 text-gray-800',
                    };
                @endphp

                <article class="group relative flex flex-col bg-white rounded-2xl shadow-sm border border-gray-200 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 overflow-hidden">
                    
                    {{-- Card Header --}}
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            {{-- Status Badge --}}
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wide {{ $statusColor }}">
                                {{ $task->status }}
                            </span>
                            
                            {{-- Priority Badge --}}
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $priorityColor }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>

                        <a href="{{ route('assignments.show', $task->id) }}" class="block group-hover:text-orange-600 transition-colors">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2">
                                {{ $task->title }}
                            </h3>
                        </a>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                            {{ $task->description }}
                        </p>

                        {{-- Metadata: Location & Time --}}
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ Str::limit($task->location_name, 25) }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Dibuat {{ $task->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="bg-gray-50 px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                        {{-- Reporter Info --}}
                        <div class="flex items-center">
                            @if($task->reporter)
                                <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xs ring-2 ring-white">
                                    {{ substr($task->reporter->name, 0, 2) }}
                                </div>
                                <div class="ml-2 text-xs">
                                    <p class="text-gray-900 font-medium">{{ $task->reporter->name }}</p>
                                    <p class="text-gray-400">Wartawan</p>
                                </div>
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs ring-2 ring-white">
                                    ?
                                </div>
                                <div class="ml-2 text-xs">
                                    <p class="text-gray-900 font-medium italic">Belum Ada</p>
                                    <p class="text-gray-400">Wartawan</p>
                                </div>
                            @endif
                        </div>

                        {{-- Deadline --}}
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Deadline</p>
                            <p class="text-xs font-semibold {{ $task->deadline->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $task->deadline->format('d M, H:i') }}
                            </p>
                        </div>
                    </div>
                </article>

            @empty
                {{-- Empty State (Jika tidak ada data) --}}
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-center bg-white rounded-xl border-2 border-dashed border-gray-300">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada penugasan</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat tugas liputan baru.</p>
                    @if(!Auth::user()->hasRole('wartawan'))
                        <div class="mt-6">
                            <a href="{{ route('assignments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Buat Penugasan Baru
                            </a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection