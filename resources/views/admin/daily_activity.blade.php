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
                        <h4 class="card-title mb-0 flex-grow-1">Daily Activity</h4>
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
                        </div>
                        <table style="margin-top: 15px" id="dailyActivityTable"
                            class="table nowrap dt-responsive align-middle table-hover table-bordered"
                            style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Action Date</th>
                                    <th>Total Upload</th>
                                    <th>Punching</th>
                                    <th>Verified</th>
                                    <th>Approved</th>
                                    <th>Financed</th>
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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Daily Activity Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Export the daily activity report for the selected date range?</p>
                <p><strong>From:</strong> <span id="modalFromDate"></span></p>
                <p><strong>To:</strong> <span id="modalToDate"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExport">Export</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/classic.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/monolith.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Flatpickr for date pickers
        flatpickr(".flatpickr", {
            dateFormat: "Y-m-d",
        });

        // Initialize DataTable
        var table = $('#dailyActivityTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('daily-activity.data') }}',
                type: 'POST',
                data: function(d) {
                    d.fromDate = $('#fromDate').val();
                    d.toDate = $('#toDate').val();
                    d._token = '{{ csrf_token() }}';
                },
            },
            columns: [{
                    data: 'ActionDate'
                },
                {
                    data: 'TotalUpload'
                },
                {
                    data: 'Punching'
                },
                {
                    data: 'Verified'
                },
                {
                    data: 'Approved'
                },
                {
                    data: 'Financed'
                },
            ],
            order: [
                [0, 'asc']
            ],
        });

        // Reload table when date range changes
        $('#fromDate, #toDate').on('change', function() {
            table.ajax.reload();
        });

        // Populate modal with selected dates when it opens
        $('#exportModal').on('show.bs.modal', function() {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            $('#modalFromDate').text(fromDate);
            $('#modalToDate').text(toDate);
        });

        // Handle export button click
        $('#confirmExport').on('click', function() {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            // Validate dates
            if (!fromDate || !toDate) {
                alert('Please select both From and To dates.');
                return;
            }

            // Create a form to submit the export request
            var form = $('<form>', {
                'method': 'POST',
                'action': '{{ route('daily-activity.export') }}',
                'target': '_blank'
            }).append(
                $('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }),
                $('<input>', {
                    'type': 'hidden',
                    'name': 'fromDate',
                    'value': fromDate
                }),
                $('<input>', {
                    'type': 'hidden',
                    'name': 'toDate',
                    'value': toDate
                })
            );

            form.appendTo('body').submit().remove();
            $('#exportModal').modal('hide');
        });
    });
</script>
@endpush
