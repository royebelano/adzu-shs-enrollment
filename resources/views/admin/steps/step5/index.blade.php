@extends('layouts.admin')

@section('title', 'Step 5 · GCO Assessment')

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
            <h1>Step 5 · GCO Assessment</h1>
            <p>Guidance and Counseling Office assessment schedule for students who finished ID capturing.</p>
        </div>

        <div class="s1-total">
            <strong>{{ $enrollments->total() }}</strong>
            <span>{{ request('q') ? 'matching records' : 'students in Step 5' }}</span>
        </div>
    </section>

    <div class="s1-note-bar s1-reveal" style="--i:1">
        <strong>For GCO personnel</strong>
        This list follows the ID Capturing step. Students are ordered by ID capturing date and time, then by family name.
        Students with a missing date are placed at the end. The order updates when a date is changed.
    </div>

    {{-- TOOLBAR --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">

        <div class="s1-chips">
            <a href="{{ route('admin.step4.index') }}" class="s1-chip">Back to Step 4</a>
        </div>

        <div class="s1-tools">
            <form method="GET" action="{{ route('admin.step5.index') }}" class="s1-search" role="search" id="s1SearchForm">
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
                <h2>GCO assessment list</h2>
                <p>Sorted by ID capturing date and time, then family name.</p>
            </div>

            <span class="s1-count">{{ $enrollments->count() }} shown</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table">

                <thead>
                    <tr>
                        <th>Family name</th>
                        <th>Given name</th>
                        <th>Suffix</th>
                        <th>Strand</th>
                        <th>ID capturing appointment</th>
                        <th>GCO assessment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($enrollments as $enrollment)

                        @php
                            $code = $enrollment->strandCode();
                            $gco = $enrollment->gco_status ?: 'pending';
                            $gcoClass = ['done' => 'approved', 'no_show' => 'rejected'][$gco] ?? 'pending';
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">
                            <td data-label="Family name"><strong>{{ $enrollment->last_name }}</strong></td>
                            <td data-label="Given name">{{ $enrollment->first_name }}</td>
                            <td data-label="Suffix" class="{{ $enrollment->extension_name ? '' : 's1-muted' }}">{{ $enrollment->extension_name ?: '—' }}</td>
                            <td data-label="Strand">@if($code)<span class="s1-strand is-{{ $code }}">{{ $code }}</span>@else<span class="s1-muted">—</span>@endif</td>
                            <td data-label="ID capturing appointment" class="s1-nowrap {{ $enrollment->idCaptureLabel() ? '' : 's1-muted' }}">{{ $enrollment->idCaptureLabel() ?: 'No date' }}</td>
                            <td data-label="GCO assessment" class="s1-nowrap {{ $enrollment->gcoLabel() ? '' : 's1-muted' }}">{{ $enrollment->gcoLabel() ?: 'Not scheduled' }}</td>
                            <td data-label="Status"><span class="s1-status is-{{ $gcoClass }}">{{ \App\Models\Enrollment::GCO_STATUSES[$gco] ?? ucfirst($gco) }}</span></td>
                            <td data-label="Action">
                                <a href="{{ route('admin.step5.view', $enrollment->id) }}" class="s1-open">
                                    Open
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="s1-empty">
                                <strong>No students here yet</strong>
                                @if(request('q'))
                                    Try a different search.
                                @else
                                    Open a student in Step 4 and choose Proceed to Step 5.
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
