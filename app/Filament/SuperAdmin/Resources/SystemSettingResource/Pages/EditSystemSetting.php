<?php

namespace App\Filament\SuperAdmin\Resources\SystemSettingResource\Pages;

use App\Filament\SuperAdmin\Resources\SystemSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
