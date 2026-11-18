{{-- Progress through the six steps. --}}
@php $current = $enrollment->stepNumber(); @endphp

<ol class="sv-steps sv-reveal" style="--i:2" aria-label="Enrollment progress">
    @foreach(\App\Models\Enrollment::STEPS as $n => $label)
        @php
            $done = $current === 6 || $n < $current;
            $state = $done ? 'is-done' : ($n === $current ? 'is-current' : '');
            $at = $enrollment->stepReachedAt($n);
        @endphp

        <li class="sv-step {{ $state }}">
            <span class="sv-step-dot">{{ $done ? '✓' : $n }}</span>
            <span class="sv-step-name">{{ $label }}</span>
            <span class="sv-step-date">{{ $at && ($done || $n === $current) ? $at->format('M d') : '' }}</span>
        </li>
    @endforeach
</ol>
