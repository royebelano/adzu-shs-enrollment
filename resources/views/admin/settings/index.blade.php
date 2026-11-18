@extends('layouts.admin')

@section('title', 'Settings')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1 sv">

    @include('admin.steps._topbar', ['title' => 'Settings', 'links' => []])

    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>System settings</h1>
            <p>These details appear on the student enrollment page. You can also close online enrollment here.</p>
        </div>
    </section>

    <section class="sv-card sv-reveal" style="--i:2; max-width: 760px;">
        <div class="sv-card-head"><h2>School and enrollment</h2></div>

        <div class="sv-card-body pad">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="sv-form">
                @csrf
                @method('PUT')

                <div class="sv-field">
                    <label for="school_name">School name</label>
                    <input type="text" id="school_name" name="school_name" class="sv-input" maxlength="120"
                           value="{{ old('school_name', $schoolName) }}" required>
                </div>

                <div class="sv-field">
                    <label for="school_year">School year</label>
                    <input type="text" id="school_year" name="school_year" class="sv-input" maxlength="9"
                           placeholder="2026-2027" value="{{ old('school_year', $schoolYear) }}" required>
                    <p class="sv-help">Shown on the student page as “SY 2026-2027”.</p>
                </div>

                <label class="sv-check">
                    <input type="checkbox" name="enrollment_open" value="1" @checked(old('enrollment_open', $enrollmentOpen ? '1' : null))>
                    <span>
                        <div>
                            <strong>Online enrollment is open</strong>
                            <small>Turn this off to stop students from booking and submitting online. Staff can still add walk-ins.</small>
                        </div>
                    </span>
                </label>

                <div class="sv-field">
                    <label for="closed_message">Message when closed (optional)</label>
                    <input type="text" id="closed_message" name="closed_message" class="sv-input" maxlength="200"
                           value="{{ old('closed_message', $closedMessage) }}" placeholder="Online enrollment is closed for now. Please visit the Admissions Office.">
                </div>

                <div class="sv-form-actions">
                    <button type="submit" class="sv-btn sv-btn-save">Save settings</button>
                </div>
            </form>
        </div>
    </section>

</div>

@endsection
