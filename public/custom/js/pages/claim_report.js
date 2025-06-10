$(document).ready(function () {
    $("#reportType").select2({ dropdownParent: $("#exportModal") });
    const fromPicker = flatpickr("#fromDate", {
        dateFormat: "Y-m-d",
        disable: [],
        enableYearSelection: true,
        onReady: function () {
            this.isDisabled = false;
        },
    });

    const toPicker = flatpickr("#toDate", {
        dateFormat: "Y-m-d",
        disable: [],
        enableYearSelection: true,
        onReady: function () {
            this.isDisabled = false;
        },
    });

    $(
        "#functionSelect, #verticalSelect, #departmentSelect, #subDepartmentSelect, #userSelect, #monthSelect, #claimTypeSelect, #claimStatusSelect, #policySelect, #wheelerTypeSelect, #vehicleTypeSelect"
    ).select2({
        width: "100%",
        placeholder: "Select options",
        allowClear: true,
    });

    $(document).on("change", "#departmentSelect", function () {
        let selectedDepartments = $(this).val() || [];
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
                    let userOptions = "";
                    response.data.forEach((employee) => {
                        let fullName = `${employee.Fname} ${
                            employee.Sname || ""
                        } ${employee.Lname}`.trim();
                        let optionText = `${employee.EmpCode} - ${fullName}`;
                        let statusClass =
                            employee.EmpStatus === "D" ? "deactivated" : "";
                        userOptions += `<option value="${employee.EmployeeID}" data-status="${employee.EmpStatus}" class="${statusClass}">${optionText}</option>`;
                    });
                    $("#userSelect").html(
                        userOptions ||
                            '<option value="">No users available</option>'
                    );

                    $("#userSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                } else {
                    console.error("Failed to fetch users:", response.message);
                    $("#userSelect").html(
                        '<option value="">Select options</option>'
                    );
                    $("#userSelect").select2({
                        placeholder: "Select options",
                        allowClear: true,
                        width: "100%",
                    });
                }
            },
            error: function (xhr) {
                console.error("Error fetching users:", xhr.responseText);
                $("#userSelect").html(
                    '<option value="">Error loading users</option>'
                );
                $("#userSelect").select2({
                    placeholder: "Select options",
                    allowClear: true,
                    width: "100%",
                });
            },
        });
    });

    let table = null;

    function initializeDataTable(buttonElement = null) {
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
                { data: "employee_name" },
                { data: "employee_code" },
                { data: "ClaimMonth" },
                { data: "CrDate" },
                { data: "BillDate" },
                { data: "FilledTAmt" },
                { data: "ClaimAtStep" },
                { data: "action", orderable: false, searchable: false },
            ],
        });
    }

    const searchButton = document.getElementById("searchButton");
    if (searchButton) {
        initializeDataTable(searchButton);
    } else {
        console.warn("Search button not found. DataTable not initialized.");
    }

    $("#searchButton").on("click", function () {
        if (table) {
            startSimpleLoader({
                currentTarget: this,
            });

            table.ajax.reload(function () {
                endSimpleLoader({
                    currentTarget: document.getElementById("searchButton"),
                });
            });
        } else {
             table.ajax.reload(function () {
                endSimpleLoader({
                    currentTarget: document.getElementById("searchButton"),
                });
            });
            console.warn("DataTable not initialized. Initializing now.");
            initializeDataTable(this);
        }
    });

    $("#exportModal").on("show.bs.modal", function () {
        var modalFilters = $("#modalFilters");
    });

    $("#exportExcelBtn").on("click", function (e) {
        const button = this;
        const columns = $(".column-checkbox:checked")
            .map(function () {
                return this.value;
            })
            .get();

        const filters = {
            function_ids: $("#functionSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            vertical_ids: $("#verticalSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            department_ids: $("#departmentSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            user_ids: $("#userSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            months: $("#monthSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            claim_type_ids: $("#claimTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            claim_statuses: $("#claimStatusSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            from_date: $("#fromDate").val(),
            to_date: $("#toDate").val(),
            date_type: $('input[name="dateType"]:checked').val() || "billDate",
            policy_ids: $("#policySelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            wheeler_type: $("#wheelerTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            vehicle_types: $("#vehicleTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
        };

        const reportType = $("#reportType").val();
        const protectSheets = $("#protectSheets").is(":checked");

        if (columns.length === 0) {
            alert("Please select at least one column to export.");
            return;
        }

        $.ajax({
            url: "/expense-claims/export",
            method: "POST",
            data: JSON.stringify({
                columns,
                reportType,
                protectSheets,
                ...filters,
            }),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            xhrFields: {
                responseType: "blob",
            },
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
            error: function (xhr, status, error) {
                console.error("Export error:", error);
                alert(
                    "Failed to export data. Please try again or contact support."
                );
            },
            complete: function () {
                endLoader({ currentTarget: button });
            },
        });
    });
});
