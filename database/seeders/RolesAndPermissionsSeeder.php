<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $sitePermissions = [
            'site.create',
            'site.get',
            'site.view',
            'site.update',
            'site.delete',
            'site.export',
            'site.images',
        ];

        $userPermissions = [
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
        ];

        $reportPermissions = [
            'report.view',
            'report.get',
            'report.create',
            'report.update',
            'report.delete',
            'report.export',
            'report.add-task',
            'report.delete-tasks',
            'report.add-note',
            'report.delete-notes',
            'report.add-part',
            'report.delete-parts',
        ];

        $allPermissions = array_merge($sitePermissions, $userPermissions, $reportPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }

        // Create roles with correct guard
        $role2 = Role::firstOrCreate(['name' => 'mtn_account', 'guard_name' => 'sanctum']);
        $role2->syncPermissions(['site.get', 'site.view', 'site.images']);

        $role1 = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'sanctum']);
        $role1->syncPermissions(['site.get', 'site.view', 'site.images', 'site.create', 'report.create']);

        $siteAdmin = Role::firstOrCreate(['name' => 'site_admin', 'guard_name' => 'sanctum']);
        $siteAdmin->syncPermissions(array_merge($sitePermissions, $reportPermissions));

        $admin = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
        $admin->syncPermissions($allPermissions);


    }

}
