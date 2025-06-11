$(document).ready(function () {
    $("#reportType").select2({ dropdownParent: $("#exportModal") });

    flatpickr("#fromDate", {
        dateFormat: "Y-m-d",
        disable: [],
        enableYearSelection: true,
        onReady: function () {
            this.isDisabled = false;
        },
    });

    flatpickr("#toDate", {
        dateFormat: "Y-m-d",
        disable: [],
        enableYearSelection: true,
        onReady: function () {
            this.isDisabled = false;
        },
    });

    const selectElements = [
        "#functionSelect",
        "#verticalSelect",
        "#departmentSelect",
        "#subDepartmentSelect",
        "#userSelect",
        "#monthSelect",
        "#claimTypeSelect",
        "#claimStatusSelect",
        "#policySelect",
        "#wheelerTypeSelect",
        "#vehicleTypeSelect",
    ];

    selectElements.forEach((selector) => {
        $(selector).select2({
            width: "100%",
            placeholder: "Select options",
            allowClear: true,
        });
    });

    $(document).on("change", "#functionSelect", function () {
        const selectedFunctions = $(this).val() || [];
        $.ajax({
            url: "verticals/by-function",
            type: "POST",
            data: JSON.stringify({ function_ids: selectedFunctions }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    let verticalOptions =
                        '<option value="">Select options</option>';
                    response.data.forEach((vertical) => {
                        verticalOptions += `<option value="${vertical.id}">${vertical.vertical_name}</option>`;
                    });
                    $("#verticalSelect").html(verticalOptions);
                    $("#verticalSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                } else {
                    console.error(
                        "Failed to fetch verticals:",
                        response.message
                    );
                    $("#verticalSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                }
            },
            error: function (xhr) {
                console.error("Error fetching verticals:", xhr.responseText);
                $("#verticalSelect")
                    .html('<option value="">Error loading verticals</option>')
                    .select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
            },
        });
    });

    $(document).on("change", "#verticalSelect", function () {
        const selectedVerticals = $(this).val() || [];
        $.ajax({
            url: "departments/by-vertical",
            type: "POST",
            data: JSON.stringify({ vertical_ids: selectedVerticals }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    let deptOptions =
                        '<option value="">Select options</option>';
                    response.data.forEach((dept) => {
                        deptOptions += `<option value="${dept.id}">${dept.department_name}</option>`;
                    });
                    $("#departmentSelect").html(deptOptions);
                    $("#departmentSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });

                    $("#subDepartmentSelect, #userSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                } else {
                    console.error(
                        "Failed to fetch departments:",
                        response.message
                    );
                    $("#departmentSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                }
            },
            error: function (xhr) {
                console.error("Error fetching departments:", xhr.responseText);
                $("#departmentSelect")
                    .html('<option value="">Error loading departments</option>')
                    .select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
            },
        });
    });

    $(document).on("change", "#departmentSelect", function () {
        const selectedDepartments = $(this).val() || [];
        $.ajax({
            url: "sub-departments/by-department",
            type: "POST",
            data: JSON.stringify({ department_ids: selectedDepartments }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    let subDeptOptions =
                        '<option value="">Select options</option>';
                    response.data.forEach((subDept) => {
                        subDeptOptions += `<option value="${subDept.id}">${subDept.sub_department_name}</option>`;
                    });
                    $("#subDepartmentSelect").html(subDeptOptions);
                    $("#subDepartmentSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                } else {
                    console.error(
                        "Failed to fetch sub-departments:",
                        response.message
                    );
                    $("#subDepartmentSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                }
            },
            error: function (xhr) {
                console.error(
                    "Error fetching sub-departments:",
                    xhr.responseText
                );
                $("#subDepartmentSelect")
                    .html(
                        '<option value="">Error loading sub-departments</option>'
                    )
                    .select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
            },
        });

        $.ajax({
            url: "employees/by-department",
            type: "POST",
            data: JSON.stringify({ department_ids: selectedDepartments }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    let userOptions =
                        '<option value="">Select options</option>';
                    response.data.forEach((employee) => {
                        const fullName = `${employee.Fname} ${
                            employee.Sname || ""
                        } ${employee.Lname}`.trim();
                        const optionText = `${employee.EmpCode} - ${fullName}`;
                        const statusClass =
                            employee.EmpStatus === "D" ? "deactivated" : "";
                        userOptions += `<option value="${employee.EmployeeID}" data-status="${employee.EmpStatus}" class="${statusClass}">${optionText}</option>`;
                    });
                    $("#userSelect").html(userOptions);
                    $("#userSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                } else {
                    console.error("Failed to fetch users:", response.message);
                    $("#userSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                }
            },
            error: function (xhr) {
                console.error("Error fetching users:", xhr.responseText);
                $("#userSelect")
                    .html('<option value="">Error loading users</option>')
                    .select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
            },
        });
    });

    $(document).on("change", "#subDepartmentSelect", function () {
        const selectedSubDepts = $(this).val() || [];
        $.ajax({
            url: "employees/by-department",
            type: "POST",
            data: JSON.stringify({ sub_department_ids: selectedSubDepts }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    let userOptions =
                        '<option value="">Select options</option>';
                    response.data.forEach((employee) => {
                        const fullName = `${employee.Fname} ${
                            employee.Sname || ""
                        } ${employee.Lname}`.trim();
                        const optionText = `${employee.EmpCode} - ${fullName}`;
                        const statusClass =
                            employee.EmpStatus === "D" ? "deactivated" : "";
                        userOptions += `<option value="${employee.EmployeeID}" data-status="${employee.EmpStatus}" class="${statusClass}">${optionText}</option>`;
                    });
                    $("#userSelect").html(userOptions);
                    $("#userSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                } else {
                    console.error("Failed to fetch users:", response.message);
                    $("#userSelect")
                        .html('<option value="">Select options</option>')
                        .select2({
                            placeholder: "Select options",
                            allowClear: true,
                            width: "100%",
                        });
                }
            },
            error: function (xhr) {
                console.error("Error fetching users:", xhr.responseText);
                $("#userSelect")
                    .html('<option value="">Error loading users</option>')
                    .select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
            },
        });
    });

    let table = null;
    if ($("#searchButton").length) {
        if ($.fn.DataTable.isDataTable("#claimReportTable")) {
            table.destroy();
            $("#claimReportTable").empty();
        }

        table = $("#claimReportTable").DataTable({
            ordering: false,
            searching: true,
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "filter-claims",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: function (d) {
                    d.function_ids = $("#functionSelect").val() || [];
                    d.vertical_ids = $("#verticalSelect").val() || [];
                    d.department_ids = $("#departmentSelect").val() || [];
                    d.sub_department_ids =
                        $("#subDepartmentSelect").val() || [];
                    d.user_ids = $("#userSelect").val() || [];
                    d.months = $("#monthSelect").val() || [];
                    d.claim_type_ids = $("#claimTypeSelect").val() || [];
                    d.claim_statuses = $("#claimStatusSelect").val() || [];
                    d.from_date = $("#fromDate").val();
                    d.to_date = $("#toDate").val();
                    d.date_type = $('input[name="dateType"]:checked').val();
                    d.policy_ids = $("#policySelect").val() || [];
                    d.wheeler_type = $("#wheelerTypeSelect").val() || [];
                    d.vehicle_types = $("#vehicleTypeSelect").val() || [];
                },
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex" },
                { data: "ExpId" },
                { data: "claim_type_name" },
                {
                    data: null,
                    name: "employee_display",
                    render: function (data, type, row) {
                        return row.employee_code + " - " + row.employee_name;
                    },
                },
                { data: "ClaimMonth" },
                { data: "CrDate" },
                { data: "BillDate" },
                { data: "FilledTAmt" },
                { data: "ClaimAtStep" },
                { data: "action", orderable: false, searchable: false },
            ],
        });
    } else {
        showAlert(
            "danger",
            "ri-error-warning-line",
            "Search button not found. DataTable not initialized"
        );
    }

    $("#searchButton").on("click", function () {
        if (table) {
            startSimpleLoader({ currentTarget: this });
            table.ajax.reload(function () {
                endSimpleLoader({ currentTarget: $("#searchButton")[0] });
            });
        } else {
            showAlert(
                "danger",
                "ri-error-warning-line",
                "DataTable not initialized"
            );
        }
    });

    $("#exportModal").on("show.bs.modal", function () {
        const modalFilters = $("#modalFilters");
    });

    $("#exportExcelBtn").on("click", function () {
        const button = this;
        const columns = $(".column-checkbox:checked")
            .map(function () {
                return this.value;
            })
            .get();

        if (columns.length === 0) {
            alert("Please select at least one column to export.");
            return;
        }

        const filters = {
            function_ids: $("#functionSelect").val() || [],
            vertical_ids: $("#verticalSelect").val() || [],
            department_ids: $("#departmentSelect").val() || [],
            sub_department_ids: $("#subDepartmentSelect").val() || [],
            user_ids: $("#userSelect").val() || [],
            months: $("#monthSelect").val() || [],
            claim_type_ids: $("#claimTypeSelect").val() || [],
            claim_statuses: $("#claimStatusSelect").val() || [],
            from_date: $("#fromDate").val(),
            to_date: $("#toDate").val(),
            date_type: $('input[name="dateType"]:checked').val() || "billDate",
            policy_ids: $("#policySelect").val() || [],
            wheeler_type: $("#wheelerTypeSelect").val() || [],
            vehicle_types: $("#vehicleTypeSelect").val() || [],
        };

        $.ajax({
            url: "/expense-claims/export",
            method: "POST",
            data: JSON.stringify({
                columns,
                reportType: $("#reportType").val(),
                protectSheets: $("#protectSheets").is(":checked"),
                ...filters,
            }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            xhrFields: { responseType: "blob" },
            beforeSend: function () {
                startLoader({ currentTarget: button });
            },
            success: function (data, status, xhr) {
                if (
                    xhr
                        .getResponseHeader("content-type")
                        .includes("application/json")
                ) {
                    data.text().then((text) => {
                        const response = JSON.parse(text);
                        alert(response.error || "Export failed.");
                    });
                    return;
                }
                const url = window.URL.createObjectURL(data);
                const a = $("<a>", {
                    href: url,
                    download: `expense_claims_${new Date()
                        .toISOString()
                        .replace(/[:.]/g, "")}.xlsx`,
                }).appendTo("body");
                a[0].click();
                a.remove();
                window.URL.revokeObjectURL(url);
                $("#exportModal").modal("hide");
            },
            error: function (xhr) {
                console.error("Export error:", xhr.responseText);
                alert(
                    "Failed to export data. Please try again or contact support."
                );
            },
            complete: function () {
                endLoader({ currentTarget: button });
            },
        });
    });

    $(document).on("click", "#viewClaimDetail", function () {
        var claimId = $(this).data("claim-id");
        $.ajax({
            url: "/get-claim-detail-view",
            method: "GET",
            data: { claim_id: claimId },
            success: function (response) {
                $("#claimDetailContent").html(response.html);
            },
            error: function () {
                $("#claimDetailContent").html(
                    '<div class="text-danger">Failed to load data.</div>'
                );
            },
        });
    });
});
