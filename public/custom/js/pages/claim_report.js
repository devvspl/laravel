$(document).ready(function () {
    $("#claimReportTable").DataTable({
        ordering: false,
        searching: true,
        paging: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
    });
    $('#functionSelect, #verticalSelect, #departmentSelect, #userSelect, #monthSelect, #claimTypeSelect, #claimStatusSelect').select2({
            width: '100%',
            placeholder: "Select options",
            allowClear: true
        });

        // Ensure DOM elements exist before initializing Flatpickr
        const fromDateElement = document.querySelector('#fromDate');
        const toDateElement = document.querySelector('#toDate');

        if (!fromDateElement || !toDateElement) {
            console.error('Flatpickr elements not found in the DOM');
            return;
        }

        // Initialize Flatpickr for date pickers without default date and with year selection
        const fromPicker = flatpickr(fromDateElement, {
            dateFormat: "Y-m-d",
            // No defaultDate, so the field starts empty
            disable: [], // Initially enabled, will be toggled
            enableYearSelection: true, // Enable year dropdown
            minDate: "2000-01-01", // Optional: Set a minimum year for the dropdown
            maxDate: "2030-12-31", // Optional: Set a maximum year for the dropdown
            onReady: function() {
                this.isDisabled = true; // Custom property to track disabled state
            }
        });

        const toPicker = flatpickr(toDateElement, {
            dateFormat: "Y-m-d",
            // No defaultDate, so the field starts empty
            disable: [],
            enableYearSelection: true, // Enable year dropdown
            minDate: "2000-01-01", // Optional: Set a minimum year for the dropdown
            maxDate: "2030-12-31", // Optional: Set a maximum year for the dropdown
            onReady: function() {
                this.isDisabled = true; // Custom property to track disabled state
            }
        });

        // Function to toggle Flatpickr enable/disable state
        function togglePicker(picker, enable) {
            if (enable) {
                picker.set('disable', []);
                picker.isDisabled = false;
                picker.element.removeAttribute('readonly');
            } else {
                picker.set('disable', [() => true]); // Disable all dates
                picker.isDisabled = true;
                picker.element.setAttribute('readonly', 'readonly');
            }
        }

        // Function to update date range based on selected months
        function updateDateRange() {
            const selectedMonths = $('#monthSelect').val();
            const year = 2025; // Adjust dynamically if needed

            if (selectedMonths && selectedMonths.length > 0 && !$('#customDate').is(':checked')) {
                // Sort selected months numerically
                const sortedMonths = selectedMonths.map(Number).sort((a, b) => a - b);
                const firstMonth = sortedMonths[0];
                const lastMonth = sortedMonths[sortedMonths.length - 1];

                // Set From date to the first day of the first selected month
                const from = new Date(year, firstMonth - 1, 1);
                fromPicker.setDate(from, true);

                // Set To date to the last day of the last selected month
                const to = new Date(year, lastMonth, 0);
                toPicker.setDate(to, true);

                // Disable date pickers when months are selected
                togglePicker(fromPicker, false);
                togglePicker(toPicker, false);
            } else {
                // Enable date pickers if no months are selected or custom date is chosen
                togglePicker(fromPicker, true);
                togglePicker(toPicker, true);
            }
        }

        // Update date range when month selection changes
        $('#monthSelect').on('change', updateDateRange);

        // Enable/disable date pickers based on Custom Date selection
        $('input[name="dateType"]').on('change', function() {
            if ($('#customDate').is(':checked')) {
                togglePicker(fromPicker, true);
                togglePicker(toPicker, true);
            } else {
                updateDateRange();
            }
        });

        // Initial update of date range
        updateDateRange();
});
