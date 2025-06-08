<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoreCompany;
use App\Models\CompanyDbConfig;
use App\Http\Requests\SaveConfigRequest;

/**
 * This controller handles settings in the admin area, like managing company details and database configurations.
 */
class SettingController extends Controller
{
    /**
     * Shows the main settings page.
     *
     * Loads a page where admins can view and manage settings.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Gets a list of all companies.
     *
     * Fetches all company records from the database and sends them back as a JSON response.
     */
    public function company()
    {
        $company = CoreCompany::all();
        return $this->jsonSuccess($company, 'Company fetched successfully.');
    }

    /**
     * Saves or updates a company's database configuration.
     *
     * Checks if the input is correct, then either updates an existing configuration
     * or creates a new one with details like database connection, host, and password.
     */
    public function saveCompanyConfig(SaveConfigRequest $request)
    {
        $validated = $request->validated();

        // Set status to 1 (active) if is_active is provided, otherwise 0 (inactive)
        $status = !empty($validated['is_active']) ? 1 : 0;

        // Prepare data to save
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

        // Check if a configuration already exists for this company and database name
        $existingConfig = CompanyDbConfig::where('company_id', $validated['company_id'])
            ->where('db_name', $validated['db_name'])
            ->first();

        if ($existingConfig) {
            // Update existing configuration
            $existingConfig->update($data);
            return $this->jsonSuccess($existingConfig, 'Configuration updated successfully.');
        } else {
            // Create new configuration
            $data['company_id'] = $validated['company_id'];
            $data['db_name'] = $validated['db_name'];
            $newConfig = CompanyDbConfig::create($data);
            return $this->jsonSuccess($newConfig, 'Configuration created successfully.');
        }
    }

    /**
     * Gets database configurations for a specific company.
     *
     * Finds all configurations for a company by its ID and organizes them
     * into 'hrims' and 'expense' categories, then sends them back as a JSON response.
     */
    public function getCompanyConfig(Request $request, string $id)
    {
        $configs = CompanyDbConfig::where('company_id', $id)->get();

        // Prepare an array to hold hrims and expense configurations
        $data = [
            'hrims' => null,
            'expense' => null,
        ];

        // Sort configurations into hrims or expense
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