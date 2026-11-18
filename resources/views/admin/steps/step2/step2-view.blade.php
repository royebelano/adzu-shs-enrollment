@extends('layouts.admin')

@section('title', 'Step 2 · Infirmary Details')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $studentData = $enrollment->data ?? [];

    if (is_string($studentData)) {
        $studentData = json_decode($studentData, true) ?? [];
    }

    $studentData = is_array($studentData) ? $studentData : [];

    $strand = data_get($studentData, 'shs_strand')
        ?: data_get($studentData, 'preferred_strand')
        ?: data_get($studentData, 'strand')
        ?: '—';

    $gradeLevel = $enrollment->grade_level
        ?: (data_get($studentData, 'grade_level') ?: '—');

    $schoolYear = $enrollment->school_year
        ?: (data_get($studentData, 'school_year') ?: '—');

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

    $icon = [
        'form'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>',
        'pulse' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
        'book'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M20 17v4H6.5A2.5 2.5 0 0 1 4 18.5"/></svg>',
    ];

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        (($value === '—' || $value === '') ? 'is-empty' : '') . '">' .
        e($value === '' ? 'Left blank' : $value) . '</dd></div>';

    $printUrl = route('admin.step2.print', $enrollment->id);
@endphp

<div class="sv">

    @include('admin.steps._topbar', [
        'module' => 'step2',
        'title' => 'Step 2 · Infirmary',
        'links' => [
            ['Step 1 details', route('admin.step1.view', $enrollment->id)],
            ['Back to Step 2', route('admin.step2.index')],
        ],
        'next' => [
            'step' => 3,
            'label' => 'Proceed to Step 3',
            'route' => 'admin.step3.proceed',
            'open' => 'admin.step3.view',
            'confirm' => 'Send this student to Step 3 (Admission)? A student ID number will be assigned.',
        ],
    ])

    {{-- HERO --}}
    <section class="sv-hero sv-reveal" style="--i:1">
        <div class="sv-avatar" aria-hidden="true">{{ $initials }}</div>

        <div>
            <h1 class="sv-hero-name">{{ $fullName ?: 'Unnamed student' }}</h1>

            <ul class="sv-hero-facts">
                <li>Grade <strong>{{ $gradeLevel }}</strong></li>
                <li>Strand <strong>{{ $strand }}</strong></li>
                <li>SY <strong>{{ $schoolYear }}</strong></li>
                <li>In Step 2 since <strong>{{ $enrollment->step2_at?->format('M d, Y') ?? '—' }}</strong></li>
            </ul>
        </div>

        <div class="sv-hero-side">
            <span class="sv-status is-step">{{ $enrollment->stageLabel() }}</span>

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

    <div class="sv-cols">

        {{-- WHAT GETS PRINTED --}}
        <section class="sv-card sv-reveal" style="--i:2">
            <div class="sv-card-head">
                <span class="sv-card-icon">{!! $icon['pulse'] !!}</span>
                <h2>Filled in on the printed form</h2>
            </div>

            <div class="sv-card-body">
                <dl class="sv-list">
                    @foreach($labels as $key => $label)
                        {!! $row($label, (string) ($filled[$key] ?? '')) !!}
                    @endforeach
                </dl>
            </div>
        </section>

        <div class="sv-stack">

            {{-- PRINT --}}
            <section class="sv-card sv-reveal" style="--i:3">
                <div class="sv-card-head">
                    <span class="sv-card-icon">{!! $icon['form'] !!}</span>
                    <h2>Health examination record</h2>
                </div>

                <div class="sv-card-body pad">
                    <p class="sv-fill-note">
                        The fields on the left come from the student's Step 1 form.
                        <strong>The doctor fills in the rest by hand</strong> after printing.
                    </p>

                    <div class="sv-actions">
                        <button type="button" class="sv-btn sv-btn-print" id="svPrint"
                                data-url="{{ $printUrl }}">
                            Print form
                        </button>

                        <a href="{{ $printUrl }}" target="_blank" rel="noopener noreferrer"
                           class="sv-btn sv-btn-view">View PDF</a>

                        <a href="{{ $printUrl }}?download=1" class="sv-btn sv-btn-download">Download PDF</a>
                    </div>

                    <ul class="sv-todo" aria-label="Completed by the doctor">
                        <li>Medical history</li>
                        <li>Immunization</li>
                        <li>Physical exam</li>
                        <li>Dental exam</li>
                    </ul>
                </div>
            </section>

            {{-- STEP 1 SUMMARY --}}
            <section class="sv-card sv-reveal" style="--i:4">
                <div class="sv-card-head">
                    <span class="sv-card-icon">{!! $icon['book'] !!}</span>
                    <h2>From Step 1</h2>
                </div>

                <div class="sv-card-body">
                    <dl class="sv-list">
                        <div class="sv-row"><dt>Enrollment ID</dt><dd>#{{ $enrollment->id }}</dd></div>
                        {!! $row('LRN', $enrollment->lrn ?: '—') !!}
                        {!! $row('Grade level', (string) $gradeLevel) !!}
                        {!! $row('SHS strand', $strand) !!}
                        {!! $row('Submitted', $enrollment->created_at ? $enrollment->created_at->format('F d, Y h:i A') : '—') !!}
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

    <div class="sv-foot sv-reveal" style="--i:5">
        <span>
            Sent to Step 2 by
            <strong>{{ $enrollment->step2_by ?: '—' }}</strong>
            on
            <strong>{{ $enrollment->step2_at?->format('F d, Y h:i A') ?? '—' }}</strong>
        </span>

        <a href="{{ route('admin.step2.index') }}" class="sv-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to Step 2
        </a>
    </div>

</div>

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
        toastTimer = setTimeout(() => toast.classList.remove('is-show'), 2200);
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

    /* Print: load the PDF in a hidden frame and open the print dialog.
       If the browser blocks that, open the PDF in a new tab instead. */
    const printBtn = document.getElementById('svPrint');

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            const url = printBtn.dataset.url;
            const label = printBtn.textContent;

            printBtn.disabled = true;
            printBtn.textContent = 'Preparing...';

            const frame = document.createElement('iframe');
            frame.style.cssText = 'position:fixed;width:0;height:0;border:0;right:0;bottom:0;';
            frame.src = url;

            const reset = () => {
                printBtn.disabled = false;
                printBtn.textContent = label;
            };

            frame.onload = () => {
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                    showToast('Print dialog opened');
                } catch (e) {
                    window.open(url, '_blank', 'noopener');
                    showToast('Opened the PDF. Press Ctrl+P to print.');
                }

                reset();
                setTimeout(() => frame.remove(), 60000);
            };

            frame.onerror = () => {
                window.open(url, '_blank', 'noopener');
                reset();
            };

            document.body.appendChild(frame);
        });
    }
})();
</script>
@endpush
