<?php

namespace App\Filament\Resources\ReferenceCategoryResource\Pages;

use App\Filament\Resources\ReferenceCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferenceCategory extends EditRecord
{
    protected static string $resource = ReferenceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
