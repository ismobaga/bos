<?php

namespace Database\Seeders;

use App\Modules\Licensing\Domain\Models\ProductModule;
use Illuminate\Database\Seeder;

class ProductModuleSeeder extends Seeder
{
    private const MODULES = [
        ['key' => 'crm', 'name' => 'CRM', 'description' => 'Customer relationship management'],
        ['key' => 'invoice', 'name' => 'Invoice', 'description' => 'Invoicing and payment tracking'],
        ['key' => 'lms', 'name' => 'LMS', 'description' => 'Learning management system'],
        ['key' => 'helpdesk', 'name' => 'Helpdesk', 'description' => 'Support ticketing system'],
        ['key' => 'hosting', 'name' => 'Hosting', 'description' => 'Hosting and deployment management'],
        ['key' => 'automation', 'name' => 'Automation', 'description' => 'Cross-app workflow automation'],
        ['key' => 'docs', 'name' => 'Docs', 'description' => 'Documentation management'],
        ['key' => 'fleet', 'name' => 'Fleet', 'description' => 'Fleet management (Go service)'],
        ['key' => 'cmail', 'name' => 'CMail', 'description' => 'Email service (Go service)'],
    ];

    public function run(): void
    {
        foreach (self::MODULES as $module) {
            ProductModule::updateOrCreate(
                ['key' => $module['key']],
                array_merge($module, ['is_active' => true])
            );
        }
    }
}
