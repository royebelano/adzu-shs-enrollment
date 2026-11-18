<div class="space-y-4">
    <h3 class="text-sm font-bold text-slate-900">Pumili ng Petsa at Oras ng Appointment</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($schedules as $sched)
        <label class="border border-slate-200 rounded-xl p-4 cursor-pointer hover:border-amber-500 transition block bg-white">
            <input type="radio" name="schedule_id" value="{{ $sched->id }}" required class="sr-only peer">
            <div class="peer-checked:border-amber-600 peer-checked:bg-amber-50/50 p-2 rounded-lg -m-2">
                <p class="text-xs font-bold font-mono text-slate-900">{{ date('F d, Y', strtotime($sched->schedule_date)) }}</p>
                <p class="text-xs font-semibold text-amber-700 mt-1">
                    {{ date('g:i A', strtotime($sched->start_time)) }} - {{ date('g:i A', strtotime($sched->end_time)) }}
                </p>
                <div class="mt-3 flex justify-between text-[10px] text-slate-500 font-mono">
                    <span>Max: {{ $sched->max_slots }}</span>
                    <span class="text-emerald-600 font-bold">{{ $sched->max_slots - $sched->appointments()->count() }} slots available</span>
                </div>
            </div>
        </label>
        @empty
        <p class="text-xs text-slate-400 col-span-3 text-py-8">Wala pang available na online appointment schedules sa ngayon.</p>
        @endforelse
    </div>
</div>
