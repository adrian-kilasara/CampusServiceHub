<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\AuditLogResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Audit Trail';
    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')->label('User')->searchable()->default('System'),
                Tables\Columns\BadgeColumn::make('event')
                    ->colors(['success' => 'created', 'warning' => 'updated', 'danger' => 'deleted']),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state ?? '')),
                Tables\Columns\TextColumn::make('description')->limit(60)->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('When')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options(['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereDate('created_at', today())->count();
    }
}
