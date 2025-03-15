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
        // Clear cache to prevent permission issues
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

        // Create permissions with the correct guard
        foreach (array_merge($sitePermissions, $userPermissions) as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'sanctum'
            ]);
        }

        // Create roles with correct guard
        $role1 = Role::firstOrCreate(['name' => 'sites_viewer', 'guard_name' => 'sanctum']);
        $role1->syncPermissions(['site.get', 'site.view', 'site.images', 'site.export']);

        $role2 = Role::firstOrCreate(['name' => 'own_sites_viewer', 'guard_name' => 'sanctum']);
        $role2->syncPermissions(['site.get', 'site.view', 'site.images', 'site.export']);

        $role3 = Role::firstOrCreate(['name' => 'site_manager', 'guard_name' => 'sanctum']);
        $role3->syncPermissions($sitePermissions);

        $role4 = Role::firstOrCreate(['name' => 'user_manager', 'guard_name' => 'sanctum']);
        $role4->syncPermissions($userPermissions);
    }
}
