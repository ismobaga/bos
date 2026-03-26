# CROMMIX BOS — Business Operating System

> **Laravel-First Multi-Tenant SaaS Platform**

CROMMIX BOS is a centralized, modular business operating system built on Laravel. It enables multiple business applications to run under one platform with shared identity, tenancy, licensing, billing, and audit infrastructure.

## Platform Overview

```
app.crommixmali.com / api.crommixmali.com
         │
         ▼
  Laravel Platform Core
         │
  ┌──────┴──────┐
  │  Core Modules│
  ├─────────────┤
  │ Identity     │  ← Authentik OIDC bridge, user provisioning
  │ Tenancy      │  ← Multi-tenant management, domains, branches
  │ AccessControl│  ← Roles, permissions, memberships
  │ Licensing    │  ← Plans, entitlements, usage metering
  │ Billing      │  ← Subscriptions, invoices, payments to CROMMIX
  │ Notifications│  ← Email, WhatsApp, SMS, in-app
  │ Files        │  ← MinIO object storage metadata
  │ Audit        │  ← Immutable audit logs
  │ AppCatalog   │  ← Per-tenant app visibility
  └─────────────┘
         │
  ┌──────┴──────┐
  │  App Modules │
  ├─────────────┤
  │ CRM          │  ← Customers, contacts, notes, tags
  │ Invoice      │  ← Invoicing, payments, reminders
  │ LMS          │  ← Courses, lessons, enrollments
  │ Helpdesk     │  ← Support tickets, comments, SLA
  │ Hosting      │  ← Deployment projects, domains
  │ Automation   │  ← Cross-app workflow rules
  └─────────────┘
```

## Architecture Principles

- **Laravel** is the platform brain
- **Filament** is the admin/backoffice framework  
- **PostgreSQL** is the primary database (shared schema with `tenant_id`)
- **Redis** for queues and cache
- **MinIO** for object storage
- **Authentik** as the identity provider (OIDC/SAML)
- **Go services** for heavy workloads (CMail, Fleet) — future

## Module Structure

Each module follows the same internal layout:

```
app/Modules/{Module}/
├── Domain/
│   ├── Models/       ← Eloquent models
│   ├── Enums/        ← PHP 8.1+ enums
│   ├── Events/       ← Domain events
│   ├── Exceptions/   ← Domain exceptions
│   └── ValueObjects/ ← Immutable value objects
├── Application/
│   ├── Actions/      ← Single-responsibility use cases
│   ├── DTOs/         ← Data transfer objects
│   ├── Services/     ← Application services
│   ├── Queries/      ← Read-side query objects
│   └── Policies/     ← Authorization policies
├── Infrastructure/
│   ├── Repositories/ ← Repository implementations
│   ├── Jobs/         ← Queue jobs
│   ├── Listeners/    ← Event listeners
│   └── Providers/    ← Module service providers
├── Http/
│   ├── Controllers/  ← HTTP controllers
│   ├── Requests/     ← Form requests (validation)
│   ├── Resources/    ← API resource transformers
│   └── Middleware/   ← HTTP middleware
└── Filament/
    ├── Resources/    ← Filament admin resources
    ├── Pages/        ← Filament pages
    └── Widgets/      ← Filament dashboard widgets
```

## Request Pipeline

Every request passes through:

```
authenticate user
  → resolve tenant
    → ensure tenant is active
      → ensure module is enabled
        → ensure feature is enabled (if applicable)
          → check permission
            → check usage limits (if applicable)
              → execute use case
                → write audit log
                  → publish domain events
                    → update usage counters
```

## API Routes

```
/api/v1/platform/me           GET  - Current user
/api/v1/platform/tenants      GET  - User's tenants
/api/v1/platform/apps         GET  - App catalog for tenant
/api/v1/platform/license      GET  - Tenant license info
/api/v1/platform/usage        GET  - Usage statistics

/api/v1/crm/customers         GET, POST
/api/v1/crm/customers/{id}    GET, PUT, DELETE

/api/v1/invoice/invoices      GET, POST
/api/v1/invoice/invoices/{id} GET, PUT, DELETE
/api/v1/invoice/invoices/{id}/send     POST
/api/v1/invoice/invoices/{id}/payments POST

/api/v1/lms/courses           GET, POST
/api/v1/lms/courses/{id}      GET, PUT, DELETE
/api/v1/lms/courses/{id}/enrollments POST

/api/v1/helpdesk/tickets      GET, POST
/api/v1/helpdesk/tickets/{id} GET, PUT
```

## Middleware Stack

| Alias           | Class                  | Purpose                          |
|-----------------|------------------------|----------------------------------|
| `tenant.resolve`| `ResolveTenant`        | Resolve tenant from request      |
| `tenant.active` | `EnsureTenantActive`   | Block suspended tenants          |
| `module`        | `EnsureModuleEnabled`  | Check module entitlement         |
| `usage`         | `CheckUsage`           | Enforce usage limits             |
| `platform.admin`| `EnsurePlatformAdmin`  | Restrict to platform staff       |

## Service Contracts

```php
interface LicensingManagerInterface {
    public function moduleEnabled(int $tenantId, string $moduleKey): bool;
    public function featureEnabled(int $tenantId, string $featureKey): bool;
    public function getLimit(int $tenantId, string $key): int|float|null;
    public function canConsume(int $tenantId, string $key, int $amount = 1): bool;
    public function consume(int $tenantId, string $key, int $amount = 1): void;
}

interface TenantResolverInterface {
    public function resolve(): TenantContext;
}

interface NotificationDispatcherInterface {
    public function send(NotificationMessageData $data): void;
}

interface AuditLoggerInterface {
    public function log(AuditEntryData $data): void;
}

interface FileStorageInterface {
    public function put(FileUploadData $data): FileAsset;
    public function signedUrl(FileAsset $file, int $ttlSeconds = 300): string;
}
```

## Key Events

| Event | Listeners |
|-------|-----------|
| `InvoiceCreated` | `IncrementInvoiceUsage` |
| `InvoiceOverdue` | `ScheduleOverdueReminder` |
| `LearnerEnrolled` | `SendEnrollmentWelcomeEmail` |
| `SubscriptionChanged` | `RecomputeTenantEntitlements` |
| `TenantCreated` | _(audit)_ |

## App Catalog Response Example

```json
{
  "user": { "name": "Ismail Bagayoko", "email": "ismail@example.com" },
  "tenant": { "name": "CROMMIX Demo", "status": "active" },
  "apps": [
    { "key": "crm", "enabled": true, "label": "CRM", "url": "/crm" },
    { "key": "invoice", "enabled": true, "label": "Invoice", "url": "/invoice" },
    { "key": "lms", "enabled": false, "label": "LMS", "url": "/lms", "reason": "not_in_plan" }
  ]
}
```

## Scheduled Tasks

| Command | Schedule | Purpose |
|---------|----------|---------|
| `crommix:reset-monthly-counters` | Monthly | Reset usage counters |
| `crommix:detect-overdue-invoices` | Daily 08:00 | Detect & notify overdue invoices |
| `crommix:expire-trials` | Daily 06:00 | Expire ended trial tenants |
| `crommix:suspend-unpaid-subscriptions` | Daily 07:00 | Suspend after grace period |
| `crommix:retry-failed-webhooks` | Every 30 min | Retry failed webhook deliveries |
| `crommix:clean-temp-files` | Weekly | Clean up temp files |

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL 15+
- Redis 7+
- MinIO (or S3-compatible storage)

### Quick Start with Docker

```bash
cp .env.example .env
# Edit .env with your configuration
docker-compose up -d
docker-compose exec platform-web php artisan key:generate
docker-compose exec platform-web php artisan migrate
docker-compose exec platform-web php artisan db:seed
```

### Local Development

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed

# Start queue worker
php artisan queue:work

# Run tests
php artisan test
```

## Database Migrations

All migrations are organized by module:

| Migration | Tables |
|-----------|--------|
| `000000_create_users_table` | `users`, `sessions` |
| `000010_create_identity_tables` | `identity_accounts`, `user_sessions`, `login_audits` |
| `000020_create_tenancy_tables` | `tenants`, `tenant_domains`, `tenant_branches`, `tenant_settings`, `tenant_brandings` |
| `000030_create_access_control_tables` | `roles`, `permissions`, `role_permissions`, `memberships`, `membership_roles` |
| `000040_create_licensing_tables` | `product_modules`, `plans`, `plan_modules`, `subscriptions`, `tenant_entitlements`, `usage_counters` |
| `000050_create_billing_tables` | `billing_customers`, `billing_subscriptions`, `billing_invoices`, `billing_payments` |
| `000060_create_notifications_tables` | `notification_templates`, `notification_messages` |
| `000070_create_files_tables` | `file_assets`, `file_attachments` |
| `000080_create_audit_tables` | `audit_logs` |
| `000090_create_integrations_tables` | `integration_connections`, `webhook_endpoints`, `webhook_deliveries` |
| `000100_create_crm_tables` | `crm_customers`, `crm_contacts`, `crm_notes`, `crm_tags` |
| `000110_create_invoice_tables` | `invoice_invoices`, `invoice_items`, `invoice_payments`, `invoice_reminders` |
| `000120_create_lms_tables` | `lms_courses`, `lms_lessons`, `lms_enrollments`, `lms_assignments`, `lms_submissions` |
| `000130_create_helpdesk_tables` | `helpdesk_tickets`, `helpdesk_comments`, `helpdesk_status_histories` |
| `000140_create_hosting_tables` | `hosting_projects`, `hosting_deployments`, `hosting_domains` |
| `000150_create_automation_tables` | `automation_rules`, `automation_actions`, `automation_runs` |

## Security

- All tenant-scoped queries enforce `tenant_id` scoping
- Signed URLs for private file access (MinIO)
- Permission checks in policies and middleware
- Audit logging for all sensitive actions
- Webhook signature validation (HMAC-SHA256)
- Secrets via environment variables only
- MFA enforced through Authentik for admin users

## Implementation Phases

- **Phase 1 (MVP)**: Identity, Tenancy, AccessControl, Licensing, Billing, Audit, AppCatalog
- **Phase 2**: CRM, Invoice, Notifications, Files
- **Phase 3**: LMS, Helpdesk, Integrations, Automation
- **Phase 4**: CMail Go service, Fleet Go service, advanced analytics
