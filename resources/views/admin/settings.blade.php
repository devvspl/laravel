@extends('layouts.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
        @section('title', ucwords(str_replace('-', ' ', Request::path())))
        <x-theme.breadcrumb title="{{ ucwords(str_replace('-', ' ', Request::path())) }}" :breadcrumbs="[
            ['label' => 'Dashboards', 'url' => '#'],
            ['label' => ucwords(str_replace('-', ' ', Request::path()))],
        ]" />
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center"
                                    role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="general-tab" data-bs-toggle="pill"
                                        href="?tab=general#general" role="tab" aria-controls="general"
                                        aria-selected="true">
                                        <i class="ri-settings-3-line d-block fs-20 mb-1"></i> General
                                    </a>
                                    <a class="nav-link" id="security-tab" data-bs-toggle="pill"
                                        href="?tab=security#security" role="tab" aria-controls="security"
                                        aria-selected="false">
                                        <i class="ri-lock-line d-block fs-20 mb-1"></i> Security
                                    </a>
                                    <a class="nav-link" id="integrations-tab" data-bs-toggle="pill"
                                        href="?tab=integrations#integrations" role="tab"
                                        aria-controls="integrations" aria-selected="false">
                                        <i class="ri-plug-line d-block fs-20 mb-1"></i> Integrations
                                    </a>
                                    <a class="nav-link" id="performance-tab" data-bs-toggle="pill"
                                        href="?tab=performance#performance" role="tab" aria-controls="performance"
                                        aria-selected="false">
                                        <i class="ri-speed-up-line d-block fs-20 mb-1"></i> Performance
                                    </a>
                                    <a class="nav-link" id="notifications-tab" data-bs-toggle="pill"
                                        href="?tab=notifications#notifications" role="tab"
                                        aria-controls="notifications" aria-selected="false">
                                        <i class="ri-notification-3-line d-block fs-20 mb-1"></i> Notifications
                                    </a>
                                    <a class="nav-link" id="appearance-tab" data-bs-toggle="pill"
                                        href="?tab=appearance#appearance" role="tab" aria-controls="appearance"
                                        aria-selected="false">
                                        <i class="ri-palette-line d-block fs-20 mb-1"></i> Appearance
                                    </a>
                                    <a class="nav-link" id="database-tab" data-bs-toggle="pill"
                                        href="?tab=database#database" role="tab" aria-controls="database"
                                        aria-selected="false">
                                        <i class="ri-database-2-line d-block fs-20 mb-1"></i> Database
                                    </a>
                                    <a class="nav-link" id="analytics-tab" data-bs-toggle="pill"
                                        href="?tab=analytics#analytics" role="tab" aria-controls="analytics"
                                        aria-selected="false">
                                        <i class="ri-line-chart-line d-block fs-20 mb-1"></i> Analytics
                                    </a>
                                    <a class="nav-link" id="backup-tab" data-bs-toggle="pill" href="?tab=backup#backup"
                                        role="tab" aria-controls="backup" aria-selected="false">
                                        <i class="ri-recycle-line d-block fs-20 mb-1"></i> Backup
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="tab-content text-muted mt-3 mt-lg-0">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel"
                                        aria-labelledby="general-tab">
                                        <h6>General Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="projectName" class="form-label">Project Name</label>
                                                <input type="text" class="form-control" id="projectName"
                                                    placeholder="Enter project name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="timeZone" class="form-label">Time Zone</label>
                                                <select class="form-select" id="timeZone">
                                                    <option value="UTC">UTC</option>
                                                    <option value="PST">Pacific Standard Time</option>
                                                    <option value="EST">Eastern Standard Time</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="language" class="form-label">Default Language</label>
                                                <select class="form-select" id="language">
                                                    <option value="en">English</option>
                                                    <option value="es">Spanish</option>
                                                    <option value="fr">French</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="maintenanceMode">
                                                <label class="form-check-label" for="maintenanceMode">Enable
                                                    Maintenance Mode</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="security" role="tabpanel"
                                        aria-labelledby="security-tab">
                                        <h6>Security Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="apiKey" class="form-label">API Key</label>
                                                <input type="text" class="form-control" id="apiKey"
                                                    placeholder="Enter API key">
                                            </div>
                                            <div class="mb-3">
                                                <label for="sessionTimeout" class="form-label">Session Timeout
                                                    (minutes)</label>
                                                <input type="number" class="form-control" id="sessionTimeout"
                                                    value="15">
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="enableEncryption">
                                                <label class="form-check-label" for="enableEncryption">Enable Data
                                                    Encryption</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="integrations" role="tabpanel"
                                        aria-labelledby="integrations-tab">
                                        <h6>Integrations Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="smtpHost" class="form-label">SMTP Host</label>
                                                <input type="text" class="form-control" id="smtpHost"
                                                    placeholder="smtp.example.com">
                                            </div>
                                            <div class="mb-3">
                                                <label for="smtpUsername" class="form-label">SMTP Username</label>
                                                <input type="text" class="form-control" id="smtpUsername"
                                                    placeholder="Enter SMTP username">
                                            </div>
                                            <div class="mb-3">
                                                <label for="webhookUrl" class="form-label">Webhook URL</label>
                                                <input type="url" class="form-control" id="webhookUrl"
                                                    placeholder="https://example.com/webhook">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="performance" role="tabpanel"
                                        aria-labelledby="performance-tab">
                                        <h6>Performance Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="cacheTTL" class="form-label">Cache TTL (seconds)</label>
                                                <input type="number" class="form-control" id="cacheTTL"
                                                    value="3600">
                                            </div>
                                            <div class="mb-3">
                                                <label for="rateLimit" class="form-label">Rate Limit
                                                    (requests/min)</label>
                                                <input type="number" class="form-control" id="rateLimit"
                                                    value="100">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="notifications" role="tabpanel"
                                        aria-labelledby="notifications-tab">
                                        <h6>Notifications Settings</h6>
                                        <form>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="adminAlerts">
                                                <label class="form-check-label" for="adminAlerts">Enable Admin
                                                    Alerts</label>
                                            </div>
                                            <div class="mb-3">
                                                <label for="notificationChannel" class="form-label">Notification
                                                    Channel</label>
                                                <select class="form-select" id="notificationChannel">
                                                    <option value="email">Email</option>
                                                    <option value="slack">Slack</option>
                                                    <option value="sms">SMS</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="appearance" role="tabpanel"
                                        aria-labelledby="appearance-tab">
                                        <h6>Appearance Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="theme" class="form-label">Theme</label>
                                                <select class="form-select" id="theme">
                                                    <option value="light">Light</option>
                                                    <option value="dark">Dark</option>
                                                    <option value="custom">Custom</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Logo</label>
                                                <input type="file" class="form-control" id="logo">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="database" role="tabpanel"
                                        aria-labelledby="database-tab">
                                        <h6>Database Settings</h6>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Company Name</th>
                                                    <th>Company Code</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="backup" role="tabpanel"
                                        aria-labelledby="backup-tab">
                                        <h6>Database Settings</h6>
                                        <form>
                                            <div class="mb-3">
                                                <label for="dbHost" class="form-label">Database Host</label>
                                                <input type="text" class="form-control" id="dbHost"
                                                    placeholder="localhost">
                                            </div>
                                            <div class="mb-3">
                                                <label for="backupSchedule" class="form-label">Backup Schedule</label>
                                                <select class="form-select" id="backupSchedule">
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                    <option value="monthly">Monthly</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="analytics" role="tabpanel"
                                        aria-labelledby="analytics-tab">
                                        <h6>Analytics Settings</h6>
                                        <form>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="enableTracking">
                                                <label class="form-check-label" for="enableTracking">Enable Usage
                                                    Tracking</label>
                                            </div>
                                            <div class="mb-3">
                                                <label for="analyticsKey" class="form-label">Analytics Key</label>
                                                <input type="text" class="form-control" id="analyticsKey"
                                                    placeholder="Enter analytics key">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="progress-container" style="display: none">
                <div class="progres" style="height: 5px;">
                    <div class="indeterminate" style="background-color: var(--vz-primary);"></div>
                </div>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="configModalLabel">Database Configuration <span
                        id="modalCompanyName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-1" id="dbTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="hrims-tab" data-bs-toggle="tab" data-bs-target="#hrims"
                            type="button" role="tab" aria-controls="hrims" aria-selected="true">HRIMS</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense"
                            type="button" role="tab" aria-controls="expense"
                            aria-selected="false">Expense</button>
                    </li>
                </ul>
                <div class="tab-content" id="dbTabContent">
                    <div class="tab-pane fade show active" id="hrims" role="tabpanel"
                        aria-labelledby="hrims-tab">
                        <form action="#" method="POST" class="mt-3">
                            <input type="hidden" name="company_id" id="hrims_company_id">
                            <input type="hidden" name="db_name" value="hrims">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_connection" class="form-label">Database Connection</label>
                                    <input type="text" name="db_connection" id="hrims_db_connection"
                                        class="form-control" value="mysql" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_host" class="form-label">Database Host</label>
                                    <input type="text" name="db_host" id="hrims_db_host" class="form-control"
                                        value="127.0.0.1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_port" class="form-label">Database Port</label>
                                    <input type="number" name="db_port" id="hrims_db_port" class="form-control"
                                        value="3306" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_database" class="form-label">Database</label>
                                    <input type="text" name="db_database" id="hrims_db_database"
                                        class="form-control" value="hrims" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_username" class="form-label">Database Username</label>
                                    <input type="text" name="db_username" id="hrims_db_username"
                                        class="form-control" value="root" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hrims_db_password" class="form-label">Database Password</label>
                                    <input type="text" name="db_password" id="hrims_db_password"
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="is_active" type="checkbox" checked
                                            role="switch" id="is_active" onchange="toggleSwitchText()" />
                                        <label class="form-check-label" for="is_active"
                                            id="is_active_label">Active</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button"
                                            class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                            id="">
                                            <i
                                                class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                                <span class="loader" style="display: none;"></span>
                                            </i>
                                            Save Config
                                        </button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="tab-pane fade" id="expense" role="tabpanel" aria-labelledby="expense-tab">
                        <form action="#" method="POST" class="mt-3">
                            <input type="hidden" name="company_id" id="expense_company_id">
                            <input type="hidden" name="db_name" value="expense">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_connection" class="form-label">Database Connection</label>
                                    <input type="text" name="db_connection" id="expense_db_connection"
                                        class="form-control" value="mysql" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_host" class="form-label">Database Host</label>
                                    <input type="text" name="db_host" id="expense_db_host" class="form-control"
                                        value="127.0.0.1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_port" class="form-label">Database Port</label>
                                    <input type="number" name="db_port" id="expense_db_port" class="form-control"
                                        value="3306" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_database" class="form-label">Database</label>
                                    <input type="text" name="db_database" id="expense_db_database"
                                        class="form-control" value="expense" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_username" class="form-label">Database Username</label>
                                    <input type="text" name="db_username" id="expense_db_username"
                                        class="form-control" value="root" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_db_password" class="form-label">Database Password</label>
                                    <input type="text" name="db_password" id="expense_db_password"
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="is_active" type="checkbox" checked
                                            role="switch" id="is_active" onchange="toggleSwitchText()" />
                                        <label class="form-check-label" for="is_active"
                                            id="is_active_label">Active</label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button"
                                            class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                                            id="">
                                            <i
                                                class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                                                <span class="loader" style="display: none;"></span>
                                            </i>
                                            Save Config
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('custom/js/pages/setting.js') }}"></script>
@endpush
