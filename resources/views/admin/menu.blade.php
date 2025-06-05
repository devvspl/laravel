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
                        <h4 class="card-title mb-0 flex-grow-1"><i class="ri-list-unordered"></i> Menu List</h4>
                        <div class="flex-shrink-0">
                            @can('Create Menu')
                                <button type="button"
                                    class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#menuModal" id="menuBtn">
                                    <i class="ri-add-circle-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Add
                                    New
                                </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="menuMasterTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>S No.</th>
                                    <th>Menu</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @can('Menu List')
                                    @foreach ($menus as $key => $menu)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $menu->title }}</td>
                                            <td>
                                                @can('Edit Menu')
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#menuModal"
                                                        id="addMenuBtn" class="btn btn-primary btn-sm edit-menu"
                                                        data-id="{{ $menu->id }}"><i class="ri-edit-2-fill"></i></button>
                                                @endcan
                                                @can('Delete Menu')
                                                    <button type="button" class="btn btn-danger btn-sm delete-menu"
                                                        data-id="{{ $menu->id }}"><i
                                                        class="ri-delete-bin-5-fill"></i></button>
                                                @endcan

                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <span class="text-danger">You do not have permission to view the menu
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
<x-modal.menu />
@endsection
@push('scripts')
<script src="{{ asset('custom/js/pages/menu.js') }}"></script>
@endpush
