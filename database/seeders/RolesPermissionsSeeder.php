<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'ADMIN_FER',
            'DCG_FER',
            'DAF_FER',
            // 'DT_FER',
            // 'DG_FER',
            // 'AUDIT_FER',
            // 'COMPTABILITE_DGIR',
            // 'DMC_AGEROUTE',
            // 'DGA_AGEROUTE',
            // 'DAFP_AGEROUTE',
        ];

        $permissions = [
            'VIEW_DASHBOARD',
            'VIEW_REPORTS',
            'MANAGE_USERS',
            'MANAGE_ROLES',
            'MANAGE_PERMISSIONS',
            'MANAGE_BANKS',
            'MANAGE_FINANCES',
            'VIEW_AUDIT_LOGS',
        ];

        $rolePermissionMap = [
            'ADMIN_FER' => $permissions,
            'DCG_FER' => ['VIEW_DASHBOARD', 'VIEW_REPORTS', 'MANAGE_BANKS', 'MANAGE_FINANCES'],
            'DAF_FER' => ['VIEW_DASHBOARD', 'VIEW_REPORTS', 'MANAGE_BANKS', 'MANAGE_FINANCES'],
            'DT_FER' => ['VIEW_DASHBOARD', 'VIEW_REPORTS'],
            'DG_FER' => ['VIEW_DASHBOARD', 'VIEW_REPORTS'],
            'AUDIT_FER' => ['VIEW_DASHBOARD', 'VIEW_REPORTS', 'VIEW_AUDIT_LOGS'],
            'COMPTABILITE_DGIR' => ['VIEW_DASHBOARD', 'VIEW_REPORTS', 'MANAGE_BANKS'],
            'DMC_AGEROUTE' => ['VIEW_DASHBOARD'],
            'DGA_AGEROUTE' => ['VIEW_DASHBOARD'],
            'DAFP_AGEROUTE' => ['VIEW_DASHBOARD'],
        ];

        foreach ($roles as $roleLabel) {
            Role::updateOrCreate(
                ['LIBELLE' => $roleLabel],
                ['IS_DELETE' => false]
            );
        }

        foreach ($permissions as $permissionLabel) {
            Permission::updateOrCreate(
                ['LIBELLE' => $permissionLabel],
                ['IS_DELETE' => false]
            );
        }

        foreach ($rolePermissionMap as $roleLabel => $permissionLabels) {
            $role = Role::where('LIBELLE', $roleLabel)->first();
            if (!$role) {
                continue;
            }

            foreach ($permissionLabels as $permissionLabel) {
                $permission = Permission::where('LIBELLE', $permissionLabel)->first();
                if (!$permission) {
                    continue;
                }

                RolePermission::updateOrCreate(
                    [
                        'ROLE_ID' => $role->ID,
                        'PERMISSION_ID' => $permission->ID,
                    ],
                    ['IS_DELETE' => false]
                );
            }
        }
    }
}
