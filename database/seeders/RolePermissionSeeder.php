<?php

namespace Database\Seeders;

use App\Modules\AccessControl\Domain\Models\Permission;
use App\Modules\AccessControl\Domain\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        // Tenant management
        ['key' => 'tenant.users.invite', 'name' => 'Invite Users', 'module' => 'tenant'],
        ['key' => 'tenant.users.manage', 'name' => 'Manage Users', 'module' => 'tenant'],
        ['key' => 'tenant.roles.manage', 'name' => 'Manage Roles', 'module' => 'tenant'],
        ['key' => 'tenant.settings.manage', 'name' => 'Manage Settings', 'module' => 'tenant'],

        // Billing
        ['key' => 'billing.manage', 'name' => 'Manage Billing', 'module' => 'billing'],
        ['key' => 'license.manage', 'name' => 'Manage License', 'module' => 'billing'],

        // CRM
        ['key' => 'crm.customer.view', 'name' => 'View Customers', 'module' => 'crm'],
        ['key' => 'crm.customer.create', 'name' => 'Create Customers', 'module' => 'crm'],
        ['key' => 'crm.customer.edit', 'name' => 'Edit Customers', 'module' => 'crm'],
        ['key' => 'crm.customer.delete', 'name' => 'Delete Customers', 'module' => 'crm'],

        // Invoice
        ['key' => 'invoice.view', 'name' => 'View Invoices', 'module' => 'invoice'],
        ['key' => 'invoice.create', 'name' => 'Create Invoices', 'module' => 'invoice'],
        ['key' => 'invoice.edit', 'name' => 'Edit Invoices', 'module' => 'invoice'],
        ['key' => 'invoice.delete', 'name' => 'Delete Invoices', 'module' => 'invoice'],
        ['key' => 'invoice.send', 'name' => 'Send Invoices', 'module' => 'invoice'],
        ['key' => 'invoice.payment.create', 'name' => 'Register Payments', 'module' => 'invoice'],

        // LMS
        ['key' => 'lms.course.view', 'name' => 'View Courses', 'module' => 'lms'],
        ['key' => 'lms.course.manage', 'name' => 'Manage Courses', 'module' => 'lms'],
        ['key' => 'lms.enrollment.create', 'name' => 'Enroll Learners', 'module' => 'lms'],

        // Helpdesk
        ['key' => 'helpdesk.ticket.view', 'name' => 'View Tickets', 'module' => 'helpdesk'],
        ['key' => 'helpdesk.ticket.create', 'name' => 'Create Tickets', 'module' => 'helpdesk'],
        ['key' => 'helpdesk.ticket.edit', 'name' => 'Edit Tickets', 'module' => 'helpdesk'],

        // Hosting
        ['key' => 'hosting.project.view', 'name' => 'View Projects', 'module' => 'hosting'],
        ['key' => 'hosting.project.manage', 'name' => 'Manage Projects', 'module' => 'hosting'],

        // Automation
        ['key' => 'automation.rule.manage', 'name' => 'Manage Automation Rules', 'module' => 'automation'],

        // Misc
        ['key' => 'docs.view', 'name' => 'View Docs', 'module' => 'docs'],
        ['key' => 'fleet.view', 'name' => 'View Fleet', 'module' => 'fleet'],
        ['key' => 'cmail.view', 'name' => 'View CMail', 'module' => 'cmail'],
    ];

    private const ROLES = [
        [
            'key' => 'tenant.admin',
            'name' => 'Tenant Admin',
            'scope' => 'tenant',
            'is_system' => true,
            'permissions' => '*', // all permissions
        ],
        [
            'key' => 'tenant.member',
            'name' => 'Member',
            'scope' => 'tenant',
            'is_system' => true,
            'permissions' => [
                'crm.customer.view',
                'invoice.view',
                'lms.course.view',
                'helpdesk.ticket.view',
                'helpdesk.ticket.create',
                'docs.view',
            ],
        ],
        [
            'key' => 'invoice.manager',
            'name' => 'Invoice Manager',
            'scope' => 'tenant',
            'is_system' => true,
            'permissions' => [
                'invoice.view', 'invoice.create', 'invoice.edit', 'invoice.delete',
                'invoice.send', 'invoice.payment.create',
                'crm.customer.view',
            ],
        ],
        [
            'key' => 'crm.manager',
            'name' => 'CRM Manager',
            'scope' => 'tenant',
            'is_system' => true,
            'permissions' => [
                'crm.customer.view', 'crm.customer.create', 'crm.customer.edit', 'crm.customer.delete',
            ],
        ],
    ];

    public function run(): void
    {
        // Create permissions
        foreach (self::PERMISSIONS as $permData) {
            Permission::updateOrCreate(['key' => $permData['key']], $permData);
        }

        $allPermissions = Permission::all();

        // Create roles and assign permissions
        foreach (self::ROLES as $roleData) {
            $permissionKeys = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(['key' => $roleData['key']], $roleData);

            if ($permissionKeys === '*') {
                $role->permissions()->sync($allPermissions->pluck('id'));
            } else {
                $ids = $allPermissions->whereIn('key', $permissionKeys)->pluck('id');
                $role->permissions()->sync($ids);
            }
        }
    }
}
