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
        // user1
        $user1 = User::factory()->create([
            'name' => 'Noor',
            'email' => 'noor@user.com',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($adminRole);

        // user2
        $user2 = User::factory()->create([
            'name' => 'Zaid',
            'email' => 'zaid@user.com',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($usertRole);

         // user2
         $user3 = User::factory()->create([
            'name' => 'Hiba',
            'email' => 'hiba@user.com',
            'password' => bcrypt('password'),
        ]);
        $user3->assignRole($usertRole);

         // user4
         $user4 = User::factory()->create([
            'name' => 'Rifaee',
            'email' => 'rifaee@user.com',
            'password' => bcrypt('password'),
        ]);
        $user4->assignRole($usertRole);

         // user5
         $user5 = User::factory()->create([
            'name' => 'Hedar',
            'email' => 'hedar@user.com',
            'password' => bcrypt('password'),
        ]);
        $user5->assignRole($usertRole);



        // Generate 100 users
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => bcrypt('password'),
            ]);
            $user->assignRole($usertRole);
        }






    }
}
