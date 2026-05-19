<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool { return false; }
    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->copyable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('student.name')->label('Student')->searchable(),
                Tables\Columns\TextColumn::make('serviceRequest.request_number')->label('Request')->copyable(),
                Tables\Columns\TextColumn::make('amount')->money('KES')->sortable(),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors(['primary' => 'wallet', 'success' => 'mpesa', 'info' => 'card', 'gray' => 'cash']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'paid', 'danger' => 'failed', 'info' => 'refunded']),
                Tables\Columns\TextColumn::make('paid_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded']),
                Tables\Filters\SelectFilter::make('method')
                    ->options(['wallet' => 'Wallet', 'mpesa' => 'M-Pesa', 'card' => 'Card', 'cash' => 'Cash']),
            ])
            ->actions([
                Tables\Actions\Action::make('refund')
                    ->icon('heroicon-o-arrow-uturn-left')->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'paid')
                    ->action(function ($record) {
                        $record->update(['status' => 'refunded']);
                        Notification::make()->title('Payment refunded')->warning()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPayments::route('/')];
    }
}
