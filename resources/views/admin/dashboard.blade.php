
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

@php
    $statusColors = [
        'pending' => '#b98535',
        'for_compliance' => '#7966a8',
        'approved' => '#27866c',
        'rejected' => '#b85b5b',
    ];

    $schoolYear = now()->month >= 6
        ? now()->year . '–' . (now()->year + 1)
        : (now()->year - 1) . '–' . now()->year;
@endphp

{{-- ADMISSIONS COMMAND HEADER --}}
<section class="dashboard-welcome">
    <div class="welcome-content">

        <div class="welcome-text">

            <span class="welcome-badge">
                <span class="live-dot"></span>
                ADMISSIONS CONTROL CENTER
            </span>

            <h2>
                Welcome back, {{ auth()->user()->name ?? 'Administrator' }}.
            </h2>

            <p>
                Your central workspace for overseeing student admissions,
                application reviews, and enrollment operations at
                AdZU Senior High School.
            </p>

            <div class="welcome-date">
                <span class="date-icon">◷</span>
                <span>{{ now()->format('l, F d, Y') }}</span>
                <span class="welcome-date-divider">/</span>
                <span>School Year {{ $schoolYear }}</span>
            </div>

        </div>

        <div class="welcome-summary">

            <div class="summary-item">
                <span class="summary-label">APPLICATIONS</span>
                <strong>{{ number_format($total ?? 0) }}</strong>
                <small>All submitted records</small>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-item">
                <span class="summary-label">FOR REVIEW</span>
                <strong>{{ number_format($pending ?? 0) }}</strong>
                <small>Awaiting review</small>
            </div>

        </div>

    </div>

    <div class="welcome-decoration decoration-one"></div>
    <div class="welcome-decoration decoration-two"></div>
    <div class="welcome-decoration decoration-three"></div>
</section>


{{-- ENROLLMENT STATISTICS --}}
<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-eyebrow">01 / PERFORMANCE OVERVIEW</span>
            <h3>Enrollment Statistics</h3>
            <p>A consolidated view of student application records.</p>
        </div>

        <a href="{{ route('admin.index') }}" class="text-link">
            View all applicants <span>→</span>
        </a>
    </div>

    <div class="dashboard-stats">

        <article class="stat-card stat-blue">
            <div class="stat-top">
                <span class="stat-label">Total Applicants</span>
                <span class="stat-icon">♙</span>
            </div>
            <h3>{{ number_format($total ?? 0) }}</h3>
            <span class="stat-description">All registered applications</span>
        </article>

        <article class="stat-card stat-cyan">
            <div class="stat-top">
                <span class="stat-label">Today's Applications</span>
                <span class="stat-icon">▦</span>
            </div>
            <h3>{{ number_format($today ?? 0) }}</h3>
            <span class="stat-description">Applications submitted today</span>
        </article>

        <article class="stat-card stat-amber">
            <div class="stat-top">
                <span class="stat-label">Pending Applications</span>
                <span class="stat-icon">◷</span>
            </div>
            <h3>{{ number_format($pending ?? 0) }}</h3>
            <span class="stat-description">Awaiting administrative review</span>
        </article>

        <article class="stat-card stat-green">
            <div class="stat-top">
                <span class="stat-label">Approved Applications</span>
                <span class="stat-icon">✓</span>
            </div>
            <h3>{{ number_format($approved ?? 0) }}</h3>
            <span class="stat-description">Successfully approved records</span>
        </article>

        <article class="stat-card stat-purple">
            <div class="stat-top">
                <span class="stat-label">Complete Requirements</span>
                <span class="stat-icon">▤</span>
            </div>
            <h3>{{ number_format($complete ?? 0) }}</h3>
            <span class="stat-description">All required documents submitted</span>
        </article>

        <article class="stat-card stat-red">
            <div class="stat-top">
                <span class="stat-label">Incomplete Requirements</span>
                <span class="stat-icon">!</span>
            </div>
            <h3>{{ number_format($incomplete ?? 0) }}</h3>
            <span class="stat-description">Records with missing documents</span>
        </article>

    </div>
</section>


{{-- OPERATIONAL MONITORING --}}
<div class="dashboard-grid">

    {{-- ENROLLMENT PIPELINE --}}
    <section class="dashboard-panel pipeline-panel">

        <div class="panel-heading">
            <div>
                <span class="section-eyebrow">02 / STUDENT WORKFLOW</span>
                <h3>Enrollment Process</h3>
                <p>Stages of the student enrollment operation.</p>
            </div>

            <span class="panel-icon">⌁</span>
        </div>

        <div class="pipeline-list">

            <div class="pipeline-item">
                <div class="pipeline-number">01</div>
                <div class="pipeline-info">
                    <strong>Enrollment</strong>
                    <span>Application form and requirements review</span>
                </div>
                <span class="pipeline-status">STEP 1</span>
            </div>

            <div class="pipeline-item">
                <div class="pipeline-number">02</div>
                <div class="pipeline-info">
                    <strong>Infirmary</strong>
                    <span>Medical and physical examination</span>
                </div>
                <span class="pipeline-status">STEP 2</span>
            </div>

            <div class="pipeline-item">
                <div class="pipeline-number">03</div>
                <div class="pipeline-info">
                    <strong>Guidance</strong>
                    <span>Student interview and assessment</span>
                </div>
                <span class="pipeline-status">STEP 3</span>
            </div>

            <div class="pipeline-item">
                <div class="pipeline-number">04</div>
                <div class="pipeline-info">
                    <strong>ID Capturing</strong>
                    <span>Student photo and identification details</span>
                </div>
                <span class="pipeline-status">STEP 4</span>
            </div>

            <div class="pipeline-item">
                <div class="pipeline-number">05</div>
                <div class="pipeline-info">
                    <strong>Registrar</strong>
                    <span>Final verification and enrollment recording</span>
                </div>
                <span class="pipeline-status">STEP 5</span>
            </div>

        </div>
    </section>


    {{-- APPLICATION STATUS --}}
    <section class="dashboard-panel status-panel">

        <div class="panel-heading">
            <div>
                <span class="section-eyebrow">03 / RECORD DISTRIBUTION</span>
                <h3>Application Status</h3>
                <p>Current distribution of application records.</p>
            </div>

            <span class="panel-icon">◉</span>
        </div>

        <div class="status-total">
            <strong>{{ number_format($total ?? 0) }}</strong>
            <span>Total applications</span>
        </div>

        <div class="status-list">

            @forelse($byStatus as $status => $count)

                @php
                    $percentage = ($total ?? 0) > 0
                        ? round(($count / $total) * 100)
                        : 0;

                    $color = $statusColors[$status] ?? '#64748b';

                    $statusLabel = match ($status) {
                        'for_compliance' => 'For Compliance',
                        default => ucwords(str_replace('_', ' ', $status)),
                    };
                @endphp

                <div class="status-row">

                    <div class="status-row-heading">
                        <span class="status-name">
                            <i style="background: {{ $color }}"></i>
                            {{ $statusLabel }}
                        </span>

                        <strong>{{ number_format($count) }}</strong>
                    </div>

                    <div class="status-track">
                        <div
                            class="status-fill"
                            style="width: {{ $percentage }}%; background: {{ $color }};"
                        ></div>
                    </div>

                    <span class="status-percentage">
                        {{ $percentage }}%
                    </span>

                </div>

            @empty

                <div class="empty-state">
                    No application status records available.
                </div>

            @endforelse

        </div>
    </section>

</div>


{{-- RECENT APPLICATIONS --}}
<section class="dashboard-panel recent-panel">

    <div class="panel-heading">
        <div>
            <span class="section-eyebrow">04 / APPLICATION REGISTER</span>
            <h3>Recent Applications</h3>
            <p>Latest student enrollment submissions.</p>
        </div>

        <a href="{{ route('admin.index') }}" class="panel-button">
            View All Applications <span>→</span>
        </a>
    </div>

    <div class="table-responsive">

        <table class="recent-table">

            <thead>
                <tr>
                    <th>STUDENT NAME</th>
                    <th>LRN</th>
                    <th>GRADE LEVEL</th>
                    <th>REQUIREMENTS</th>
                    <th>STATUS</th>
                    <th>DATE SUBMITTED</th>
                    <th>ACTION</th>
                </tr>
            </thead>

            <tbody>

                @forelse($recent as $enrollment)

                    @php
                        $fullName = trim(
                            ($enrollment->last_name ?? '') . ', ' .
                            ($enrollment->first_name ?? '') . ' ' .
                            ($enrollment->middle_name ?? '') . ' ' .
                            ($enrollment->extension_name ?? '')
                        );

                        $status = $enrollment->status ?? 'pending';

                        $statusLabel = match ($status) {
                            'for_compliance' => 'For Compliance',
                            default => ucwords(str_replace('_', ' ', $status)),
                        };
                    @endphp

                    <tr>

                        <td>
                            <div class="student-cell">

                                <div class="student-avatar">
                                    {{ strtoupper(substr($enrollment->first_name ?? 'S', 0, 1)) }}
                                </div>

                                <strong>
                                    {{ $fullName ?: 'Unnamed Student' }}
                                </strong>

                            </div>
                        </td>

                        <td>{{ $enrollment->lrn ?: '—' }}</td>

                        <td>{{ $enrollment->grade_level ?: '—' }}</td>

                        <td>
                            <span class="document-count">
                                {{ $enrollment->documents_count ?? 0 }} files
                            </span>
                        </td>

                        <td>
                            <span class="status-badge status-{{ $status }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td>
                            {{ $enrollment->created_at?->format('M d, Y') ?? '—' }}
                        </td>

                        <td>
                            <a
                                href="{{ route('admin.step1.view', $enrollment->id) }}"
                                class="view-button"
                            >
                                View
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                No enrollment applications have been submitted yet.
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>

@endsection
