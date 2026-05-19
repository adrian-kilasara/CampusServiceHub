<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\TicketResource;
use Filament\Resources\Pages\ListRecords;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;
}
