@extends('layouts.dashboard')
@section('title', 'User Management')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">API User Management</p>
                    <span class="fs-semibold text-muted">
                        <p>Access control and permission assignment</p>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-12 col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row ">
                                <div class="col-xl-12">
                                    <div class="card custom-card ">

                                        <div class="card-body">

                                            <form method="GET" class="mb-4 d-flex">
                                                <input type="text" name="search" value="{{ request('search') }}"
                                                    placeholder="Search..." class="form-control me-2">
                                                <input type="number" name="per_page" value="{{ request('per_page', 10) }}"
                                                    class="form-control me-2" placeholder="Per page">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </form>

                                            @if (session('success'))
                                                <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif

                                            <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Email</th>
                                                            <th>Name</th>
                                                            <th>Phone</th>
                                                            <th>Balance (₦)</th>
                                                            <th>Units</th>
                                                            <th>Active</th>
                                                            <th>Role</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($users as $user)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $user->email }}</td>
                                                                <td>
                                                                    {!! $user->name
                                                                        ? e(trim("{$user->name}"))
                                                                        : '<i class="bx bx-info-circle text-muted " title="Name missing"> Missing</i>' !!}
                                                                </td>

                                                                <td>
                                                                    {!! $user->phone_number
                                                                        ? e($user->phone_number)
                                                                        : '<i class="bx bx-info-circle text-muted" title="Phone no missing"> Missing</i>' !!}
                                                                </td>
                                                                <td>{{ number_format($user->naira_balance,2) }}</td>
                                                                <td> <span class="badge bg-primary-transparent" style="font-size:12px">BVN: {{ number_format($user->bvn_balance, 0) }} </span>
                                                                  <span class="badge bg-secondary-transparent" style="font-size:12px">NIN: {{number_format($user->nin_balance, 0)}} </span></td>
                                                                <td>{{ $user->is_active ? 'Yes' : 'No' }}</td>
                                                                <td><i class="bx bx-user text-muted" title="Role"> </i>
                                                                    {{ ucwords($user->usertype) }}</td>
                                                                <td>


                                                                    <form method="POST"
                                                                        action="{{ route('api.users.activate', $user->id) }}"
                                                                        class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit"
                                                                            class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                                                                            <i
                                                                                class="bx {{ $user->is_active ? 'bx-user-x' : 'bx-user-check' }}"></i>
                                                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7">No users found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                                <div class="d-flex justify-content-center mt-3">
                                                    {{ $users->links('pagination::bootstrap-4') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endsection
