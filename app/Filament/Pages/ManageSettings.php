<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?string $title = 'Einstellungen';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.manage-settings';

    public array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->mapWithKeys(fn ($s) => ["{$s->group}__{$s->key}" => $s->value]);
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Allgemein')
                            ->schema([
                                Forms\Components\TextInput::make('general__company_name')->label('Firmenname')->required(),
                                Forms\Components\TextInput::make('general__company_slogan')->label('Slogan'),
                                Forms\Components\TextInput::make('general__company_subtitle')->label('Untertitel'),
                                Forms\Components\TextInput::make('general__founded_year')->label('Gründungsjahr'),
                                Forms\Components\Textarea::make('general__about_short')->label('Kurzbeschreibung')->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('Kontakt')
                            ->schema([
                                Forms\Components\TextInput::make('contact__address_line1')->label('Adresse'),
                                Forms\Components\TextInput::make('contact__address_zip_city')->label('PLZ / Ort'),
                                Forms\Components\TextInput::make('contact__address_country')->label('Land'),
                                Forms\Components\TextInput::make('contact__phone_support_infra')->label('Telefon IT-Infrastruktur'),
                                Forms\Components\TextInput::make('contact__phone_label_infra')->label('Label IT-Infrastruktur'),
                                Forms\Components\TextInput::make('contact__phone_support_software')->label('Telefon Software'),
                                Forms\Components\TextInput::make('contact__phone_label_software')->label('Label Software'),
                                Forms\Components\TextInput::make('contact__email')->label('E-Mail'),
                                Forms\Components\TextInput::make('contact__website')->label('Website'),
                                Forms\Components\TextInput::make('contact__business_hours')->label('Öffnungszeiten'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Footer')
                            ->schema([
                                Forms\Components\Textarea::make('footer__cta_heading')->label('CTA Überschrift')->rows(2),
                                Forms\Components\TextInput::make('footer__cta_button_text')->label('CTA Button Text'),
                                Forms\Components\TextInput::make('footer__cta_button_url')->label('CTA Button URL'),
                                Forms\Components\TextInput::make('footer__copyright_text')->label('Copyright'),
                                Forms\Components\TextInput::make('footer__teamviewer_text')->label('TeamViewer Text'),
                                Forms\Components\TextInput::make('footer__teamviewer_url')->label('TeamViewer URL'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->schema([
                                Forms\Components\TextInput::make('social__linkedin')->label('LinkedIn URL'),
                                Forms\Components\TextInput::make('social__github')->label('GitHub URL'),
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
