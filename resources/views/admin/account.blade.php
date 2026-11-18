@extends('layouts.admin')
@section('title', 'Password ko')

@section('content')
    <div class="page-head">
        <h1>Palitan ang password ko</h1>
    </div>

    <section class="card" style="max-width: 460px;">
        <form method="POST" action="{{ route('admin.account.update') }}">
            @csrf
            @method('PUT')

            <label class="f" for="current_password">Kasalukuyang password</label>
            <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>

            <label class="f" for="password">Bagong password (hindi bababa sa 8 characters)</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required>

            <label class="f" for="password_confirmation">Ulitin ang bagong password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>

            <p><button class="btn primary" type="submit">Palitan</button></p>
        </form>
    </section>
@endsection
