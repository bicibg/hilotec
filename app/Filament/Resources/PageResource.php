<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Inhalte';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Seiten';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('hero_heading')->maxLength(255),
            Forms\Components\TextInput::make('hero_subheading')->maxLength(255),
            Forms\Components\TextInput::make('hero_image')->maxLength(255),
            Forms\Components\RichEditor::make('body')->columnSpanFull(),
            Forms\Components\TextInput::make('meta_title')->maxLength(255),
            Forms\Components\Textarea::make('meta_description')->rows(2),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
