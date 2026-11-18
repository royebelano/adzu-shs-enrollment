
@extends('layouts.admin')

@section('title', 'Step 4 · ID Capturing Details')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $canEdit = auth()->user()->canEdit('step4');

    $contact = $enrollment->contactPerson();
    $address = $enrollment->currentAddress();

    $strand = $enrollment->strandCode() ?: '—';

    $barcodeValue = old(
        'barcode',
        $enrollment->barcode ?: $enrollment->student_id
    );

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        (($value === '—' || $value === '') ? 'is-empty' : '') . '">' .
        e($value === '' ? '—' : $value) .
        '</dd></div>';
@endphp

<div class="sv">

    {{-- TOPBAR --}}
    @include('admin.steps._topbar', [
        'module' => 'step4',
        'title' => 'Step 4 · ID Capturing',
        'links' => [
            ['Step 3 details', route('admin.step3.view', $enrollment->id)],
            ['Back to Step 4', route('admin.step4.index')],
        ],
        'next' => [
            'step' => 5,
            'label' => 'Proceed to Step 5',
            'route' => 'admin.step5.proceed',
            'open' => 'admin.step5.view',
            'confirm' => 'Send this student to Step 5 (GCO Assessment)?',
        ],
    ])

    {{-- HERO --}}
    @include('admin.steps._hero', ['facts' => [
        'ID No.' => $enrollment->student_id,
        'Grade' => $enrollment->grade_level,
        'Strand' => $strand,
        'ID capturing' => $enrollment->idCaptureLabel() ?: 'Not scheduled',
    ]])

    {{-- STEPPER --}}
    @include('admin.steps._stepper')

    <div class="sv-cols">

        {{-- ID CAPTURING APPOINTMENT --}}
        <section class="sv-card sv-reveal" style="--i:3">

            <div class="sv-card-head">

                <span class="sv-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 11h18"/>
                    </svg>
                </span>

                <h2>ID capturing appointment</h2>

            </div>

            <div class="sv-card-body pad">

                <form method="POST"
                      action="{{ route('admin.step4.save', $enrollment->id) }}"
                      class="sv-form">

                    @csrf
                    @method('PUT')

                    <fieldset class="sv-lock" @disabled(!$canEdit)>

                        <div class="sv-fields">

                            {{-- APPOINTMENT DATE --}}
                            <div class="sv-field is-wide">

                                <label for="id_capture_date">
                                    Appointment date
                                </label>

                                <input
                                    type="date"
                                    id="id_capture_date"
                                    name="id_capture_date"
                                    class="sv-input"
                                    value="{{ old('id_capture_date', $enrollment->id_capture_date?->format('Y-m-d')) }}"
                                >

                            </div>

                            {{-- START TIME --}}
                            <div class="sv-field">

                                <label for="id_capture_time">
                                    Start time
                                </label>

                                <input
                                    type="time"
                                    id="id_capture_time"
                                    name="id_capture_time"
                                    class="sv-input"
                                    value="{{ old('id_capture_time', $enrollment->id_capture_time) }}"
                                >

                            </div>

                            {{-- END TIME --}}
                            <div class="sv-field">

                                <label for="id_capture_end">
                                    End time
                                </label>

                                <input
                                    type="time"
                                    id="id_capture_end"
                                    name="id_capture_end"
                                    class="sv-input"
                                    value="{{ old('id_capture_end', $enrollment->id_capture_end) }}"
                                >

                            </div>

                            {{-- BARCODE --}}
                            <div class="sv-field is-wide">

                                <label for="barcode">
                                    Bar code
                                </label>

                                <input
                                    type="text"
                                    id="barcode"
                                    name="barcode"
                                    class="sv-input s1-mono"
                                    maxlength="40"
                                    value="{{ $barcodeValue }}"
                                >

                                <p class="sv-help">
                                    Uses the student ID number by default.
                                </p>

                            </div>

                        </div>

                        {{-- ID CAPTURED CHECKBOX --}}
                        <label class="sv-check">

                            <input
                                type="checkbox"
                                name="id_captured"
                                value="1"
                                @checked(old('id_captured', $enrollment->id_captured))
                            >

                            <span>
                                <div>
                                    <strong>ID captured</strong>

                                    <small>
                                        Tick this once the student's photo and ID have been taken
                                    </small>
                                </div>
                            </span>

                        </label>

                        {{-- FORM ACTIONS --}}
                        @if($canEdit)

                            <div class="sv-form-actions">

                                <button type="submit" class="sv-btn sv-btn-save">
                                    Save appointment
                                </button>

                            </div>

                        @else

                            <div class="sv-form-actions">
                                <span class="sv-readonly-notice">
                                    Read-only access
                                </span>
                            </div>

                        @endif

                    </fieldset>

                </form>

            </div>

        </section>

        <div class="sv-stack">

            {{-- CONTACT PERSON --}}
            <section class="sv-card sv-reveal" style="--i:4">

                <div class="sv-card-head">

                    <span class="sv-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 4-7 8-7s8 3 8 7"/>
                        </svg>
                    </span>

                    <h2>Contact person</h2>

                </div>

                <div class="sv-card-body">

                    <dl class="sv-list">

                        {!! $row('Contact person', $contact['name']) !!}

                        {!! $row('Relationship', $contact['relationship']) !!}

                        {!! $row('Contact number', $contact['number']) !!}

                        {!! $row('Contact address', $address) !!}

                    </dl>

                </div>

            </section>

            {{-- BARCODE DISPLAY --}}
            <section class="sv-card sv-reveal" style="--i:5">

                <div class="sv-card-head">

                    <span class="sv-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 5v14M8 5v14M12 5v14M17 5v14M20 5v14"/>
                        </svg>
                    </span>

                    <h2>Bar code</h2>

                </div>

                <div class="sv-card-body pad">

                    <div class="sv-barcode">

                        <div class="sv-barcode-bars" aria-hidden="true"></div>

                        <div class="sv-barcode-text s1-mono">
                            {{ $enrollment->barcode ?: $enrollment->student_id ?: '—' }}
                        </div>

                    </div>

                </div>

            </section>

        </div>

    </div>

</div>

@endsection

@push('scripts')
    @include('admin.steps._scripts')
@endpush
