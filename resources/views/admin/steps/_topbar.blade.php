{{--
  Top bar for the step pages.
  $title      text on the left
  $links      [[label, url], ...] back links
  $next       optional: ['step','label','route','open','confirm']
--}}
<div class="sv-bar sv-reveal" style="--i:0">
    <p class="sv-bar-title">{{ $title }}</p>

    <div class="sv-bar-actions">
        @isset($module)
            @unless(auth()->user()->canEdit($module))
                <span class="sv-viewonly">View only</span>
            @endunless
        @endisset

        @isset($next)
            @if($enrollment->reachedStep($next['step']))
                @if(auth()->user()->canView('step' . $next['step']))
                    <a href="{{ route($next['open'], $enrollment->id) }}" class="sv-step-note">
                        Open Step {{ $next['step'] }}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @endif
            @elseif(auth()->user()->canEdit($next['from'] ?? 'step' . ($next['step'] - 1)))
                <form method="POST"
                      action="{{ route($next['route'], $enrollment->id) }}"
                      onsubmit="return confirm(@js($next['confirm']));">
                    @csrf
                    <button type="submit" class="sv-btn sv-btn-next">
                        {{ $next['label'] }}
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </form>
            @endif
        @endisset

        @foreach($links as [$label, $url])
            <a href="{{ $url }}" class="sv-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

@if($errors->any())
    <div class="sv-alert sv-reveal" style="--i:0">{{ $errors->first() }}</div>
@endif
