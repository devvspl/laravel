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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Permissions List</h4>
                        <div class="flex-shrink-0">
                            <button type="button"
                                class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#permissionModal" id="addPermissionBtn">
                                <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add
                                New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="permissionsMasterTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>S No.</th>
                                    <th>Permission</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($permissions as $key => $permission)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->group_name }}</td>
                                        <td>
                                            @if ($permission->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success badge-border">Active</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger badge-border">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ date('d-m-Y', strtotime($permission->created_at)) }}</td>
                                        <td>{{ date('d-m-Y', strtotime($permission->updated_at)) }}</td>
                                        <td>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#permissionModal" id="addPermissionBtn" class="btn btn-primary btn-sm edit-permission" data-id="{{ $permission->id }}"><i class="ri-edit-2-fill"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm delete-permission" data-id="{{ $permission->id }}"><i class="ri-delete-bin-5-fill"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal.permission />
@endsection
@push('scripts')
<script src="{{ asset('custom/js/pages/permissions.js') }}"></script>
@endpush
