@extends('layouts.index')
@section('title', 'Mga Estudyante')

@section('content')
    <div class="page-head">
        <h1>Mga Estudyante</h1>
        <div class="actions">
            <a class="btn" href="{{ route('admin.export', request()->query()) }}">I-download (CSV / Excel)</a>
        </div>
    </div>

    <form class="card" method="GET" action="{{ route('admin.index') }}">
        <div class="filters">
            <div>
                <label class="f" for="q">Hanapin</label>
                <input id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Pangalan o LRN">
            </div>
            <div>
                <label class="f" for="grade">Grade</label>
                <select id="grade" name="grade">
                    <option value="">Lahat</option>
                    @foreach ($grades as $g)
                        <option value="{{ $g }}" {{ $filters['grade'] === (string) $g ? 'selected' : '' }}>Grade {{ ltrim($g, '0') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f" for="sy">School Year</label>
                <select id="sy" name="sy">
                    <option value="">Lahat</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}" {{ $filters['sy'] === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f" for="sex">Sex</label>
                <select id="sex" name="sex">
                    <option value="">Lahat</option>
                    <option value="Male" {{ $filters['sex'] === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $filters['sex'] === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div>
                <label class="f" for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Lahat</option>
                    @foreach (\App\Models\Enrollment::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ $filters['status'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="f" for="docs">Requirements</label>
                <select id="docs" name="docs">
                    <option value="">Lahat</option>
                    <option value="complete" {{ $filters['docs'] === 'complete' ? 'selected' : '' }}>Kumpleto</option>
                    <option value="incomplete" {{ $filters['docs'] === 'incomplete' ? 'selected' : '' }}>Kulang</option>
                </select>
            </div>
            <div>
                <label class="f" for="sort">Ayos</label>
                <select id="sort" name="sort">
                    <option value="name" {{ $filters['sort'] !== 'latest' ? 'selected' : '' }}>Ayon sa pangalan</option>
                    <option value="latest" {{ $filters['sort'] === 'latest' ? 'selected' : '' }}>Pinakabago muna</option>
                </select>
            </div>
            <div class="actions">
                <button class="btn primary" type="submit">Ilapat</button>
                <a class="btn" href="{{ route('admin.index') }}">I-clear</a>
            </div>
        </div>
    </form>

    <p class="count">{{ $enrollments->total() }} na estudyante</p>

    <div class="table-wrap">
        @if ($enrollments->count())
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>LRN</th>
                        <th>Pangalan</th>
                        <th>Sex</th>
                        <th>Grade</th>
                        <th>School Year</th>
                        <th>Requirements</th>
                        <th>Status</th>
                        <th>Na-submit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($enrollments as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->lrn ?? '—' }}</td>
                        <td>{{ $e->last_name }}, {{ $e->first_name }} {{ $e->middle_name }} {{ $e->extension_name }}</td>
                        <td>{{ $e->sex ?? '—' }}</td>
                        <td>{{ $e->grade_level ? ltrim($e->grade_level, '0') : '—' }}</td>
                        <td>{{ $e->school_year ?? '—' }}</td>
                        <td>{{ $e->documents_count }} / {{ count(\App\Support\Requirements::ALL) }}</td>
                        <td><span class="badge st-{{ $e->status }}">{{ $e->statusLabel() }}</span></td>
                        <td>{{ optional($e->created_at)->format('M d, Y h:i A') }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn primary" href="{{ route('admin.show', $e) }}">Buksan</a>
                                <a class="btn" href="{{ route('admin.pdf', $e) }}" target="_blank" rel="noopener">PDF ng form</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">Walang estudyanteng tugma.</div>
        @endif
    </div>

    @if ($enrollments->hasPages())
        <div class="pager">
            @if ($enrollments->previousPageUrl())
                <a class="btn" href="{{ $enrollments->previousPageUrl() }}">&larr; Nauna</a>
            @endif
            <span>Page {{ $enrollments->currentPage() }} of {{ $enrollments->lastPage() }}</span>
            @if ($enrollments->nextPageUrl())
                <a class="btn" href="{{ $enrollments->nextPageUrl() }}">Susunod &rarr;</a>
            @endif
        </div>
    @endif
@endsection
