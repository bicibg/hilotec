<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMembersSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Jürg Schweizer',
                'role' => 'Verwaltungsratspräsident, Mitglied der Geschäftsleitung',
                'bio' => 'Dipl. El.-Ing. HTL / NDS Unternehmensführung. Netzwerktechnik, Server und Arbeitsplätze, VoIP-Telefonanlagen, Überwachungslösungen.',
            ],
            [
                'name' => 'Jean-Pierre Schwickerath',
                'role' => 'Geschäftsführer, Mitglied des Verwaltungsrats',
                'bio' => 'Dipl. Wirtschaftsinformatiker HF. Leitung Software-Projekte, Sage50-Spezialist, M-SOFT-Spezialist, IT-Infrastruktur.',
            ],
            [
                'name' => 'David Wenger',
                'role' => 'Informatiker EFZ',
                'bio' => 'Informatiker EFZ. Netzwerktechnik, Server und Arbeitsplätze, VoIP-Telefonanlagen.',
            ],
            [
                'name' => 'Halid Vakuyev',
                'role' => 'Betriebsinformatiker EFZ',
                'bio' => 'Betriebsinformatiker EFZ. Netzwerktechnik, Server und Arbeitsplätze.',
            ],
            [
                'name' => 'Nirosh Kailasanathan',
                'role' => 'Informatiker',
                'bio' => 'Informatiker. Netzwerktechnik, Server und Arbeitsplätze.',
            ],
            [
                'name' => 'Miguel Schweizer',
                'role' => 'Informatiker BSc i.A.',
                'bio' => 'Informatiker BSc in Ausbildung. Software-Entwicklung, IT-Infrastruktur.',
            ],
            [
                'name' => 'Yves Salvisberg',
                'role' => 'Informatiker (Plattformentwicklung) EFZ',
                'bio' => 'Informatiker (Plattformentwicklung) EFZ. Software-Entwicklung, Plattformentwicklung.',
            ],
            [
                'name' => 'Abirnan Sivarajah',
                'role' => 'Informatiker in Ausbildung',
                'bio' => 'Informatiker in Ausbildung. IT-Infrastruktur.',
            ],
            [
                'name' => 'Agatha Aschwanden',
                'role' => 'Mitglied des Verwaltungsrats',
                'bio' => 'Mitglied des Verwaltungsrats.',
            ],
        ];

        foreach ($members as $index => $member) {
            TeamMember::updateOrCreate(
                ['name' => $member['name']],
                [
                    'role' => $member['role'],
                    'bio' => $member['bio'],
                    'photo' => null,
                    'email' => null,
                    'phone' => null,
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
