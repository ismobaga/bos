<?php

namespace App\Modules\AppCatalog\Application\Services;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Tenancy\Domain\Models\Tenant;

class AppCatalogService
{
    /**
     * All available platform apps.
     */
    private const APPS = [
        ['key' => 'crm', 'label' => 'CRM', 'url' => '/crm', 'module' => 'crm', 'permission' => 'crm.customer.view'],
        ['key' => 'invoice', 'label' => 'Invoice', 'url' => '/invoice', 'module' => 'invoice', 'permission' => 'invoice.view'],
        ['key' => 'lms', 'label' => 'LMS', 'url' => '/lms', 'module' => 'lms', 'permission' => 'lms.course.view'],
        ['key' => 'helpdesk', 'label' => 'Helpdesk', 'url' => '/helpdesk', 'module' => 'helpdesk', 'permission' => 'helpdesk.ticket.view'],
        ['key' => 'hosting', 'label' => 'Hosting', 'url' => '/hosting', 'module' => 'hosting', 'permission' => 'hosting.project.view'],
        ['key' => 'automation', 'label' => 'Automation', 'url' => '/automation', 'module' => 'automation', 'permission' => 'automation.rule.manage'],
        ['key' => 'docs', 'label' => 'Docs', 'url' => '/docs', 'module' => 'docs', 'permission' => 'docs.view'],
        ['key' => 'fleet', 'label' => 'Fleet', 'url' => '/fleet', 'module' => 'fleet', 'permission' => 'fleet.view'],
        ['key' => 'cmail', 'label' => 'CMail', 'url' => '/cmail', 'module' => 'cmail', 'permission' => 'cmail.view'],
    ];

    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function getAppsForTenant(Tenant $tenant, ?array $userPermissions = []): array
    {
        $apps = [];

        foreach (self::APPS as $app) {
            $moduleEnabled = $this->licensing->moduleEnabled($tenant->id, $app['module']);

            $entry = [
                'key' => $app['key'],
                'label' => $app['label'],
                'url' => $app['url'],
                'enabled' => $moduleEnabled,
            ];

            if (! $moduleEnabled) {
                $entry['reason'] = 'not_in_plan';
            }

            $apps[] = $entry;
        }

        return $apps;
    }
}
