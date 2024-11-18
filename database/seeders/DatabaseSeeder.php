<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $permissions = [
            'create-receptionist',
            'edit-receptionist',
            'delete-receptionist',
            'view-receptionist',
            'create-student',
            'edit-student',
            'delete-student',
            'view-student',
            'create-invoice',
            'edit-invoice',
            'delete-invoice',
            'view-invoice',
            'create-payment',
            'edit-payment',
            'delete-payment',
            'view-payment',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
            ]);
        }

        $superAdminRole = Role::create(
            [
                "name" => "Super_Admin",
            ]
        );

        $receptionistRole = Role::create(
            [
                "name" => "Receptionist",
            ]
        );

        $superAdminRole->givePermissionTo($permissions);

        $receptionistRole->givePermissionTo([
            'create-student',
            'create-payment',
            'edit-student',
            'view-student',
            'view-invoice',
            'view-payment',
        ]);

        $superAdminOne = \App\Models\User::factory()->create([
            'name' => 'Thamoddya Rashmitha',
            'email' => 'thamo@gmail.com',
            'nic' => '200000000V',
            'password' => Hash::make('1234'),
        ]);

        $superAdminOne->assignRole($superAdminRole);

        $this->call([
            StudentSeeder::class,
            PaymentSeeder::class,
        ]);

    }
}
