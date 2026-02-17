<?php

namespace Database\Seeders;

use App\Models\Reference;
use App\Models\ReferenceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReferencesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Baugewerbe' => [
                ['Bauhandwerk AG', 'Hohle Gasse 5, 3550 Langnau i.E.', 'Ubuntu Linux Server, VMWare, Asterisk VoIP-Telefonanlage mit CTI, snom-Telefone, Website CMS, OpenVPN', 'bauhandwerkag.ch'],
                ['Bigler Gipser und Maler AG', 'Allmendstrasse 66, 3600 Thun', 'Linux Server, Groupware, AGFEO-Telefonanlage mit CTI, OpenVPN', null],
                ['Bigler Maler und Gipser AG', 'Verladeplatz 5, 3550 Langnau i.E.', 'Kolab-Groupware, AGFEO + Asterisk VoIP, snom-Telefone, OpenVPN', null],
                ['Global Fence Ltd.', 'Aurütelistrasse 2, 8552 Felben', 'Gentoo Linux, VMWare, M-Soft, Sage Sesam, Kasse mit Bondrucker', null],
                ['PM Mangold Holzbau AG', 'Hemmikerstrasse 55, 4466 Ormalingen BL', 'Suse Linux, VMWare, M-Soft, Sage Sesam, Immobilienverwaltungstool', null],
                ['Schüpbach Holzbau AG', 'Mungnau, 3436 Zollbrück', 'Gentoo Linux Server, AGFEO-Telefonanlage, DNS/Mail/Webhosting', 'schuepbach-holzbau.ch'],
                ['Zimmerei Kühni AG', 'Bodenmatt, 3435 Ramsei', 'Windows 2003 Server, Exchange, AGFEO + Asterisk VoIP, M-Soft, Sage Sesam', 'kuehni-ag.ch'],
                ['Zaunteam Zaunsysteme GmbH', 'Tösswiesenstrasse 10, 8413 Neftenbach', 'Betreuung M-Soft und Sage Sesam', null],
            ],
            'Dienstleistungsgewerbe' => [
                ['AARE Immobilien-Treuhand AG', 'Thunstrasse 42, 3074 Muri bei Bern', 'Gentoo Linux, VMWare, redundanter Server, Siemens-Telefonanlage, Website CMS', 'aareimmo.ch'],
                ['Egger, Tanner + Partner Notariat', 'Kirchgasse 9, 3550 Langnau i.E.', 'Gentoo Linux, VMWare, Samba, eGroupware, AGFEO, CRM, Liegenschaftsverwaltung', 'egger-tanner.ch'],
                ['Hofercomputing GmbH', 'Dorfbergstrasse 3, 3550 Langnau i.E.', 'AGFEO-Telefonanlage, VMWare auf Ubuntu, Serverhardware', null],
                ['ZiC Internet & Communication AG', 'Spinnerei, 3436 Zollbrück', 'Asterisk VoIP, SIP-Softphones, Gigaset DECT, Serverhardware, Kooperation Hosting/Housing', null],
            ],
            'Detailhandel' => [
                ['Carrosseriesattlerei Martin Zysset', 'Hauptstrasse 15, 3535 Schüpbach', 'DNS/Mail/Webhosting', 'carrosseriesattlerei.ch'],
                ['Sicilvini', 'Tiergartenstrasse 15A, 4410 Liestal', 'DNS/Mailhosting, Website CMS mit E-Shop', 'sicilvini.ch'],
                ['Walter Schmocker Weine', 'Kirchgasse 9, 3550 Langnau i.E.', 'DNS/Mail/Webhosting', 'schmocker-weine.ch'],
            ],
            'Elektrotechnik- und Elektronikgewerbe' => [
                ['Allgemeiner Elektro-Bau M & L GmbH KG', 'Eichhornstrasse 8-12, D-50735 Köln', 'DNS/Mailhosting, Website CMS', 'aeb-ml.de'],
                ['Stahn Electronics GmbH', 'Kirchgasse 12, 3312 Fraubrunnen', 'DNS/Mail/Webhosting', 'stahn.com'],
            ],
            'Erziehungs-, Schul- und Weiterbildungswesen' => [
                ['Brisanz, Hanspeter Utz', 'Brügglerweg 7, 3006 Bern', 'DNS/Mail/Webhosting', 'brisanz.ch'],
                ['Familientreff Bern', 'Muristrasse 27, 3006 Bern', 'DNS/Mailhosting, Website CMS, Workstations', 'familientreff.ch'],
                ['Loop-Pool Organisationsberatung', null, 'DNS/Mail/Webhosting', 'looppool.ch'],
                ['Risorsa, Gruppe für systemische Entwicklung', null, 'DNS/Mail/Webhosting', 'risorsa.ch'],
                ['Rudolf-Steinerschule Langnau', 'Schlossstrasse 6, 3550 Langnau i.E.', 'DNS/Mailhosting, Website CMS', 'steinerschule-langnau.ch'],
                ['Verein Elternbildung Stadt Bern', null, 'DNS/Mail/Webhosting', 'elternbildung-bern.ch'],
                ['Verein Elternbildung Kanton Bern', 'Gerechtigkeitsgasse 81, 3011 Bern', 'DNS/Mailhosting, Website CMS', 'elternbildung-be.ch'],
            ],
            'Gastronomie und Hotellerie' => [
                ['Daily Food GmbH', 'Moosackerweg 8, 4105 Biel-Benken', 'Website CMS, VoIP, Web-Shop mit Auftragsbearbeitung und Sesam-Schnittstelle', null],
                ['Gasthaus Bäregghöhe', '3555 Trubschachen', 'DNS/Mail/Webhosting mit Web-Speisekarte', 'baeregghoehe.ch'],
                ['Gasthof zum Bären', '3550 Trubschachen', 'DNS/Mail/Webhosting', 'aeltester-baeren.ch'],
                ['Hotel Restaurant Kemmeriboden-Bad', '6197 Schangnau', 'Gentoo Linux Server, DNS/Mailhosting, Website CMS', 'kemmeriboden.ch'],
                ['Restaurant Waldheim', 'Waldheimstrasse 40, 3012 Bern', 'DNS/Mailhosting, Website CMS, Telefonanlage, Kassenimport in Sesam', 'waldheim-bern.ch'],
            ],
            'Geologie und Umweltwesen' => [
                ['B-I-G Büro für Ingenieurgeologie AG', 'Dorfstrasse 10, 3073 Gümligen', 'DNS/Mailhosting, Website CMS, SuSE Linux, Online-Archiv ca. 100GB, OpenVPN', 'b-i-g.ch'],
                ['Partner/-innen in Umweltfragen', 'Waldeggstrasse 47, 3097 Liebefeld', 'DNS/Mail/Webhosting', 'piu-welt.ch'],
                ['TransGeo AG', 'Dorfstrasse 10, 3073 Gümligen', 'DNS/Mail/Webhosting', 'transgeo.ch'],
            ],
            'Gesundheitswesen' => [
                ['Ambulante Herzrehabilitation Zürich, Dr. med. Lorenz Felder', 'Grütstrasse 60, 8802 Kilchberg', 'Open-Source Praxissoftware Elexis, Plugin-Entwicklung für Erhebungsdaten', null],
                ['Augen Glattzentrum AG', 'Winterthurerstrasse 99, 8301 Glattzentrum', 'VMWare Windows Server, Exchange, DNS/Mail/Webhosting, Praxissoftware AKCM', 'augenglatt.ch'],
                ['Praxis Dr. med. Armin Brunner', 'Bernstrasse 15, 3550 Langnau i.E.', 'Arbeitsplatzrechner, Praxissoftware AKCM, Dokumentenverwaltung', null],
                ['Dr. med. Peter Brunner', 'Thieracherweg 2, 3608 Thun-Allmendingen', 'DNS/Mailhosting, Website CMS', 'chimed.ch'],
                ['Kindertagesklinik Liestal AG', 'Oriastalstrasse 87a, 4410 Liestal', 'Server, Workstations, Praxissoftware Vitomed, VMWare', null],
                ['RVK Unimedes Managed Care', 'Haldenstr. 25, 6006 Luzern', 'Windows Server, Exchange, SQL, Website CMS, Hausarztsystem, Telemedizin', 'unimedes.com'],
                ['Variosoft', 'Dufourstrasse 45, 3005 Bern', 'AGFEO-Telefonanlage mit CTI und DECT', null],
                ['Dr. med. Bernhard Vökt', 'Oberstrasse 8, 3550 Langnau i.E.', 'AGFEO-Telefonanlage, Workstation, Praxissoftware mFmed Evolution', null],
            ],
            'Industrie- und Metallverarbeitung' => [
                ['Baier Rohrleitungsbau AG', 'Weidenstr. 6, 4147 Aesch BL', 'Gentoo Linux, eGroupware, OpenVPN, Website CMS', 'baier-rohr.com'],
                ['edi Entsorgungsdienste AG', 'Industriering 10, 3250 Lyss', 'DNS/Mailhosting, Website CMS', 'edi-entsorgung.ch'],
                ['ESPRAM AG', 'Industriestrasse, 3362 Niederönz', 'Gentoo Linux, OpenVPN, Website CMS, Open-Source ERP', 'espram.ch'],
                ['Flükiger & Co AG Gesenkschmiede', 'Emmentalstr. 75, 3414 Oberburg', 'Windows Server, Gentoo Linux Gateway, OpenVPN, DNS/Mail/Webhosting', 'gesenkschmiede.ch'],
                ['Jakob Drahtseil AG', 'Dorfstrasse, 3555 Trubschachen', 'VMWare auf Ubuntu, Website CMS, NIDS auf Hardened Gentoo, eGroupware, OpenVPN', 'jakob.ch'],
                ['Jürg Kühni Sanitär', 'Sägestrasse 26, 3550 Langnau i.E.', 'Windows Server, Plancal CAD, DNS/Mail/Webhosting, CTI', 'kuehniteam.ch'],
            ],
            'Kunst und Literatur' => [
                ['Libretto Antiquariat, Jörg Mäder', 'Oberstrasse 34, 3550 Langnau i.E.', 'DNS/Mailhosting, Website CMS mit Online-Buchshop', 'libretto-antiquariat.ch'],
                ['The Pebbles, Axel Hesslenberg', null, 'DNS/Mailhosting, Website CMS mit Fotogalerie', 'thepebbles.com'],
            ],
            'Öffentliches Wesen und Politik' => [
                ['Bauamt Langnau', 'Sägestrasse, 3550 Langnau i.E.', 'Gentoo Linux, Workstations, OpenVPN, Open-Xchange Groupware', null],
                ['Bernhard Antener (Nationalratswahlen)', null, 'DNS/Mailhosting, Website CMS', 'bernhard-antener.ch'],
                ['SP Langnau', null, 'DNS/Mail/Webhosting', 'splangnau.ch'],
            ],
            'Tourismusbranche' => [
                ['Château du Blat', 'F-07110 Beaumont, Frankreich', 'DNS/Mail/Webhosting', 'chateau-du-blat.fr'],
                ['Chesa Bellavista La Punt', '7522 La Punt', 'DNS/Mail/Website CMS', 'bellavistalapunt.ch'],
                ['Gunel Yacht Charter', 'Via F. Caracciolo 14, I-80122 Napoli', 'DNS/Mailhosting, Website CMS', 'yachtcharter-med.com'],
                ['Segelferien.info', null, 'DNS/Mailhosting, Web-Portal', 'segelferien.info'],
                ['Urlaub-Europa.com', null, 'DNS/Mailhosting, Web-Portal', 'urlaub-europa.com'],
            ],
            'Solarenergietechnik' => [
                ['BE Netz AG', 'Bernstrasse 57a, 6003 Luzern', 'Gentoo Linux, Asterisk VoIP, VMWare, AGFEO + VoIP, OpenVPN, Website CMS', 'benetz.ch'],
                ['Solar Technologie International GmbH', 'D-08393 Meerane', 'DNS/Mailhosting, Website CMS', 'sti-solar.de'],
            ],
            'Tiere' => [
                ['Chodenhunden Schweiz, Patricie Nesvadba', null, 'DNS/Mailhosting, Website CMS', 'chodsky-pes.ch'],
                ['Soap Bubble Ragdoll-Zucht, Paula Brunner', 'Bernstrasse 17, 3550 Langnau i.E.', 'DNS/Mail/Webhosting', 'soap-bubble.ch'],
            ],
            'Weitere Projekte' => [
                ['Association Aïkido Journal Aiki-Dojo', 'F-07260 Joyeuse', 'DNS/Mail/Webhosting', 'aikidojournal.eu'],
                ['Privatsternwarte Loberg, Urs Flükiger', 'Gsteigweg 7, 3423 Ersigen', 'DNS/Mail/Webhosting', 'ursusmajor.ch'],
                ['Schwicky.net, Jean-Pierre Schwickerath', 'Napfstrasse 46, 3550 Langnau i.E.', 'DNS/Mail/Webhosting, Asterisk VoIP', 'schwicky.net'],
            ],
        ];

        $catOrder = 1;
        foreach ($categories as $categoryName => $refs) {
            $category = ReferenceCategory::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'sort_order' => $catOrder++]
            );

            $refOrder = 1;
            foreach ($refs as [$company, $address, $description, $website]) {
                Reference::updateOrCreate(
                    ['company_name' => $company, 'reference_category_id' => $category->id],
                    [
                        'address' => $address,
                        'description' => $description,
                        'website' => $website,
                        'sort_order' => $refOrder++,
                    ]
                );
            }
        }
    }
}
