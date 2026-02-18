<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
