@extends('layouts.admin')

@section('title', 'Step 6 · Enrolled Student')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $contact = $enrollment->contactPerson();
    $strand = $enrollment->strandCode() ?: '—';
    $gcoStatus = \App\Models\Enrollment::GCO_STATUSES[$enrollment->gco_status ?: 'pending'] ?? '—';

    $row = fn ($label, $value) =>
        '<div class="sv-row"><dt>' . e($label) . '</dt><dd class="' .
        (($value === '—' || $value === '') ? 'is-empty' : '') . '">' . e($value === '' ? '—' : $value) . '</dd></div>';
@endphp

<div class="sv">

    @include('admin.steps._topbar', [
        'module' => 'step6',
        'title' => 'Step 6 · Enrolled',
        'links' => [
            ['Step 5 details', route('admin.step5.view', $enrollment->id)],
            ['Back to Step 6', route('admin.step6.index')],
        ],
    ])

    @include('admin.steps._hero', ['facts' => [
        'ID No.' => $enrollment->student_id,
        'Grade' => $enrollment->grade_level,
        'Strand' => $strand,
        'Enrolled' => $enrollment->step6_at?->format('M d, Y'),
    ]])

    @include('admin.steps._stepper')

    <div class="sv-cols">

        <section class="sv-card sv-reveal" style="--i:3">
            <div class="sv-card-head">
                <span class="sv-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>
                </span>
                <h2>Student record</h2>
            </div>

            <div class="sv-card-body">
                <dl class="sv-list">
                    {!! $row('Student ID', (string) $enrollment->student_id) !!}
                    {!! $row('Family name', (string) $enrollment->last_name) !!}
                    {!! $row('Given name', (string) $enrollment->first_name) !!}
                    {!! $row('Middle name', (string) $enrollment->middle_name) !!}
                    {!! $row('Suffix', (string) $enrollment->extension_name) !!}
                    {!! $row('LRN', (string) $enrollment->lrn) !!}
                    {!! $row('Grade level', (string) $enrollment->grade_level) !!}
                    {!! $row('Strand', $strand) !!}
                    {!! $row('Bar code', (string) $enrollment->barcode) !!}
                </dl>
            </div>
        </section>

        <div class="sv-stack">

            <section class="sv-card sv-reveal" style="--i:4">
                <div class="sv-card-head">
                    <span class="sv-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                    </span>
                    <h2>Appointments</h2>
                </div>

                <div class="sv-card-body">
                    <dl class="sv-list">
                        {!! $row('ID capturing', $enrollment->idCaptureLabel()) !!}
                        {!! $row('ID captured', $enrollment->id_captured ? 'Yes' : 'Not yet') !!}
                        {!! $row('GCO assessment', $enrollment->gcoLabel()) !!}
                        {!! $row('GCO status', $gcoStatus) !!}
                    </dl>
                </div>
            </section>

            <section class="sv-card sv-reveal" style="--i:5">
                <div class="sv-card-head">
                    <span class="sv-card-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>
                    </span>
                    <h2>Contact</h2>
                </div>

                <div class="sv-card-body">
                    <dl class="sv-list">
                        {!! $row('Student mobile', (string) $enrollment->phone_number) !!}
                        {!! $row('Email', (string) $enrollment->email) !!}
                        {!! $row('Contact person', $contact['name']) !!}
                        {!! $row('Relationship', $contact['relationship']) !!}
                    </dl>
                </div>
            </section>

        </div>
    </div>

</div>

@endsection

@push('scripts')
    @include('admin.steps._scripts')
@endpush
