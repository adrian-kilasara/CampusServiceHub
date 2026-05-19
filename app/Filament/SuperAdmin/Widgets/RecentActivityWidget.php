<?php

namespace App\Filament\SuperAdmin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Audit Activity';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->limit(15))
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')->label('User')->default('System'),
                Tables\Columns\BadgeColumn::make('event')
                    ->colors(['success' => 'created', 'warning' => 'updated', 'danger' => 'deleted']),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state ?? 'Unknown')),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('When')
                    ->since()->color('gray'),
            ])
            ->poll('15s');
    }
}
