<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'company_name', 'value' => 'HILOTEC Engineering + Consulting AG'],
            ['group' => 'general', 'key' => 'company_slogan', 'value' => 'Sichere IT, die einfach funktioniert.'],
            ['group' => 'general', 'key' => 'company_subtitle', 'value' => 'Alles was Ihr KMU im Bereich der Informationstechnologie braucht.'],
            ['group' => 'general', 'key' => 'founded_year', 'value' => '1995'],
            ['group' => 'general', 'key' => 'about_short', 'value' => 'Die Firma HILOTEC Engineering + Consulting AG ist ein Ingenieurunternehmen und besteht seit 1995. Wir sind primär im Bereich der IT-Komplettbetreuung für KMUs tätig.'],

            // Contact
            ['group' => 'contact', 'key' => 'address_line1', 'value' => 'Untere Hohle Gasse 5'],
            ['group' => 'contact', 'key' => 'address_zip_city', 'value' => 'CH-3550 Langnau i.E.'],
            ['group' => 'contact', 'key' => 'address_country', 'value' => 'Schweiz'],
            ['group' => 'contact', 'key' => 'phone_support_infra', 'value' => '+41 34 408 01 00'],
            ['group' => 'contact', 'key' => 'phone_label_infra', 'value' => 'Support IT-Infrastruktur'],
            ['group' => 'contact', 'key' => 'phone_support_software', 'value' => '+41 34 408 01 01'],
            ['group' => 'contact', 'key' => 'phone_label_software', 'value' => 'Support Chronikos, M-Soft und Sage50'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@hilotec.com'],
            ['group' => 'contact', 'key' => 'website', 'value' => 'https://www.hilotec.com'],
            ['group' => 'contact', 'key' => 'business_hours', 'value' => 'Mo-Fr, 08:00-12:00 und 13:30-18:00'],

            // Footer
            ['group' => 'footer', 'key' => 'cta_heading', 'value' => 'Sie haben ein Problem oder eine Frage? Wir bieten Ihnen die Lösung, die Sie suchen.'],
            ['group' => 'footer', 'key' => 'cta_button_text', 'value' => 'Kontakt aufnehmen'],
            ['group' => 'footer', 'key' => 'cta_button_url', 'value' => '/kontakt'],
            ['group' => 'footer', 'key' => 'copyright_text', 'value' => '© Copyright 2025, HILOTEC Engineering + Consulting AG'],
            ['group' => 'footer', 'key' => 'teamviewer_text', 'value' => 'Klicken Sie hier um den TeamViewer Tool zu starten'],
            ['group' => 'footer', 'key' => 'teamviewer_url', 'value' => 'https://get.teamviewer.com/hilotec'],

            // Social
            ['group' => 'social', 'key' => 'linkedin', 'value' => 'https://www.linkedin.com/company/hilotec-engineering-consulting-ag'],
            ['group' => 'social', 'key' => 'github', 'value' => 'https://github.com/hilotec'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
