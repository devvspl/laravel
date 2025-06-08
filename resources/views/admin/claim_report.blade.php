@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
        @section('titleMaybe', ucwords(str_replace('-', ' ', Request::path())))
        <x-theme.breadcrumb title="{{ ucwords(str_replace('-', ' ', Request::path())) }}" :breadcrumbs="[['label' => 'Reports', 'url' => '#'], ['label' => ucwords(str_replace('-', ' ', Request::path()))]]" />
        <div class="row">
            <div class="col-xl-12">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Claim Report</h4>
                        <div class="flex-shrink-0 ms-2">
                            <button class="btn btn-soft-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#exportModal">
                                Get Report<i class="mdi mdi-chevron-down align-middle ms-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body pb-3 pt-0">
                        <div
                            class="row bg-light-subtle border-top-dashed border border-start-0 border-end-0 border-bottom-dashed py-3 mb-3">
                            @can('Filter Function')
                                <div class="col-md-3">
                                    <label for="functionSelect" class="form-label mt-1">Function</label>
                                    <select class="form-select" id="functionSelect" multiple>
                                        @foreach ($functions as $function)
                                            <option value="{{ $function->id }}">{{ $function->function_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Vertical')
                                <div class="col-md-3">
                                    <label for="verticalSelect" class="form-label mt-1">Vertical</label>
                                    <select class="form-select" id="verticalSelect" multiple>
                                        @foreach ($verticals as $vertical)
                                            <option value="{{ $vertical->id }}">{{ $vertical->vertical_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Department')
                                <div class="col-md-3">
                                    <label for="departmentSelect" class="form-label mt-1">Department</label>
                                    <select class="form-select" id="departmentSelect" multiple>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                             @can('Filter Sub Department')
                                <div class="col-md-3">
                                    <label for="subDepartmentSelect" class="form-label mt-1">Sub Department</label>
                                    <select class="form-select" id="subDepartmentSelect" multiple>
                                       
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Users')
                                <div class="col-md-3">
                                    <label for="userSelect" class="form-label mt-1">Users</label>
                                    <select class="form-select" id="userSelect" multiple>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->EmpCode }}"
                                                data-status="{{ $employee->EmpStatus }}"
                                                class="{{ $employee->EmpStatus === 'D' ? 'deactivated' : '' }}">
                                                {{ $employee->EmpCode }} - {{ $employee->Fname }}
                                                {{ $employee->Sname ?? '' }} {{ $employee->Lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Month')
                                <div class="col-md-3">
                                    <label for="monthSelect" class="form-label mt-1">Month</label>
                                    <select class="form-select" id="monthSelect" multiple>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Claim Type')
                                <div class="col-md-3">
                                    <label for="claimTypeSelect" class="form-label mt-1">Claim Type</label>
                                    <select class="form-select" id="claimTypeSelect" multiple>
                                        @foreach ($claimTypes as $claimType)
                                            <option value="{{ $claimType->ClaimId }}">{{ $claimType->ClaimName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                            @can('Filter Claim Status')
                                <div class="col-md-3">
                                    <label for="claimStatusSelect" class="form-label mt-1">Claim Status</label>
                                    <select class="form-select" id="claimStatusSelect" multiple>
                                        <option value="1">Draft</option>
                                        <option value="2">Submitted</option>
                                        <option value="3">Filled</option>
                                        <option value="4">Approved</option>
                                        <option value="5">Financed</option>
                                        <option value="6">Payment</option>
                                    </select>
                                </div>
                            @endcan
                            <div class="col-md-3">
                                <label for="fromDate" class="form-label mt-1">From</label>
                                <input type="text" class="form-control flatpickr" id="fromDate"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="toDate" class="form-label mt-1">To</label>
                                <input type="text" class="form-control flatpickr" id="toDate"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="billDate"
                                        value="billDate" checked>
                                    <label class="form-check-label" for="billDate">Bill Date</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="uploadDate"
                                        value="upload Date">
                                    <label class="form-check-label" for="uploadDate">Upload Date</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="filledDate"
                                        value="filledDate">
                                    <label class="form-check-label" for="filledDate">Filled Date</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button"
                                    class="btn mt-3 btn-primary btn-label waves-effect waves-light rounded-pill w-100"
                                    id="searchButton">
                                    <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                        <span class="loader" style="display: none;"></span>
                                    </i>
                                    Search
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-center">
                                    <a href="#additionalFilters" class="text-primary" data-bs-toggle="collapse"
                                        aria-expanded="false" aria-controls="additionalFilters">
                                        <i class="ri-equalizer-line fs-20 me-2"></i>
                                        <span class="align-middle">More Filters</span>
                                    </a>
                                </div>
                                <div class="collapse mt-3" id="additionalFilters">
                                    <div class="row p-3">
                                        @can('Filter Policy')
                                            <div class="col-md-3">
                                                <label for="policySelect" class="form-label mt-1">Policy</label>
                                                <select class="form-select" id="policySelect" multiple>
                                                    @foreach ($eligibility_policy as $policy)
                                                        <option value="{{ $policy->PolicyId }}">{{ $policy->PolicyName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endcan
                                        @can('Filter Wheeler Type')
                                            <div class="col-md-3">
                                                <label for="wheelerTypeSelect" class="form-label mt-1">Wheeler Type</label>
                                                <select class="form-select" id="wheelerTypeSelect" multiple>
                                                    <option value="2">2 Wheeler</option>
                                                    <option value="4">4 Wheeler</option>
                                                </select>
                                            </div>
                                        @endcan
                                        @can('Filter Vehicle Type')
                                            <div class="col-md-3">
                                                <label for="vehicleTypeSelect" class="form-label mt-1">Vehicle Type</label>
                                                <select class="form-select" id="vehicleTypeSelect" multiple>
                                                    <option value="new">New</option>
                                                    <option value="old">Old</option>
                                                </select>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table style="margin-top: 15px" id="claimReportTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Sn</th>
                                    <th>Claim ID</th>
                                    <th>Claim Type</th>
                                    <th>Emp Name</th>
                                    <th>Emp Code</th>
                                    <th>Month</th>
                                    <th>Upload Date</th>
                                    <th>Bill Date</th>
                                    <th>Claimed Amt</th>
                                    <th>Claim Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal.claim_report />
@endsection
@push('styles')
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/classic.min.css" />
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/monolith.min.css" />
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/nano.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('custom/js/pages/claim_report.js') }}"></script>
@endpush
