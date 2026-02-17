<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferenceResource\Pages;
use App\Models\Reference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferenceResource extends Resource
{
    protected static ?string $model = Reference::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Referenzen';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('reference_category_id')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('company_name')->required()->maxLength(255),
            Forms\Components\TextInput::make('address')->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\TextInput::make('website')->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('website'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('company_name')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferences::route('/'),
            'create' => Pages\CreateReference::route('/create'),
            'edit' => Pages\EditReference::route('/{record}/edit'),
        ];
    }
}
