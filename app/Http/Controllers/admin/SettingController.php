<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoreCompany;
use App\Models\CompanyDbConfig;
use App\Http\Requests\SaveConfigRequest;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }
    public function company()
    {
        $company = CoreCompany::all();
        return $this->jsonSuccess($company, 'company fetched successfully.');
    }
    public function saveCompanyConfig(SaveConfigRequest $request)
    {
        $validated = $request->validated();

        $status = !empty($validated['is_active']) ? 1 : 0;

        $data = [
            'db_connection' => $validated['db_connection'],
            'db_host' => $validated['db_host'],
            'db_port' => $validated['db_port'],
            'db_database' => $validated['db_database'],
            'db_username' => $validated['db_username'],
            'db_password' => $validated['db_password'],
            'status' => $status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        $existingConfig = CompanyDbConfig::where('company_id', $validated['company_id'])
            ->where('db_name', $validated['db_name'])
            ->first();

        if ($existingConfig) {
            $existingConfig->update($data);
            return $this->jsonSuccess($existingConfig, 'Configuration updated successfully.');
        } else {
            $data['company_id'] = $validated['company_id'];
            $data['db_name'] = $validated['db_name'];
            $newConfig = CompanyDbConfig::create($data);
            return $this->jsonSuccess($newConfig, 'Configuration created successfully.');
        }
    }

    public function getCompanyConfig(Request $request, string $id)
    {
        $configs = CompanyDbConfig::where('company_id', $id)->get();

        $data = [
            'hrims' => null,
            'expense' => null,
        ];

        foreach ($configs as $config) {
            if ($config->db_name === 'hrims') {
                $data['hrims'] = $config;
            } elseif ($config->db_name === 'expense') {
                $data['expense'] = $config;
            }
        }

        return $this->jsonSuccess($data, 'Company details fetched successfully.');
    }

}
