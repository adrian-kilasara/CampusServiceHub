<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\NewTicketReply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'])
                ->required(),
            Forms\Components\Select::make('priority')
                ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])
                ->required(),
            Forms\Components\Select::make('assigned_to')
                ->label('Assign To')
                ->options(User::role(['admin', 'support_staff'])->pluck('name', 'id'))
                ->searchable()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')->searchable()->copyable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('user.name')->label('Submitted By')->searchable(),
                Tables\Columns\TextColumn::make('subject')->limit(50)->searchable(),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors(['gray' => 'low', 'info' => 'medium', 'warning' => 'high', 'danger' => 'urgent']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['danger' => 'open', 'warning' => 'in_progress', 'success' => 'resolved', 'gray' => 'closed']),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->default('Unassigned'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed']),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->icon('heroicon-o-chat-bubble-left')->color('primary')
                    ->form([
                        Forms\Components\Textarea::make('message')->required()->rows(4)->label('Your Reply'),
                    ])
                    ->action(function ($record, array $data) {
                        TicketReply::create([
                            'ticket_id'      => $record->id,
                            'user_id'        => auth()->id(),
                            'message'        => $data['message'],
                            'is_admin_reply' => true,
                        ]);
                        if ($record->status === 'open') {
                            $record->update(['status' => 'in_progress']);
                        }
                        $record->user->notify(new NewTicketReply($record));
                        Notification::make()->title('Reply sent')->success()->send();
                    }),
                Tables\Actions\Action::make('resolve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !in_array($record->status, ['resolved', 'closed']))
                    ->action(function ($record) {
                        $record->update(['status' => 'resolved', 'resolved_at' => now()]);
                        Notification::make()->title('Ticket resolved')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $open = Ticket::where('status', 'open')->count();
        return $open > 0 ? (string) $open : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
