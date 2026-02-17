<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'ueber-uns'],
            [
                'title' => 'Über uns',
                'hero_heading' => 'Ihr Partner aus Langnau im Emmental',
                'hero_subheading' => 'Langfristiger Nutzen dank einer langfristigen Partnerschaft mit HILOTEC',
                'hero_image' => 'heroes/ueber_uns_hero_bg.jpg',
                'body' => '<h2>HILOTEC Engineering + Consulting AG</h2>
<p>Die Firma HILOTEC Engineering + Consulting AG ist ein Ingenieurunternehmen und besteht seit 1995. Wir sind primär im Bereich der IT-Komplettbetreuung für KMUs tätig und decken das gesamte Spektrum von Hardware über Software bis hin zu Netzwerk und Sicherheit ab.</p>
<p>HILOTEC steht für <strong>HI</strong>gh <strong>LO</strong>w <strong>TEC</strong>hnology — wir verbinden modernste Technologie mit bodenständiger, praxisnaher Umsetzung. Unser Ziel ist es, für unsere Kunden IT-Lösungen zu schaffen, die einfach funktionieren.</p>
<h3>Unser Standort</h3>
<p>Unser Sitz befindet sich in Langnau im Emmental, im Herzen des Kantons Bern. Von hier aus betreuen wir Kunden in der ganzen Schweiz und im angrenzenden Ausland.</p>
<h3>Erneuerbare Energien</h3>
<p>Neben unserer Kerntätigkeit im IT-Bereich engagieren wir uns auch im Bereich der erneuerbaren Energien und Solarenergietechnik. Wir sind überzeugt, dass nachhaltige Technologien die Zukunft gestalten.</p>
<h3>Unsere Philosophie</h3>
<p>Langfristiger Nutzen dank einer langfristigen Partnerschaft — das ist unser Versprechen an Sie. Wir setzen auf persönliche Betreuung, transparente Kommunikation und nachhaltige Lösungen.</p>',
                'meta_title' => 'Über uns — HILOTEC Engineering + Consulting AG',
                'meta_description' => 'HILOTEC Engineering + Consulting AG — Ihr IT-Partner aus Langnau im Emmental seit 1995.',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'impressum'],
            [
                'title' => 'Impressum',
                'body' => '<h2>Impressum</h2>
<h3>Firma</h3>
<p>HILOTEC Engineering + Consulting AG<br>
Untere Hohle Gasse 5<br>
CH-3550 Langnau i.E.<br>
Schweiz</p>
<h3>Kontakt</h3>
<p>Telefon: +41 34 408 01 00<br>
E-Mail: info@hilotec.com<br>
Website: www.hilotec.com</p>
<h3>Handelsregistereintrag</h3>
<p>Eingetragener Firmenname: HILOTEC Engineering + Consulting AG<br>
UID: CHE-108.368.800<br>
Handelsregister: Kanton Bern</p>
<h3>Haftungsausschluss</h3>
<p>Der Autor übernimmt keinerlei Gewähr hinsichtlich der inhaltlichen Richtigkeit, Genauigkeit, Aktualität, Zuverlässigkeit und Vollständigkeit der Informationen. Haftungsansprüche gegen den Autor wegen Schäden materieller oder immaterieller Art, welche aus dem Zugriff oder der Nutzung bzw. Nichtnutzung der veröffentlichten Informationen entstanden sind, werden grundsätzlich ausgeschlossen.</p>',
                'meta_title' => 'Impressum — HILOTEC Engineering + Consulting AG',
                'meta_description' => 'Impressum der HILOTEC Engineering + Consulting AG, Langnau i.E.',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'datenschutz'],
            [
                'title' => 'Datenschutz',
                'body' => '<h2>Datenschutzerklärung</h2>
<h3>Allgemeiner Hinweis</h3>
<p>Gestützt auf Artikel 13 der schweizerischen Bundesverfassung und die datenschutzrechtlichen Bestimmungen des Bundes (Datenschutzgesetz, DSG) hat jede Person Anspruch auf Schutz ihrer Privatsphäre sowie auf Schutz vor Missbrauch ihrer persönlichen Daten. Die HILOTEC Engineering + Consulting AG hält diese Bestimmungen ein.</p>
<h3>Bearbeitung von Personendaten</h3>
<p>Personendaten werden vertraulich behandelt und weder an Dritte verkauft noch weitergegeben. In enger Zusammenarbeit mit unseren Hosting-Providern bemühen wir uns, die Datenbanken so gut wie möglich vor unberechtigtem Zugriff, Verlust, Missbrauch oder Fälschung zu schützen.</p>
<h3>Kontaktformular</h3>
<p>Wenn Sie uns über das Kontaktformular Anfragen zukommen lassen, werden Ihre Angaben aus dem Anfrageformular inklusive der von Ihnen dort angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage bei uns gespeichert.</p>
<h3>Cookies</h3>
<p>Diese Website verwendet technisch notwendige Cookies. Es werden keine Tracking-Cookies oder Analyse-Tools eingesetzt.</p>
<h3>Ihre Rechte</h3>
<p>Ihnen stehen grundsätzlich die Rechte auf Auskunft, Berichtigung, Löschung, Einschränkung, Datenübertragbarkeit, Widerruf und Widerspruch zu. Wenn Sie glauben, dass die Verarbeitung Ihrer Daten gegen das Datenschutzrecht verstösst, können Sie sich bei uns oder der zuständigen Aufsichtsbehörde beschweren.</p>
<h3>Kontakt</h3>
<p>HILOTEC Engineering + Consulting AG<br>
Untere Hohle Gasse 5<br>
CH-3550 Langnau i.E.<br>
info@hilotec.com</p>',
                'meta_title' => 'Datenschutz — HILOTEC Engineering + Consulting AG',
                'meta_description' => 'Datenschutzerklärung der HILOTEC Engineering + Consulting AG.',
            ]
        );
    }
}
