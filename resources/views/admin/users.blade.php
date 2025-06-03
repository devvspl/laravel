@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
        @section('title', ucwords(str_replace('-', ' ', Request::path())))
        <x-theme.breadcrumb title="{{ ucwords(str_replace('-', ' ', Request::path())) }}" :breadcrumbs="[['label' => 'Master', 'url' => '#'], ['label' => ucwords(str_replace('-', ' ', Request::path()))]]" />
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> User List</h4>
                        <div class="flex-shrink-0">
                            @can('Create User')
                                <button type="button"
                                    class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#userModal" id="addUserBtn">
                                    <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add
                                    New
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="usersMasterTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>S No.</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Roles</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @can('List User')
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td><a class="text-underline"
                                                    href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                            <td>
                                                @forelse ($user->roles as $role)
                                                    <span
                                                        class="badge bg-primary-subtle text-primary">{{ $role->name }}</span>
                                                @empty
                                                    <span class="badge bg-secondary-subtle text-secondary">No
                                                        Roles</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @if ($user->status == 1)
                                                    <span
                                                        class="badge bg-success-subtle text-success badge-border">Active</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger badge-border">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('Edit User')
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#userModal"
                                                        id="adduserBtn" class="btn btn-primary btn-sm edit-user"
                                                        data-id="{{ $user->id }}"><i class="ri-edit-2-fill"></i></button>
                                                @endcan
                                                @can('Delete User')
                                                    <button type="button" class="btn btn-danger btn-sm delete-user"
                                                        data-id="{{ $user->id }}"><i
                                                            class="ri-delete-bin-5-fill"></i></button>
                                                @endcan
                                                @can('Permission Access')
                                                    <button type="button" class="btn btn-info btn-sm permission-access-user"
                                                        data-bs-toggle="modal" data-bs-target="#permissionModal"
                                                        data-id="{{ $user->id }}"><i class="ri-lock-line"></i></button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <span class="text-danger">You do not have permission to view the user
                                                list.</span>
                                        </td>
                                    </tr>
                                @endcan
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal.users />
@endsection
@push('scripts')
<script src="{{ asset('custom/js/pages/users.js') }}"></script>
@endpush
