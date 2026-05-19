<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permissions ---
        $permissions = [
            'manage_users', 'suspend_users', 'ban_users', 'view_users',
            'manage_providers', 'verify_providers', 'view_providers',
            'manage_requests', 'assign_requests', 'resolve_disputes', 'view_requests',
            'manage_payments', 'refund_payments', 'view_payments',
            'manage_reviews', 'remove_reviews',
            'manage_categories', 'manage_services',
            'view_analytics', 'export_reports',
            'manage_roles', 'manage_permissions',
            'manage_settings', 'toggle_maintenance', 'view_audit_logs',
            'manage_announcements', 'manage_api_keys',
            'manage_tickets',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // --- Roles ---
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $support    = Role::firstOrCreate(['name' => 'support_staff', 'guard_name' => 'web']);
        $finance    = Role::firstOrCreate(['name' => 'finance_officer', 'guard_name' => 'web']);
        $moderator  = Role::firstOrCreate(['name' => 'content_moderator', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'view_users', 'manage_users', 'suspend_users',
            'view_providers', 'manage_providers', 'verify_providers',
            'view_requests', 'manage_requests', 'assign_requests', 'resolve_disputes',
            'view_payments', 'manage_payments', 'refund_payments',
            'manage_reviews', 'remove_reviews',
            'manage_categories', 'manage_services',
            'view_analytics', 'export_reports',
            'manage_announcements', 'manage_tickets',
        ]);

        $support->syncPermissions([
            'view_users', 'view_requests', 'manage_tickets', 'view_providers',
        ]);

        $finance->syncPermissions([
            'view_payments', 'manage_payments', 'refund_payments', 'view_analytics', 'export_reports',
        ]);

        $moderator->syncPermissions([
            'manage_reviews', 'remove_reviews', 'manage_announcements',
        ]);

        // --- Users ---
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@campushub.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->syncRoles(['super_admin']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@campushub.local'],
            [
                'name' => 'Campus Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['admin']);

        $student1 = User::firstOrCreate(
            ['email' => 'john.student@campushub.local'],
            [
                'name' => 'John Mwangi',
                'password' => Hash::make('password'),
                'phone' => '0712345678',
                'student_id' => 'STU-001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $student1->syncRoles(['student']);

        $student2 = User::firstOrCreate(
            ['email' => 'mary.student@campushub.local'],
            [
                'name' => 'Mary Wanjiku',
                'password' => Hash::make('password'),
                'phone' => '0723456789',
                'student_id' => 'STU-002',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $student2->syncRoles(['student']);

        $providerUser = User::firstOrCreate(
            ['email' => 'provider@campushub.local'],
            [
                'name' => 'James Kamau',
                'password' => Hash::make('password'),
                'phone' => '0734567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $providerUser->syncRoles(['provider']);

        $provider = Provider::firstOrCreate(
            ['user_id' => $providerUser->id],
            [
                'business_name' => 'Kamau Tech Solutions',
                'bio' => 'Experienced technician offering laptop repairs, printing and delivery on campus.',
                'location' => 'Block C, Room 12',
                'whatsapp' => '0734567890',
                'status' => 'approved',
                'verified_at' => now(),
                'rating_avg' => 4.5,
                'completed_jobs' => 12,
            ]
        );

        // --- Service Categories ---
        $categories = [
            ['name' => 'Printing & Photocopying', 'slug' => 'printing', 'icon' => 'heroicon-o-printer', 'color' => '#6366f1'],
            ['name' => 'Laptop & Phone Repair', 'slug' => 'repair', 'icon' => 'heroicon-o-wrench-screwdriver', 'color' => '#f59e0b'],
            ['name' => 'Delivery & Transport', 'slug' => 'delivery', 'icon' => 'heroicon-o-truck', 'color' => '#10b981'],
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => 'heroicon-o-camera', 'color' => '#ec4899'],
            ['name' => 'Tutoring & Academic Help', 'slug' => 'tutoring', 'icon' => 'heroicon-o-academic-cap', 'color' => '#3b82f6'],
            ['name' => 'Laundry', 'slug' => 'laundry', 'icon' => 'heroicon-o-sparkles', 'color' => '#14b8a6'],
        ];

        foreach ($categories as $i => $cat) {
            ServiceCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['sort_order' => $i + 1, 'is_active' => true])
            );
        }

        // --- Services ---
        $printCat    = ServiceCategory::where('slug', 'printing')->first();
        $repairCat   = ServiceCategory::where('slug', 'repair')->first();
        $deliveryCat = ServiceCategory::where('slug', 'delivery')->first();

        $services = [
            ['category_id' => $printCat->id, 'name' => 'Black & White Printing', 'slug' => 'bw-printing', 'base_price' => 5],
            ['category_id' => $printCat->id, 'name' => 'Colour Printing', 'slug' => 'colour-printing', 'base_price' => 15],
            ['category_id' => $printCat->id, 'name' => 'Binding & Lamination', 'slug' => 'binding', 'base_price' => 50],
            ['category_id' => $repairCat->id, 'name' => 'Laptop Screen Repair', 'slug' => 'laptop-screen', 'base_price' => 3000],
            ['category_id' => $repairCat->id, 'name' => 'Software Installation', 'slug' => 'software-install', 'base_price' => 200],
            ['category_id' => $repairCat->id, 'name' => 'Phone Charging Port Fix', 'slug' => 'phone-port', 'base_price' => 500],
            ['category_id' => $deliveryCat->id, 'name' => 'On-Campus Delivery', 'slug' => 'campus-delivery', 'base_price' => 50],
        ];

        foreach ($services as $svc) {
            Service::firstOrCreate(['slug' => $svc['slug']], array_merge($svc, ['is_active' => true]));
        }

        $provider->services()->syncWithoutDetaching(
            Service::whereIn('slug', ['bw-printing', 'colour-printing', 'software-install'])->pluck('id')
        );

        // --- Demo Service Requests ---
        ServiceRequest::firstOrCreate(
            ['request_number' => 'REQ-DEMO001'],
            [
                'student_id' => $student1->id,
                'provider_id' => $provider->id,
                'service_id' => Service::where('slug', 'bw-printing')->first()->id,
                'title' => 'Print 50 pages assignment',
                'description' => 'Need 50 pages printed double-sided for CAT submission tomorrow.',
                'urgency' => 'high',
                'status' => 'completed',
                'quoted_price' => 250,
                'final_price' => 250,
                'accepted_at' => now()->subDays(2),
                'completed_at' => now()->subDay(),
            ]
        );

        ServiceRequest::firstOrCreate(
            ['request_number' => 'REQ-DEMO002'],
            [
                'student_id' => $student2->id,
                'provider_id' => null,
                'service_id' => Service::where('slug', 'software-install')->first()->id,
                'title' => 'Install Microsoft Office',
                'description' => 'Need Microsoft Office 2021 installed on my laptop.',
                'urgency' => 'medium',
                'status' => 'pending',
            ]
        );

        // --- Demo Ticket ---
        Ticket::firstOrCreate(
            ['ticket_number' => 'TKT-DEMO001'],
            [
                'user_id' => $student1->id,
                'subject' => 'Provider did not complete my request',
                'description' => 'My printing request was marked complete but I never received the prints.',
                'status' => 'open',
                'priority' => 'high',
            ]
        );

        // --- Announcement ---
        Announcement::firstOrCreate(
            ['title' => 'Welcome to CampusHub!'],
            [
                'created_by' => $adminUser->id,
                'body' => 'CampusHub is now live! Browse services, request help, and track your requests in real time.',
                'type' => 'info',
                'audience' => 'all',
                'send_email' => false,
                'published_at' => now(),
            ]
        );

        // --- System Settings ---
        $settings = [
            ['key' => 'site_name',          'value' => 'CampusHub',                    'group' => 'branding'],
            ['key' => 'site_tagline',        'value' => 'Smart Campus Service Platform', 'group' => 'branding'],
            ['key' => 'currency',            'value' => 'KES',                           'group' => 'general'],
            ['key' => 'timezone',            'value' => 'Africa/Nairobi',               'group' => 'general'],
            ['key' => 'maintenance_mode',    'value' => '0',                             'group' => 'system'],
            ['key' => 'allow_registrations', 'value' => '1',                             'group' => 'system'],
            ['key' => 'max_file_upload_mb',  'value' => '10',                            'group' => 'system'],
            ['key' => 'support_email',       'value' => 'support@campushub.local',       'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('');
        $this->command->info('CampusHub seeded successfully.');
        $this->command->info('  Super Admin : superadmin@campushub.local / password');
        $this->command->info('  Admin       : admin@campushub.local / password');
        $this->command->info('  Student     : john.student@campushub.local / password');
        $this->command->info('  Provider    : provider@campushub.local / password');
    }
}
