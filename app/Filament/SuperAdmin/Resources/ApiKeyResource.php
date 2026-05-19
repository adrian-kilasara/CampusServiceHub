<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\ApiKeyResource\Pages;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'API Keys';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')->searchable()->preload()->required(),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\DateTimePicker::make('expires_at')->label('Expires At (optional)'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Owner')->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->formatStateUsing(fn ($state) => substr($state, 0, 8).'••••••••')
                    ->copyable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('usage_count')->label('Uses')->sortable(),
                Tables\Columns\TextColumn::make('last_used_at')->dateTime()->label('Last Used')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn ($record) => $record->isActive() ? 'active' : ($record->isRevoked() ? 'revoked' : 'expired'))
                    ->colors(['success' => 'active', 'danger' => 'revoked', 'warning' => 'expired']),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->sortable()->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->isActive())
                    ->action(function ($record) {
                        $record->update(['revoked_at' => now()]);
                        Notification::make()->title('API Key revoked')->danger()->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
        ];
    }
}
