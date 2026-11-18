{{-- Student header. $facts = ['Label' => 'value', ...] --}}
@php
    $heroName = trim(implode(' ', array_filter([
        $enrollment->first_name,
        $enrollment->middle_name,
        $enrollment->last_name,
        $enrollment->extension_name,
    ])));

    $heroInitials = strtoupper(
        mb_substr((string) $enrollment->first_name, 0, 1) .
        mb_substr((string) $enrollment->last_name, 0, 1)
    ) ?: '?';
@endphp

<section class="sv-hero sv-reveal" style="--i:1">
    <div class="sv-avatar" aria-hidden="true">{{ $heroInitials }}</div>

    <div>
        <h1 class="sv-hero-name">{{ $heroName ?: 'Unnamed student' }}</h1>

        <ul class="sv-hero-facts">
            @foreach($facts as $label => $value)
                <li>{{ $label }} <strong>{{ $value !== null && $value !== '' ? $value : '—' }}</strong></li>
            @endforeach
        </ul>
    </div>

    <div class="sv-hero-side">
        <span class="sv-status is-step">{{ $enrollment->stageLabel() }}</span>

        @if($enrollment->tracking_number)
            <div class="sv-ref">
                <div>
                    <div class="sv-ref-label">Reference ID</div>
                    <div class="sv-ref-code" id="svRefCode">{{ $enrollment->tracking_number }}</div>
                </div>
                <button type="button" class="sv-copy" id="svCopy">Copy</button>
            </div>
        @endif
    </div>
</section>
