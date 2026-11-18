
@extends('layouts.admin')

@section('title', 'Step 5 · GCO Assessment Details')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $canEdit = auth()->user()->canEdit('step5');

    $strand = $enrollment->strandCode() ?: '—';

    $gco = old('gco_status', $enrollment->gco_status ?: 'pending');

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        (($value === '—' || $value === '') ? 'is-empty' : '') . '">' .
        e($value === '' ? '—' : $value) .
        '</dd></div>';
@endphp

<div class="sv">

    {{-- TOPBAR --}}
    @include('admin.steps._topbar', [
        'module' => 'step5',
        'title' => 'Step 5 · GCO Assessment',
        'links' => [
            ['Step 4 details', route('admin.step4.view', $enrollment->id)],
            ['Back to Step 5', route('admin.step5.index')],
        ],
        'next' => [
            'step' => 6,
            'label' => 'Mark as enrolled',
            'route' => 'admin.step6.proceed',
            'open' => 'admin.step6.view',
            'confirm' => 'Mark this student as enrolled? The GCO assessment must be Done.',
        ],
    ])

    {{-- HERO --}}
    @include('admin.steps._hero', ['facts' => [
        'ID No.' => $enrollment->student_id,
        'Grade' => $enrollment->grade_level,
        'Strand' => $strand,
        'GCO assessment' => $enrollment->gcoLabel() ?: 'Not scheduled',
    ]])

    {{-- STEPPER --}}
    @include('admin.steps._stepper')

    <div class="sv-cols">

        {{-- GCO ASSESSMENT --}}
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

                <h2>GCO assessment</h2>

            </div>

            <div class="sv-card-body pad">

                <form method="POST"
                      action="{{ route('admin.step5.save', $enrollment->id) }}"
                      class="sv-form">

                    @csrf
                    @method('PUT')

                    <fieldset class="sv-lock" @disabled(!$canEdit)>

                        <div class="sv-fields">

                            {{-- ASSESSMENT DATE --}}
                            <div class="sv-field">

                                <label for="gco_date">
                                    Assessment date
                                </label>

                                <input
                                    type="date"
                                    id="gco_date"
                                    name="gco_date"
                                    class="sv-input"
                                    value="{{ old('gco_date', $enrollment->gco_date?->format('Y-m-d')) }}"
                                >

                            </div>

                            {{-- ASSESSMENT TIME --}}
                            <div class="sv-field">

                                <label for="gco_time">
                                    Assessment time
                                </label>

                                <input
                                    type="time"
                                    id="gco_time"
                                    name="gco_time"
                                    class="sv-input"
                                    value="{{ old('gco_time', $enrollment->gco_time) }}"
                                >

                            </div>

                            {{-- GCO STATUS --}}
                            <div class="sv-field is-wide">

                                <label for="gco_status">
                                    Status
                                </label>

                                <select
                                    id="gco_status"
                                    name="gco_status"
                                    class="sv-select"
                                >

                                    @foreach(\App\Models\Enrollment::GCO_STATUSES as $value => $label)

                                        <option
                                            value="{{ $value }}"
                                            @selected($gco === $value)
                                        >
                                            {{ $label }}
                                        </option>

                                    @endforeach

                                </select>

                                <p class="sv-help">
                                    Set this to Done before marking the student as enrolled.
                                </p>

                            </div>

                        </div>

                        {{-- FORM ACTIONS --}}
                        @if($canEdit)

                            <div class="sv-form-actions">

                                <button type="submit" class="sv-btn sv-btn-save">
                                    Save assessment
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

        {{-- STUDENT INFORMATION --}}
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

                <h2>From ID capturing</h2>

            </div>

            <div class="sv-card-body">

                <dl class="sv-list">

                    {!! $row('Family name', (string) $enrollment->last_name) !!}

                    {!! $row('Given name', (string) $enrollment->first_name) !!}

                    {!! $row('Suffix', (string) $enrollment->extension_name) !!}

                    {!! $row('Strand', $strand) !!}

                    {!! $row('ID capturing appointment', $enrollment->idCaptureLabel()) !!}

                    {!! $row(
                        'ID captured',
                        $enrollment->id_captured ? 'Yes' : 'Not yet'
                    ) !!}

                </dl>

            </div>

        </section>

    </div>

</div>

@endsection

@push('scripts')
    @include('admin.steps._scripts')
@endpush
