@extends('layouts.admin')

@section('title', 'Step 1 · Student Details')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .sv {
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

        max-width: 1180px;
        margin: 0 auto;
        padding: 6px 4px 40px;
        color: var(--ink);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
    }

    .sv *, .sv *::before, .sv *::after { box-sizing: border-box; }

    /* ---------- one page-load reveal ---------- */
    .sv-reveal {
        opacity: 0;
        transform: translateY(14px);
        animation: sv-rise .6s var(--ease) forwards;
        animation-delay: calc(var(--i, 0) * 90ms);
    }

    @keyframes sv-rise {
        to { opacity: 1; transform: none; }
    }

    /* ---------- top bar ---------- */
    .sv-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sv-bar-title {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--ink-soft);
    }

    .sv-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px 9px 12px;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--ink);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: border-color .2s, box-shadow .2s, transform .2s var(--ease);
    }

    .sv-back svg { transition: transform .25s var(--ease); }

    .sv-back:hover {
        border-color: var(--brand);
        box-shadow: 0 4px 14px rgba(29, 58, 122, .12);
    }

    .sv-back:hover svg { transform: translateX(-3px); }

    .sv-status.is-step { background: rgba(255, 255, 255, .16); color: #fff; }

    /* ---------- next step ---------- */
    .sv-bar-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
    .sv-bar-actions form { margin: 0; }

    .sv-btn-next {
        background: var(--gold);
        color: var(--brand-deep);
        border-color: #c4902f;
        padding: 10px 18px;
        border-radius: 999px;
    }

    .sv-btn-next:hover { background: #e6b551; }
    .sv-btn-next svg { transition: transform .25s var(--ease); }
    .sv-btn-next:hover svg { transform: translateX(3px); }

    .sv-step-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 999px;
        background: #dcf5e5;
        color: #11693a;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform .2s var(--ease), box-shadow .2s;
    }

    .sv-step-note:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(17, 105, 58, .18); }

    /* ---------- hero ---------- */
    .sv-hero {
        position: relative;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 26px;
        padding: 30px 34px;
        border-radius: 22px;
        color: #fff;
        background:
            radial-gradient(900px 260px at 100% 0%, rgba(217, 164, 65, .28), transparent 60%),
            linear-gradient(120deg, var(--brand-deep), var(--brand));
        overflow: hidden;
        margin-bottom: 22px;
    }

    .sv-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        bottom: -90px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        border: 38px solid rgba(255, 255, 255, .05);
        pointer-events: none;
    }

    .sv-avatar {
        width: 84px;
        height: 84px;
        border-radius: 24px;
        display: grid;
        place-items: center;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: .02em;
        color: var(--brand-deep);
        background: linear-gradient(145deg, #fff, #ffe9b8);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .25);
        transform: rotate(-4deg);
        transition: transform .4s var(--ease);
    }

    .sv-hero:hover .sv-avatar { transform: rotate(0deg) scale(1.04); }

    .sv-hero-name {
        margin: 0 0 8px;
        font-size: clamp(22px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .sv-hero-facts {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 22px;
        margin: 0;
        padding: 0;
        list-style: none;
        font-size: 14px;
        color: rgba(255, 255, 255, .8);
    }

    .sv-hero-facts strong { color: #fff; font-weight: 700; }

    .sv-hero-side {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .sv-ref {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px 8px 16px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .22);
        backdrop-filter: blur(4px);
    }

    .sv-ref-label { font-size: 12px; color: rgba(255, 255, 255, .7); }

    .sv-ref-code {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: .06em;
        font-variant-numeric: tabular-nums;
    }

    .sv-copy {
        border: 0;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 10px;
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        color: var(--brand-deep);
        background: var(--gold);
        transition: transform .2s var(--ease), background .2s;
    }

    .sv-copy:hover { transform: translateY(-1px); background: #e6b551; }
    .sv-copy:active { transform: scale(.96); }
    .sv-copy.is-done { background: #8be0a8; }

    /* ---------- status pill ---------- */
    .sv-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        background: #fff3d6;
        color: #8a5a00;
    }

    .sv-status::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .sv-status.is-pending::before { animation: sv-pulse 1.8s ease-out infinite; }
    .sv-status.is-approved { background: #dcf5e5; color: #11693a; }
    .sv-status.is-rejected { background: #fde2e2; color: #a21c1c; }
    .sv-status.is-for_compliance { background: #dfeaff; color: #1a46a8; }

    @keyframes sv-pulse {
        0%   { box-shadow: 0 0 0 0 currentColor; }
        70%  { box-shadow: 0 0 0 8px transparent; }
        100% { box-shadow: 0 0 0 0 transparent; }
    }

    /* ---------- alert ---------- */
    .sv-alert {
        margin-bottom: 18px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #fde8e8;
        color: #9b1c1c;
        font-size: 14px;
        font-weight: 600;
    }

    /* ---------- layout ---------- */
    .sv-cols {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 22px;
        align-items: start;
        margin-bottom: 22px;
    }

    .sv-stack { display: grid; gap: 22px; }

    .sv-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 20px;
        overflow: hidden;
    }

    .sv-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 24px;
        border-bottom: 1px solid var(--line);
    }

    .sv-card-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: var(--brand);
        background: #e8eefc;
    }

    .sv-card-head h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .sv-card-body { padding: 8px 24px 18px; }
    .sv-card-body.pad { padding: 22px 24px; }

    /* ---------- key / value rows ---------- */
    .sv-list { margin: 0; }

    .sv-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 13px 10px;
        margin: 0 -10px;
        border-bottom: 1px dashed var(--line);
        border-radius: 10px;
        transition: background .2s;
    }

    .sv-row:last-child { border-bottom: 0; }
    .sv-row:hover { background: var(--wash); }

    .sv-row dt { color: var(--ink-soft); font-size: 13px; font-weight: 500; }

    .sv-row dd {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .sv-row dd.is-empty { color: var(--ink-faint); font-weight: 500; }

    /* ---------- file rows ---------- */
    .sv-note { margin: 0 0 16px; color: var(--ink-soft); font-size: 14px; }

    .sv-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px 18px;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: var(--surface);
        transition: border-color .25s, box-shadow .25s, transform .25s var(--ease);
    }

    .sv-file:hover {
        border-color: #c4cfec;
        box-shadow: 0 10px 26px rgba(20, 38, 79, .08);
        transform: translateY(-2px);
    }

    .sv-file-info { display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1 1 280px; }

    .sv-badge {
        flex: none;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 12px;
        font-weight: 800;
        background: #fde8e8;
        color: #c22b2b;
    }

    .sv-badge.is-image { background: #dfeaff; color: #1a46a8; }
    .sv-badge.is-file  { background: #ece9f8; color: #5a46b5; }

    .sv-file-info strong { display: block; font-size: 15px; font-weight: 700; margin-bottom: 2px; }

    .sv-file-info p {
        margin: 0;
        font-size: 13px;
        color: var(--ink-soft);
        overflow-wrap: anywhere;
    }

    .sv-file-info small { font-size: 12px; color: var(--ink-faint); }

    .sv-actions { display: flex; flex-wrap: wrap; gap: 8px; }

    .sv-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 16px;
        border: 1px solid transparent;
        border-radius: 11px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: transform .2s var(--ease), box-shadow .2s, background .2s;
    }

    .sv-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(20, 38, 79, .14); }
    .sv-btn:active { transform: translateY(0) scale(.98); }

    .sv-btn-view { background: #e8eefc; color: var(--brand); border-color: #cfdaf7; }
    .sv-btn-download { background: #fff; color: var(--ink); border-color: var(--line); }
    .sv-btn-zip { background: var(--brand-deep); color: #fff; }
    .sv-btn-zip:hover { background: var(--brand); }

    .sv-docs { display: grid; gap: 12px; }

    .sv-empty {
        margin: 0;
        padding: 26px;
        text-align: center;
        color: var(--ink-faint);
        font-size: 14px;
        border: 1px dashed var(--line);
        border-radius: 14px;
    }

    /* ---------- footer ---------- */
    .sv-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
        color: var(--ink-soft);
        font-size: 13px;
    }

    /* ---------- image lightbox (opens on click) ---------- */
    .sv-lightbox {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 24px;
        background: rgba(10, 18, 40, .72);
        backdrop-filter: blur(6px);
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s ease;
    }

    .sv-lightbox.is-open { opacity: 1; pointer-events: auto; }

    .sv-lightbox-box {
        position: relative;
        max-width: min(92vw, 980px);
        max-height: 90vh;
        padding: 12px;
        border-radius: 18px;
        background: #fff;
        transform: scale(.94) translateY(10px);
        transition: transform .3s var(--ease);
    }

    .sv-lightbox.is-open .sv-lightbox-box { transform: none; }

    .sv-lightbox-img {
        display: block;
        max-width: 100%;
        max-height: calc(90vh - 24px);
        border-radius: 10px;
        margin: 0 auto;
    }

    .sv-lightbox-close {
        position: absolute;
        top: -14px;
        right: -14px;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        background: var(--brand-deep);
        box-shadow: 0 6px 16px rgba(0, 0, 0, .3);
        transition: transform .2s var(--ease);
    }

    .sv-lightbox-close:hover { transform: rotate(90deg); }

    /* ---------- toast ---------- */
    .sv-toast {
        position: fixed;
        left: 50%;
        bottom: 28px;
        z-index: 10000;
        padding: 11px 18px;
        border-radius: 12px;
        background: var(--brand-deep);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        transform: translate(-50%, 20px);
        opacity: 0;
        pointer-events: none;
        transition: transform .3s var(--ease), opacity .3s;
    }

    .sv-toast.is-show { transform: translate(-50%, 0); opacity: 1; }

    /* ---------- focus + responsive + motion ---------- */
    .sv a:focus-visible,
    .sv button:focus-visible,
    .sv-lightbox button:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    @media (max-width: 960px) {
        .sv-cols { grid-template-columns: 1fr; }
        .sv-hero { grid-template-columns: auto 1fr; }
        .sv-hero-side { grid-column: 1 / -1; align-items: flex-start; flex-direction: row; flex-wrap: wrap; }
    }

    @media (max-width: 560px) {
        .sv-hero { padding: 22px; gap: 18px; }
        .sv-avatar { width: 64px; height: 64px; font-size: 22px; border-radius: 18px; }
        .sv-card-head, .sv-card-body.pad { padding-left: 18px; padding-right: 18px; }
        .sv-card-body { padding-left: 18px; padding-right: 18px; }
        .sv-actions { width: 100%; }
        .sv-actions .sv-btn { flex: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sv-reveal { animation: none; opacity: 1; transform: none; }
        .sv *, .sv-lightbox, .sv-lightbox-box, .sv-toast { transition: none !important; animation: none !important; }
    }

    :root[data-theme="dark"] .sv-status { background: #49391e; color: #f7d48a; }
    :root[data-theme="dark"] .sv-status.is-approved,
    :root[data-theme="dark"] .sv-step-note { background: #193d30; color: #91e0b4; }
    :root[data-theme="dark"] .sv-status.is-rejected,
    :root[data-theme="dark"] .sv-alert { background: #48292d; color: #ffaaaa; }
    :root[data-theme="dark"] .sv-status.is-for_compliance { background: #34284b; color: #d0b5ff; }
    :root[data-theme="dark"] .sv-card-icon,
    :root[data-theme="dark"] .sv-btn-view { background: #243b5b; border-color: #38577c; color: #b9d7ff; }
    :root[data-theme="dark"] .sv-file:hover { border-color: #58769b; }
    :root[data-theme="dark"] .sv-badge { background: #48292d; color: #ffaaaa; }
    :root[data-theme="dark"] .sv-badge.is-image { background: #243b5b; color: #b9d7ff; }
    :root[data-theme="dark"] .sv-badge.is-file { background: #34284b; color: #d0b5ff; }
    :root[data-theme="dark"] .sv-btn-download,
    :root[data-theme="dark"] .sv-lightbox-box { background: var(--surface); color: var(--ink); border-color: var(--line); }
</style>
@endpush

@section('content')

@php
    $studentData = $enrollment->data ?? [];

    if (is_string($studentData)) {
        $studentData = json_decode($studentData, true) ?? [];
    }

    $studentData = is_array($studentData) ? $studentData : [];

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

    $sex = $enrollment->sexLabel() ?: '—';

    $gradeLevel = $enrollment->grade_level
        ?: (data_get($studentData, 'grade_level') ?: '—');

    $schoolYear = $enrollment->school_year
        ?: (data_get($studentData, 'school_year') ?: '—');

    $birthdate = $enrollment->birthdate
        ? \Illuminate\Support\Carbon::parse($enrollment->birthdate)->format('F d, Y')
        : (data_get($studentData, 'birthdate') ?: '—');

    $fullName = trim(implode(' ', array_filter([
        $enrollment->first_name,
        $enrollment->middle_name,
        $enrollment->last_name,
        $enrollment->extension_name,
    ])));

    $initials = strtoupper(
        mb_substr((string) $enrollment->first_name, 0, 1) .
        mb_substr((string) $enrollment->last_name, 0, 1)
    ) ?: '?';

    $documents = $enrollment->documents ?? collect();
    $docs = $documents;

    $icon = [
        'user'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>',
        'phone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>',
        'book'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M20 17v4H6.5A2.5 2.5 0 0 1 4 18.5"/></svg>',
        'form'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>',
        'clip'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8 12.5 21.3a6 6 0 0 1-8.5-8.5l9-9a4 4 0 0 1 5.7 5.7l-9 9a2 2 0 0 1-2.9-2.9L14 7.5"/></svg>',
    ];

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        ($value === '—' ? 'is-empty' : '') . '">' . e($value) . '</dd></div>';
@endphp

<div class="sv">

    {{-- TOP BAR --}}
    <div class="sv-bar sv-reveal" style="--i:0">
        <p class="sv-bar-title">Step 1 · Appointment booked</p>

        <div class="sv-bar-actions">
            @unless(auth()->user()->canEdit('step1'))
                <span class="sv-viewonly">View only</span>
            @endunless

            @if($enrollment->isInStep2())
                @if(auth()->user()->canView('step2'))
                <a href="{{ route('admin.step2.view', $enrollment->id) }}" class="sv-step-note">
                    In Step 2, open
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                @endif
            @elseif(auth()->user()->canEdit('step1'))
                <form method="POST"
                      action="{{ route('admin.step2.proceed', $enrollment->id) }}"
                      onsubmit="return confirm('Send this student to Step 2 (Infirmary)?');">
                    @csrf
                    <button type="submit" class="sv-btn sv-btn-next">
                        Proceed to Step 2
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.index') }}" class="sv-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Back to Step 1
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="sv-alert sv-reveal" style="--i:0">{{ $errors->first() }}</div>
    @endif

    {{-- HERO --}}
    <section class="sv-hero sv-reveal" style="--i:1">
        <div class="sv-avatar" aria-hidden="true">{{ $initials }}</div>

        <div>
            <h1 class="sv-hero-name">{{ $fullName ?: 'Unnamed student' }}</h1>

            <ul class="sv-hero-facts">
                <li>Grade <strong>{{ $gradeLevel }}</strong></li>
                <li>Strand <strong>{{ $strand }}</strong></li>
                <li>SY <strong>{{ $schoolYear }}</strong></li>
                <li>Submitted <strong>{{ $enrollment->created_at?->format('M d, Y') ?? '—' }}</strong></li>
            </ul>
        </div>

        <div class="sv-hero-side">
            <span class="sv-status is-{{ $enrollment->status ?? 'pending' }}">
                {{ $enrollment->statusLabel() }}
            </span>

            @if($enrollment->stepNumber() > 1)
                <span class="sv-status is-step">{{ $enrollment->stageLabel() }}</span>
            @endif

            @if($enrollment->tracking_number)
                <div class="sv-ref">
                    <div>
                        <div class="sv-ref-label">Reference ID</div>
                        <div class="sv-ref-code" id="svRefCode">{{ $enrollment->tracking_number }}</div>
                    </div>
                    <button type="button" class="sv-copy" id="svCopy">Copy</button>
                </div>
            @endif
        </div>
    </section>

    {{-- DETAILS --}}
    <div class="sv-cols">

        <section class="sv-card sv-reveal" style="--i:2">
            <div class="sv-card-head">
                <span class="sv-card-icon">{!! $icon['user'] !!}</span>
                <h2>Student information</h2>
            </div>

            <div class="sv-card-body">
                <dl class="sv-list">
                    <div class="sv-row"><dt>Enrollment ID</dt><dd>#{{ $enrollment->id }}</dd></div>
                    {!! $row('Last name', $enrollment->last_name ?: '—') !!}
                    {!! $row('First name', $enrollment->first_name ?: '—') !!}
                    {!! $row('Middle name', $enrollment->middle_name ?: '—') !!}
                    {!! $row('Extension name', $enrollment->extension_name ?: '—') !!}
                    {!! $row('LRN', $enrollment->lrn ?: '—') !!}
                    {!! $row('Birthdate', $birthdate) !!}
                    {!! $row('Sex', $sex) !!}
                    {!! $row('Grade level', (string) $gradeLevel) !!}
                </dl>
            </div>
        </section>

        <div class="sv-stack">

            <section class="sv-card sv-reveal" style="--i:3">
                <div class="sv-card-head">
                    <span class="sv-card-icon">{!! $icon['phone'] !!}</span>
                    <h2>Contact information</h2>
                </div>

                <div class="sv-card-body">
                    <dl class="sv-list">
                        {!! $row('Mobile number', $mobile) !!}
                        {!! $row('Email address', $email) !!}
                    </dl>
                </div>
            </section>

            <section class="sv-card sv-reveal" style="--i:4">
                <div class="sv-card-head">
                    <span class="sv-card-icon">{!! $icon['book'] !!}</span>
                    <h2>Enrollment information</h2>
                </div>

                <div class="sv-card-body">
                    <dl class="sv-list">
                        {!! $row('School year', (string) $schoolYear) !!}
                        {!! $row('SHS strand', $strand) !!}
                        {!! $row('Submitted', $enrollment->created_at ? $enrollment->created_at->format('F d, Y h:i A') : '—') !!}
                        {!! $row('Admission appointment', $enrollment->appointmentLabel() ?: '—') !!}
                        {!! $row('Source', $enrollment->is_walk_in ? 'Walk-in' . ($enrollment->walk_in_by ? ' (entered by ' . $enrollment->walk_in_by . ')' : '') : 'Online') !!}
                        <div class="sv-row">
                            <dt>Application status</dt>
                            <dd>
                                <span class="sv-status is-{{ $enrollment->status ?? 'pending' }}">
                                    {{ $enrollment->statusLabel() }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>

        </div>
    </div>

    {{-- GENERATED FORM PDF --}}
    <section class="sv-card sv-reveal" style="--i:5; margin-bottom:22px;">
        <div class="sv-card-head">
            <span class="sv-card-icon">{!! $icon['form'] !!}</span>
            <h2>Filled-up enrollment form</h2>
        </div>

        <div class="sv-card-body pad">
            <p class="sv-note">
                DepEd enrollment form generated from the student's submitted information.
            </p>

            <div class="sv-file">
                <div class="sv-file-info">
                    <div class="sv-badge">PDF</div>

                    <div>
                        <strong>Completed DepEd Enrollment Form</strong>
                        <p>{{ $fullName }}</p>
                        <small>
                            Enrollment #{{ $enrollment->id }},
                            {{ $enrollment->created_at?->format('F d, Y') ?? '—' }}
                        </small>
                    </div>
                </div>

                <div class="sv-actions">
                    <a href="{{ route('admin.pdf', $enrollment->id) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="sv-btn sv-btn-view">View PDF</a>

                    <a href="{{ route('admin.pdf', $enrollment->id) }}?download=1"
                       class="sv-btn sv-btn-download">Download PDF</a>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <div class="sv-foot sv-reveal" style="--i:7">
        <span>
            Record created
            <strong>{{ $enrollment->created_at?->format('F d, Y h:i A') ?? '—' }}</strong>
        </span>

        <a href="{{ route('admin.index') }}" class="sv-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to student list
        </a>
    </div>

</div>

{{-- TOAST --}}
<div class="sv-toast" id="svToast" role="status" aria-live="polite"></div>

@endsection

@push('scripts')
<script>
(function () {
    const toast = document.getElementById('svToast');
    let toastTimer;

    function showToast(text) {
        toast.textContent = text;
        toast.classList.add('is-show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('is-show'), 1800);
    }

    /* Copy reference ID */
    const copyBtn = document.getElementById('svCopy');
    const refCode = document.getElementById('svRefCode');

    if (copyBtn && refCode) {
        copyBtn.addEventListener('click', async () => {
            const text = refCode.textContent.trim();

            try {
                await navigator.clipboard.writeText(text);
            } catch (e) {
                const range = document.createRange();
                range.selectNodeContents(refCode);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                document.execCommand('copy');
                sel.removeAllRanges();
            }

            copyBtn.textContent = 'Copied';
            copyBtn.classList.add('is-done');
            showToast('Reference ID copied');

            setTimeout(() => {
                copyBtn.textContent = 'Copy';
                copyBtn.classList.remove('is-done');
            }, 1600);
        });
    }
})();
</script>
@endpush
