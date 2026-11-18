@extends('layouts.admin')

@section('title', 'Manage Account')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1 sv">

    @include('admin.steps._topbar', ['title' => 'Manage Account', 'links' => []])

    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>Manage accounts</h1>
            <p>
                Create an account for each person in the Admission Office, Infirmary,
                ID Capturing Office, and GCO. What they can open is set in Access Rights.
            </p>
        </div>

        <div class="s1-total-group">
            <div class="s1-total"><strong>{{ $users->count() }}</strong><span>Shown</span></div>
            @foreach($roles as $key => $label)
                <div class="s1-total"><strong>{{ $counts->get($key, 0) }}</strong><span>{{ $label }}</span></div>
            @endforeach
        </div>
    </section>

    {{-- ADD --}}
    <section class="sv-card sv-reveal" style="--i:2; margin-bottom:22px;">
        <details class="cap-add" @if($errors->hasAny(['name', 'email', 'role', 'password']) && old('name') !== null) open @endif>
            <summary class="sv-card-head">
                <span class="sv-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                <h2>Add an account</h2>
                <span class="cap-add-hint">One account for each person</span>
            </summary>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.accounts.store') }}" class="sv-form" autocomplete="off">
                    @csrf

                    <div class="sv-fields">
                        <div class="sv-field">
                            <label for="name">Full name</label>
                            <input type="text" id="name" name="name" class="sv-input" value="{{ old('name') }}" required maxlength="120">
                        </div>

                        <div class="sv-field">
                            <label for="email">Email (used to sign in)</label>
                            <input type="email" id="email" name="email" class="sv-input" value="{{ old('email') }}" required maxlength="255">
                        </div>

                        <div class="sv-field is-wide">
                            <label for="role">Office</label>
                            <select id="role" name="role" class="sv-select" required>
                                <option value="">Choose an office</option>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" @selected(old('role') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sv-field">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="sv-input" minlength="8" required autocomplete="new-password">
                        </div>

                        <div class="sv-field">
                            <label for="password_confirmation">Confirm password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="sv-input" minlength="8" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="sv-form-actions">
                        <button type="button" class="sv-btn sv-btn-download" id="genPassword">Generate a password</button>
                        <button type="submit" class="sv-btn sv-btn-save">Create account</button>
                    </div>
                    <p class="sv-help" id="genNote" hidden></p>
                </form>
            </div>
        </details>
    </section>

    {{-- FILTER --}}
    <section class="s1-toolbar s1-reveal" style="--i:3">
        <nav class="s1-chips" aria-label="Filter by office">
            <a href="{{ route('admin.accounts.index', array_filter(['q' => request('q')])) }}" class="s1-chip {{ request('role') ? '' : 'is-active' }}">All</a>
            @foreach($roles as $key => $label)
                <a href="{{ route('admin.accounts.index', array_filter(['q' => request('q'), 'role' => $key])) }}"
                   class="s1-chip {{ request('role') === $key ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('admin.accounts.index') }}" class="s1-tools" role="search">
            @if(request('role'))<input type="hidden" name="role" value="{{ request('role') }}">@endif

            <div class="s1-search">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email" aria-label="Search accounts">
            </div>
            <button type="submit" class="s1-btn s1-btn-primary">Search</button>
        </form>
    </section>

    {{-- LIST --}}
    <section class="s1-card s1-reveal" style="--i:4">
        <div class="s1-card-head">
            <div>
                <h2>Accounts</h2>
                <p>Disabled accounts cannot sign in. Their records are kept.</p>
            </div>
            <span class="s1-count">{{ $users->count() }} shown</span>
        </div>

        <div class="s1-table-wrap">
            <table class="s1-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Office</th>
                        <th>Status</th>
                        <th>Last sign in</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $account)
                        @php
                            $isMe = $account->id === auth()->id();
                            $active = $account->is_active !== false;
                        @endphp

                        <tr class="s1-row-in" style="--i:{{ min($loop->index, 12) }}">
                            <td data-label="Name">
                                <div class="s1-student">
                                    <span class="s1-avatar" aria-hidden="true">{{ strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                                    <div>
                                        <strong>{{ $account->name }}</strong>
                                        @if($isMe)<small>This is you</small>@endif
                                    </div>
                                </div>
                            </td>

                            <td data-label="Email" class="s1-email">{{ $account->email }}</td>

                            <td data-label="Office"><span class="acct-role is-{{ $account->roleKey() }}">{{ $account->roleLabel() }}</span></td>

                            <td data-label="Status">
                                <span class="s1-status {{ $active ? 'is-approved' : 'is-rejected' }}">{{ $active ? 'Active' : 'Disabled' }}</span>
                            </td>

                            <td data-label="Last sign in" class="s1-nowrap {{ $account->last_login_at ? '' : 's1-muted' }}">
                                {{ $account->last_login_at ? $account->last_login_at->format('M d, Y h:i A') : 'Never' }}
                            </td>

                            <td data-label="Action">
                                <div class="acct-actions">
                                    <a href="{{ route('admin.accounts.edit', $account) }}" class="s1-open">Edit</a>

                                    @unless($isMe)
                                        <form method="POST" action="{{ route('admin.accounts.toggle', $account) }}"
                                              onsubmit="return confirm(@js(($active ? 'Disable ' : 'Enable ') . $account->name . '?'));">
                                            @csrf
                                            <button type="submit" class="acct-link">{{ $active ? 'Disable' : 'Enable' }}</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.accounts.destroy', $account) }}"
                                              onsubmit="return confirm(@js('Delete ' . $account->name . '? This cannot be undone.'));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="acct-link is-danger">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="s1-empty">
                                <strong>No accounts found</strong>
                                Try a different search or filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
(function () {
    const btn = document.getElementById('genPassword');
    const note = document.getElementById('genNote');
    const pw = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');

    if (!btn) return;

    btn.addEventListener('click', () => {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        const bytes = new Uint32Array(12);
        crypto.getRandomValues(bytes);

        const value = Array.from(bytes, n => chars[n % chars.length]).join('');

        pw.type = 'text';
        confirm.type = 'text';
        pw.value = value;
        confirm.value = value;

        note.hidden = false;
        note.textContent = 'Password: ' + value + '. Write it down and give it to the person now. It cannot be shown again after you save.';
    });
})();
</script>
@endpush
