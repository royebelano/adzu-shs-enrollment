@extends('layouts.admin')
@section('title', 'Mga Admin')

@section('content')
    <div class="page-head">
        <h1>Mga Admin</h1>
    </div>

    <p class="count">Lahat ng account dito ay may buong access sa data ng mga estudyante. Magdagdag lang ng mga taong pinagkakatiwalaan.</p>

    <div class="table-wrap" style="margin-bottom: 18px;">
        <table>
            <thead>
                <tr><th>Pangalan</th><th>Email</th><th>Nagawa</th><th>Palitan ang password</th><th></th></tr>
            </thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $u->name }} @if ($u->id === auth()->id()) <span class="badge optional">ikaw</span> @endif</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ optional($u->created_at)->format('M d, Y') }}</td>
                    <td>
                        <form class="actions" method="POST" action="{{ route('admin.users.password', $u) }}">
                            @csrf
                            @method('PATCH')
                            <input type="password" name="password" placeholder="Bagong password" autocomplete="new-password" style="width: 170px;" required>
                            <input type="password" name="password_confirmation" placeholder="Ulitin" autocomplete="new-password" style="width: 130px;" required>
                            <button class="btn" type="submit">Palitan</button>
                        </form>
                    </td>
                    <td>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                  onsubmit="return confirm('Buburahin ang admin account ni {{ e($u->name) }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Burahin</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <section class="card">
        <h2>Magdagdag ng admin</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="grid2">
                <div>
                    <label class="f" for="name">Pangalan</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="f" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="f" for="password">Password (hindi bababa sa 8 characters)</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" required>
                </div>
                <div>
                    <label class="f" for="password_confirmation">Ulitin ang password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                </div>
            </div>
            <p><button class="btn primary" type="submit">Idagdag</button></p>
        </form>
    </section>
@endsection
