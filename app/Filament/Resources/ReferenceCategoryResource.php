<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferenceCategoryResource\Pages;
use App\Models\ReferenceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferenceCategoryResource extends Resource
{
    protected static ?string $model = ReferenceCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Referenzen';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Kategorien';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('references_count')->counts('references')->label('Referenzen'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferenceCategories::route('/'),
            'create' => Pages\CreateReferenceCategory::route('/create'),
            'edit' => Pages\EditReferenceCategory::route('/{record}/edit'),
        ];
    }
}
