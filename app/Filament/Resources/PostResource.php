<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Inhalte';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Beiträge';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('excerpt')->rows(3),
            Forms\Components\RichEditor::make('body')->columnSpanFull(),
            Forms\Components\FileUpload::make('featured_image')->image()->directory('posts'),
            Forms\Components\DateTimePicker::make('published_at'),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('d.m.Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('published_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
