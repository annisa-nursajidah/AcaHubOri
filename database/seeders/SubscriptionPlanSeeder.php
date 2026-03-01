<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Seed default subscription plans.
     */
    public function run(): void
    {
        $plans = [
            [
                'name'              => 'Starter',
                'description'       => 'Cocok untuk sekolah kecil dengan kebutuhan dasar',
                'price_per_account' => 15000,
                'min_accounts'      => 10,
                'max_accounts'      => 50,
                'duration_days'     => 365,
                'features'          => [
                    'Manajemen siswa & guru',
                    'Input nilai & rapor',
                    'Absensi online',
                    'Pengumuman sekolah',
                    'Email support',
                ],
                'sort_order' => 0,
                'is_active'  => true,
            ],
            [
                'name'              => 'Professional',
                'description'       => 'Untuk sekolah menengah yang ingin fitur lengkap',
                'price_per_account' => 10000,
                'min_accounts'      => 50,
                'max_accounts'      => 200,
                'duration_days'     => 365,
                'features'          => [
                    'Semua fitur Starter',
                    'Pesan / chat antar user',
                    'Kalender & event',
                    'Manajemen kelas & pendaftaran',
                    'Export rapor PDF',
                    'Priority email support',
                ],
                'sort_order' => 1,
                'is_active'  => true,
            ],
            [
                'name'              => 'Enterprise',
                'description'       => 'Solusi lengkap untuk sekolah besar & yayasan',
                'price_per_account' => 7500,
                'min_accounts'      => 200,
                'max_accounts'      => null,
                'duration_days'     => 365,
                'features'          => [
                    'Semua fitur Professional',
                    'Multi tahun ajaran',
                    'Notifikasi real-time',
                    'API access',
                    'Dedicated support',
                    'Custom branding',
                ],
                'sort_order' => 2,
                'is_active'  => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
