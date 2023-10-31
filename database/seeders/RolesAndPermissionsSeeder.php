<?php

namespace Database\Seeders;

use App\Enums\Permissions\Admin;
use App\Enums\Permissions\Constructions;
use App\Enums\Permissions\Languages;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionEnums = [
            Constructions::class,
            Languages::class,
            Admin::class,
        ];

        foreach ($permissionEnums as $permissionEnum) {
            foreach ($permissionEnum::cases() as $permission) {
                Permission::create(['name' => $permission->code()]);
            }
        }

        // create roles and assign created permissions
        Role::create(['name' => RolesEnum::MODERATOR])
            ->givePermissionTo([
                (Constructions::VIEW)->code(),
                (Languages::VIEW)->code(),
                (Admin::VIEW)->code(),
            ]);

        Role::create(['name' => RolesEnum::ADMIN])
            ->givePermissionTo(Permission::all());
    }
}
