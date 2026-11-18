@extends('layouts.admin')

@section('title', 'Reports')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php
    $rangeUrl = fn ($days) => route('admin.reports.index', [
        'from' => today()->subDays($days - 1)->toDateString(),
        'to' => today()->toDateString(),
    ]);

    $maxDaily = max(1, collect($daily)->max('count'));
    $count = count($daily);
    $barW = 600 / max(1, $count);
@endphp

<div class="s1 sv">

    {{-- BANNER --}}
    <section class="s1-banner s1-reveal no-print-banner" style="--i:0">
        <div>
            <h1>Reports</h1>
            <p>
                Enrollment progress from {{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }},
                counted by the day each student submitted the form.
            </p>
        </div>

        <div class="s1-total-group">
            <div class="s1-total"><strong>{{ $kpis['total'] }}</strong><span>Applicants</span></div>
            <div class="s1-total"><strong>{{ $kpis['enrolled'] }}</strong><span>Enrolled</span></div>
            <div class="s1-total"><strong>{{ $kpis['conversion'] }}%</strong><span>Reached enrolled</span></div>
        </div>
    </section>

    {{-- DATE RANGE --}}
    <section class="s1-toolbar s1-reveal no-print" style="--i:1">
        <nav class="s1-chips" aria-label="Quick ranges">
            <a href="{{ $rangeUrl(7) }}" class="s1-chip">Last 7 days</a>
            <a href="{{ $rangeUrl(30) }}" class="s1-chip">Last 30 days</a>
            <a href="{{ $rangeUrl(90) }}" class="s1-chip">Last 90 days</a>
        </nav>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="s1-tools">
            <input type="date" name="from" class="sv-input rp-date" value="{{ $from->toDateString() }}" aria-label="From">
            <input type="date" name="to" class="sv-input rp-date" value="{{ $to->toDateString() }}" aria-label="To">
            <button type="submit" class="s1-btn s1-btn-primary">Apply</button>
            <button type="button" class="s1-btn s1-btn-ghost" onclick="window.print()">Print</button>
        </form>
    </section>

    {{-- NUMBERS --}}
    <div class="rp-kpis s1-reveal" style="--i:2">
        <div class="rp-kpi"><span>Applicants</span><strong>{{ $kpis['total'] }}</strong></div>
        <div class="rp-kpi is-good"><span>Enrolled</span><strong>{{ $kpis['enrolled'] }}</strong></div>
        <div class="rp-kpi"><span>Still in progress</span><strong>{{ $kpis['in_progress'] }}</strong></div>
        <div class="rp-kpi is-bad"><span>Rejected</span><strong>{{ $kpis['rejected'] }}</strong></div>
        <div class="rp-kpi"><span>Walk-ins</span><strong>{{ $kpis['walk_ins'] }}</strong></div>
        <div class="rp-kpi"><span>Average days to enroll</span><strong>{{ $kpis['avg_days'] ?? '—' }}</strong></div>
    </div>

    <div class="sv-cols">

        {{-- FUNNEL --}}
        <section class="sv-card s1-reveal" style="--i:3">
            <div class="sv-card-head"><h2>Where students are in the process</h2></div>

            <div class="sv-card-body pad">
                <ul class="rp-bars rp-funnel">
                    @foreach($funnel as $row)
                        <li>
                            <span class="rp-bar-label">{{ $row['label'] }}</span>
                            <span class="rp-bar-track"><span class="rp-bar-fill" style="width: {{ $row['pct'] }}%"></span></span>
                            <strong class="rp-bar-value">{{ $row['count'] }}</strong>
                            <small class="rp-bar-note">{{ $row['drop'] ? '−' . $row['drop'] . ' stayed behind' : '' }}</small>
                        </li>
                    @endforeach
                </ul>
                <p class="sv-help">Each bar counts students who reached that step or went past it.</p>
            </div>
        </section>

        {{-- DAILY --}}
        <section class="sv-card s1-reveal" style="--i:4">
            <div class="sv-card-head"><h2>Forms submitted per day</h2></div>

            <div class="sv-card-body pad">
                @if($kpis['total'] > 0)
                    <svg viewBox="0 0 600 180" class="rp-chart" role="img" aria-label="Forms submitted per day">
                        @foreach($daily as $i => $day)
                            @php
                                $h = $day['count'] / $maxDaily * 140;
                            @endphp
                            <rect x="{{ $i * $barW + 1 }}" y="{{ 150 - $h }}" width="{{ max(1, $barW - 2) }}" height="{{ max(0, $h) }}" rx="2" class="rp-chart-bar">
                                <title>{{ $day['date']->format('M d') }}: {{ $day['count'] }}</title>
                            </rect>
                        @endforeach
                        <line x1="0" y1="150" x2="600" y2="150" class="rp-chart-axis"/>
                        <text x="0" y="172" class="rp-chart-text">{{ $from->format('M d') }}</text>
                        <text x="600" y="172" text-anchor="end" class="rp-chart-text">{{ $to->format('M d') }}</text>
                        <text x="600" y="12" text-anchor="end" class="rp-chart-text">Busiest day: {{ $maxDaily }}</text>
                    </svg>
                @else
                    <p class="sv-empty">No forms were submitted in this period.</p>
                @endif
            </div>
        </section>

    </div>

    <div class="rp-grid">

        <section class="sv-card s1-reveal" style="--i:5">
            <div class="sv-card-head"><h2>By strand</h2></div>
            <div class="sv-card-body pad">@include('admin.reports._bars', ['rows' => $strands, 'total' => $kpis['total']])</div>
        </section>

        <section class="sv-card s1-reveal" style="--i:5">
            <div class="sv-card-head"><h2>By grade level</h2></div>
            <div class="sv-card-body pad">@include('admin.reports._bars', ['rows' => $grades, 'total' => $kpis['total']])</div>
        </section>

        <section class="sv-card s1-reveal" style="--i:5">
            <div class="sv-card-head"><h2>By sex</h2></div>
            <div class="sv-card-body pad">@include('admin.reports._bars', ['rows' => $genders, 'total' => $kpis['total']])</div>
        </section>

        <section class="sv-card s1-reveal" style="--i:6">
            <div class="sv-card-head"><h2>Online and walk-in</h2></div>
            <div class="sv-card-body pad">@include('admin.reports._bars', ['rows' => $sources, 'total' => $kpis['total']])</div>
        </section>

        <section class="sv-card s1-reveal" style="--i:6">
            <div class="sv-card-head"><h2>Admission checklist</h2></div>
            <div class="sv-card-body pad">
                @include('admin.reports._bars', ['rows' => $checklist, 'tone' => 'is-good'])
                <p class="sv-help">Checked, out of students who reached Admission.</p>
            </div>
        </section>

        <section class="sv-card s1-reveal" style="--i:6">
            <div class="sv-card-head"><h2>GCO assessment</h2></div>
            <div class="sv-card-body pad">
                @include('admin.reports._bars', ['rows' => $gco])
                <p class="sv-help">Students who reached the GCO step.</p>
            </div>
        </section>

    </div>

    <div class="sv-cols">

        {{-- TIME PER STEP --}}
        <section class="sv-card s1-reveal" style="--i:7">
            <div class="sv-card-head"><h2>Average time between steps</h2></div>

            <div class="sv-card-body">
                <dl class="sv-list">
                    @foreach($stepTimes as $row)
                        <div class="sv-row">
                            <dt>{{ $row['label'] }}</dt>
                            <dd class="{{ $row['days'] === null ? 'is-empty' : '' }}">
                                {{ $row['days'] === null ? 'No data yet' : $row['days'] . ' days' }}
                                @if($row['students'])
                                    <small class="rp-small">({{ $row['students'] }} students)</small>
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- MISSING DOCS --}}
        <section class="sv-card s1-reveal" style="--i:8">
            <div class="sv-card-head"><h2>Missing required files</h2></div>

            <div class="sv-card-body pad">
                @include('admin.reports._bars', ['rows' => $missingDocs, 'total' => max(1, $onlineCount), 'tone' => 'is-bad'])
                <p class="sv-help">Online applicants who have not uploaded the file. Walk-ins are not counted.</p>
            </div>
        </section>

    </div>

    <div class="sv-cols">

        {{-- NEXT 7 DAYS --}}
        <section class="sv-card s1-reveal" style="--i:9">
            <div class="sv-card-head"><h2>Admission appointments, next 7 days</h2></div>

            <div class="sv-card-body">
                <div class="cap-days-list">
                    @foreach($week as $day)
                        @php
                            $used = $day['online'] + $day['walk_ins'];
                            $pct = $day['capacity'] > 0 ? min(100, round($used / $day['capacity'] * 100)) : 0;
                        @endphp

                        <a href="{{ route('admin.capacity.index', ['from' => $day['date']->toDateString()]) }}" class="cap-day-row">
                            <span class="cap-day-name">
                                <strong>{{ $day['date']->format('D, M d') }}</strong>
                                <small>
                                    @if($day['closed'])
                                        Closed
                                    @else
                                        {{ $day['online'] }} online, {{ $day['walk_ins'] }} walk-in, of {{ $day['capacity'] }} seats
                                    @endif
                                </small>
                            </span>

                            <span class="cap-bar" aria-hidden="true">
                                <span style="width: {{ $pct }}%" class="{{ $pct >= 90 ? 'is-full' : '' }}"></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- BUSIEST HOURS --}}
        <section class="sv-card s1-reveal" style="--i:10">
            <div class="sv-card-head"><h2>Busiest hours</h2></div>

            <div class="sv-card-body pad">
                @include('admin.reports._bars', ['rows' => $busiest])
                <p class="sv-help">Most booked appointment hours in this period.</p>
            </div>
        </section>

    </div>

</div>

@endsection
