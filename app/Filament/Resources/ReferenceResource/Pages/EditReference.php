<?php

namespace App\Filament\Resources\ReferenceResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReference extends EditRecord
{
    protected static string $resource = ReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
