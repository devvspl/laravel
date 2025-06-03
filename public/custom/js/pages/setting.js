$(document).ready(function () {
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    const activeTab = getQueryParam("tab") || "general";
    $(".nav-link").removeClass("active");
    $(".tab-pane").removeClass("show active");
    $(`#${activeTab}-tab`).addClass("active");
    $(`#${activeTab}`).addClass("show active");

    $(".nav-link").on("click", function (e) {
        e.preventDefault();
        const tabId = $(this).attr("href").split("#")[1];
        const newUrl = `${window.location.pathname}?tab=${tabId}`;
        window.history.pushState({ tab: tabId }, "", newUrl);
        $(".nav-link").removeClass("active");
        $(".tab-pane").removeClass("show active");
        $(this).addClass("active");
        $(`#${tabId}`).addClass("show active");
    });

    window.onpopstate = function (event) {
        const tabId = getQueryParam("tab") || "general";
        $(".nav-link").removeClass("active");
        $(".tab-pane").removeClass("show active");
        $(`#${tabId}-tab`).addClass("active");
        $(`#${tabId}`).addClass("show active");
    };

    $(document).on("click", "#database-tab", function (event) {
        fetchCompanies(event);
    });

    $(document).on("click", "[data-bs-target='#configModal']", function (event) {
        const companyId = $(this).data("company-id");
        const companyName = $(this).data("company-name");

        $("#hrims_company_id").val(companyId);
        $("#expense_company_id").val(companyId);
        $("#modalCompanyName").text(`- ${companyName}`);

        fetchCompanyConfig(companyId, event);

        $("#dbTabs .nav-link").removeClass("active");
        $("#dbTabContent .tab-pane").removeClass("show active");
        $("#hrims-tab").addClass("active");
        $("#hrims").addClass("show active");
    });

    $("#hrims .btn-primary").on("click", function (event) {
        const button = event.currentTarget;
        const formData = {
            company_id: $("#hrims_company_id").val(),
            db_name: $("#hrims input[name='db_name']").val(),
            db_connection: $("#hrims_db_connection").val(),
            db_host: $("#hrims_db_host").val(),
            db_port: $("#hrims_db_port").val(),
            db_database: $("#hrims_db_database").val(),
            db_username: $("#hrims_db_username").val(),
            db_password: $("#hrims_db_password").val(),
            is_active: $("#hrims #is_active").is(":checked") ? 1 : 0,
        };
        saveCompanyConfig(formData, button);
    });

    $("#expense .btn-primary").on("click", function (event) {
        const button = event.currentTarget;
        const formData = {
            company_id: $("#expense_company_id").val(),
            db_name: $("#expense input[name='db_name']").val(),
            db_connection: $("#expense_db_connection").val(),
            db_host: $("#expense_db_host").val(),
            db_port: $("#expense_db_port").val(),
            db_database: $("#expense_db_database").val(),
            db_username: $("#expense_db_username").val(),
            db_password: $("#expense_db_password").val(),
            is_active: $("#expense #is_active").is(":checked") ? 1 : 0,
        };
        saveCompanyConfig(formData, button);
    });

    function fetchCompanies(event) {
        const button = event.currentTarget;
        $.ajax({
            url: "company",
            method: "GET",
            dataType: "json",
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                    const tbody = $("#database table tbody");
                    tbody.empty();

                    response.data.forEach(function (company) {
                        const row = `
                        <tr>
                            <td>${company.id}</td>
                            <td>${company.company_name}</td>
                            <td>${company.company_code}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#configModal"
                                    data-company-id="${company.id}"
                                    data-company-name="${company.company_name}">
                                    <i class="ri-add-circle-line"></i> Config
                                </button>
                            </td>
                        </tr>
                    `;
                        tbody.append(row);
                    });
                } else {
                    console.error(
                        "Failed to fetch companies:",
                        response.message
                    );
                    alert("Failed to fetch companies: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert("An error occurred while fetching companies.");
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    }

    function fetchCompanyConfig(companyId, event) {
        const button = event.currentTarget;
        $.ajax({
            url: `get-company-config/${companyId}`,
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                    if (response.data.hrims) {
                        const hrims = response.data.hrims;
                        $("#hrims_db_connection").val(
                            hrims.db_connection || "mysql"
                        );
                        $("#hrims_db_host").val(hrims.db_host || "127.0.0.1");
                        $("#hrims_db_port").val(hrims.db_port || "3306");
                        $("#hrims_db_database").val(
                            hrims.db_database || "hrims"
                        );
                        $("#hrims_db_username").val(
                            hrims.db_username || "root"
                        );
                        $("#hrims_db_password").val(hrims.db_password || "");
                        $("#hrims #is_active").prop(
                            "checked",
                            hrims.status == 1
                        );
                        $("#hrims #is_active_label").text(
                            hrims.status == 1 ? "Active" : "Inactive"
                        );
                    }

                    if (response.data.expense) {
                        const expense = response.data.expense;
                        $("#expense_db_connection").val(
                            expense.db_connection || "mysql"
                        );
                        $("#expense_db_host").val(
                            expense.db_host || "127.0.0.1"
                        );
                        $("#expense_db_port").val(expense.db_port || "3306");
                        $("#expense_db_database").val(
                            expense.db_database || "expense"
                        );
                        $("#expense_db_username").val(
                            expense.db_username || "root"
                        );
                        $("#expense_db_password").val(
                            expense.db_password || ""
                        );
                        $("#expense #is_active").prop(
                            "checked",
                            expense.status == 1
                        );
                        $("#expense #is_active_label").text(
                            expense.status == 1 ? "Active" : "Inactive"
                        );
                    }
                } else {
                    console.error("Failed to fetch config:", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    }

    function saveCompanyConfig(formData, button) {
        $.ajax({
            url: "save-config",
            method: "POST",
            data: JSON.stringify(formData),
            contentType: "application/json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                     showAlert("success","ri-checkbox-circle-line",`${formData.db_name.toUpperCase()} configuration saved successfully!`);
                    $("#configModal").modal("hide");
                } else {
                    showAlert("danger","ri-error-warning-line",response.message || "An error occurred while saving.");
                }
            },
            error: function (xhr, status, error) {
               showAlert("danger","ri-error-warning-line",response.message || "An error occurred while saving the configuration.");
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    }
});
