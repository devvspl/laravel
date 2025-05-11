@extends('layouts.app') @section('content')
<div class="page-content">
    <div class="container-fluid">
        @section('title', ucwords(str_replace('-', ' ', Request::path())))
        <x-theme.breadcrumb
            title="{{ ucwords(str_replace('-', ' ', Request::path())) }}"
            :breadcrumbs="[
            ['label' => 'Dashboards', 'url' => '#'],
            ['label' => ucwords(str_replace('-', ' ', Request::path()))],
        ]"
        />
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                    <i class="ri-wallet-3-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Total Expense</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="15000">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-warning rounded-circle fs-3">
                                    <i class="ri-time-line align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Draft</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="1300">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-secondary rounded-circle fs-3">
                                    <i class="ri-archive-line align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Deactivated</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="300">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-dark rounded-circle fs-3">
                                    <i class="ri-send-plane-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Submitted</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="8000">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-info rounded-circle fs-3">
                                    <i class="ri-file-list-3-line align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Filled</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="2200">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-success rounded-circle fs-3">
                                    <i class="ri-eye-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Verified</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="5000">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-success rounded-circle fs-3">
                                    <i class="ri-check-double-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Approved</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="4200">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded-circle fs-3">
                                    <i class="ri-hand-coin-fill align-middle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">Financed</p>
                                <h4 class="mb-0"><span class="counter-value" data-target="10000">0</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Expense Tracking Dashboard</h4>
                        <div>
                            <button type="button" class="btn btn-soft-secondary btn-sm material-shadow-none">
                                Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-header p-0 border-0 bg-light-subtle">
                        <div class="row g-0 text-center">
                            @php $months = [ 'jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov'
                            => 'November', 'dec' => 'December' ]; @endphp
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">
                                    <label class="form-label mb-1 text-muted">Month</label>
                                    <select class="form-select select2" name="month" id="select-month">
                                        <option value="">Select Month</option>
                                        @foreach ($months as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">
                                    <label class="form-label mb-1 text-muted">Claim Type</label>
                                    <select class="form-select select2" name="claim_type" id="select-claim-type">
                                        <option value="">Select Type</option>
                                        <option value="medical">Medical</option>
                                        <option value="travel">Travel</option>
                                        <option value="meal">Meal</option>
                                        <option value="misc">Miscellaneous</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0">
                                    <label class="form-label mb-1 text-muted">Department</label>
                                    <select class="form-select select2" name="department" id="select-department">
                                        <option value="">Select Department</option>
                                        <option value="hr">HR</option>
                                        <option value="finance">Finance</option>
                                        <option value="it">IT</option>
                                        <option value="sales">Sales</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">
                                    <label class="form-label mb-1 text-muted">Claim Status</label>
                                    <select class="form-select select2" name="claim_status" id="select-claim-status">
                                        <option value="">Select Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="processed">Processed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-2">
                        <div class="row">
                            <div class="col-xl-6 mb-3">
                                <h6 class="text-muted text-center">CY vs PY Expenses by Department</h6>
                                <canvas id="departmentExpensesChart" height="300"></canvas>
                            </div>
                            <div class="col-xl-6 mb-3">
                                <h6 class="text-muted text-center">CY Expenses by Category</h6>
                                <canvas id="categoryExpensesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @push('styles') @endpush @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    $(document).ready(function () {
        $(".select2").select2({ width: "100%" });

        const departmentCtx = document.getElementById("departmentExpensesChart").getContext("2d");
        new Chart(departmentCtx, {
            type: "bar",
            data: {
                labels: ["HR", "Finance", "IT", "Sales"],
                datasets: [
                    {
                        label: "CY",
                        data: [12000, 15000, 10000, 17000],
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                    },
                    {
                        label: "PY",
                        data: [10000, 13000, 9000, 14000],
                        backgroundColor: "rgba(255, 206, 86, 0.7)",
                    },
                ],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    legend: {
                        position: "top",
                    },
                },
            },
        });

        const categoryCtx = document.getElementById("categoryExpensesChart").getContext("2d");
        new Chart(categoryCtx, {
            type: "bar",
            data: {
                labels: ["Medical", "Travel", "Meal", "Misc"],
                datasets: [
                    {
                        label: "CY Expenses",
                        data: [3000, 5000, 2000, 1000],
                        backgroundColor: ["rgba(255, 99, 132, 0.7)", "rgba(54, 162, 235, 0.7)", "rgba(255, 206, 86, 0.7)", "rgba(75, 192, 192, 0.7)"],
                    },
                ],
            },
            options: {
                indexAxis: "y",
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    });
</script>
@endpush
