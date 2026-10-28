<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Unit;

class DepartmentUnitSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            // ==========================================
            // 1. Departemen Human Capital & GRC (Kode dipertahankan)
            // ==========================================
            ['code' => 'HC_GRC', 'name' => 'Department of Human Capital & GRC', 'units' => [
                ['code' => 'HCPD', 'name' => 'Unit of HC Planning & Development'],
                ['code' => 'HCO', 'name' => 'Unit of HC Operational'],
                ['code' => 'BUSDEV', 'name' => 'Unit of Business & System Development'],
                ['code' => 'GRC', 'name' => 'Unit of GRC Management'],
                ['code' => 'HSIH', 'name' => 'Unit of Health Serv & Industrial Hygiene'],
            ]],
            
            // ==========================================
            // 2. Departemen Lainnya
            // ==========================================
            ['code' => 'FIN_ACC', 'name' => 'Department of Finance & Accounting', 'units' => [
                ['code' => 'ACC', 'name' => 'Unit of Accounting'],
                ['code' => 'BUD_PERF', 'name' => 'Unit of Budgeting & Performance'],
                ['code' => 'FIN', 'name' => 'Unit of Finance'],
            ]],
            ['code' => 'INT_AUD', 'name' => 'Department of Internal Audit', 'units' => [
                ['code' => 'STAFF_IA', 'name' => 'Staff of Internal Audit'],
            ]],
            ['code' => 'PROD_CC', 'name' => 'Dept. of Clinker & Cement Production', 'units' => [
                ['code' => 'CEMENT', 'name' => 'Unit of Cement Production'],
                ['code' => 'CLINKER', 'name' => 'Unit of Clinker Production'],
                ['code' => 'DERIV', 'name' => 'Unit of Derivative Product & Support'],
            ]],
            ['code' => 'COMM_LGA', 'name' => 'Dept. of Communication, Legal & GA', 'units' => [
                ['code' => 'COMM_SEC', 'name' => 'Unit of Communication & Secretariat'],
                ['code' => 'GEN_FAC', 'name' => 'Unit of General Facility & Asset'],
                ['code' => 'LEGAL', 'name' => 'Unit of Legal'],
                ['code' => 'SECURITY', 'name' => 'Unit of Security'],
                ['code' => 'TJSL', 'name' => 'Unit of TJSL'],
            ]],
            ['code' => 'INFRA', 'name' => 'Dept. of Infrastructure', 'units' => [
                ['code' => 'PLAN_EVAL', 'name' => 'Staff of Planning Eval & Prod Dist'],
                ['code' => 'INTER_LOG', 'name' => 'Unit of Interplant Logistic'],
                ['code' => 'PACK1', 'name' => 'Unit of Packing Plant 1'],
                ['code' => 'PACK2', 'name' => 'Unit of Packing Plant 2'],
                ['code' => 'PORT_DISCH', 'name' => 'Unit of Plant & Port Prod Discharge'],
                ['code' => 'SCM_PORT', 'name' => 'Unit of SCM Infra Port Management'],
            ]],
            ['code' => 'MAINT', 'name' => 'Dept. of Maintenance', 'units' => [
                ['code' => 'ELINS1', 'name' => 'Unit of Elins Maintenance 1'],
                ['code' => 'ELINS2', 'name' => 'Unit of Elins Maintenance 2'],
                ['code' => 'MACH1', 'name' => 'Unit of Machine Maintenance 1'],
                ['code' => 'MACH2', 'name' => 'Unit of Machine Maintenance 2'],
                ['code' => 'PORT_MAINT', 'name' => 'Unit of Port Product Discharge Maint.'],
                ['code' => 'REL_MAINT', 'name' => 'Unit of Reliability Maintenance'],
            ]],
            ['code' => 'MKT_DEV', 'name' => 'Dept. of Market Planning & Development', 'units' => [
                ['code' => 'SALES_PROC', 'name' => 'Staff of Sales Process Management'],
                ['code' => 'MKT_INTEL', 'name' => 'Unit of Market Intel. Channel & SF Mgmt'],
                ['code' => 'PRICING', 'name' => 'Unit of Product Pricing & Promotion'],
            ]],
            ['code' => 'MINE_PWR', 'name' => 'Dept. of Mining & Power Plant', 'units' => [
                ['code' => 'MINE_PLAN', 'name' => 'Unit of Mine Plan'],
                ['code' => 'MINE_OP', 'name' => 'Unit of Mining Operation'],
                ['code' => 'PWR_DIST', 'name' => 'Unit of Power Distribution'],
                ['code' => 'PWR_ELINS', 'name' => 'Unit of Power Plant Elins Maint.'],
                ['code' => 'PWR_MACH', 'name' => 'Unit of Power Plant Machine Maint.'],
                ['code' => 'PWR_OP', 'name' => 'Unit of Power Plant Operation'],
                ['code' => 'RAW_MAT', 'name' => 'Unit of Raw Material Management'],
            ]],
            ['code' => 'PROD_PLAN', 'name' => 'Dept. of Production Planning & Control', 'units' => [
                ['code' => 'AFR_ENG', 'name' => 'Unit of AFR & Energy'],
                ['code' => 'OHS', 'name' => 'Unit of OHS'],
                ['code' => 'PPE_ENV', 'name' => 'Unit of Prod. Plan Eval. & Environment'],
                ['code' => 'PROD_SUPP', 'name' => 'Unit of Production Support'],
                ['code' => 'QC', 'name' => 'Unit of Quality Control'],
            ]],
            ['code' => 'PROJ_MAINT', 'name' => 'Dept. of Project Mngmnt & Maint. Support', 'units' => [
                ['code' => 'TPM_OFF', 'name' => 'Staff of TPM Officer'],
                ['code' => 'CAPEX', 'name' => 'Unit of CAPEX Management'],
                ['code' => 'ENGIN', 'name' => 'Unit of Engineering'],
                ['code' => 'MAINT_PLAN', 'name' => 'Unit of Maintenance Planning & Eval.'],
                ['code' => 'PROJ_MGT', 'name' => 'Unit of Project Management'],
                ['code' => 'WORKSHOP', 'name' => 'Unit of Workshop'],
            ]],
            ['code' => 'SALES', 'name' => 'Dept. of Sales', 'units' => [
                ['code' => 'STAFF_SALES', 'name' => 'Staff of Sales'],
                ['code' => 'CORP_SALES', 'name' => 'Unit of Corporate Sales'],
                ['code' => 'OUT_LOG', 'name' => 'Unit of Outbound Logistic'],
                ['code' => 'SALES1', 'name' => 'Unit of Sales 1'],
                ['code' => 'SALES2', 'name' => 'Unit of Sales 2'],
            ]],
        ];

        foreach ($departments as $deptData) {
            // UpdateOrCreate memastikan tidak akan ada error duplikat
            $dept = Department::updateOrCreate(
                ['code_dept' => $deptData['code']],
                ['name_dept' => $deptData['name']]
            );

            foreach ($deptData['units'] as $unitData) {
                Unit::updateOrCreate(
                    ['code_unit' => $unitData['code']],
                    [
                        'name_unit'     => $unitData['name'],
                        'department_id' => $dept->id
                    ]
                );
            }
        }
    }
}