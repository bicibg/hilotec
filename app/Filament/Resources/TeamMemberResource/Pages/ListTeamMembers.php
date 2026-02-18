<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TeamMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamMembers extends ListRecords
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
