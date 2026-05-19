<?php

namespace App\Filament\SuperAdmin\Resources\ApiKeyResource\Pages;

use App\Filament\SuperAdmin\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $key = \Illuminate\Support\Str::random(64);
        Notification::make()
            ->title('API Key Generated')
            ->body('Key: '.$key.' — copy it now, it will not be shown again.')
            ->warning()
            ->persistent()
            ->send();
        return ApiKey::create(array_merge($data, ['key' => $key]));
    }
}
