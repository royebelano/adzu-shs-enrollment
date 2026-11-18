{{-- Horizontal bars. $rows = ['Label' => count, ...]. $total sets the 100% mark (defaults to the biggest). --}}
@php
    $max = max(1, (int) ($total ?? max(array_merge([0], array_values($rows)))));
    $tone = $tone ?? '';
@endphp

@if(count($rows) && array_sum($rows) > 0)
    <ul class="rp-bars">
        @foreach($rows as $label => $value)
            <li>
                <span class="rp-bar-label">{{ $label }}</span>
                <span class="rp-bar-track"><span class="rp-bar-fill {{ $tone }}" style="width: {{ min(100, round($value / $max * 100)) }}%"></span></span>
                <strong class="rp-bar-value">{{ $value }}</strong>
            </li>
        @endforeach
    </ul>
@else
    <p class="sv-empty">No data for this period.</p>
@endif
