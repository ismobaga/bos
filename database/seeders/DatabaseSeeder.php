<?php

namespace Database\Seeders;

use App\Modules\AccessControl\Domain\Models\Permission;
use App\Modules\AccessControl\Domain\Models\Role;
use App\Modules\Licensing\Domain\Models\Plan;
use App\Modules\Licensing\Domain\Models\ProductModule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProductModuleSeeder::class,
            PlanSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
