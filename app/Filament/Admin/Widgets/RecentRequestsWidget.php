<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ServiceRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentRequestsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Service Requests';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ServiceRequest::with(['student', 'service', 'provider'])->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('request_number')->fontFamily('mono')->copyable(),
                Tables\Columns\TextColumn::make('student.name')->label('Student'),
                Tables\Columns\TextColumn::make('service.name')->label('Service'),
                Tables\Columns\TextColumn::make('provider.business_name')->label('Provider')->default('Unassigned'),
                Tables\Columns\BadgeColumn::make('urgency')
                    ->colors(['gray' => 'low', 'info' => 'medium', 'warning' => 'high', 'danger' => 'urgent']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['gray' => 'pending', 'info' => 'accepted', 'warning' => 'in_progress',
                        'success' => 'completed', 'danger' => 'cancelled']),
                Tables\Columns\TextColumn::make('created_at')->since()->color('gray'),
            ])
            ->poll('20s');
    }
}
