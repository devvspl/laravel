<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HRMEmployees extends Model
{
    protected $connection = 'hrims';
    protected $table = 'hrm_employee';
    protected $primaryKey = 'EmployeeID';
    public $timestamps = false;
    protected $fillable = ['EmployeeID', 'EmpCode', 'Fname', 'Sname', 'Lname', 'EmpStatus'];

    /**
     * Define the relationship with the Department model.
     * Assumes DepartmentId in hrm_employee_general references Department model.
     */
    public function department()
    {
        return $this->belongsTo(CoreDepartments::class, 'DepartmentId', 'DepartmentId')->join('hrm_employee_general', 'hrm_employee.EmployeeID', '=', 'hrm_employee_general.EmployeeID');
    }
}