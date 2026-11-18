@extends('layouts.admin')

@section('title', 'Capacity Control')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $canEdit = auth()->user()->canEdit('capacity');
    $dayNames = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
@endphp

<div class="s1 sv">

    @include('admin.steps._topbar', [
        'module' => 'capacity',
        'title' => 'Capacity Control',
        'links' => auth()->user()->canEdit('step1') ? [['Walk-in enrollment', route('admin.walkin.create')]] : [],
    ])

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>Admission schedule</h1>
            <p>
                You decide every date and every hour. Add a date, then add its hours one by one
                and choose Online or Walk-in with the capacity of each hour.
                Students only see the dates and hours you add.
            </p>
        </div>

        <div class="s1-total-group">
            <div class="s1-total"><strong>{{ $stats['dates'] }}</strong><span>Dates ahead</span></div>
            <div class="s1-total"><strong>{{ $stats['online_seats'] }}</strong><span>Online seats</span></div>
            <div class="s1-total"><strong>{{ $stats['walkin_seats'] }}</strong><span>Walk-in seats</span></div>
            <div class="s1-total"><strong>{{ $stats['booked'] }}</strong><span>Booked</span></div>
        </div>
    </section>

    <div class="s1-note-bar s1-reveal" style="--i:2">
        <strong>Admissions controls the booking mode.</strong>
        Walk-in schedules are hidden from online reservation but remain visible to applicants.
    </div>

    {{-- ADD A DATE --}}
    @if($canEdit)
    <section class="sv-card sv-reveal" style="--i:3; margin-bottom:22px;">
        <details class="cap-add" @if(!$hasAny || old('hours') || $errors->has('date')) open @endif>
            <summary class="sv-card-head">
                <span class="sv-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                <h2>Add a date</h2>
                <span class="cap-add-hint">Pick the date, then add the hours you want to open</span>
            </summary>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.capacity.add') }}" class="sv-form" id="addForm">
                    @csrf

                    <div class="sv-fields">
                        <div class="sv-field">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" class="sv-input"
                                   min="{{ today()->toDateString() }}" value="{{ old('date') }}" required>
                        </div>
                    </div>

                    <div class="sv-field">
                        <label>Hours</label>
                        <div class="cap-build" id="buildRows"></div>

                        <button type="button" class="sv-btn sv-btn-download cap-build-add" id="buildAdd">+ Add hour</button>
                        <p class="sv-help">For each hour, choose the time, whether it is Online or Walk-in, and how many seats.</p>
                    </div>

                    <div class="sv-form-actions">
                        <button type="submit" class="sv-btn sv-btn-save">Add this date</button>
                    </div>
                </form>
            </div>
        </details>
    </section>
    @endif

    {{-- SCHEDULE BY DATE --}}
    <section class="sv-card sv-reveal" style="--i:4">
        <div class="sv-card-head">
            <span class="sv-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
            </span>
            <h2>Schedule by date</h2>

            <nav class="s1-chips cap-tabs" aria-label="Upcoming or past">
                <a href="{{ route('admin.capacity.index') }}" class="s1-chip {{ $past ? '' : 'is-active' }}">Upcoming</a>
                <a href="{{ route('admin.capacity.index', ['view' => 'past']) }}" class="s1-chip {{ $past ? 'is-active' : '' }}">Past</a>
            </nav>
        </div>

        @if(count($schedule))
            <form method="POST" action="{{ route('admin.capacity.save') }}" id="saveForm">
                @csrf
                @method('PUT')

                <fieldset class="sv-lock" @disabled(!$canEdit)>
                @foreach($schedule as $row)
                    @php $date = $row['date']; @endphp

                    <div class="cap-row">
                        <div class="cap-date-col">
                            <span>DATE</span>
                            <strong class="s1-mono">{{ $date->toDateString() }}</strong>
                            <small>{{ $date->format('l') }}</small>

                            @if($canEdit)
                            <button type="submit" form="rm-{{ $date->toDateString() }}" class="cap-remove"
                                    onclick="return confirm(@js('Remove ' . $date->format('F d, Y') . ' from the schedule?'));">
                                Remove date
                            </button>
                            @endif
                        </div>

                        <div class="cap-cards">
                            @foreach($row['blocks'] as $slot)
                                <div class="cap-card is-{{ $slot['mode'] }}">
                                    <div class="cap-card-top">
                                        <strong>{{ $slot['label'] }}</strong>
                                        <small>CAPACITY</small>
                                    </div>

                                    <div class="cap-card-controls">
                                        <select name="slots[{{ $date->toDateString() }}][{{ $slot['time'] }}][mode]" class="sv-select"
                                                aria-label="Mode for {{ $date->toDateString() }} {{ $slot['label'] }}">
                                            <option value="online" @selected($slot['mode'] === 'online')>Online</option>
                                            <option value="walkin" @selected($slot['mode'] === 'walkin')>Walk-in</option>
                                            <option value="closed" @selected($slot['mode'] === 'closed')>Closed</option>
                                        </select>

                                        <input type="number" class="sv-input cap-num" min="0" max="1000"
                                               name="slots[{{ $date->toDateString() }}][{{ $slot['time'] }}][capacity]"
                                               value="{{ $slot['capacity'] }}"
                                               aria-label="Capacity for {{ $date->toDateString() }} {{ $slot['label'] }}">
                                    </div>

                                    <div class="cap-card-foot">
                                        <span>{{ $slot['booked'] }} booked</span>
                                        <span class="{{ $slot['left'] < 1 ? 'is-none' : 'is-left' }}">{{ $slot['left'] }} left</span>
                                    </div>

                                    @if($canEdit)
                                    <label class="cap-rm">
                                        <input type="checkbox" name="slots[{{ $date->toDateString() }}][{{ $slot['time'] }}][remove]" value="1">
                                        Remove this hour
                                    </label>
                                    @endif
                                </div>
                            @endforeach

                            @php
                                $used = array_column($row['blocks'], 'time');
                                $free = array_diff_key($hourOptions, array_flip($used));
                                $key = $date->toDateString();
                            @endphp

                            @if($canEdit && count($free))
                                <div class="cap-card cap-card-new">
                                    <div class="cap-card-top">
                                        <strong>Add an hour</strong>
                                    </div>

                                    <div class="cap-card-controls is-stack">
                                        <select name="time" form="ah-{{ $key }}" class="sv-select" required aria-label="Hour to add on {{ $key }}">
                                            <option value="">Choose hour</option>
                                            @foreach($free as $time => $text)
                                                <option value="{{ $time }}">{{ $text }}</option>
                                            @endforeach
                                        </select>

                                        <select name="mode" form="ah-{{ $key }}" class="sv-select" required aria-label="Mode for the new hour on {{ $key }}">
                                            <option value="">Choose mode</option>
                                            <option value="online">Online</option>
                                            <option value="walkin">Walk-in</option>
                                        </select>

                                        <input type="number" name="capacity" form="ah-{{ $key }}" class="sv-input" min="0" max="1000"
                                               placeholder="Seats" required aria-label="Seats for the new hour on {{ $key }}">

                                        <button type="submit" form="ah-{{ $key }}" class="sv-btn sv-btn-view">Add hour</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                </fieldset>

                <div class="cap-save-bar">
                    <p class="sv-help">Booked counts online students and walk-ins together. Staff can still add a walk-in when an hour is full.</p>
                    @if($canEdit)
                        <button type="submit" class="sv-btn sv-btn-save">Save changes</button>
                    @endif
                </div>
            </form>

            @foreach($schedule as $row)
                <form method="POST" action="{{ route('admin.capacity.remove') }}" id="rm-{{ $row['date']->toDateString() }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="date" value="{{ $row['date']->toDateString() }}">
                </form>

                <form method="POST" action="{{ route('admin.capacity.hour') }}" id="ah-{{ $row['date']->toDateString() }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $row['date']->toDateString() }}">
                </form>
            @endforeach

            @if($dates->hasPages())
                <div class="s1-card-foot">
                    <span>Page {{ $dates->currentPage() }} of {{ $dates->lastPage() }}</span>

                    <div class="s1-pager">
                        <a href="{{ $dates->previousPageUrl() ?: '#' }}"
                           class="s1-page-btn {{ $dates->onFirstPage() ? 'is-off' : '' }}">Previous</a>
                        <a href="{{ $dates->nextPageUrl() ?: '#' }}"
                           class="s1-page-btn {{ $dates->hasMorePages() ? '' : 'is-off' }}">Next</a>
                    </div>
                </div>
            @endif
        @else
            <div class="sv-card-body pad">
                <p class="sv-empty">
                    {{ $past ? 'No past dates.' : 'No dates yet. Use Add dates above to open the admission schedule.' }}
                </p>
            </div>
        @endif
    </section>

</div>

@endsection

@push('scripts')
<script>
(function () {
    const rows = document.getElementById('buildRows');
    const addBtn = document.getElementById('buildAdd');

    if (!rows || !addBtn) return;

    const hours = @json($hourOptions);
    const previous = @json(array_values((array) old('hours', [])));

    let counter = 0;

    const usedTimes = () =>
        Array.from(rows.querySelectorAll('.cap-build-time')).map(s => s.value).filter(Boolean);

    // Do not let the same hour be picked twice.
    const syncOptions = () => {
        const used = usedTimes();

        rows.querySelectorAll('.cap-build-time').forEach(select => {
            Array.from(select.options).forEach(option => {
                option.disabled = option.value !== '' && option.value !== select.value && used.includes(option.value);
            });
        });
    };

    const addRow = (data) => {
        const index = counter++;
        const row = document.createElement('div');
        row.className = 'cap-build-row';

        const time = document.createElement('select');
        time.className = 'sv-select cap-build-time';
        time.name = 'hours[' + index + '][time]';
        time.required = true;
        time.setAttribute('aria-label', 'Hour');

        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = 'Choose hour';
        time.appendChild(blank);

        Object.entries(hours).forEach(([value, text]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = text;
            option.selected = !!data && data.time === value;
            time.appendChild(option);
        });

        const mode = document.createElement('select');
        mode.className = 'sv-select';
        mode.name = 'hours[' + index + '][mode]';
        mode.required = true;
        mode.setAttribute('aria-label', 'Mode');

        [['', 'Choose mode'], ['online', 'Online'], ['walkin', 'Walk-in']].forEach(([value, text]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = text;
            option.selected = !!data && data.mode === value && value !== '';
            mode.appendChild(option);
        });

        const seats = document.createElement('input');
        seats.type = 'number';
        seats.className = 'sv-input';
        seats.name = 'hours[' + index + '][capacity]';
        seats.min = '0';
        seats.max = '1000';
        seats.placeholder = 'Seats';
        seats.required = true;
        seats.setAttribute('aria-label', 'Seats');
        if (data && data.capacity !== undefined && data.capacity !== null) seats.value = data.capacity;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'cap-build-remove';
        remove.textContent = '×';
        remove.setAttribute('aria-label', 'Remove this hour');
        remove.addEventListener('click', () => {
            row.remove();
            syncOptions();
        });

        time.addEventListener('change', syncOptions);

        row.append(time, mode, seats, remove);
        rows.appendChild(row);
        syncOptions();
    };

    addBtn.addEventListener('click', () => addRow());

    // Start with whatever was typed before an error, or one empty row.
    if (previous.length) {
        previous.forEach(addRow);
    } else {
        addRow();
    }
})();
</script>
@endpush
