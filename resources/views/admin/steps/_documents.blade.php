{{-- Uploaded student documents, with preview, download and ZIP. --}}
@php
    $documents = $enrollment->documents ?? collect();
    $requirements = \App\Support\Requirements::ALL;

    $uploadedTypes = $documents->pluck('type')->unique()->all();

    $missing = collect($requirements)
        ->filter(fn ($r, $key) => ($r['required'] ?? false) && !in_array($key, $uploadedTypes, true))
        ->map(fn ($r) => $r['label'])
        ->values();
@endphp

<section class="sv-card sv-reveal" style="--i:5; margin-bottom:22px;">
    <div class="sv-card-head">
        <span class="sv-card-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8 12.5 21.3a6 6 0 0 1-8.5-8.5l9-9a4 4 0 0 1 5.7 5.7l-9 9a2 2 0 0 1-2.9-2.9L14 7.5"/></svg>
        </span>
        <h2>Uploaded documents</h2>
    </div>

    <div class="sv-card-body pad">
        <div class="sv-doc-tools">
            <a href="{{ route('admin.zip', $enrollment->id) }}" class="sv-btn sv-btn-zip">Download complete ZIP</a>
            <a href="{{ route('admin.pdf', $enrollment->id) }}" target="_blank" rel="noopener noreferrer"
               class="sv-btn sv-btn-view">View enrollment form</a>
        </div>

        @if($missing->isNotEmpty())
            <p class="sv-fill-note">
                <strong>Missing required files:</strong> {{ $missing->implode(', ') }}.
            </p>
        @endif

        @if($documents->isNotEmpty())
            <div class="sv-docs">
                @foreach($documents as $document)
                    @php
                        $filename = $document->original_name ?: 'Uploaded document';
                        $extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION) ?: 'FILE');
                        $lowerExtension = strtolower($extension);
                        $mime = strtolower($document->mime ?: '');

                        $isImage = str_starts_with($mime, 'image/')
                            || in_array($lowerExtension, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                        $isPdf = $mime === 'application/pdf' || $lowerExtension === 'pdf';

                        $previewLabel = $isImage ? 'View image' : ($isPdf ? 'View PDF' : 'View file');

                        $docLabel = $requirements[$document->type]['label']
                            ?? ucwords(str_replace('_', ' ', $document->type));

                        $viewUrl = route('admin.document', [
                            'enrollment' => $enrollment->id,
                            'type' => $document->type,
                            'doc' => $document->id,
                        ]);

                        $downloadUrl = route('admin.document', [
                            'enrollment' => $enrollment->id,
                            'type' => $document->type,
                            'doc' => $document->id,
                            'download' => 1,
                        ]);
                    @endphp

                    <div class="sv-file">
                        <div class="sv-file-info">
                            <div class="sv-badge {{ $isImage ? 'is-image' : ($isPdf ? '' : 'is-file') }}">{{ $extension }}</div>

                            <div>
                                <strong>{{ $docLabel }}</strong>
                                <p>{{ $filename }}</p>
                                <small>
                                    {{ $document->mime ?: 'Unknown file type' }}
                                    @if($document->size)
                                        , {{ number_format($document->size / 1024, 2) }} KB
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="sv-actions">
                            <a href="{{ $viewUrl }}" target="_blank" rel="noopener noreferrer"
                               class="sv-btn sv-btn-view {{ $isImage ? 'js-lightbox' : '' }}"
                               @if($isImage) data-title="{{ $filename }}" @endif>{{ $previewLabel }}</a>

                            <a href="{{ $downloadUrl }}" class="sv-btn sv-btn-download">Download {{ $extension }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="sv-empty">This student has not uploaded any documents yet.</p>
        @endif
    </div>
</section>

<div class="sv-lightbox" id="svLightbox" aria-hidden="true">
    <div class="sv-lightbox-box" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button" class="sv-lightbox-close" id="svLightboxClose" aria-label="Close preview">&times;</button>
        <img class="sv-lightbox-img" id="svLightboxImg" alt="">
    </div>
</div>

@push('scripts')
<script>
(function () {
    const box = document.getElementById('svLightbox');
    const img = document.getElementById('svLightboxImg');
    const closeBtn = document.getElementById('svLightboxClose');
    let lastTrigger = null;

    function closeBox() {
        box.classList.remove('is-open');
        box.setAttribute('aria-hidden', 'true');
        img.removeAttribute('src');
        if (lastTrigger) lastTrigger.focus();
    }

    function openBox(url, title, trigger) {
        lastTrigger = trigger;
        img.alt = title || 'Uploaded image';
        img.onerror = () => {
            closeBox();
            window.open(url, '_blank', 'noopener');
        };
        img.src = url;
        box.classList.add('is-open');
        box.setAttribute('aria-hidden', 'false');
        closeBtn.focus();
    }

    document.querySelectorAll('.js-lightbox').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) return;
            event.preventDefault();
            openBox(link.href, link.dataset.title, link);
        });
    });

    closeBtn.addEventListener('click', closeBox);
    box.addEventListener('click', (event) => { if (event.target === box) closeBox(); });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && box.classList.contains('is-open')) closeBox();
    });
})();
</script>
@endpush
