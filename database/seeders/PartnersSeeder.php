<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnersSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            // Local partners
            ['name' => 'Hofercomputing GmbH', 'type' => 'local'],
            ['name' => 'Murer EDV', 'type' => 'local'],
            ['name' => 'Utiger Elektro GmbH', 'type' => 'local'],
            ['name' => 'YOUHEY AG', 'type' => 'local'],

            // Technology partners
            ['name' => 'Acer', 'type' => 'technology'],
            ['name' => 'Adaptec', 'type' => 'technology'],
            ['name' => 'beroNet', 'type' => 'technology'],
            ['name' => 'Cisco', 'type' => 'technology', 'description' => 'Select Partner'],
            ['name' => 'Digium/Asterisk', 'type' => 'technology'],
            ['name' => 'F-Secure', 'type' => 'technology'],
            ['name' => 'HP', 'type' => 'technology'],
            ['name' => 'KENTIX', 'type' => 'technology', 'description' => 'Silver Partner'],
            ['name' => 'LANCOM', 'type' => 'technology'],
            ['name' => 'Langmeier Backup', 'type' => 'technology'],
            ['name' => 'Mailstore', 'type' => 'technology', 'description' => 'Certified Partner'],
            ['name' => 'Microsoft', 'type' => 'technology'],
            ['name' => 'M-SOFT', 'type' => 'technology'],
            ['name' => 'Netgear', 'type' => 'technology'],
            ['name' => 'OKI', 'type' => 'technology'],
            ['name' => 'Sage', 'type' => 'technology', 'description' => 'Key Partner'],
            ['name' => 'Samsung', 'type' => 'technology'],
            ['name' => 'Seagate', 'type' => 'technology'],
            ['name' => 'snom', 'type' => 'technology'],
            ['name' => 'Swisscom', 'type' => 'technology'],
            ['name' => 'Symantec', 'type' => 'technology'],
            ['name' => 'Veeam', 'type' => 'technology', 'description' => 'Pro Partner'],
            ['name' => 'Watchguard', 'type' => 'technology', 'description' => 'ONE Gold Partner'],
            ['name' => 'Western Digital', 'type' => 'technology'],
            ['name' => 'Xorcom', 'type' => 'technology'],
        ];

        foreach ($partners as $index => $partner) {
            Partner::updateOrCreate(
                ['name' => $partner['name']],
                [
                    'type' => $partner['type'],
                    'description' => $partner['description'] ?? null,
                    'logo' => null,
                    'website' => null,
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
