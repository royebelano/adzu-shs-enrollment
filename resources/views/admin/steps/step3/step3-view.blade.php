
@extends('layouts.admin')

@section('title', 'Step 3 · Admission Details')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $canEdit = auth()->user()->canEdit('step3');

    $docTypes = $enrollment->documents->pluck('type')->unique()->all();

    $has = fn ($type) => in_array($type, $docTypes, true);

    $school = $enrollment->last_school ?: data_get($enrollment->data, 'last_school');

    $strand = $enrollment->strandCode() ?: '—';

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        (($value === '—' || $value === '') ? 'is-empty' : '') . '">' .
        e($value === '' ? '—' : $value) .
        '</dd></div>';
@endphp

<div class="sv">

    {{-- TOPBAR --}}
    @include('admin.steps._topbar', [
        'module' => 'step3',
        'title' => 'Step 3 · Admission',
        'links' => [
            ['Step 2 details', route('admin.step2.view', $enrollment->id)],
            ['Back to Step 3', route('admin.step3.index')],
        ],
        'next' => [
            'step' => 4,
            'label' => 'Proceed to Step 4',
            'route' => 'admin.step4.proceed',
            'open' => 'admin.step4.view',
            'confirm' => 'Send this student to Step 4 (ID Capturing)?',
        ],
    ])

    {{-- HERO --}}
    @include('admin.steps._hero', ['facts' => [
        'ID No.' => $enrollment->student_id,
        'Grade' => $enrollment->grade_level,
        'Strand' => $strand,
        'Admitted' => $enrollment->step3_at?->format('M d, Y'),
    ]])

    {{-- STEPPER --}}
    @include('admin.steps._stepper')

    <div class="sv-cols">

        {{-- ADMISSION CHECKLIST --}}
        <section class="sv-card sv-reveal" style="--i:3">

            <div class="sv-card-head">
                <span class="sv-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m5 12 5 5 9-10"/>
                    </svg>
                </span>

                <h2>Admission checklist</h2>
            </div>

            <div class="sv-card-body pad">

                <form method="POST"
                      action="{{ route('admin.step3.save', $enrollment->id) }}"
                      class="sv-form">

                    @csrf
                    @method('PUT')

                    <fieldset class="sv-lock" @disabled(!$canEdit)>

                        <div class="sv-checks">

                            {{-- F137 --}}
                            <label class="sv-check">
                                <input type="checkbox"
                                       name="adm_f137"
                                       value="1"
                                       @checked(old('adm_f137', $enrollment->adm_f137))>

                                <span>
                                    <div>
                                        <strong>F137</strong>
                                        <small>
                                            {{ $has('report_card')
                                                ? 'Report card uploaded'
                                                : 'No report card uploaded' }}
                                        </small>
                                    </div>
                                </span>
                            </label>

                            {{-- PSA --}}
                            <label class="sv-check">
                                <input type="checkbox"
                                       name="adm_psa"
                                       value="1"
                                       @checked(old('adm_psa', $enrollment->adm_psa))>

                                <span>
                                    <div>
                                        <strong>PSA birth certificate</strong>
                                        <small>
                                            {{ $has('psa_birth_certificate')
                                                ? 'File uploaded'
                                                : 'No file uploaded' }}
                                        </small>
                                    </div>
                                </span>
                            </label>

                            {{-- RECOMMENDATION MOD --}}
                            <label class="sv-check">
                                <input type="checkbox"
                                       name="adm_rec_mod"
                                       value="1"
                                       @checked(old('adm_rec_mod', $enrollment->adm_rec_mod))>

                                <span>
                                    <div>
                                        <strong>Recommendation: Mod</strong>
                                        <small>Checked by admission staff</small>
                                    </div>
                                </span>
                            </label>

                            {{-- RECOMMENDATION GC --}}
                            <label class="sv-check">
                                <input type="checkbox"
                                       name="adm_rec_gc"
                                       value="1"
                                       @checked(old('adm_rec_gc', $enrollment->adm_rec_gc))>

                                <span>
                                    <div>
                                        <strong>Recommendation: GC</strong>
                                        <small>
                                            {{ $has('good_moral')
                                                ? 'Good moral certificate uploaded'
                                                : 'Checked by admission staff' }}
                                        </small>
                                    </div>
                                </span>
                            </label>

                        </div>

                        {{-- REMARKS --}}
                        <div class="sv-field">

                            <label for="admission_remarks">
                                Remarks
                            </label>

                            <textarea
                                id="admission_remarks"
                                name="admission_remarks"
                                class="sv-textarea"
                                maxlength="1000"
                                placeholder="Optional notes for this student">{{ old('admission_remarks', $enrollment->admission_remarks) }}</textarea>

                        </div>

                        {{-- ACTIONS --}}
                        @if($canEdit)
                            <div class="sv-form-actions">
                                <button type="submit" class="sv-btn sv-btn-save">
                                    Save checklist
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

        {{-- STUDENT DETAILS --}}
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

                <h2>Student details</h2>

            </div>

            <div class="sv-card-body">

                <dl class="sv-list">

                    {!! $row('ID number', (string) $enrollment->student_id) !!}

                    {!! $row('Last name', (string) $enrollment->last_name) !!}

                    {!! $row('Given name', (string) $enrollment->first_name) !!}

                    {!! $row('Middle name', (string) $enrollment->middle_name) !!}

                    {!! $row('Suffix', (string) $enrollment->extension_name) !!}

                    {!! $row('School', (string) $school) !!}

                    {!! $row(
                        'Birthdate',
                        $enrollment->birthdate
                            ? $enrollment->birthdate->format('F d, Y')
                            : ''
                    ) !!}

                    {!! $row('Sex', $enrollment->sexLabel()) !!}

                    {!! $row('LRN', (string) $enrollment->lrn) !!}

                </dl>

            </div>

        </section>

    </div>

    {{-- DOCUMENTS --}}
    @include('admin.steps._documents')

</div>

@endsection

@push('scripts')
    @include('admin.steps._scripts')
@endpush
