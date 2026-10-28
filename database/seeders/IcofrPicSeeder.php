<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IcofrPic;

class IcofrPicSeeder extends Seeder
{
    public function run(): void
    {
        $pics = [
            ['name' => 'Chief Executive Officer', 'position' => 'Chief Executive Officer', 'unit' => 'Chief Executive Officer', 'department' => 'Chief Executive Officer'],
            ['name' => 'Ricky Novianto, S.Ak.', 'position' => 'SM of General Facility & Aset', 'unit' => 'Unit of General Facility & Aset', 'department' => 'Department of Communication, Legal & GA'],
            ['name' => 'Muhammad Fatwatul Haq, S. Tr. Ak.', 'position' => 'AR Management Officer', 'unit' => 'Unit of Finance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Alamsyah, S.M.', 'position' => 'Financial Accounting Officer', 'unit' => 'Unit of Accounting', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Amiruddin Said, SE.', 'position' => 'SM of Accounting', 'unit' => 'Unit of Accounting', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Eunike Yulinah S, SE., MM.', 'position' => 'SM of Budgeting & Performance', 'unit' => 'Unit of Budgeting & Performance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Muh. Fachreza Nugraha, SE.', 'position' => 'Cost Accounting Officer', 'unit' => 'Unit of Budgeting & Performance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Muh. Rifqi Sanuddin, SE.', 'position' => 'Treasury Officer', 'unit' => 'Unit of Finance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Hj. Jumriah Hafid, SE, M.Ak', 'position' => 'SM of Finance', 'unit' => 'Unit of Finance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Endah Sulistyo Haryani, SE., Akt., MSi.', 'position' => 'Business Controller', 'unit' => 'Unit of Finance, Unit of Accounting', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Misnawati, SE.', 'position' => 'Taxation Officer', 'unit' => 'Unit of Accounting', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Firdaus, SE.', 'position' => 'Performance Evaluation Officer', 'unit' => 'Unit of Budgeting & Performance', 'department' => 'Department of Finance & Accounting'],
            ['name' => 'Muh. Akhdharisa SJ, SH. MH, CLA.CGP., Dr..', 'position' => 'GM of Human Capital & GRC', 'unit' => 'Unit of HC Operational', 'department' => 'Dept of Human Capital & GRC'],
            ['name' => 'Asril Gazali', 'position' => 'Mgr of HC Operation & Performance', 'unit' => 'Unit of HC Operational', 'department' => 'Dept of Human Capital & GRC'],
            ['name' => 'Widya Kurniasari Rahim, ST.', 'position' => 'SM of HC Operational', 'unit' => 'Unit of HC Operational', 'department' => 'Dept of Human Capital & GRC'],
            ['name' => null, 'position' => 'Senior Internal Audit Officer', 'unit' => 'Senior Internal Audit Officer', 'department' => 'Dept of Internal Audit'],
            ['name' => null, 'position' => 'Mgr of Packing Management', 'unit' => 'Dept of of Infrastructure', 'department' => 'Dept of of Infrastructure'],
            ['name' => 'Jasmiati, ST.', 'position' => 'SM of Prod. Planning & Environmental', 'unit' => 'Unit of Production Plan & Evaluation', 'department' => 'Department of Production Planning & Control'],
            ['name' => 'Muh. Asis Asri, ST.', 'position' => 'SM of CAPEX Management', 'unit' => 'Unit of CAPEX Management', 'department' => 'Department of Project Mngmnt & Maint. Support'],
            ['name' => 'Yatman Setiawan, ST., MM.', 'position' => 'SM of Project Management', 'unit' => 'Unit of CAPEX Management', 'department' => 'Department of Project Mngmnt & Maint. Support'],
            ['name' => 'Syafardino, Ir., ST., IPM.', 'position' => 'GM Project Management and Maintenance Support', 'unit' => 'Unit of CAPEX Management', 'department' => 'Department of Project Mngmnt & Maint. Support'],
            ['name' => null, 'position' => 'Manager of Project Execution', 'unit' => 'Unit of CAPEX Management', 'department' => 'Department of Project Mngmnt & Maint. Support'],
            ['name' => null, 'position' => 'SM of Maintenance Planning & Evaluation', 'unit' => 'Unit of Maintenance Planning & Evaluation', 'department' => 'Department of Project Mngmnt & Maint. Support'],
            ['name' => null, 'position' => 'Mgr of Land Transportation', 'unit' => 'Unit of Outbound Logistic', 'department' => 'Dept of Sales'],
            ['name' => null, 'position' => 'Mgr of Channel Process', 'unit' => 'Dept of Market Planning & Development', 'department' => 'Dept of Sales'],
            ['name' => null, 'position' => 'Sales Manager of Regional Sales Export', 'unit' => 'Dept of Sales', 'department' => 'Dept of Sales'],
            ['name' => null, 'position' => 'Sales Administration Jr Officer', 'unit' => 'Dept of Sales', 'department' => 'Dept of Sales'],
            ['name' => null, 'position' => 'Mgr of Channel Process', 'unit' => 'Dept of Market Planning & Development', 'department' => 'Department of Market Planning & Development'],
            ['name' => 'Aqmal Kusumah', 'position' => 'Sales Process Management Officer', 'unit' => 'GM of Market Planning & Development', 'department' => 'Dept of Market Planning & Development'],
            ['name' => 'Manat Silitonga, ST., MM.', 'position' => 'GM Inisiator Capex', 'unit' => 'Inisiator CAPEX', 'department' => 'Department of Maintenance'],
            ['name' => null, 'position' => 'Senior Manager Inisiator CAPEX', 'unit' => 'Unit of CAPEX Management', 'department' => 'Department of Maintenance'],
            ['name' => null, 'position' => 'Unit Kerja Pemilik Aset', 'unit' => 'Unit Kerja Pemilik Aset', 'department' => 'Unit Kerja Pemilik Aset'],
            ['name' => 'Eldy Putratama, SE.', 'position' => 'SM of Warehouse', 'unit' => 'Unit of Warehouse', 'department' => 'Unit of Warehouse'],
            ['name' => 'M. Natsir, S.Sos.', 'position' => 'Warehouse Officer', 'unit' => 'Unit of Warehouse', 'department' => 'Unit of Warehouse'],
        ];

        foreach ($pics as $pic) {
            IcofrPic::updateOrCreate(
                [
                    'position' => $pic['position'],
                    'unit'     => $pic['unit'],
                ],
                [
                    'name'       => $pic['name'],
                    'department' => $pic['department'],
                ]
            );
        }
    }
}

