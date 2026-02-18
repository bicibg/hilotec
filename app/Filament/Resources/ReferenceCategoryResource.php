<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ReferenceCategoryResource\Pages\ListReferenceCategories;
use App\Filament\Resources\ReferenceCategoryResource\Pages\CreateReferenceCategory;
use App\Filament\Resources\ReferenceCategoryResource\Pages\EditReferenceCategory;
use App\Filament\Resources\ReferenceCategoryResource\Pages;
use App\Models\ReferenceCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferenceCategoryResource extends Resource
{
    protected static ?string $model = ReferenceCategory::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static string | \UnitEnum | null $navigationGroup = 'Referenzen';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Kategorien';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('references_count')->counts('references')->label('Referenzen'),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReferenceCategories::route('/'),
            'create' => CreateReferenceCategory::route('/create'),
            'edit' => EditReferenceCategory::route('/{record}/edit'),
        ];
    }
}
