@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Developer Panel</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex gap-2 mb-3">
            <form method="POST" action="{{ route('developer.flush') }}">
                @csrf
                <button class="btn btn-danger">Refresh Migrations</button>
            </form>

            <form method="POST" action="{{ route('developer.flush_and_seed') }}">
                @csrf
                <button class="btn btn-danger">Refresh Migrations & Seed</button>
            </form>

            <form method="POST" action="{{ route('developer.seed') }}">
                @csrf
                <button class="btn btn-primary">Run Seeders</button>
            </form>

            <form method="POST" action="{{ route('developer.session.flush') }}">
                @csrf
                <button class="btn btn-warning">Clear Sessions</button>
            </form>

            <form method="POST" action="{{ route('developer.clear_cache') }}">
                @csrf
                <button class="btn btn-secondary">Clear Cache</button>
            </form>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Password Hint</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>password</td>
                    <td>
                        <form method="POST" action="{{ route('developer.login_as', $user) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Login as this user</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
