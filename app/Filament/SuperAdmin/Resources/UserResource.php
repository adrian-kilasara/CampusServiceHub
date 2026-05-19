<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account Info')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(20),
                Forms\Components\TextInput::make('student_id')->maxLength(50)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')->password()->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create'),
            ])->columns(2),

            Forms\Components\Section::make('Status & Role')->schema([
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'banned' => 'Banned'])
                    ->required(),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->options(Role::pluck('name', 'name'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->circular()->defaultImageUrl(fn ($record) =>
                    'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7C3AED&background=EDE9FE'
                ),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('roles.name')->badge()->separator(','),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'warning' => 'suspended', 'danger' => 'banned']),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'banned' => 'Banned']),
                Tables\Filters\SelectFilter::make('roles')->relationship('roles', 'name')->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(function ($record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('User suspended')->warning()->send();
                    }),
                Tables\Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'active')
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        Notification::make()->title('User activated')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
