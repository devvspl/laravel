@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
        @section('title', ucwords(str_replace('-', ' ', Request::path())))
        <x-theme.breadcrumb title="{{ ucwords(str_replace('-', ' ', Request::path())) }}" :breadcrumbs="[
            ['label' => 'Reports', 'url' => '#'],
            ['label' => ucwords(str_replace('-', ' ', Request::path()))],
        ]" />
        <div class="row">
            <div class="col-xl-12">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Claim Report</h4>
                        <div class="flex-shrink-0 ms-2">
                            <div class="dropdown card-header-dropdown">
                                <a class="btn btn-soft-primary btn-sm" role="button" href="#"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Get Report<i class="mdi mdi-chevron-down align-middle ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Download PDF</a>
                                    <a class="dropdown-item" href="#">Download Excel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-3 pt-0">
                        <div class="row bg-light-subtle border-top-dashed border border-start-0 border-end-0 border-bottom-dashed py-3 mb-3">
                            <div class="col-md-3">
                                <label for="functionSelect" class="form-label">Function</label>
                                <select class="form-select" id="functionSelect" multiple>
                                    <option value="finance">Finance</option>
                                    <option value="hr">HR</option>
                                    <option value="operations">Operations</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="verticalSelect" class="form-label">Vertical</label>
                                <select class="form-select" id="verticalSelect" multiple>
                                    <option value="sales">Sales</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="support">Support</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="departmentSelect" class="form-label">Department</label>
                                <select class="form-select" id="departmentSelect" multiple>
                                    <option value="accounts">Accounts</option>
                                    <option value="training">Training</option>
                                    <option value="logistics">Logistics</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="userSelect" class="form-label">Users</label>
                                <select class="form-select" id="userSelect" multiple>
                                    <option value="pradeep">Pradeep Subhashrao Patil</option>
                                    <option value="dharam">Dharam Pal</option>
                                    <option value="raj">Raj Ratan Singh</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monthSelect" class="form-label">Month</label>
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
                            <div class="col-md-3">
                                <label for="claimTypeSelect" class="form-label">Claim Type</label>
                                <select class="form-select" id="claimTypeSelect" multiple>
                                    <option value="meals">Meals</option>
                                    <option value="miscellaneous">Miscellaneous</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="claimStatusSelect" class="form-label">Claim Status</label>
                                <select class="form-select" id="claimStatusSelect" multiple>
                                    <option value="submitted">Submitted</option>
                                    <option value="filled">Filled</option>
                                    <option value="approved">Approved</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fromDate" class="form-label">From</label>
                                <input type="text" class="form-control flatpickr" id="fromDate" value="{{ date('Y-m-d') }}"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="toDate" class="form-label">To</label>
                                <input type="text" class="form-control flatpickr" id="toDate"
                                    value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="billDate"
                                        checked>
                                    <label class="form-check-label" for="billDate">Bill Date</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="uploadDate">
                                    <label class="form-check-label" for="uploadDate">Upload Date</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="filledDate">
                                    <label class="form-check-label" for="filledDate">Filled Date</label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="dateType" id="customDate">
                                    <label class="form-check-label" for="customDate">Custom Date</label>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                        <table style="margin-top: 15px" id="claimReportTable" style="width:100%"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered ">
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
                            <tbody>
                                <tr>
                                    <td>131</td>
                                    <td>39916</td>
                                    <td>Meals</td>
                                    <td>Pradeep Subhashrao Patil</td>
                                    <td>971</td>
                                    <td>May</td>
                                    <td>30-05-2025</td>
                                    <td>30-05-2025</td>
                                    <td>170</td>

                                    <td><span class="badge bg-success">Filled</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>132</td>
                                    <td>40430</td>
                                    <td>Miscellaneous</td>
                                    <td>Dharam Pal</td>
                                    <td>1074</td>
                                    <td>May</td>
                                    <td>30-05-2025</td>
                                    <td>30-05-2025</td>
                                    <td>45</td>

                                    <td><span class="badge bg-success">Filled</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>133</td>
                                    <td>40177</td>
                                    <td>Meals</td>
                                    <td>Raj Ratan Singh</td>
                                    <td>1474</td>
                                    <td>May</td>
                                    <td>30-05-2025</td>
                                    <td>30-05-2025</td>
                                    <td>357</td>

                                    <td><span class="badge bg-success">Filled</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>134</td>
                                    <td>40434</td>
                                    <td>Miscellaneous</td>
                                    <td>Dharam Pal</td>
                                    <td>1074</td>
                                    <td>May</td>
                                    <td>30-05-2025</td>
                                    <td>30-05-2025</td>
                                    <td>20</td>

                                    <td><span class="badge bg-success">Filled</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>135</td>
                                    <td>40438</td>
                                    <td>Miscellaneous</td>
                                    <td>Dharam Pal</td>
                                    <td>1074</td>
                                    <td>May</td>
                                    <td>30-05-2025</td>
                                    <td>30-05-2025</td>
                                    <td>10</td>

                                    <td><span class="badge bg-success">Filled</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/classic.min.css" /> 
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/monolith.min.css" /> 
<link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/nano.min.css" /> 
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="{{ asset('custom/js/pages/claim_report.js') }}"></script>
@endpush
