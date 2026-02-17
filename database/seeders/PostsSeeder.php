<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        Post::updateOrCreate(
            ['slug' => 'it-sicherheit-fuer-kmu-schutz-gegen-ransomware'],
            [
                'title' => 'IT-Sicherheit für KMU: Schutz gegen Ransomware',
                'excerpt' => 'Ransomware-Angriffe auf KMU nehmen stetig zu. Erfahren Sie, wie Sie Ihr Unternehmen mit mehrschichtigen Sicherheitslösungen von Watchguard effektiv schützen können.',
                'body' => '<p>Ransomware-Angriffe stellen eine der grössten Bedrohungen für kleine und mittlere Unternehmen dar. Die Angreifer verschlüsseln wichtige Unternehmensdaten und fordern Lösegeld für deren Freigabe.</p>
<h3>Mehrschichtiger Schutz mit Watchguard</h3>
<p>Als Watchguard Gold-Partner setzen wir auf bewährte, mehrschichtige Sicherheitslösungen:</p>
<ul>
<li><strong>Firewalls mit APT-Blocker</strong> — Erkennung und Blockierung von Advanced Persistent Threats in Echtzeit</li>
<li><strong>VPN-Standortvernetzung</strong> — Sichere Verbindung zwischen Standorten und für Remote-Mitarbeitende</li>
<li><strong>Endpoint-Security</strong> — Umfassender Schutz für alle Endgeräte im Netzwerk</li>
</ul>
<h3>Prävention ist der beste Schutz</h3>
<p>Neben technischen Massnahmen ist die Sensibilisierung der Mitarbeitenden entscheidend. Phishing-Mails sind nach wie vor das häufigste Einfallstor für Ransomware. Regelmässige Schulungen und ein gesundes Misstrauen gegenüber unbekannten E-Mails sind wichtige Bausteine einer umfassenden Sicherheitsstrategie.</p>
<p>Kontaktieren Sie uns für eine unverbindliche Sicherheitsberatung.</p>',
                'is_published' => true,
                'published_at' => '2024-11-15 10:00:00',
            ]
        );

        Post::updateOrCreate(
            ['slug' => 'm-soft-passt-prime-branchenloesung-gebaeudetechnik'],
            [
                'title' => 'M-SOFT PASST.prime: Die Branchenlösung für die Gebäudetechnik',
                'excerpt' => 'M-SOFT PASST.prime ist die spezialisierte ERP-Lösung für das Baugewerbe und die Gebäudetechnik. Erfahren Sie, wie die Software Ihre Geschäftsprozesse optimiert.',
                'body' => '<p>M-SOFT PASST.prime ist eine umfassende Branchenlösung, die speziell für die Bedürfnisse des Baugewerbes und der Gebäudetechnik entwickelt wurde. Als zertifizierter M-SOFT Partner unterstützen wir Sie bei der Einführung und dem laufenden Betrieb.</p>
<h3>Funktionsumfang</h3>
<ul>
<li><strong>Auftragsbearbeitung</strong> — Von der Offerte über die Auftragsbestätigung bis zur Rechnung</li>
<li><strong>Projektmanagement</strong> — Übersicht über alle laufenden Projekte mit Kostenkontrolle</li>
<li><strong>Material- und Lagerverwaltung</strong> — Effiziente Verwaltung von Materialbeständen</li>
<li><strong>Integration mit Sage Sesam</strong> — Nahtlose Anbindung an die Buchhaltungssoftware</li>
</ul>
<h3>Unsere Dienstleistungen</h3>
<p>Wir begleiten Sie durch den gesamten Prozess: Evaluation, Installation, Datenmigration aus bestehenden Systemen, Schulung der Mitarbeitenden und laufender Support. Als langjähriger Partner kennen wir die Software und die Branche.</p>',
                'is_published' => true,
                'published_at' => '2024-10-20 09:00:00',
            ]
        );
    }
}
