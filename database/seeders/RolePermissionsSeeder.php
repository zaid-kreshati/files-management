<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $usertRole = Role::create(['name' => 'user']);

        // Define Permissions
        $adminPeermission = [
            'category.create',
            'category.delete',
            'category.update',
            'category.index',
            'activities.index',
            'metrix.index'
        ];

        $userPermissions = [
            'post.create',
            'post.delete',
            'post.update',
            'post.index',
            'comment.create',
            'comment.update',
            'comment.delete',
            'search'
        ];


        // Create Permissions
        $allPermissions = array_unique(array_merge($adminPeermission, $userPermissions));

        foreach ($allPermissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web'); // Also for API
        }

        // Assign Permissions to Roles
        $adminRole->syncPermissions($adminPeermission); // Admin gets all permissions
        $usertRole->syncPermissions($userPermissions);

        // Create users and assign roles
        // admin
        $admin = User::factory()->create([
            'name' => 'amin',
            'email' => 'amin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        // user
        $User = User::factory()->create([
            'name' => 'zaid',
            'email' => 'zaid@user.com',
            'password' => bcrypt('password'),
        ]);
        $User->assignRole($usertRole);
    }
}
