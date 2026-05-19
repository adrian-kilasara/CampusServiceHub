<?php

namespace App\Filament\SuperAdmin\Resources\AuditLogResource\Pages;

use App\Filament\SuperAdmin\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;
}
