@extends('layouts.admin')

@section('title', 'Access Rights')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1 sv">

    @include('admin.steps._topbar', ['title' => 'Access Rights', 'links' => []])

    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>Access rights</h1>
            <p>
                Choose what each office can do in each part of the system.
                <strong>View only</strong> lets them open and read, but not change anything.
            </p>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.access.update') }}" id="accessForm">
        @csrf
        @method('PUT')

        <section class="s1-card s1-reveal" style="--i:2">
            <div class="s1-card-head">
                <div>
                    <h2>What each office can open</h2>
                    <p>The Administrator always has full access, and is the only one who can open Manage Account, Access Rights, and Settings.</p>
                </div>
            </div>

            <div class="s1-table-wrap">
                <table class="s1-table acc-matrix">
                    <thead>
                        <tr>
                            <th>Part of the system</th>
                            @foreach($offices as $key => $label)
                                <th>{{ $label }}<small class="acc-count">{{ $people->get($key, 0) }} account{{ $people->get($key, 0) === 1 ? '' : 's' }}</small></th>
                            @endforeach
                            <th>Administrator</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($modules as $module => $moduleLabel)
                            <tr>
                                <td data-label="Part"><strong>{{ $moduleLabel }}</strong></td>

                                @foreach($offices as $role => $roleLabel)
                                    @php $current = old("access.$role.$module", $matrix[$role][$module]); @endphp

                                    <td data-label="{{ $roleLabel }}">
                                        <select name="access[{{ $role }}][{{ $module }}]" class="sv-select acc-select is-{{ $current }}"
                                                aria-label="{{ $roleLabel }}: {{ $moduleLabel }}">
                                            @foreach($levels as $value => $text)
                                                <option value="{{ $value }}" @selected($current === $value)>{{ $text }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach

                                <td data-label="Administrator"><span class="s1-status is-approved">Full access</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cap-save-bar">
                <p class="sv-help">
                    Moving a student to the next step needs <strong>Can edit</strong> on the step they are leaving.
                    For example, the Infirmary needs Can edit on Step 2 to send a student to Admission.
                </p>
                <button type="submit" class="sv-btn sv-btn-save">Save access rights</button>
            </div>
        </section>
    </form>

    <form method="POST" action="{{ route('admin.access.reset') }}" class="cap-reset"
          onsubmit="return confirm(@js('Set every office back to the starting access? Your changes will be lost.'));">
        @csrf
        <button type="submit" class="sv-btn sv-btn-download">Reset to starting setup</button>
    </form>

</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.acc-select').forEach(select => {
    select.addEventListener('change', () => {
        select.classList.remove('is-none', 'is-view', 'is-edit');
        select.classList.add('is-' + select.value);
    });
});
</script>
@endpush
