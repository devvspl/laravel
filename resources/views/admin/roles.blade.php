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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Roles List</h4>
                        <div class="flex-shrink-0">
                            @can('Create Role')
                                <button type="button"
                                    class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#roleModal" id="addRoleBtn">
                                    <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add
                                    New
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="rolesMasterTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>S No.</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @can('Role List')
                                    @foreach ($roles as $key => $role)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $role->name }}</td>  
                                            <td>
                                                @can('Edit Role')
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#roleModal"
                                                        id="addRoleBtn" class="btn btn-primary btn-sm edit-role"
                                                        data-id="{{ $role->id }}"><i class="ri-edit-2-fill"></i></button>
                                                @endcan
                                                @can('Delete Role')
                                                    <button type="button" class="btn btn-danger btn-sm delete-role"
                                                        data-id="{{ $role->id }}"><i
                                                            class="ri-delete-bin-5-fill"></i></button>
                                                @endcan

                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <span class="text-danger">You do not have permission to view the role
                                                list.</span>
                                        </td>
                                    </tr>
                                @endcan
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal.roles />
@endsection
@push('scripts')
<script src="{{ asset('custom/js/pages/roles.js') }}"></script>
@endpush
