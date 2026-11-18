@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300">

    <!-- Top Bar: Date & Status Badge -->
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
            {{ now()->format('l, F j, Y') }}
        </p>

        <div class="flex items-center space-x-2 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full text-emerald-700 text-xs font-semibold shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>Admissions Admin &bull; Real-time DB</span>
        </div>
    </div>

    <!-- Overview Header -->
    <div>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">
            Overview
        </h2>

        <p class="text-xs text-slate-500 mt-0.5">
            Live enrollment snapshot &mdash; all data is real-time from the portal
        </p>

        <!-- 4 Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">

            <!-- Card 1: Scheduled Today -->
            <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-l-blue-600 relative overflow-hidden group">
                <div class="text-2xl font-bold text-slate-900 mb-1 group-hover:text-blue-600 transition-colors">
                    {{ $stats['scheduled_today'] }}
                </div>

                <div class="text-[11px] font-bold text-blue-900 tracking-wider uppercase">
                    Scheduled Today
                </div>

                <div class="text-[11px] text-slate-500 mt-1">
                    {{ now()->format('F j') }}
                </div>
            </div>

            <!-- Card 2: Officially Enrolled -->
            <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-l-emerald-500 relative overflow-hidden group">
                <div class="text-2xl font-bold text-slate-900 mb-1 group-hover:text-emerald-600 transition-colors">
                    {{ $stats['officially_enrolled'] }}
                </div>

                <div class="text-[11px] font-bold text-emerald-800 tracking-wider uppercase">
                    Officially Enrolled
                </div>

                <div class="text-[11px] text-slate-500 mt-1">
                    Completed all steps
                </div>
            </div>

            <!-- Card 3: New / Pending -->
            <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-l-amber-400 relative overflow-hidden group">
                <div class="text-2xl font-bold text-slate-900 mb-1 group-hover:text-amber-600 transition-colors">
                    {{ $stats['pending_count'] }}
                </div>

                <div class="text-[11px] font-bold text-amber-800 tracking-wider uppercase">
                    New / Pending
                </div>

                <div class="text-[11px] text-slate-500 mt-1">
                    Awaiting admin confirmation
                </div>
            </div>

            <!-- Card 4: Incomplete Docs -->
            <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-l-purple-500 relative overflow-hidden group">
                <div class="text-2xl font-bold text-slate-900 mb-1 group-hover:text-purple-600 transition-colors">
                    {{ $stats['incomplete_docs'] }}
                </div>

                <div class="text-[11px] font-bold text-purple-900 tracking-wider uppercase">
                    Incomplete Docs
                </div>

                <div class="text-[11px] text-slate-500 mt-1">
                    Missing requirements
                </div>
            </div>

        </div>
    </div>

    <!-- Enrollment Pipeline Tables -->
    <div class="pt-2">

        <h3 class="text-base font-bold text-slate-900 tracking-tight">
            Enrollment pipeline tables
        </h3>

        <p class="text-xs text-slate-500 mb-4">
            Each enrollment step has a separate table in the Overview.
        </p>

        <!-- Table 1: Appointment Booked / Step 1 -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-6 transition-all duration-300 hover:shadow-md">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">

                <div class="flex items-center space-x-3">

                    <span class="px-2.5 py-1 bg-[#0a1128] text-white text-[11px] font-bold rounded-lg shadow-sm">
                        01
                    </span>

                    <div>
                        <h4 class="text-sm font-bold text-slate-900">
                            Step 1 &bull; Appointment Booked
                        </h4>

                        <p class="text-[11px] text-slate-400">
                            {{ count($appointments) }} recent records found
                        </p>
                    </div>

                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Mobile Number</th>
                            <th class="px-6 py-3">Preferred Strand</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">

                        @forelse($appointments as $student)

                            <tr class="hover:bg-blue-50/40 transition-colors duration-150">

                                <td class="px-6 py-3.5 font-semibold text-slate-900">
                                    {{ $student->last_name ?? '' }},
                                    {{ $student->first_name ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $student->contact_number ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-3.5 text-slate-600">
                                    {{ $student->preferred_strand ?? 'STEM' }}
                                </td>

                                <td class="px-6 py-3.5">
                                    <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold shadow-sm">
                                        {{ $student->status ?? 'In progress' }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs">

                                    <div class="flex flex-col items-center justify-center space-y-1">

                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>

                                        <span>
                                            Walang kasalukuyang tala o records sa database.
                                        </span>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <!-- Enrollment Step Navigation -->
    <div class="pt-2">

        <h3 class="text-base font-bold text-slate-900 tracking-tight">
            Enrollment Steps
        </h3>

        <p class="text-xs text-slate-500 mt-0.5 mb-4">
            Access enrollment steps based on your assigned role.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Step 2: Infirmary -->
            @if(in_array(Auth::user()->role, ['Infirmary', 'Super Admin']))
                <a href="{{ route('admin.step2') }}"
                   class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">
                        Step 2
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 mt-2">
                        Infirmary
                    </h4>

                    <p class="text-xs text-slate-500 mt-1">
                        Infirmary processing.
                    </p>
                </a>
            @endif

            <!-- Step 3: Admission -->
            @if(in_array(Auth::user()->role, ['Admission', 'Super Admin']))
                <a href="{{ route('admin.step3') }}"
                   class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="text-xs font-bold text-blue-700 uppercase tracking-wider">
                        Step 3
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 mt-2">
                        Admission
                    </h4>

                    <p class="text-xs text-slate-500 mt-1">
                        Admission processing.
                    </p>
                </a>
            @endif

            <!-- Step 4: ID Capturing -->
            @if(in_array(Auth::user()->role, ['ID Capturing', 'Super Admin']))
                <a href="{{ route('admin.step4') }}"
                   class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="text-xs font-bold text-purple-700 uppercase tracking-wider">
                        Step 4
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 mt-2">
                        ID Capturing
                    </h4>

                    <p class="text-xs text-slate-500 mt-1">
                        Student ID processing.
                    </p>
                </a>
            @endif

            <!-- Step 5: GCO Assessment -->
            @if(in_array(Auth::user()->role, ['GCO Staff', 'Super Admin']))
                <a href="{{ route('admin.step5') }}"
                   class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">

                    <div class="text-xs font-bold text-amber-700 uppercase tracking-wider">
                        Step 5
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 mt-2">
                        GCO Assessment
                    </h4>

                    <p class="text-xs text-slate-500 mt-1">
                        Guidance assessment.
                    </p>
                </a>
            @endif

        </div>
    </div>

</div>
@endsection
