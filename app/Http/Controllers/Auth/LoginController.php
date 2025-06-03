<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\FinancialYear;
use App\Models\CoreCompany; // Import the CoreCompany model

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        $financialYears = FinancialYear::orderBy('y1', 'desc')->get();
        $companies = CoreCompany::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        return view('auth.login', compact('financialYears', 'companies'));
    }
}