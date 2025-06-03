<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="progress-container" style="display: none;">
                <div class="progres" style="height: 5px;">
                    <div class="indeterminate" style="background-color: var(--vz-primary);"></div>
                </div>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="addPermissionLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0);" id="userForm" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="userName" name="user_name"
                                placeholder="Enter user name" required />
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email (Username)</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter email address" required />
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="position-relative auth-pass-inputgroup">
                                <input type="password" class="form-control pe-10 password-input" id="password"
                                    name="password" placeholder="Enter password" required
                                    autocomplete="current-password">
                                <button type="button"
                                    class="btn btn-link position-absolute top-0 end-0 me-4 text-decoration-none text-muted material-shadow-none"
                                    id="generatePassword" style="z-index: 2;">
                                    <i class="ri-refresh-line align-middle"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-link position-absolute top-0 end-0 text-decoration-none text-muted password-addon material-shadow-none"
                                    id="passwordAddon" style="z-index: 2;">
                                    <i class="ri-eye-fill align-middle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select select2" name="role_id" id="role_id" multiple="multiple"
                                required>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="is_active" type="checkbox" checked
                                    permission="switch" id="is_active" onchange="toggleSwitchText()" />
                                <label class="form-check-label" for="is_active" id="is_active_label">Active</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="button"
                                    class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                    id="saveUserBtn">
                                    <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                        <span class="loader" style="display: none;"></span>
                                    </i>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">Manage Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="current-permissions">
                    <h6 class="text-primary">Current Permissions</h6>
                    <div id="permissions-table-container" class="accordion nesting-accordion custom-accordionwithicon-plus">
                        <!-- Accordion for current permissions -->
                    </div>
                </div>
                @can('Assign Permission')
                    <div id="add-permissions" class="mt-4">
                        <h6 class="text-primary">All Permissions</h6>
                        <div id="all-permissions-table-container" class="accordion nesting-accordion custom-accordionwithicon-plus">
                            <!-- Accordion for all permissions -->
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>