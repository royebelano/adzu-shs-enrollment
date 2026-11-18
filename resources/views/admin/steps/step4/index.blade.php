@extends('layouts.admin')

@section('title', 'Step 4 · ID Capturing')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1">

    <section class="s1-banner s1-reveal" style="--i:0">
        <div>
            <h1>Step 4 · ID Capturing</h1>
            <p>Set each student's ID capturing appointment. Contact details are carried over from the enrollment form.</p>
        </div>

        <div class="s1-total">
            <strong>{{ $enrollments->total() }}</strong>
            <span>{{ request('q') ? 'matching records' : 'students in Step 4' }}</span>
        </div>
    </section>


    {{-- TOOLBAR --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">

        <div class="s1-chips">
            <a href="{{ route('admin.step3.index') }}" class="s1-chip">Back to Step 3</a>
        </div>

        <div class="s1-tools">
            <form method="GET" action="{{ route('admin.step4.index') }}" class="s1-search" role="search" id="s1SearchForm">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>

                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Search name, student ID, LRN, or reference ID"
                       aria-label="Search students">
            </form>

            <button type="submit" form="s1SearchForm" class="s1-btn s1-btn-primary">Search</button>
        </div>
    </section>

    <section class="s1-card s1-reveal" style="--i:2">

        <div class="s1-card-head">
            <div>
                <h2>ID capturing list</h2>
                <p>Ordered by student ID.</p>
            </div>

            <span class="s1-count">{{ $enrollments->count() }} shown</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table is-wide">

                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Family name</th>
                        <th>First name</th>
                        <th>Contact person</th>
                        <th>Contact address</th>
                        <th>Relationship</th>
                        <th>Bar code</th>
                        <th>Strand</th>
                        <th>ID capturing</th>
                        <th>Captured</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($enrollments as $enrollment)

                        @php
                            $contact = $enrollment->contactPerson();
                            $address = $enrollment->currentAddress();
                            $code = $enrollment->strandCode();
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">
                            <td data-label="Student ID"><span class="s1-mono s1-id">{{ $enrollment->student_id ?: '—' }}</span></td>
                            <td data-label="Family name"><strong>{{ $enrollment->last_name }}</strong></td>
                            <td data-label="First name">{{ $enrollment->first_name }}</td>
                            <td data-label="Contact person" class="{{ $contact['name'] ? '' : 's1-muted' }}">{{ $contact['name'] ?: '—' }}</td>
                            <td data-label="Contact address" class="s1-email {{ $address ? '' : 's1-muted' }}">{{ $address ?: '—' }}</td>
                            <td data-label="Relationship" class="{{ $contact['relationship'] ? '' : 's1-muted' }}">{{ $contact['relationship'] ?: '—' }}</td>
                            <td data-label="Bar code"><span class="s1-mono">{{ $enrollment->barcode ?: '—' }}</span></td>
                            <td data-label="Strand">@if($code)<span class="s1-strand is-{{ $code }}">{{ $code }}</span>@else<span class="s1-muted">—</span>@endif</td>
                            <td data-label="ID capturing" class="s1-nowrap {{ $enrollment->idCaptureLabel() ? '' : 's1-muted' }}">{{ $enrollment->idCaptureLabel() ?: 'Not scheduled' }}</td>
                            <td data-label="Captured" class="s1-center"><span class="s1-tick {{ $enrollment->id_captured ? 'is-yes' : '' }}">{{ $enrollment->id_captured ? '✓' : '·' }}</span></td>
                            <td data-label="Action">
                                <a href="{{ route('admin.step4.view', $enrollment->id) }}" class="s1-open">
                                    Open
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="11" class="s1-empty">
                                <strong>No students here yet</strong>
                                @if(request('q'))
                                    Try a different search.
                                @else
                                    Open a student in Step 3 and choose Proceed to Step 4.
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
