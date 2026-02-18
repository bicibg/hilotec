<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ReferenceResource\Pages\ListReferences;
use App\Filament\Resources\ReferenceResource\Pages\CreateReference;
use App\Filament\Resources\ReferenceResource\Pages\EditReference;
use App\Filament\Resources\ReferenceResource\Pages;
use App\Models\Reference;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferenceResource extends Resource
{
    protected static ?string $model = Reference::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string | \UnitEnum | null $navigationGroup = 'Referenzen';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('reference_category_id')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('company_name')->required()->maxLength(255),
            TextInput::make('address')->maxLength(255),
            Textarea::make('description')->rows(3),
            TextInput::make('website')->maxLength(255),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')->searchable()->sortable(),
                TextColumn::make('category.name')->sortable(),
                TextColumn::make('website'),
                IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('company_name')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReferences::route('/'),
            'create' => CreateReference::route('/create'),
            'edit' => EditReference::route('/{record}/edit'),
        ];
    }
}
