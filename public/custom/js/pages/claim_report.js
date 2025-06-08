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

    $("#functionSelect, #verticalSelect, #departmentSelect, #subDepartmentSelect, #userSelect, #monthSelect, #claimTypeSelect, #claimStatusSelect, #policySelect, #wheelerTypeSelect, #vehicleTypeSelect"
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
            info: true,
            lengthChange: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
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
                beforeSend: function () {
                    startSimpleLoader({ currentTarget: buttonElement });
                },
                complete: function () {
                    endSimpleLoader({ currentTarget: buttonElement });
                },
                error: function (xhr) {
                    console.error("Error fetching claims:", xhr.responseText);
                    endSimpleLoader({ currentTarget: buttonElement });
                },
            },
            columns: [
                { data: "Sn", name: "Sn" },
                { data: "ExpId" },
                { data: "ClaimType" },
                { data: "EmpName" },
                { data: "EmpCode" },
                {
                    data: "ClaimMonth",
                    render: function (data) {
                        const monthNames = [
                            "January",
                            "February",
                            "March",
                            "April",
                            "May",
                            "June",
                            "July",
                            "August",
                            "September",
                            "October",
                            "November",
                            "December",
                        ];
                        return data
                            ? monthNames[parseInt(data) - 1] || data
                            : "-";
                    },
                },
                { data: "UploadDate" },
                { data: "BillDate" },
                { data: "ClaimedAmount" },
                {
                    data: "ClaimStatus",
                    render: function (data) {
                        let badgeClass = "badge bg-secondary";
                        switch (data) {
                            case "Saved":
                            case "Draft":
                                badgeClass = "badge bg-warning";
                                break;
                            case "Submitted":
                                badgeClass = "badge bg-info";
                                break;
                            case "Filled":
                            case "Paid":
                                badgeClass = "badge bg-success";
                                break;
                            case "Approved":
                                badgeClass = "badge bg-primary";
                                break;
                        }
                        return `<span class="${badgeClass}">${
                            data || "Unknown"
                        }</span>`;
                    },
                },
                {
                    data: null,
                    render: function () {
                        return '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#claimDetailModal" id="viewClaimDetail"><i class="ri-eye-fill"></i></button>';
                    },
                },
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
            functionSelect: $("#functionSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            verticalSelect: $("#verticalSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            departmentSelect: $("#departmentSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            userSelect: $("#userSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            monthSelect: $("#monthSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            claimTypeSelect: $("#claimTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            claimStatusSelect: $("#claimStatusSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            fromDate: $("#fromDate").val(),
            toDate: $("#toDate").val(),
            dateType: $('input[name="dateType"]:checked').val() || "billDate",
            policySelect: $("#policySelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            wheelerTypeSelect: $("#wheelerTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
            vehicleTypeSelect: $("#vehicleTypeSelect option:selected")
                .map(function () {
                    return this.value;
                })
                .get(),
        };

        // Add report type and protect sheets option to the payload
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
