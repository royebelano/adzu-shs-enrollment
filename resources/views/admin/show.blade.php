@extends('layouts.index')
@section('title', $enrollment->last_name . ', ' . $enrollment->first_name)

@section('content')
    @php
        $d = $enrollment->data ?? [];
        $dash = fn ($v) => ($v ?? '') !== '' ? $v : '—';
        $join = fn (array $parts) => collect($parts)->map(fn ($k) => $d[$k] ?? '')->filter(fn ($v) => $v !== '')->implode(' ');
        $address = collect(['cur_house', 'cur_street', 'cur_barangay', 'cur_city', 'cur_province'])
            ->map(fn ($k) => $d[$k] ?? '')->filter(fn ($v) => $v !== '')->implode(', ');
    @endphp

    <div class="page-head">
        <h1>{{ $enrollment->last_name }}, {{ $enrollment->first_name }} {{ $enrollment->middle_name }} {{ $enrollment->extension_name }}</h1>
        <div class="actions">
            <a class="btn" href="{{ route('admin.index') }}">&larr; Listahan</a>
            <a class="btn primary" href="{{ route('admin.pdf', $enrollment) }}" target="_blank" rel="noopener">Tingnan ang form (PDF)</a>
            <a class="btn" href="{{ route('admin.pdf', [$enrollment, 'download' => 1]) }}">I-download ang form</a>
            <a class="btn" href="{{ route('admin.zip', $enrollment) }}">I-download lahat (ZIP)</a>
        </div>
    </div>

    <section class="card">
        <h2>Impormasyon</h2>
        <dl>
            <div><dt>LRN</dt><dd>{{ $dash($enrollment->lrn) }}</dd></div>
            <div><dt>Sex</dt><dd>{{ $dash($enrollment->sex) }}</dd></div>
            <div><dt>Birthdate</dt><dd>{{ $enrollment->birthdate ? $enrollment->birthdate->format('m/d/Y') : '—' }}</dd></div>
            <div><dt>Grade</dt><dd>{{ $enrollment->grade_level ? ltrim($enrollment->grade_level, '0') : '—' }}</dd></div>
            <div><dt>School Year</dt><dd>{{ $dash($enrollment->school_year) }}</dd></div>
            <div><dt>Na-submit</dt><dd>{{ optional($enrollment->created_at)->format('M d, Y h:i A') }}</dd></div>
            <div><dt>Address</dt><dd>{{ $dash($address) }}</dd></div>
            <div><dt>Ama</dt><dd>{{ $dash($join(['father_first', 'father_middle', 'father_last'])) }} &middot; {{ $dash($d['father_contact'] ?? '') }}</dd></div>
            <div><dt>Ina</dt><dd>{{ $dash($join(['mother_first', 'mother_middle', 'mother_last'])) }} &middot; {{ $dash($d['mother_contact'] ?? '') }}</dd></div>
            <div><dt>Guardian</dt><dd>{{ $dash($join(['guardian_first', 'guardian_middle', 'guardian_last'])) }} &middot; {{ $dash($d['guardian_contact'] ?? '') }}</dd></div>
        </dl>
    </section>

    <section class="card">
        <h2>Status ng enrollment</h2>
        <p>
            Kasalukuyan: <span class="badge st-{{ $enrollment->status }}">{{ $enrollment->statusLabel() }}</span>
            @if ($enrollment->reviewed_at)
                <span class="meta">&middot; Huling na-review ni {{ $enrollment->reviewed_by }} noong {{ $enrollment->reviewed_at->format('M d, Y h:i A') }}</span>
            @endif
        </p>
        <form method="POST" action="{{ route('admin.status', $enrollment) }}">
            @csrf
            @method('PATCH')
            <div class="grid2">
                <div>
                    <label class="f" for="status">Palitan ang status</label>
                    <select id="status" name="status">
                        @foreach (\App\Models\Enrollment::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $enrollment->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="f" for="remarks">Tala (opsyonal, admin lang ang nakakakita)</label>
            <textarea id="remarks" name="remarks" maxlength="1000">{{ old('remarks', $enrollment->remarks) }}</textarea>
            <p><button class="btn primary" type="submit">I-save ang status</button></p>
        </form>
    </section>

    <section class="card">
        <h2>Mga Requirements ({{ $docs->count() }} / {{ count($requirements) }})</h2>
        <div class="docs">
            @foreach ($requirements as $key => $req)
                @php $doc = $docs->get($key); @endphp
                <div class="doc">
                    <div class="label">{{ $req['label'] }}</div>
                    <div>
                        @if ($doc)
                            <span class="badge ok">Na-upload</span>
                        @elseif ($req['required'])
                            <span class="badge missing">Kulang</span>
                        @else
                            <span class="badge optional">Hindi kailangan / wala</span>
                        @endif
                    </div>

                    @if ($doc)
                        <a href="{{ route('admin.document', [$enrollment, $key]) }}" target="_blank" rel="noopener">
                            @if ($doc->isImage())
                                <img class="thumb" src="{{ route('admin.document', [$enrollment, $key]) }}" alt="{{ $req['label'] }}" loading="lazy">
                            @else
                                <div class="pdfbox">PDF</div>
                            @endif
                        </a>
                        <div class="meta">{{ $doc->original_name }} &middot; {{ number_format($doc->size / 1024, 0) }} KB</div>
                        <div class="actions">
                            <a class="btn primary" href="{{ route('admin.document', [$enrollment, $key]) }}" target="_blank" rel="noopener">Buksan</a>
                            <a class="btn" href="{{ route('admin.document', [$enrollment, $key, 'download' => 1]) }}">I-download</a>
                        </div>
                    @else
                        <div class="none">Walang file</div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="card danger-zone">
        <h2>Burahin ang record</h2>
        <p class="meta">Mabubura ang form at lahat ng na-upload na file ng estudyanteng ito. Hindi na ito maibabalik.</p>
        <form method="POST" action="{{ route('admin.destroy', $enrollment) }}"
              onsubmit="return confirm('Buburahin ang record ni {{ e($enrollment->first_name) }} {{ e($enrollment->last_name) }} at lahat ng file niya? Hindi na ito maibabalik.');">
            @csrf
            @method('DELETE')
            <button class="btn danger" type="submit">Burahin ang record</button>
        </form>
    </section>
@endsection
