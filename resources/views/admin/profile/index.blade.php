@extends('layouts.admin')

@section('title', 'My Account')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/steps/workflow.css') }}">
@endpush

@section('content')

<div class="s1 sv">

    @include('admin.steps._topbar', ['title' => 'My account', 'links' => []])

    <section class="s1-banner s1-reveal" style="--i:1">
        <div>
            <h1>{{ $account->name }}</h1>
            <p>{{ $account->roleLabel() }} · {{ $account->email }}</p>
        </div>
    </section>

    <div class="sv-cols">

        <section class="sv-card sv-reveal" style="--i:2">
            <div class="sv-card-head"><h2>My details</h2></div>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.profile.update') }}" class="sv-form">
                    @csrf
                    @method('PUT')

                    <div class="sv-field">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" class="sv-input" value="{{ old('name', $account->name) }}" required maxlength="120">
                    </div>

                    <div class="sv-field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="sv-input" value="{{ old('email', $account->email) }}" required maxlength="255">
                    </div>

                    <div class="sv-form-actions">
                        <button type="submit" class="sv-btn sv-btn-save">Save details</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="sv-card sv-reveal" style="--i:3">
            <div class="sv-card-head"><h2>Change password</h2></div>

            <div class="sv-card-body pad">
                <form method="POST" action="{{ route('admin.profile.password') }}" class="sv-form" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="sv-field">
                        <label for="current_password">Current password</label>
                        <input type="password" id="current_password" name="current_password" class="sv-input" required autocomplete="current-password">
                    </div>

                    <div class="sv-field">
                        <label for="password">New password</label>
                        <input type="password" id="password" name="password" class="sv-input" minlength="8" required autocomplete="new-password">
                    </div>

                    <div class="sv-field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="sv-input" minlength="8" required autocomplete="new-password">
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
