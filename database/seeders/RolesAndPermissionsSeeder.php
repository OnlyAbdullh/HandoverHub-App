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
            'site.show',
            'site.update',
            'site.delete',
            'site.export',
            'site.images',
        ];

        // Define user-related permissions for user management APIs
        $userPermissions = [
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
        ];

        // Create or get the site permissions
        foreach ($sitePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create or get the user permissions
        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Role 1: Can read all the sites.
        $role1 = Role::firstOrCreate(['name' => 'sites_viewer']);
        $role1->syncPermissions(['site.view', 'site.images', 'site.export']);

        // Role 2: Can read only his own sites.
        $role2 = Role::firstOrCreate(['name' => 'own_sites_viewer']);
        $role2->syncPermissions(['site.view', 'site.images', 'site.export']);

        // Role 3: Site Manager (full access on sites, no user management)
        $role3 = Role::firstOrCreate(['name' => 'site_manager']);
        $role3->syncPermissions($sitePermissions);

        // Role 4: User Manager (can edit/delete sites & manage users)
        $role4 = Role::firstOrCreate(['name' => 'user_manager']);
        $role4->syncPermissions($userPermissions);
    }
}
