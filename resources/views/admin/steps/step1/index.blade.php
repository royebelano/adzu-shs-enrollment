@extends('layouts.admin')

@section('title', 'Step 1 · Enrollment Form')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .s1 {
        --ink: var(--admin-text, #14213d);
        --ink-soft: var(--admin-text-soft, #5b6784);
        --ink-faint: var(--admin-text-muted, #8c96ad);
        --line: var(--admin-border, #e4e8f1);
        --surface: var(--admin-panel, #ffffff);
        --wash: var(--admin-panel-soft, #f5f7fc);
        --brand: #1d3a7a;
        --brand-deep: #14264f;
        --gold: #d9a441;
        --ease: cubic-bezier(.22, .8, .24, 1);

        max-width: 1280px;
        margin: 0 auto;
        padding: 6px 4px 40px;
        color: var(--ink);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
    }

    .s1 *, .s1 *::before, .s1 *::after { box-sizing: border-box; }

    /* ---------- page-load reveal (banner, toolbar, card, then rows) ---------- */
    .s1-reveal {
        opacity: 0;
        transform: translateY(14px);
        animation: s1-rise .6s var(--ease) forwards;
        animation-delay: calc(var(--i, 0) * 90ms);
    }

    .s1-row-in {
        opacity: 0;
        transform: translateY(8px);
        animation: s1-rise .5s var(--ease) forwards;
        animation-delay: calc(300ms + var(--i, 0) * 45ms);
    }

    @keyframes s1-rise { to { opacity: 1; transform: none; } }

    /* ---------- banner ---------- */
    .s1-banner {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        padding: 30px 34px;
        margin-bottom: 20px;
        border-radius: 22px;
        color: #fff;
        overflow: hidden;
        background:
            radial-gradient(800px 240px at 100% 0%, rgba(217, 164, 65, .28), transparent 60%),
            linear-gradient(120deg, var(--brand-deep), var(--brand));
    }

    .s1-banner::after {
        content: "";
        position: absolute;
        right: -50px;
        bottom: -90px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        border: 36px solid rgba(255, 255, 255, .05);
        pointer-events: none;
    }

    .s1-banner h1 {
        margin: 0 0 6px;
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .s1-banner p {
        margin: 0;
        max-width: 520px;
        font-size: 14px;
        line-height: 1.55;
        color: rgba(255, 255, 255, .78);
    }

    .s1-total {
        position: relative;
        z-index: 1;
        padding: 12px 20px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .22);
        text-align: center;
    }

    .s1-total strong {
        display: block;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .s1-total span { font-size: 12px; color: rgba(255, 255, 255, .72); }

    /* ---------- toolbar ---------- */
    .s1-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
    }

    .s1-chips { display: flex; flex-wrap: wrap; gap: 8px; }

    .s1-chip {
        padding: 8px 15px;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink-soft);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: border-color .2s, color .2s, background .2s, transform .2s var(--ease);
    }

    .s1-chip:hover { border-color: var(--brand); color: var(--brand); transform: translateY(-1px); }

    .s1-chip.is-active {
        background: var(--brand-deep);
        border-color: var(--brand-deep);
        color: #fff;
    }

    .s1-tools { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }

    .s1-search {
        position: relative;
        display: flex;
        align-items: center;
    }

    .s1-search svg {
        position: absolute;
        left: 14px;
        color: var(--ink-faint);
        pointer-events: none;
        transition: color .2s;
    }

    .s1-search input {
        width: 310px;
        max-width: 100%;
        padding: 11px 14px 11px 40px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: var(--surface);
        font: inherit;
        font-size: 14px;
        color: var(--ink);
        transition: border-color .2s, box-shadow .2s, width .3s var(--ease);
    }

    .s1-search input::placeholder { color: var(--ink-faint); }

    .s1-search input:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(29, 58, 122, .12);
        width: 360px;
    }

    .s1-search:focus-within svg { color: var(--brand); }

    .s1-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 18px;
        border: 1px solid transparent;
        border-radius: 12px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: transform .2s var(--ease), box-shadow .2s, background .2s;
    }

    .s1-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(20, 38, 79, .14); }
    .s1-btn:active { transform: translateY(0) scale(.98); }

    .s1-btn-primary { background: var(--brand-deep); color: #fff; }
    .s1-btn-primary:hover { background: var(--brand); }
    .s1-btn-ghost { background: var(--surface); color: var(--ink); border-color: var(--line); }

    /* ---------- card + table ---------- */
    .s1-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 20px;
        overflow: hidden;
    }

    .s1-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 24px;
        border-bottom: 1px solid var(--line);
    }

    .s1-card-head h2 { margin: 0 0 2px; font-size: 16px; font-weight: 700; letter-spacing: -.01em; }
    .s1-card-head p { margin: 0; font-size: 13px; color: var(--ink-soft); }

    .s1-count {
        padding: 6px 12px;
        border-radius: 999px;
        background: #e8eefc;
        color: var(--brand);
        font-size: 12px;
        font-weight: 700;
    }

    .s1-table-wrap { overflow-x: auto; }

    .s1-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 920px;
    }

    .s1-table th {
        position: sticky;
        top: 0;
        padding: 13px 18px;
        background: var(--wash);
        border-bottom: 1px solid var(--line);
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: var(--ink-soft);
        white-space: nowrap;
    }

    .s1-table td {
        padding: 15px 18px;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
        vertical-align: middle;
        transition: background .2s;
    }

    .s1-table tbody tr:last-child td { border-bottom: 0; }
    .s1-table tbody tr:hover td { background: #f8faff; }

    .s1-ref {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 8px;
        background: var(--wash);
        border: 1px solid var(--line);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .05em;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .s1-ref.is-none { color: var(--ink-faint); font-weight: 500; letter-spacing: 0; }

    .s1-student { display: flex; align-items: center; gap: 12px; min-width: 200px; }

    .s1-avatar {
        flex: none;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 13px;
        font-weight: 800;
        color: var(--brand-deep);
        background: linear-gradient(145deg, #eef2ff, #ffe9b8);
        transition: transform .3s var(--ease);
    }

    .s1-table tbody tr:hover .s1-avatar { transform: rotate(-6deg) scale(1.06); }

    .s1-student strong { display: block; font-weight: 700; }
    .s1-student small { color: var(--ink-soft); font-size: 12px; }

    .s1-muted { color: var(--ink-faint); }
    .s1-nowrap { white-space: nowrap; }
    .s1-email { max-width: 220px; overflow-wrap: anywhere; }

    /* ---------- status ---------- */
    .s1-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        background: #fff3d6;
        color: #8a5a00;
    }

    .s1-status::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .s1-status.is-pending::before { animation: s1-pulse 1.8s ease-out infinite; }
    .s1-status.is-approved { background: #dcf5e5; color: #11693a; }
    .s1-status.is-rejected { background: #fde2e2; color: #a21c1c; }
    .sv-viewonly { padding: 5px 12px; border-radius: 999px; background: #eef1f7; color: #5b6784; font-size: 12px; font-weight: 700; }

    .s1-stage { display: block; margin-top: 5px; font-size: 12px; color: var(--ink-soft); white-space: nowrap; }

    .s1-status.is-for_compliance { background: #dfeaff; color: #1a46a8; }

    @keyframes s1-pulse {
        0%   { box-shadow: 0 0 0 0 currentColor; }
        70%  { box-shadow: 0 0 0 7px transparent; }
        100% { box-shadow: 0 0 0 0 transparent; }
    }

    /* ---------- action ---------- */
    .s1-open {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 14px;
        border-radius: 10px;
        background: #e8eefc;
        border: 1px solid #cfdaf7;
        color: var(--brand);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition: background .2s, color .2s, transform .2s var(--ease), box-shadow .2s;
    }

    .s1-open svg { transition: transform .25s var(--ease); }

    .s1-open:hover {
        background: var(--brand-deep);
        border-color: var(--brand-deep);
        color: #fff;
        box-shadow: 0 8px 18px rgba(20, 38, 79, .2);
    }

    .s1-open:hover svg { transform: translateX(3px); }

    /* ---------- empty ---------- */
    .s1-empty {
        padding: 54px 20px !important;
        text-align: center;
        color: var(--ink-soft);
    }

    .s1-empty strong { display: block; margin-bottom: 4px; font-size: 16px; color: var(--ink); }

    /* ---------- footer / pager ---------- */
    .s1-card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 24px;
        border-top: 1px solid var(--line);
        background: var(--wash);
        font-size: 13px;
        color: var(--ink-soft);
    }

    .s1-pager { display: flex; align-items: center; gap: 8px; }

    .s1-page-btn {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: border-color .2s, transform .2s var(--ease);
    }

    .s1-page-btn:hover { border-color: var(--brand); transform: translateY(-1px); }
    .s1-page-btn.is-off { opacity: .4; pointer-events: none; }

    /* ---------- focus ---------- */
    .s1 a:focus-visible,
    .s1 button:focus-visible,
    .s1 input:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    .s1-search input:focus-visible { outline: none; }

    /* ---------- mobile: rows become cards ---------- */
    @media (max-width: 760px) {
        .s1-banner { padding: 22px; }
        .s1-search, .s1-search input, .s1-search input:focus { width: 100%; }
        .s1-tools { width: 100%; }
        .s1-search { flex: 1 1 100%; }

        .s1-table { min-width: 0; }
        .s1-table thead { display: none; }
        .s1-table, .s1-table tbody, .s1-table tr, .s1-table td { display: block; width: 100%; }

        .s1-table tr {
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
        }

        .s1-table td {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 7px 0;
            border: 0;
            text-align: right;
        }

        .s1-table td::before {
            content: attr(data-label);
            flex: none;
            color: var(--ink-soft);
            font-size: 12px;
            font-weight: 600;
            text-align: left;
        }

        .s1-table tbody tr:hover td { background: transparent; }
        .s1-student { text-align: left; }
        .s1-card-head, .s1-card-foot { padding-left: 18px; padding-right: 18px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .s1-reveal, .s1-row-in { animation: none; opacity: 1; transform: none; }
        .s1 *, .s1 *::before { transition: none !important; animation: none !important; }
    }

    :root[data-theme="dark"] .s1-count,
    :root[data-theme="dark"] .s1-open,
    :root[data-theme="dark"] .s1 .sv-viewonly { background: #243b5b; border-color: #38577c; color: #b9d7ff; }
    :root[data-theme="dark"] .s1-table tbody tr:hover td { background: var(--wash); }
    :root[data-theme="dark"] .s1-status { background: #49391e; color: #f7d48a; }
    :root[data-theme="dark"] .s1-status.is-approved { background: #193d30; color: #91e0b4; }
    :root[data-theme="dark"] .s1-status.is-rejected { background: #48292d; color: #ffaaaa; }
    :root[data-theme="dark"] .s1-status.is-for_compliance { background: #34284b; color: #d0b5ff; }
    :root[data-theme="dark"] .s1-open:hover { background: var(--brand-deep); border-color: var(--brand-deep); color: #fff; }
</style>
@endpush

@section('content')

@php
    $currentStatus = request('status');
    $currentQuery  = request('q');

    $chipUrl = fn ($status) => route('admin.index', array_filter([
        'q' => $currentQuery,
        'status' => $status,
    ], fn ($v) => filled($v)));
@endphp

<div class="s1">

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal" style="--i:0">
        <div>
            <h1>Step 1 · Appointment booked</h1>
            <p>
                Review students who have submitted their enrollment form.
                Records stay here after they move to the next step.
            </p>
        </div>

        <div class="s1-total">
            <strong>{{ $enrollments->total() }}</strong>
            <span>{{ $currentStatus || $currentQuery ? 'matching records' : 'total records' }}</span>
        </div>
    </section>

    {{-- TOOLBAR --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">

        <nav class="s1-chips" aria-label="Filter by status">
            <a href="{{ $chipUrl(null) }}"
               class="s1-chip {{ $currentStatus ? '' : 'is-active' }}">All</a>

            @foreach(\App\Models\Enrollment::STATUSES as $value => $label)
                <a href="{{ $chipUrl($value) }}"
                   class="s1-chip {{ $currentStatus === $value ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="s1-tools">
            <form method="GET" action="{{ route('admin.index') }}" class="s1-search" role="search" id="s1SearchForm">
                @if($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif

                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>

                <input
                    type="search"
                    name="q"
                    value="{{ $currentQuery }}"
                    placeholder="Search name, LRN, or reference ID"
                    aria-label="Search students"
                >
            </form>

            <button type="submit" form="s1SearchForm" class="s1-btn s1-btn-primary">
                Search
            </button>

            @if(auth()->user()->canEdit('step1'))
                <a href="{{ route('admin.walkin.create') }}" class="s1-btn s1-btn-ghost">New walk-in</a>
            @endif

            <a href="{{ route('admin.export', array_filter(['q' => $currentQuery, 'status' => $currentStatus], fn ($v) => filled($v))) }}"
               class="s1-btn s1-btn-ghost">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 21h16"/></svg>
                Export CSV
            </a>
        </div>
    </section>

    {{-- TABLE --}}
    <section class="s1-card s1-reveal" style="--i:2">

        <div class="s1-card-head">
            <div>
                <h2>Enrollment forms</h2>
                <p>Submitted details and status for each student.</p>
            </div>

            <span class="s1-count">{{ $enrollments->count() }} shown</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table">

                <thead>
                    <tr>
                        <th>Reference ID</th>
                        <th>Student</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Strand</th>
                        <th>Appointment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($enrollments as $enrollment)

                        @php
                            $studentData = $enrollment->data ?? [];

                            if (is_string($studentData)) {
                                $studentData = json_decode($studentData, true) ?? [];
                            }

                            $mobile = $enrollment->phone_number
                                ?: data_get($studentData, 'phone_number')
                                ?: data_get($studentData, 'contact_number')
                                ?: '—';

                            $email = $enrollment->email
                                ?: data_get($studentData, 'email')
                                ?: '—';

                            $strand = data_get($studentData, 'shs_strand')
                                ?: data_get($studentData, 'preferred_strand')
                                ?: data_get($studentData, 'strand')
                                ?: '—';

                            $initials = strtoupper(
                                mb_substr((string) $enrollment->first_name, 0, 1) .
                                mb_substr((string) $enrollment->last_name, 0, 1)
                            ) ?: '?';

                            $otherNames = trim(implode(' ', array_filter([
                                $enrollment->middle_name,
                                $enrollment->extension_name,
                            ])));
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">

                            <td data-label="Reference ID">
                                @if($enrollment->tracking_number)
                                    <span class="s1-ref">{{ $enrollment->tracking_number }}</span>
                                @else
                                    <span class="s1-ref is-none">No ID</span>
                                @endif
                            </td>

                            <td data-label="Student">
                                <div class="s1-student">
                                    <span class="s1-avatar" aria-hidden="true">{{ $initials }}</span>

                                    <div>
                                        <strong>
                                            {{ $enrollment->last_name }}, {{ $enrollment->first_name }}
                                        </strong>

                                        @if($otherNames)
                                            <small>{{ $otherNames }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td data-label="Mobile" class="s1-nowrap {{ $mobile === '—' ? 's1-muted' : '' }}">
                                {{ $mobile }}
                            </td>

                            <td data-label="Email" class="s1-email {{ $email === '—' ? 's1-muted' : '' }}">
                                {{ $email }}
                            </td>

                            <td data-label="Strand" class="{{ $strand === '—' ? 's1-muted' : '' }}">
                                {{ $strand }}
                            </td>

                            <td data-label="Appointment" class="s1-nowrap">
                                @if($enrollment->appointmentLabel())
                                    {{ $enrollment->appointmentLabel() }}
                                @else
                                    {{ $enrollment->created_at ? $enrollment->created_at->format('M d, Y') : '—' }}
                                @endif

                                @if($enrollment->is_walk_in)
                                    <small class="s1-stage">Walk-in</small>
                                @endif
                            </td>

                            <td data-label="Status">
                                <span class="s1-status is-{{ $enrollment->status ?? 'pending' }}">
                                    {{ $enrollment->statusLabel() }}
                                </span>

                                @if($enrollment->stepNumber() > 1)
                                    <small class="s1-stage">Now in {{ $enrollment->stageLabel() }}</small>
                                @endif
                            </td>

                            <td data-label="Action">
                                <a href="{{ route('admin.step1.view', $enrollment->id) }}" class="s1-open">
                                    View details
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="s1-empty">
                                <strong>No enrollment records found</strong>
                                @if($currentQuery || $currentStatus)
                                    Try a different search or clear the status filter.
                                @else
                                    New submissions will show up here.
                                @endif
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <div class="s1-card-foot">
            <span>
                Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }}
                of {{ $enrollments->total() }} records
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
