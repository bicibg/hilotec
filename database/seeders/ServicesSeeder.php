<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'IT-Infrastruktur & Netzwerke',
                'icon' => 'server.svg',
                'excerpt' => 'Angepasst auf Ihre Bedürfnisse, stellen wir Arbeitsplatzrechner und Server zusammen. Dabei setzen wir auf bewährte und erprobte Komponenten um Ihnen eine optimale Zuverlässigkeit und Performance zu bieten.',
                'body' => '<p>Angepasst auf Ihre Bedürfnisse, stellen wir Arbeitsplatzrechner und Server zusammen. Dabei setzen wir auf bewährte und erprobte Komponenten um Ihnen eine optimale Zuverlässigkeit und Performance zu bieten.</p><p>Wir evaluieren geeignete Softwarewerkzeuge, liefern entsprechende Hardware und richten Systeme über Vernetzung zu den Arbeitsplätzen ein. Einführung, Schulung, Support als Hilfe zur Selbsthilfe der Anwender bekommen Sie aus einer Hand.</p>',
            ],
            [
                'title' => 'IT-Sicherheit',
                'icon' => 'security.svg',
                'excerpt' => 'Als Watchguard Gold-Partner schützen wir Ihr Netzwerk gegen Ransomware, Phishing und gezielte Angriffe.',
                'body' => '<p>Als Watchguard Gold-Partner schützen wir Ihr Netzwerk gegen Ransomware, Phishing und gezielte Angriffe. Unsere mehrschichtigen Sicherheitslösungen umfassen Firewalls mit APT-Blocker, VPN-Standortvernetzung und umfassende Endpoint-Security.</p><p>Der Schutz Ihrer Daten ist uns ein grosses Anliegen.</p>',
            ],
            [
                'title' => 'Cloud & Hosting',
                'icon' => 'cloud.svg',
                'excerpt' => 'Hosting auf unseren eigenen Servern in der Schweiz, streng nach Schweizer Recht.',
                'body' => '<p>Hosting auf unseren eigenen Servern in der Schweiz, streng nach Schweizer Recht. DNS-Hosting, E-Mail-Hosting mit wirkungsvollen Spam- und Virenfiltern, Webhosting und IPv6-Verfügbarkeit.</p><p>Sichere und vertrauliche Cloud-Lösungen aus dem eigenen Rechenzentrum.</p>',
            ],
            [
                'title' => 'Backup-Lösungen',
                'icon' => 'backup.svg',
                'excerpt' => 'Mit Veeam-basierten Sicherungslösungen erzielen wir Recovery Time and Point Objectives von weniger als 15 Minuten.',
                'body' => '<p>Mit Veeam-basierten Sicherungslösungen erzielen wir Recovery Time and Point Objectives von weniger als 15 Minuten für alle Anwendungen und Daten.</p><p>High-Speed Recovery, Vermeidung von Datenverlust, Verified Protection und vollständige Transparenz.</p>',
            ],
            [
                'title' => 'Software & Branchenlösungen',
                'icon' => 'software.svg',
                'excerpt' => 'Als unabhängiger Softwarevertreiber bieten wir stets die optimale Software für Ihre Bedürfnisse.',
                'body' => '<p>Als unabhängiger Softwarevertreiber bieten wir stets die optimale Software für Ihre Bedürfnisse: Sage 50, M-SOFT PASST.prime für das Baugewerbe, Chronikos Zeiterfassung, Elexis für Arztpraxen.</p><p>Evaluation, Einführung, Datenmigration, Schulung und Support.</p>',
            ],
            [
                'title' => 'VoIP-Telefonie',
                'icon' => 'phone.svg',
                'excerpt' => 'Asterisk-basierte VoIP-Telefonanlagen mit CTI-Integration, SIP-Telefonen und DECT-Mobilgeräten.',
                'body' => '<p>Asterisk-basierte VoIP-Telefonanlagen mit CTI-Integration, SIP-Telefonen von snom, DECT-Mobilgeräten und analogen Endgeräten.</p><p>Internettelefonie, Standortvernetzung und Integration in bestehende Infrastruktur.</p>',
            ],
            [
                'title' => 'Virtualisierung',
                'icon' => 'virtualization.svg',
                'excerpt' => 'Konsolidierung Ihrer Serverlandschaft mit VMWare vSphere (ESXi) oder KVM.',
                'body' => '<p>Konsolidierung Ihrer Serverlandschaft mit VMWare vSphere (ESXi) oder KVM. Dadurch gewinnen Sie an Sicherheit und Redundanzmöglichkeiten und sparen Kosten bei Wartung und Energieverbrauch.</p><p>Wir virtualisieren bestehende Server oder migrieren auf neue Infrastruktur.</p>',
            ],
            [
                'title' => 'Beratung & Projektierung',
                'icon' => 'consulting.svg',
                'excerpt' => 'Als Unternehmensberater für Informationssysteme formulieren wir mit Ihnen Ihre informationstechnischen Bedürfnisse.',
                'body' => '<p>Als Unternehmensberater für Informationssysteme formulieren wir mit Ihnen Ihre informationstechnischen Bedürfnisse und erstellen einen Anforderungskatalog.</p><p>Von der Planung über die Ausführung bis zur langfristigen Betreuung — alles aus einer Hand.</p>',
            ],
        ];

        foreach ($services as $i => $data) {
            Service::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                array_merge($data, ['slug' => Str::slug($data['title']), 'sort_order' => $i + 1])
            );
        }
    }
}
