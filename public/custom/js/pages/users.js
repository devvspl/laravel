$(document).ready(function () {
    $("#userModal").on("shown.bs.modal", function () {
        $("#role_id").select2({
            dropdownParent: $("#userModal"),
            width: "100%",
            placeholder: "Select a role",
        });
    });
    $("#addUserBtn").on("click", function () {
        selectRoleList("role_id");
        $("#addUserLabel").text("Add New User");
        $("#userName").val("");
        $("#email").val("").removeAttr("readonly");
        $("#password").val("").removeAttr("readonly");
    });
    $("#passwordAddon").on("click", function () {
        const passwordInput = $("#password");
        const icon = $(this).find("i");
        const type =
            passwordInput.attr("type") === "password" ? "text" : "password";
        passwordInput.attr("type", type);
        icon.toggleClass("ri-eye-fill ri-eye-off-fill");
    });
    $("#generatePassword").on("click", function () {
        const length = 12;
        const charset =
            "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        let password = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        $("#password").val(password);
    });
    $("#saveUserBtn").click(function (event) {
        event.preventDefault();
        const button = event.currentTarget;

        const formData = {
            name: $("#userName").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            role_id: $("#role_id").val(),
            is_active: $("#is_active").is(":checked") ? 1 : 0,
            id: $(button).data("user-id") || null,
        };

        const requestType = formData.id ? "PUT" : "POST";
        const url = formData.id ? `users/${formData.id}` : "users";

        $.ajax({
            url: url,
            type: requestType,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: formData,
            dataType: "json",
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                    showAlert(
                        "success",
                        "ri-checkbox-circle-line",
                        response.message || "User saved successfully!"
                    );
                    setTimeout(() => {
                        window.location.href = window.location.href;
                    }, 2000);
                } else {
                    showAlert(
                        "danger",
                        "ri-error-warning-line",
                        response.message || "An error occurred while saving."
                    );
                }
            },
            error: function (xhr, status, error) {
                let errorMsg = "Failed to save user.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showAlert("danger", "ri-error-warning-line", errorMsg);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    });
    $(".edit-user").click(function (event) {
        event.preventDefault();
        const userId = $(this).data("id");
        const button = event.currentTarget;

        $.ajax({
            url: "users/" + userId + "/edit",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const user = response.data;
                    $("#addUserLabel").text("Edit User - " + user.name);
                    $("#userName").val(user.name);
                    $("#email").val(user.email).attr("readonly", true);
                    $("#password").val("").attr("readonly", true);
                    $("#role_id").val(user.role_id);
                    $("#is_active").prop("checked", user.is_active);
                    $("#saveUserBtn").attr("data-user-id", user.id);
                    const selectedRoles = user.role_ids || [];
                    selectRoleList("role_id", selectedRoles);
                } else {
                    showAlert(
                        "danger",
                        "ri-error-warning-line",
                        response.message || "Error fetching user."
                    );
                }
            },
            error: function (xhr) {
                const errorMsg =
                    xhr.responseJSON?.message || "Failed to fetch user.";
                showAlert("danger", "ri-error-warning-line", errorMsg);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    });
    $(".delete-user").click(function (event) {
        event.preventDefault();
        const userId = $(this).data("id");
        const confirmation = confirm(
            "Are you sure you want to delete this user?"
        );
        if (confirmation) {
            $.ajax({
                url: "users/" + userId,
                type: "DELETE",
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        showAlert(
                            "success",
                            "ri-checkbox-circle-line",
                            response.message || "user deleted successfully!"
                        );
                        setTimeout(() => {
                            window.location.href = window.location.href;
                        }, 5000);
                    } else {
                        showAlert(
                            "danger",
                            "ri-error-warning-line",
                            response.message ||
                                "An error occurred while deleting."
                        );
                    }
                },
                error: function (xhr, status, error) {
                    let errorMsg = "Failed to delete user.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert("danger", "ri-error-warning-line", errorMsg);
                },
            });
        }
    });
    $(".permission-access-user").click(function () {
        const userId = $(this).data("id");
        const button = event.currentTarget;

        let userPermissions = [];
        $.ajax({
            url: `users/${userId}/permissions`,
            type: "GET",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                    const groupedPermissions = response.data;
                    const tableContainer = $("#permissions-table-container");
                    tableContainer.empty();

                    userPermissions = [];
                    Object.keys(groupedPermissions).forEach((group) => {
                        groupedPermissions[group].forEach((permission) => {
                            userPermissions.push(permission.id);
                        });
                    });

                    if (Object.keys(groupedPermissions).length === 0) {
                        tableContainer.append(
                            "<p>No permissions assigned.</p>"
                        );
                    } else {
                        Object.keys(groupedPermissions).forEach(
                            (group, index) => {
                                const permissions = groupedPermissions[group];
                                const accordionId = `current-accor-plus-${index}`;
                                let accordionHtml = `
                                <div class="accordion-item material-shadow">
                                    <h2 class="accordion-header" id="current-accordionwithplus-${index}">
                                        <button class="accordion-button ${
                                            index === 0 ? "" : "collapsed"
                                        }" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#${accordionId}" 
                                                aria-expanded="${
                                                    index === 0
                                                        ? "true"
                                                        : "false"
                                                }" 
                                                aria-controls="${accordionId}">
                                            ${group}
                                        </button>
                                    </h2>
                                    <div id="${accordionId}" 
                                         class="accordion-collapse collapse ${
                                             index === 0 ? "show" : ""
                                         }" 
                                         aria-labelledby="current-accordionwithplus-${index}" 
                                         data-bs-parent="#permissions-table-container">
                                        <div class="accordion-body">
                            `;

                                permissions.forEach((permission) => {
                                    accordionHtml += `
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>${permission.name}</span>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input permission-toggle" 
                                                   type="checkbox" 
                                                   role="switch" 
                                                   id="current-toggle-${
                                                       permission.id
                                                   }" 
                                                   data-user-id="${userId}" 
                                                   data-permission-id="${
                                                       permission.id
                                                   }" 
                                                   ${
                                                       userPermissions.includes(
                                                           permission.id
                                                       )
                                                           ? "checked"
                                                           : ""
                                                   }>
                                            <label class="form-check-label" for="current-toggle-${
                                                permission.id
                                            }"></label>
                                        </div>
                                    </div>
                                `;
                                });

                                accordionHtml += `
                                        </div>
                                    </div>
                                </div>
                            `;
                                tableContainer.append(accordionHtml);
                            }
                        );
                    }
                } else {
                    showAlert(
                        "danger",
                        "ri-error-warning-line",
                        response.message || "Error fetching permissions."
                    );
                }
            },
            error: function (xhr) {
                const errorMsg =
                    xhr.responseJSON?.message || "Failed to fetch permissions.";
                showAlert("danger", "ri-error-warning-line", errorMsg);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });

        $.ajax({
            url: "permissions-list",
            type: "GET",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                startLoader({
                    currentTarget: button,
                });
            },
            success: function (response) {
                if (response.success) {
                    const groupedPermissions = response.data;
                    const tableContainer = $(
                        "#all-permissions-table-container"
                    );
                    tableContainer.empty();

                    if (Object.keys(groupedPermissions).length === 0) {
                        tableContainer.append(
                            "<p>No permissions available.</p>"
                        );
                        return;
                    }

                    Object.keys(groupedPermissions).forEach((group, index) => {
                        const permissions = groupedPermissions[group];
                        const accordionId = `all-accor-plus-${index}`;
                        let accordionHtml = `
                <div class="accordion-item material-shadow">
                    <h2 class="accordion-header" id="all-accordionwithplus-${index}">
                        <button class="accordion-button ${
                            index === 0 ? "" : "collapsed"
                        }" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#${accordionId}" 
                                aria-expanded="${
                                    index === 0 ? "true" : "false"
                                }" 
                                aria-controls="${accordionId}">
                            ${group}
                        </button>
                    </h2>
                    <div id="${accordionId}" 
                         class="accordion-collapse collapse ${
                             index === 0 ? "show" : ""
                         }" 
                         aria-labelledby="all-accordionwithplus-${index}" 
                         data-bs-parent="#all-permissions-table-container">
                        <div class="accordion-body">
                            <div class="row">
            `;

                        permissions.forEach((permission) => {
                            const isAssigned = userPermissions.includes(
                                permission.id
                            );
                            accordionHtml += `
                    <div class="col-4 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>${permission.name}</span>
                            <div class="form-check form-switch" style="margin-top: 0px;">
                                <input class="form-check-input permission-toggle" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="all-toggle-${permission.id}" 
                                       data-user-id="${userId}" 
                                       data-permission-id="${permission.id}" 
                                       ${isAssigned ? "checked" : ""}>
                                <label class="form-check-label" for="all-toggle-${
                                    permission.id
                                }"></label>
                            </div>
                        </div>
                    </div>
                `;
                        });

                        accordionHtml += `
                            </div>
                        </div>
                    </div>
                </div>
            `;
                        tableContainer.append(accordionHtml);
                    });
                }
            },
            error: function (xhr) {
                console.error("Failed to fetch permissions:", xhr);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    });
    $(document).on("change", ".permission-toggle", function () {
        const userId = $(this).data("user-id");
        const permissionId = $(this).data("permission-id");
        const isChecked = $(this).is(":checked");
        const actionUrl = isChecked
            ? `users/${userId}/permissions/assign`
            : `users/${userId}/permissions/revoke`;

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: { permission_id: permissionId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    showAlert(
                        "success",
                        "ri-check-line",
                        response.message ||
                            (isChecked
                                ? "Permission assigned successfully."
                                : "Permission revoked successfully.")
                    );
                    $(
                        '.permission-access-user[data-id="' + userId + '"]'
                    ).trigger("click");
                } else {
                    showAlert(
                        "danger",
                        "ri-error-warning-line",
                        response.message ||
                            (isChecked
                                ? "Error assigning permission."
                                : "Error revoking permission.")
                    );

                    $(this).prop("checked", !isChecked);
                }
            }.bind(this),
            error: function (xhr) {
                const errorMsg =
                    xhr.responseJSON?.message ||
                    (isChecked
                        ? "Failed to assign permission."
                        : "Failed to revoke permission.");
                showAlert("danger", "ri-error-warning-line", errorMsg);

                $(this).prop("checked", !isChecked);
            }.bind(this),
        });
    });
    function selectRoleList(fieldId, ids = []) {
        $.ajax({
            url: "get-roles",
            type: "GET",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                const options = data.data.map(function (item) {
                    const isSelected = ids.includes(item.id);
                    return new Option(
                        item.name,
                        item.id,
                        isSelected,
                        isSelected
                    );
                });
                $(`#${fieldId}`).empty().append(options);
                if (ids.length > 0) {
                    $(`#${fieldId}`).val(ids);
                }
                $(`#${fieldId}`).trigger("change");
            },
            error: function (xhr, status, error) {
                console.error("Failed to fetch groups:", error);
            },
        });
    }
});
