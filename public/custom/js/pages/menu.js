$(document).ready(function () {
    $("#menuMasterTable").DataTable({
        ordering: false,
        searching: true,
        paging: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
    });
    $("#menuBtn").click(function () {
        $("#addMenuLabel").text("Add New Menu");
        selectMenuList("menuSelect", "menuModal");
        selectPermisstion("permissionSelect", "menuModal");
    });
    $("#saveMenuBtn").click(function (event) {
        event.preventDefault();
        const button = event.currentTarget;

        const formData = {
            menu_name: document.getElementById("title").value,
            parent_id: document.getElementById("menuSelect").value || null,
            icon: document.getElementById("icon").value || null,
            order: document.getElementById("order").value || 0,
            url: document.getElementById("url").value,
            permission:
                document.getElementById("permissionSelect").value || null,
            is_active: document.getElementById("is_active").checked ? 1 : 0,
            id: $(button).data("menu-id") || null,
        };

        const requestType = formData.id ? "PUT" : "POST";
        const url = formData.id ? `menu/${formData.id}` : "menu";

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
                        response.message || "Menu saved successfully!"
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
                let errorMsg = "Failed to save menu.";
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors).flat().join(" ");
                    } else if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
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
    $(document).on("click", ".edit-menu", function (event) {
        event.preventDefault();
        const menuId = $(this).data("id");
        const button = event.currentTarget;
        $.ajax({
            url: "menu/" + menuId + "/edit",
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
                    const menu = response.data;
                    $("#addMenuLabel").text("Edit Menu - " + menu.title);
                    $("#title").val(menu.title);
                    $("#icon").val(menu.icon || "");
                    $("#order").val(menu.order || 0);
                    $("#url").val(menu.url);
                    $("#is_active").prop("checked", menu.status == 1);
                    selectMenuList("menuSelect", "menuModal", menu.parent_id);
                    selectPermisstion("permissionSelect","menuModal",menu.permission_name);
                    $("#saveMenuBtn").data("menu-id", menu.id);
                    $("#menuModal").modal("show");
                } else {
                    showAlert(
                        "danger",
                        "ri-error-warning-line",
                        response.message || "Error fetching menu."
                    );
                }
            },
            error: function (xhr) {
                const errorMsg =
                    xhr.responseJSON?.message || "Failed to fetch menu.";
                showAlert("danger", "ri-error-warning-line", errorMsg);
            },
            complete: function () {
                endLoader({
                    currentTarget: button,
                });
            },
        });
    });
    $(".delete-menu").click(function (event) {
        event.preventDefault();
        const menuId = $(this).data("id");
        const confirmation = confirm(
            "Are you sure you want to delete this menu?"
        );
        if (confirmation) {
            $.ajax({
                url: "menu/" + menuId,
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
                            response.message || "Role deleted successfully!"
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
                    let errorMsg = "Failed to delete menu.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showAlert("danger", "ri-error-warning-line", errorMsg);
                },
            });
        }
    });
    function selectMenuList(feildId, modalFeildId, id = null) {
        $.ajax({
            url: "menu-list",
            type: "GET",
            dataType: "json",
            delay: 250,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                const placeholderOption = new Option(
                    "Select a menu",
                    "",
                    false,
                    false
                );
                const noParentOption = new Option(
                    "No Parent",
                    "0",
                    id === null || id === 0,
                    id === null || id === 0
                );
                const options = data.map(function (item) {
                    const isSelected = item.id === id;
                    return new Option(
                        item.title,
                        item.id,
                        isSelected,
                        isSelected
                    );
                });
                const selectElement = $(`#${feildId}`);
                selectElement
                    .empty()
                    .append(placeholderOption)
                    .append(noParentOption)
                    .append(options);
                if (id !== null) {
                    selectElement.val(id === 0 ? "0" : id);
                }
                selectElement.select2({
                    dropdownParent: $(`#${modalFeildId}`),
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching menu list:", error);
            },
        });
    }
    function selectPermisstion(feildId, modalFeildId, permissionName = null) {
        $.ajax({
            url: "permissions-list",
            type: "GET",
            dataType: "json",
            delay: 250,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success && response.data) {
     

                    const selectElement = $(`#${feildId}`);

                    const permissionData = Object.keys(response.data).map(
                        function (group) {
                            return {
                                text: group,
                                children: response.data[group].map(function (
                                    item
                                ) {
                                    return {
                                        id: item.name,
                                        text: item.name,
                                        group: group,
                                        name: item.name,
                                    };
                                }),
                            };
                        }
                    );

                    selectElement.empty().select2({
                        dropdownParent: $(`#${modalFeildId}`),
                        placeholder: "Select a permission",
                        allowClear: true,
                        data: permissionData,
                        templateResult: function (data, container) {
                            if (!data.id) {
                                return data.text;
                            }

                            return $(`<span>${data.name}</span>`);
                        },
                        templateSelection: function (data) {
                            if (!data.id) {
                                return data.text;
                            }

                            return `${data.group}: ${data.name}`;
                        },
                    });

                    if (permissionName !== null) {
                        const permissionExists = permissionData.some((group) =>
                            group.children.some(
                                (item) => item.id === permissionName
                            )
                        );
                        if (permissionExists) {
                            selectElement.val(permissionName).trigger("change");
                        } else {
                            selectElement.val(null).trigger("change");
                        }
                    } else {
                        selectElement.val(null).trigger("change");
                    }
                } else {
                    console.error("No permissions found in response.");
                    const selectElement = $(`#${feildId}`);
                    selectElement
                        .empty()
                        .append(
                            new Option(
                                "No permissions available",
                                "",
                                true,
                                true
                            )
                        );
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching permissions list:", error);
                const selectElement = $(`#${feildId}`);
                selectElement
                    .empty()
                    .append(
                        new Option("Error loading permissions", "", true, true)
                    );
            },
        });
    }
});
