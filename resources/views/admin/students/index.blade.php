@extends('layouts.admin')

@section('title', 'Student Directory')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $filters = request()->only(['q', 'step', 'status', 'strand', 'grade', 'source']);
    $activeStep = request('step');

    $stepUrl = fn ($step) => route('admin.students.index', array_filter(
        array_merge($filters, ['step' => $step, 'page' => null]),
        fn ($v) => filled($v)
    ));

    $viewRoute = fn ($e) => $e->stepNumber() === 1
        ? route('admin.step1.view', $e->id)
        : route('admin.step' . $e->stepNumber() . '.view', $e->id);

    $hasFilters = collect($filters)->filter(fn ($v) => filled($v))->isNotEmpty();
@endphp

<div class="s1">

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal" style="--i:0">
        <div>
            <h1>Student directory</h1>
            <p>
                Every student who submitted an enrollment form, and the step each one is in right now.
            </p>
        </div>

        <div class="s1-total-group">
            <div class="s1-total"><strong>{{ $stats['total'] }}</strong><span>All students</span></div>
            <div class="s1-total"><strong>{{ $stats['enrolled'] }}</strong><span>Enrolled</span></div>
            <div class="s1-total"><strong>{{ $stats['in_progress'] }}</strong><span>In progress</span></div>
            <div class="s1-total"><strong>{{ $stats['walk_ins'] }}</strong><span>Walk-ins</span></div>
        </div>
    </section>

    {{-- STEP FILTER --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">
        <nav class="s1-chips" aria-label="Filter by step">
            <a href="{{ $stepUrl(null) }}" class="s1-chip {{ $activeStep ? '' : 'is-active' }}">
                All ({{ $stats['total'] }})
            </a>

            @foreach(\App\Models\Enrollment::STEPS as $n => $label)
                <a href="{{ $stepUrl($n) }}" class="s1-chip {{ (string) $activeStep === (string) $n ? 'is-active' : '' }}">
                    {{ $n === 6 ? 'Enrolled' : 'Step ' . $n . ' · ' . $label }} ({{ $stats['per_step'][$n] }})
                </a>
            @endforeach
        </nav>

        <div class="s1-tools">
            @if(auth()->user()->canEdit('step1'))
                <a href="{{ route('admin.walkin.create') }}" class="s1-btn s1-btn-ghost">New walk-in</a>
            @endif
            <a href="{{ route('admin.students.export', $filters) }}" class="s1-btn s1-btn-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 21h16"/></svg>
                Export CSV
            </a>
        </div>
    </section>

    {{-- SEARCH + FILTERS --}}
    <form method="GET" action="{{ route('admin.students.index') }}" class="dir-filters s1-reveal" style="--i:2">
        @if($activeStep)
            <input type="hidden" name="step" value="{{ $activeStep }}">
        @endif

        <div class="s1-search" role="search">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Search name, student ID, LRN, or reference ID" aria-label="Search students">
        </div>

        <select name="status" class="sv-select" aria-label="Application status">
            <option value="">Any status</option>
            @foreach(\App\Models\Enrollment::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="strand" class="sv-select" aria-label="Strand">
            <option value="">Any strand</option>
            @foreach(['ABM', 'HUMSS', 'STEM'] as $strand)
                <option value="{{ $strand }}" @selected(request('strand') === $strand)>{{ $strand }}</option>
            @endforeach
        </select>

        <select name="grade" class="sv-select" aria-label="Grade level">
            <option value="">Any grade</option>
            @foreach($grades as $grade)
                <option value="{{ $grade }}" @selected((string) request('grade') === (string) $grade)>Grade {{ $grade }}</option>
            @endforeach
        </select>

        <select name="source" class="sv-select" aria-label="Source">
            <option value="">Online and walk-in</option>
            <option value="online" @selected(request('source') === 'online')>Online</option>
            <option value="walkin" @selected(request('source') === 'walkin')>Walk-in</option>
        </select>

        <button type="submit" class="s1-btn s1-btn-primary">Apply</button>

        @if($hasFilters)
            <a href="{{ route('admin.students.index') }}" class="s1-btn s1-btn-ghost">Clear</a>
        @endif
    </form>

    {{-- TABLE --}}
    <section class="s1-card s1-reveal" style="--i:3">

        <div class="s1-card-head">
            <div>
                <h2>Students</h2>
                <p>Newest first.</p>
            </div>

            <span class="s1-count">{{ $enrollments->total() }} {{ $hasFilters ? 'matching' : 'total' }}</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table is-wide">

                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Reference ID</th>
                        <th>Grade</th>
                        <th>Strand</th>
                        <th>Where they are now</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($enrollments as $enrollment)

                        @php
                            $code = $enrollment->strandCode();
                            $step = $enrollment->stepNumber();

                            $initials = strtoupper(
                                mb_substr((string) $enrollment->first_name, 0, 1) .
                                mb_substr((string) $enrollment->last_name, 0, 1)
                            ) ?: '?';
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">

                            <td data-label="Student">
                                <div class="s1-student">
                                    <span class="s1-avatar" aria-hidden="true">{{ $initials }}</span>
                                    <div>
                                        <strong>{{ $enrollment->last_name }}, {{ $enrollment->first_name }}</strong>
                                        <small>{{ trim($enrollment->middle_name . ' ' . $enrollment->extension_name) }}</small>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Student ID">
                                @if($enrollment->student_id)
                                    <span class="s1-mono s1-id">{{ $enrollment->student_id }}</span>
                                @else
                                    <span class="s1-muted">Not assigned</span>
                                @endif
                            </td>

                            <td data-label="Reference ID">
                                @if($enrollment->tracking_number)
                                    <span class="s1-ref">{{ $enrollment->tracking_number }}</span>
                                @else
                                    <span class="s1-muted">—</span>
                                @endif
                            </td>

                            <td data-label="Grade" class="{{ $enrollment->grade_level ? '' : 's1-muted' }}">{{ $enrollment->grade_level ?: '—' }}</td>

                            <td data-label="Strand">
                                @if($code)<span class="s1-strand is-{{ $code }}">{{ $code }}</span>@else<span class="s1-muted">—</span>@endif
                            </td>

                            <td data-label="Where they are now">
                                <div class="dir-stage">
                                    <span class="dir-steps" aria-hidden="true">
                                        @for($i = 1; $i <= 6; $i++)
                                            <i class="{{ $i < $step || $step === 6 ? 'is-done' : ($i === $step ? 'is-now' : '') }}"></i>
                                        @endfor
                                    </span>
                                    <strong>{{ $enrollment->stageLabel() }}</strong>
                                </div>
                            </td>

                            <td data-label="Status">
                                <span class="s1-status is-{{ $enrollment->status ?? 'pending' }}">{{ $enrollment->statusLabel() }}</span>
                            </td>

                            <td data-label="Source" class="s1-nowrap">{{ $enrollment->is_walk_in ? 'Walk-in' : 'Online' }}</td>

                            <td data-label="Submitted" class="s1-nowrap">{{ $enrollment->created_at?->format('M d, Y') ?? '—' }}</td>

                            <td data-label="Action">
                                <a href="{{ $viewRoute($enrollment) }}" class="s1-open">
                                    Open
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="10" class="s1-empty">
                                <strong>No students found</strong>
                                {{ $hasFilters ? 'Try different filters or clear them.' : 'Submitted enrollment forms will show up here.' }}
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <div class="s1-card-foot">
            <span>
                Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }}
                of {{ $enrollments->total() }} students
            </span>

            @if($enrollments->hasPages())
                <div class="s1-pager">
                    <a href="{{ $enrollments->previousPageUrl() ?: '#' }}"
                       class="s1-page-btn {{ $enrollments->onFirstPage() ? 'is-off' : '' }}">Previous</a>

                    <span>Page {{ $enrollments->currentPage() }} of {{ $enrollments->lastPage() }}</span>

                    <a href="{{ $enrollments->nextPageUrl() ?: '#' }}"
                       class="s1-page-btn {{ $enrollments->hasMorePages() ? '' : 'is-off' }}">Next</a>
                </div>
            @endif
        </div>

    </section>

</div>

@endsection
