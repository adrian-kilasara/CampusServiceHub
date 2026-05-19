<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProviderResource\Pages;
use App\Models\Provider;
use App\Notifications\ProviderApprovalDecision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Provider Info')->schema([
                Forms\Components\TextInput::make('business_name')->required(),
                Forms\Components\Textarea::make('bio')->rows(3),
                Forms\Components\TextInput::make('location'),
                Forms\Components\TextInput::make('whatsapp')->tel(),
            ])->columns(2),

            Forms\Components\Section::make('Verification')->schema([
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended'])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')->label('Rejection Reason (if rejected)')->rows(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Owner')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending', 'success' => 'approved',
                        'danger' => 'rejected', 'gray' => 'suspended',
                    ]),
                Tables\Columns\TextColumn::make('rating_avg')->label('Rating')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "⭐ {$state}" : 'No ratings')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_jobs')->label('Jobs')->sortable(),
                Tables\Columns\TextColumn::make('verified_at')->label('Verified')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved', 'verified_at' => now()]);
                        $record->user->notify(new ProviderApprovalDecision(approved: true));
                        Notification::make()->title('Provider approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        $record->user->notify(new ProviderApprovalDecision(approved: false));
                        Notification::make()->title('Provider rejected')->danger()->send();
                    }),
                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->action(function ($record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('Provider suspended')->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Provider::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
