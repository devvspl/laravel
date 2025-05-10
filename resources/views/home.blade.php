@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <x-theme.breadcrumb title="{{ ucwords(str_replace('-', ' ', Request::path())) }}" :breadcrumbs="[
                ['label' => 'Dashboards', 'url' => '#'],
                ['label' => ucwords(str_replace('-', ' ', Request::path()))],
            ]" />
            <div class="row">
            </div>
        </div>
    </div>
@endsection
@push('styles')
@endpush
@push('scripts')
@endpush