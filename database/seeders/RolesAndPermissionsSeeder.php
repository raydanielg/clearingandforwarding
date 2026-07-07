<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──
        $permissions = [
            // Users & roles
            'users.manage', 'roles.manage', 'settings.manage',
            // Clients
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            // Files
            'files.view', 'files.create', 'files.edit', 'files.assign', 'files.close', 'files.delete',
            // Declarations
            'declarations.view', 'declarations.create', 'declarations.edit', 'declarations.delete',
            // HS Codes
            'hscodes.view', 'hscodes.create', 'hscodes.edit', 'hscodes.delete',
            // Customs queries
            'queries.view', 'queries.respond',
            // Documents
            'documents.view', 'documents.upload', 'documents.verify', 'documents.delete',
            // Milestones
            'milestones.view', 'milestones.update',
            // Transport
            'transport.view', 'transport.create', 'transport.edit', 'transport.delete',
            // Finance
            'finance.view',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'receipts.view', 'receipts.create',
            'disbursements.view', 'disbursements.create', 'disbursements.edit', 'disbursements.approve',
            'pettycash.view', 'pettycash.create', 'pettycash.edit',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'charges.view', 'charges.create', 'charges.edit', 'charges.delete',
            // Partners
            'partners.view', 'partners.create', 'partners.edit', 'partners.delete',
            // Insurance
            'insurance.view', 'insurance.create', 'insurance.edit', 'insurance.delete',
            // Reports
            'reports.view', 'reports.finance', 'reports.operations', 'reports.insurance',
            // Portal
            'portal.view-own',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── Roles with permissions ──
        $roles = [
            'Super Admin' => $permissions, // all
            'Admin' => array_diff($permissions, ['users.manage', 'roles.manage']), // includes insurance + reports.insurance
            'Operations Manager' => [
                'files.view', 'files.create', 'files.edit', 'files.assign', 'files.close',
                'declarations.view', 'declarations.create', 'declarations.edit',
                'documents.view', 'documents.upload', 'documents.verify',
                'milestones.view', 'milestones.update',
                'transport.view', 'transport.create', 'transport.edit',
                'insurance.view', 'insurance.create', 'insurance.edit',
                'clients.view', 'clients.create', 'clients.edit',
                'partners.view',
                'reports.view', 'reports.operations',
                'queries.view', 'queries.respond',
                'hscodes.view',
            ],
            'Declaration Officer' => [
                'files.view',
                'declarations.view', 'declarations.create', 'declarations.edit',
                'hscodes.view', 'hscodes.create', 'hscodes.edit',
                'documents.view', 'documents.upload',
                'milestones.view', 'milestones.update',
                'queries.view', 'queries.respond',
            ],
            'Documentation Officer' => [
                'files.view', 'files.create',
                'documents.view', 'documents.upload', 'documents.verify', 'documents.delete',
                'clients.view', 'clients.create', 'clients.edit',
                'milestones.view',
            ],
            'Clearing Officer' => [
                'files.view',
                'documents.view', 'documents.upload',
                'milestones.view', 'milestones.update',
            ],
            'Transport Officer' => [
                'files.view',
                'transport.view', 'transport.create', 'transport.edit',
                'documents.view', 'documents.upload',
                'milestones.view', 'milestones.update',
            ],
            'Accountant' => [
                'files.view',
                'finance.view',
                'invoices.view', 'invoices.create', 'invoices.edit',
                'receipts.view', 'receipts.create',
                'disbursements.view', 'disbursements.create', 'disbursements.edit',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'charges.view', 'charges.create', 'charges.edit',
                'clients.view',
                'insurance.view',
                'reports.view', 'reports.finance', 'reports.insurance',
            ],
            'Cashier' => [
                'finance.view',
                'receipts.view', 'receipts.create',
                'disbursements.view', 'disbursements.create',
                'pettycash.view', 'pettycash.create', 'pettycash.edit',
            ],
            'Insurance Officer' => [
                'insurance.view', 'insurance.create', 'insurance.edit', 'insurance.delete',
                'files.view',
                'clients.view', 'clients.create', 'clients.edit',
                'documents.view', 'documents.upload',
                'transport.view',
                'partners.view',
                'reports.view', 'reports.insurance',
            ],
            'Branch Manager' => [
                'files.view', 'files.create', 'files.edit', 'files.assign', 'files.close',
                'declarations.view', 'declarations.create', 'declarations.edit',
                'documents.view', 'documents.upload', 'documents.verify',
                'milestones.view', 'milestones.update',
                'transport.view', 'transport.create', 'transport.edit',
                'insurance.view',
                'clients.view', 'clients.create', 'clients.edit',
                'partners.view',
                'queries.view', 'queries.respond',
                'hscodes.view',
                'reports.view', 'reports.operations',
            ],
            'Client' => ['portal.view-own'],
        ];

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePerms);
        }

        // ── Create Super Admin user ──
        $admin = User::firstOrCreate(
            ['email' => 'admin@caf.co.tz'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('Super Admin');
    }
}
