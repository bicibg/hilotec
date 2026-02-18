<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\Setting;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $title = 'Einstellungen';
    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.manage-settings';

    public array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->mapWithKeys(fn ($s) => ["{$s->group}__{$s->key}" => $s->value]);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Allgemein')
                            ->schema([
                                TextInput::make('general__company_name')->label('Firmenname')->required(),
                                TextInput::make('general__company_slogan')->label('Slogan'),
                                TextInput::make('general__company_subtitle')->label('Untertitel'),
                                TextInput::make('general__founded_year')->label('Gründungsjahr'),
                                Textarea::make('general__about_short')->label('Kurzbeschreibung')->rows(3),
                            ]),
                        Tab::make('Kontakt')
                            ->schema([
                                TextInput::make('contact__address_line1')->label('Adresse'),
                                TextInput::make('contact__address_zip_city')->label('PLZ / Ort'),
                                TextInput::make('contact__address_country')->label('Land'),
                                TextInput::make('contact__phone_support_infra')->label('Telefon IT-Infrastruktur'),
                                TextInput::make('contact__phone_label_infra')->label('Label IT-Infrastruktur'),
                                TextInput::make('contact__phone_support_software')->label('Telefon Software'),
                                TextInput::make('contact__phone_label_software')->label('Label Software'),
                                TextInput::make('contact__email')->label('E-Mail'),
                                TextInput::make('contact__website')->label('Website'),
                                TextInput::make('contact__business_hours')->label('Öffnungszeiten'),
                            ]),
                        Tab::make('Footer')
                            ->schema([
                                Textarea::make('footer__cta_heading')->label('CTA Überschrift')->rows(2),
                                TextInput::make('footer__cta_button_text')->label('CTA Button Text'),
                                TextInput::make('footer__cta_button_url')->label('CTA Button URL'),
                                TextInput::make('footer__copyright_text')->label('Copyright'),
                                TextInput::make('footer__teamviewer_text')->label('TeamViewer Text'),
                                TextInput::make('footer__teamviewer_url')->label('TeamViewer URL'),
                            ]),
                        Tab::make('Social Media')
                            ->schema([
                                TextInput::make('social__linkedin')->label('LinkedIn URL'),
                                TextInput::make('social__github')->label('GitHub URL'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (! str_contains($key, '__')) {
                continue;
            }
            [$group, $settingKey] = explode('__', $key, 2);
            Setting::set("{$group}.{$settingKey}", $value);
        }

        Notification::make()
            ->title('Einstellungen gespeichert')
            ->success()
            ->send();
    }
}
