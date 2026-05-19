<?php

namespace App\Filament\SuperAdmin\Resources\ApiKeyResource\Pages;

use App\Filament\SuperAdmin\Resources\ApiKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiKeys extends ListRecords
{
    protected static string $resource = ApiKeyResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
