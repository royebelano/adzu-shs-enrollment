
@extends('layouts.admin')

@section('title', 'Step 2 · Infirmary')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $currentQuery = request('q');
@endphp

<div class="s1">

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal" style="--i:0">
        <div>
            <h1>Step 2 · Infirmary</h1>
            <p>
                Students sent from Step 1. Print their health examination record,
                then the school doctor completes the medical history, physical exam,
                and dental exam by hand.
            </p>
        </div>

        <div class="s1-total">
            <strong>{{ $enrollments->total() }}</strong>
            <span>
                {{ $currentQuery ? 'matching records' : 'students in Step 2' }}
            </span>
        </div>
    </section>


    {{-- TOOLBAR --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">

        <div class="s1-chips">
            <a href="{{ route('admin.index') }}" class="s1-chip">
                Back to Step 1
            </a>
        </div>

        <div class="s1-tools">

            <form
                method="GET"
                action="{{ route('admin.step2.index') }}"
                class="s1-search"
                role="search"
                id="s1SearchForm"
            >

                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    value="{{ $currentQuery }}"
                    placeholder="Search name, LRN, or reference ID"
                    aria-label="Search students"
                >

            </form>

            <button
                type="submit"
                form="s1SearchForm"
                class="s1-btn s1-btn-primary"
            >
                Search
            </button>

        </div>
    </section>


    {{-- TABLE --}}
    <section class="s1-card s1-reveal" style="--i:2">

        <div class="s1-card-head">
            <div>
                <h2>Infirmary queue</h2>
                <p>Newest arrivals first.</p>
            </div>

            <span class="s1-count">
                {{ $enrollments->count() }} shown
            </span>
        </div>


        <div class="s1-table-wrap">

            <table class="s1-table">

                <thead>
                    <tr>
                        <th>Reference ID</th>
                        <th>Student</th>
                        <th>Mobile</th>
                        <th>Grade</th>
                        <th>Sent to Step 2</th>
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

                            $grade = $enrollment->grade_level
                                ?: (data_get($studentData, 'grade_level') ?: '—');

                            $initials = strtoupper(
                                mb_substr((string) $enrollment->first_name, 0, 1) .
                                mb_substr((string) $enrollment->last_name, 0, 1)
                            ) ?: '?';

                            $otherNames = trim(implode(' ', array_filter([
                                $enrollment->middle_name,
                                $enrollment->extension_name,
                            ])));

                            $step2Time = $enrollment->step2_at
                                ? \Carbon\Carbon::parse($enrollment->step2_at)
                                    ->timezone('Asia/Manila')
                                : null;
                        @endphp


                        <tr
                            class="s1-row-in"
                            style="--i:{{ min($loop->index, 12) }}"
                        >

                            {{-- REFERENCE ID --}}
                            <td data-label="Reference ID">

                                @if($enrollment->tracking_number)
                                    <span class="s1-ref">
                                        {{ $enrollment->tracking_number }}
                                    </span>
                                @else
                                    <span class="s1-ref is-none">
                                        No ID
                                    </span>
                                @endif

                            </td>


                            {{-- STUDENT --}}
                            <td data-label="Student">

                                <div class="s1-student">

                                    <span class="s1-avatar" aria-hidden="true">
                                        {{ $initials }}
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $enrollment->last_name }},
                                            {{ $enrollment->first_name }}
                                        </strong>

                                        @if($otherNames)
                                            <small>{{ $otherNames }}</small>
                                        @endif
                                    </div>

                                </div>

                            </td>


                            {{-- MOBILE --}}
                            <td
                                data-label="Mobile"
                                class="s1-nowrap {{ $mobile === '—' ? 's1-muted' : '' }}"
                            >
                                {{ $mobile }}
                            </td>


                            {{-- GRADE --}}
                            <td
                                data-label="Grade"
                                class="{{ $grade === '—' ? 's1-muted' : '' }}"
                            >
                                {{ $grade }}
                            </td>


                            {{-- SENT TO STEP 2 --}}
                            <td data-label="Sent to Step 2">

                                @if($step2Time)

                                    <span class="s1-when">
                                        {{ $step2Time->format('M d, Y') }}
                                    </span>

                                    <small class="s1-stage">
                                        {{ $step2Time->format('h:i:s A') }}

                                    </small>

                                @else

                                    <span class="s1-when">—</span>

                                @endif

                                <small class="s1-stage">
                                    {{ $enrollment->stageLabel() }}
                                </small>

                            </td>


                            {{-- ACTION --}}
                            <td data-label="Action">

                                <a
                                    href="{{ route('admin.step2.view', $enrollment->id) }}"
                                    class="s1-open"
                                >
                                    Open

                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2.4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m9 18 6-6-6-6"/>
                                    </svg>

                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>
                            <td colspan="6" class="s1-empty">

                                <strong>No students in Step 2 yet</strong>

                                @if($currentQuery)
                                    Try a different search.
                                @else
                                    Open a student in Step 1 and choose Proceed to Step 2.
                                @endif

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        <div class="s1-card-foot">

            <span>
                Showing {{ $enrollments->firstItem() ?? 0 }}
                to {{ $enrollments->lastItem() ?? 0 }}
                of {{ $enrollments->total() }} records
            </span>

            @if($enrollments->hasPages())

                <div class="s1-pager">

                    <a
                        href="{{ $enrollments->previousPageUrl() ?: '#' }}"
                        class="s1-page-btn {{ $enrollments->onFirstPage() ? 'is-off' : '' }}"
                    >
                        Previous
                    </a>

                    <span>
                        Page {{ $enrollments->currentPage() }}
                        of {{ $enrollments->lastPage() }}
                    </span>

                    <a
                        href="{{ $enrollments->nextPageUrl() ?: '#' }}"
                        class="s1-page-btn {{ $enrollments->hasMorePages() ? '' : 'is-off' }}"
                    >
                        Next
                    </a>

                </div>

            @endif

        </div>

    </section>

</div>

@endsection
