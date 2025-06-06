<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="progress-container" style="display: none;">
                <div class="progres" style="height: 5px;">
                    <div class="indeterminate" style="background-color: var(--vz-primary);"></div>
                </div>
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Claim Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-4">
                        <h6>Select Columns to Export</h6>
                        <div class="row">
                            <!-- Claim Section -->
                            <div class="col-12 mb-3">
                                <h6 class="text-primary">Claim Information</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="sn" id="colSn" checked>
                                            <label class="form-check-label" for="colSn">Sn</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="claim_id" id="colClaimId" checked>
                                            <label class="form-check-label" for="colClaimId">Claim ID</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="claim_type" id="colClaimType" checked>
                                            <label class="form-check-label" for="colClaimType">Claim Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="claim_status" id="colClaimStatus" checked>
                                            <label class="form-check-label" for="colClaimStatus">Claim Status</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="emp_name" id="colEmpName" checked>
                                            <label class="form-check-label" for="colEmpName">Emp Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="emp_code" id="colEmpCode" checked>
                                            <label class="form-check-label" for="colEmpCode">Emp Code</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="month" id="colMonth" checked>
                                            <label class="form-check-label" for="colMonth">Month</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="upload_date" id="colUploadDate" checked>
                                            <label class="form-check-label" for="colUploadDate">Upload Date</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="bill_date" id="colBillDate" checked>
                                            <label class="form-check-label" for="colBillDate">Bill Date</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="function" id="colFunction">
                                            <label class="form-check-label" for="colFunction">Function</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="vertical" id="colVertical">
                                            <label class="form-check-label" for="colVertical">Vertical</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="department" id="colDepartment">
                                            <label class="form-check-label" for="colDepartment">Department</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="policy" id="colPolicy">
                                            <label class="form-check-label" for="colPolicy">Policy</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="vehicle_type" id="colVehicleType">
                                            <label class="form-check-label" for="colVehicleType">Vehicle Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="odomtr_opening" id="colOdoOpening">
                                            <label class="form-check-label" for="colOdoOpening">Odomtr Opening</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="odomtr_closing" id="colOdoClosing">
                                            <label class="form-check-label" for="colOdoClosing">Odomtr Closing</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="TotKm" id="colTotKm">
                                            <label class="form-check-label" for="colTotKm">Total KM</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="WType" id="colWType">
                                            <label class="form-check-label" for="colWType">Wheeler Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="RatePerKM" id="colRatePerKM">
                                            <label class="form-check-label" for="colRatePerKM">Rate Per KM</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filled Section -->
                            <div class="col-12 mb-3">
                                <h6 class="text-primary">Filled Details</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="claimed_amt" id="colClaimedAmt" checked>
                                            <label class="form-check-label" for="colClaimedAmt">Claimed Amt</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="FilledDate" id="colFilledDate">
                                            <label class="form-check-label" for="colFilledDate">Filled Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Verify Section -->
                            <div class="col-12 mb-3">
                                <h6 class="text-primary">Verification Details</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="VerifyAmt" id="colVerifyAmt" checked>
                                            <label class="form-check-label" for="colVerifyAmt">Verified Amt</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="VerifyTRemark" id="colVerifyTRemark">
                                            <label class="form-check-label" for="colVerifyTRemark">Verify
                                                Remark</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="VerifyDate" id="colVerifyDate">
                                            <label class="form-check-label" for="colVerifyDate">Verify Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approve and Finance Section -->
                            <div class="col-12 mb-3">
                                <h6 class="text-primary">Approval and Finance Details</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="ApprAmt" id="colApprAmt" checked>
                                            <label class="form-check-label" for="colApprAmt">Approved Amt</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="ApprTRemark" id="colApprTRemark">
                                            <label class="form-check-label" for="colApprTRemark">Approval
                                                Remark</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="ApprDate" id="colApprDate">
                                            <label class="form-check-label" for="colApprDate">Approval Date</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="FinancedAmt" id="colFinancedAmt" checked>
                                            <label class="form-check-label" for="colFinancedAmt">Financed Amt</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="FinancedTRemark" id="colFinancedTRemark">
                                            <label class="form-check-label" for="colFinancedTRemark">Finance
                                                Remark</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input column-checkbox" type="checkbox"
                                                value="FinancedDate" id="colFinancedDate">
                                            <label class="form-check-label" for="colFinancedDate">Finance Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-primary btn-label waves-effect waves-light rounded-pill"
                        id="exportExcelBtn">
                        <i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2">
                            <span class="loader" style="display: none;"></span>
                        </i>
                        Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
