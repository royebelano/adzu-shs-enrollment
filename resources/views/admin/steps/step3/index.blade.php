@extends('layouts.admin')

@section('title', 'Step 3 · Admission')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1">

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal" style="--i:0">
        <div>
            <h1>Step 3 · Admission</h1>
            <p>
                Enrollment data for students who passed the infirmary.
                Check their documents and requirements here.
            </p>
        </div>

        <div class="s1-total-group">
            <div class="s1-total"><strong>{{ $stats['total'] }}</strong><span>Total</span></div>
            <div class="s1-total"><strong>{{ $stats['strands']['ABM'] }}</strong><span>ABM</span></div>
            <div class="s1-total"><strong>{{ $stats['strands']['HUMSS'] }}</strong><span>HUMSS</span></div>
            <div class="s1-total"><strong>{{ $stats['strands']['STEM'] }}</strong><span>STEM</span></div>
            <div class="s1-total"><strong>{{ $stats['male'] }}</strong><span>Male</span></div>
            <div class="s1-total"><strong>{{ $stats['female'] }}</strong><span>Female</span></div>
        </div>
    </section>

    {{-- TOOLBAR --}}
    <section class="s1-toolbar s1-reveal" style="--i:1">

        <div class="s1-chips">
            <a href="{{ route('admin.step2.index') }}" class="s1-chip">Back to Step 2</a>
        </div>

        <div class="s1-tools">
            <form method="GET" action="{{ route('admin.step3.index') }}" class="s1-search" role="search" id="s1SearchForm">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>

                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Search name, ID number, LRN, or reference ID"
                       aria-label="Search students">
            </form>

            <button type="submit" form="s1SearchForm" class="s1-btn s1-btn-primary">Search</button>
        </div>
    </section>

    {{-- TABLE --}}
    <section class="s1-card s1-reveal" style="--i:2">

        <div class="s1-card-head">
            <div>
                <h2>Admission list</h2>
                <p>Ordered by student ID number.</p>
            </div>

            <span class="s1-count">{{ $enrollments->count() }} shown</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table is-wide">

                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">ID number</th>
                        <th rowspan="2">Last name</th>
                        <th rowspan="2">Given name</th>
                        <th rowspan="2">Middle name</th>
                        <th rowspan="2">Suffix</th>
                        <th rowspan="2">School</th>
                        <th colspan="3" class="s1-center">Strand</th>
                        <th colspan="2" class="s1-center">Documents</th>
                        <th colspan="2" class="s1-center">Recomm.</th>
                        <th rowspan="2">Birthdate</th>
                        <th colspan="2" class="s1-center">Gender</th>
                        <th rowspan="2">LRN</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th class="s1-center">A</th><th class="s1-center">H</th><th class="s1-center">S</th>
                        <th class="s1-center">F137</th><th class="s1-center">PSA</th>
                        <th class="s1-center">Mod</th><th class="s1-center">GC</th>
                        <th class="s1-center">M</th><th class="s1-center">F</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($enrollments as $enrollment)

                        @php
                            $code = $enrollment->strandCode();
                            $sex = $enrollment->sexLabel();
                            $school = $enrollment->last_school ?: data_get($enrollment->data, 'last_school');
                            $tick = fn ($on) => '<span class="s1-tick ' . ($on ? 'is-yes' : '') . '">' . ($on ? '✓' : '·') . '</span>';
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">
                            <td data-label="#" class="s1-muted">{{ $enrollments->firstItem() + $loop->index }}</td>
                            <td data-label="ID number"><span class="s1-mono s1-id">{{ $enrollment->student_id ?: '—' }}</span></td>
                            <td data-label="Last name"><strong>{{ $enrollment->last_name }}</strong></td>
                            <td data-label="Given name">{{ $enrollment->first_name }}</td>
                            <td data-label="Middle name" class="{{ $enrollment->middle_name ? '' : 's1-muted' }}">{{ $enrollment->middle_name ?: '—' }}</td>
                            <td data-label="Suffix" class="{{ $enrollment->extension_name ? '' : 's1-muted' }}">{{ $enrollment->extension_name ?: '—' }}</td>
                            <td data-label="School" class="s1-email {{ $school ? '' : 's1-muted' }}">{{ $school ?: '—' }}</td>

                            <td data-label="ABM" class="s1-center">{!! $tick($code === 'ABM') !!}</td>
                            <td data-label="HUMSS" class="s1-center">{!! $tick($code === 'HUMSS') !!}</td>
                            <td data-label="STEM" class="s1-center">{!! $tick($code === 'STEM') !!}</td>

                            <td data-label="F137" class="s1-center">{!! $tick($enrollment->adm_f137) !!}</td>
                            <td data-label="PSA" class="s1-center">{!! $tick($enrollment->adm_psa) !!}</td>
                            <td data-label="Recomm. Mod" class="s1-center">{!! $tick($enrollment->adm_rec_mod) !!}</td>
                            <td data-label="Recomm. GC" class="s1-center">{!! $tick($enrollment->adm_rec_gc) !!}</td>

                            <td data-label="Birthdate" class="s1-nowrap">{{ $enrollment->birthdate ? $enrollment->birthdate->format('Y-m-d') : '—' }}</td>

                            <td data-label="Male" class="s1-center">{!! $tick($sex === 'Male') !!}</td>
                            <td data-label="Female" class="s1-center">{!! $tick($sex === 'Female') !!}</td>

                            <td data-label="LRN" class="s1-nowrap {{ $enrollment->lrn ? '' : 's1-muted' }}">{{ $enrollment->lrn ?: '—' }}</td>

                            <td data-label="Action">
                                <a href="{{ route('admin.step3.view', $enrollment->id) }}" class="s1-open">
                                    Open
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="19" class="s1-empty">
                                <strong>No students in Admission yet</strong>
                                @if(request('q'))
                                    Try a different search.
                                @else
                                    Open a student in Step 2 and choose Proceed to Step 3.
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
