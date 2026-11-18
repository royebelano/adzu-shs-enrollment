@extends('layouts.admin')

@section('title', 'Edit Account')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

@php $isMe = $account->id === auth()->id(); @endphp

<div class="s1 sv">

    @include('admin.steps._topbar', [
        'title' => 'Manage Account',
        'links' => [['Back to accounts', route('admin.accounts.index')]],
    ])

    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>{{ $account->name }}</h1>
            <p>{{ $account->email }} · {{ $account->roleLabel() }}{{ $account->is_active === false ? ' · Disabled' : '' }}</p>
        </div>
    </section>

    <div class="sv-cols">

        <section class="sv-card sv-reveal" style="--i:2">
            <div class="sv-card-head"><h2>Details</h2></div>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.accounts.update', $account) }}" class="sv-form">
                    @csrf
                    @method('PUT')

                    <div class="sv-field">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" class="sv-input" value="{{ old('name', $account->name) }}" required maxlength="120">
                    </div>

                    <div class="sv-field">
                        <label for="email">Email (used to sign in)</label>
                        <input type="email" id="email" name="email" class="sv-input" value="{{ old('email', $account->email) }}" required maxlength="255">
                    </div>

                    <div class="sv-field">
                        <label for="role">Office</label>
                        <select id="role" name="role" class="sv-select" required>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" @selected(old('role', $account->roleKey()) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="sv-help">Changing the office changes what this person can open from their next page.</p>
                    </div>

                    <div class="sv-form-actions">
                        <button type="submit" class="sv-btn sv-btn-save">Save details</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="sv-card sv-reveal" style="--i:3">
            <div class="sv-card-head"><h2>Reset password</h2></div>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.accounts.password', $account) }}" class="sv-form" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="sv-field">
                        <label for="new_password">New password</label>
                        <input type="password" id="new_password" name="password" class="sv-input" minlength="8" required autocomplete="new-password">
                    </div>

                    <div class="sv-field">
                        <label for="new_password_confirmation">Confirm new password</label>
                        <input type="password" id="new_password_confirmation" name="password_confirmation" class="sv-input" minlength="8" required autocomplete="new-password">
                    </div>

                    <div class="sv-form-actions">
                        <button type="submit" class="sv-btn sv-btn-save">Change password</button>
                    </div>
                </form>
            </div>
        </section>

    </div>

</div>

@endsection
