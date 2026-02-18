<?php

namespace App\Filament\Resources\ReferenceCategoryResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ReferenceCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferenceCategories extends ListRecords
{
    protected static string $resource = ReferenceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
