<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true)->disabled(fn (string $operation) => $operation === 'edit'),
            Forms\Components\Select::make('group')
                ->options(['branding' => 'Branding', 'general' => 'General', 'system' => 'System', 'email' => 'Email'])
                ->required(),
            Forms\Components\Textarea::make('value')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable()->copyable()->fontFamily('mono'),
                Tables\Columns\BadgeColumn::make('group')
                    ->colors(['primary' => 'branding', 'success' => 'general', 'warning' => 'system', 'info' => 'email']),
                Tables\Columns\TextColumn::make('value')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options(['branding' => 'Branding', 'general' => 'General', 'system' => 'System', 'email' => 'Email']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
