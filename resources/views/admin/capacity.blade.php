@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12" x-data="{ openAddModal: {{ $errors->any() ? 'true' : 'false' }} }">

    <!-- Top Bar Info & Add Slot Button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Capacity Control &amp; Time-Slot Management</h2>
            <p class="text-xs text-slate-500 mt-0.5">Set whether each time block accepts online bookings or walk-ins, then assign its maximum capacity.</p>
        </div>
        <button @click="openAddModal = true" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Add Date / Time Block</span>
        </button>
    </div>

    <!-- Notification Banner -->
    <div class="p-3.5 bg-amber-50/80 border border-amber-200 text-amber-900 rounded-xl text-xs flex items-center space-x-2">
        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><strong>Admissions controls the booking mode.</strong> Walk-in schedules are hidden from online reservation but remain visible to applicants.</span>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center space-x-2">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Add New Time Block Modal (Cleaned & Without Walk-In Field) -->
    <div x-show="openAddModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
        <div @click.away="openAddModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5 border border-slate-200">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Add New Schedule Time Block</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Specify date, operating hours, and maximum slot capacity.</p>
                </div>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.capacity.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Schedule Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="schedule_date" value="{{ old('schedule_date') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/25">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Start Time <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/25">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">End Time <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-amber-500/25">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Maximum Capacity (Persons) <span class="text-rose-500">*</span></label>
                    <input type="number" name="max_slots" value="{{ old('max_slots', 20) }}" min="1" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">Total number of slots available for this specific time block.</p>
                </div>

                <div class="flex justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-semibold transition cursor-pointer shadow-sm">Save Time Block</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Grid Form for Bulk Update -->
    <form action="{{ route('admin.capacity.update') }}" method="POST">
        @csrf

        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs p-6 space-y-6">

            @php
                $groupedSchedules = $schedules->groupBy('schedule_date');
            @endphp

            @forelse($groupedSchedules as $date => $dateSchedules)
            <div class="border-b border-slate-100 pb-6 last:border-b-0 last:pb-0">
                <div class="mb-3 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black tracking-wider text-slate-400 uppercase">DATE</span>
                        <h3 class="text-sm font-bold font-mono text-slate-900">{{ date('F d, Y', strtotime($date)) }}</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                    @foreach($dateSchedules as $sched)
                    <div x-data="{ mode: '{{ $sched->mode ?? 'Online' }}' }"
                         :class="{
                            'border-rose-400 bg-rose-50/20': mode === 'Closed',
                            'border-emerald-500 bg-emerald-50/20': mode === 'Online',
                            'border-amber-500 bg-amber-50/20': mode === 'Walk-In'
                         }"
                         class="border-2 rounded-xl p-3 space-y-2.5 transition shadow-2xs bg-white">

                        <!-- Time Block Header Range -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-[11px] font-bold font-mono text-slate-800">
                                {{ date('g:i A', strtotime($sched->start_time)) }} - {{ date('g:i A', strtotime($sched->end_time)) }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">CAPACITY</span>
                        </div>

                        <!-- Mode Selector & Capacity Input -->
                        <div class="space-y-2">
                            <div>
                                <select name="schedules[{{ $sched->id }}][mode]"
                                        x-model="mode"
                                        class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px] font-semibold text-slate-700 focus:outline-none cursor-pointer">
                                    <option value="Online" {{ ($sched->mode ?? 'Online') == 'Online' ? 'selected' : '' }}>Online</option>
                                    <option value="Walk-In" {{ ($sched->mode ?? '') == 'Walk-In' ? 'selected' : '' }}>Walk-In</option>
                                    <option value="Closed" {{ ($sched->mode ?? '') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div>
                                <input type="number" name="schedules[{{ $sched->id }}][max_slots]" value="{{ $sched->max_slots }}" min="1" class="w-full px-2 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono font-bold text-center text-slate-800">
                            </div>
                        </div>

                        <!-- Booked Counter Footer -->
                        <div class="flex items-center justify-between text-[10px] pt-1 border-t border-slate-100 font-mono">
                            <span class="text-slate-500">{{ $sched->booked_count }} booked</span>
                            <span class="font-bold {{ $sched->remaining_slots > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $sched->remaining_slots }} left</span>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                Walang pang nakaset na schedule. I-click ang <span class="font-semibold text-slate-600">"+ Add Date / Time Block"</span> button sa itaas para magdagdag.
            </div>
            @endforelse

        </div>

        @if(count($schedules) > 0)
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-2 cursor-pointer">
                <span>Save Schedule</span>
            </button>
        </div>
        @endif

    </form>

</div>
@endsection
