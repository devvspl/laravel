<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClaimViewController extends Controller
{
    public function getClaimDetailView(Request $request)
    {
        $claimId = $request->input('claim_id');
        $componentPath = resource_path("views/components/modal/claim_view/claim_detail_{$claimId}.blade.php");
        if (file_exists($componentPath)) {
            $html = view("components.modal.claim_view.claim_detail_{$claimId}", ['claimId' => $claimId])->render();
        } else {
            $html = '<div class="alert alert-warning">Component for Claim ID ' . $claimId . ' not found.</div>';
        }
        return response()->json(['html' => $html]);
    }
}
