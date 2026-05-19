<?php

namespace App\Filament\Admin\Resources;

use App\Exports\ServiceRequestsExport;
use App\Filament\Admin\Resources\ServiceRequestResource\Pages;
use App\Models\Provider;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Service Requests';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Request Details')->schema([
                Forms\Components\TextInput::make('request_number')->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending', 'accepted' => 'Accepted',
                        'in_progress' => 'In Progress', 'completed' => 'Completed',
                        'cancelled' => 'Cancelled', 'disputed' => 'Disputed',
                    ])->required(),
                Forms\Components\Select::make('provider_id')
                    ->label('Assign Provider')
                    ->options(Provider::where('status', 'approved')->with('user')
                        ->get()->mapWithKeys(fn ($p) => [$p->id => $p->business_name]))
                    ->searchable()->nullable(),
                Forms\Components\TextInput::make('quoted_price')->numeric()->prefix('KES'),
                Forms\Components\TextInput::make('final_price')->numeric()->prefix('KES'),
                Forms\Components\Textarea::make('admin_notes')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_number')->searchable()->copyable()->fontFamily('mono'),
                Tables\Columns\TextColumn::make('student.name')->label('Student')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Service')->searchable(),
                Tables\Columns\TextColumn::make('provider.business_name')->label('Provider')->default('Unassigned'),
                Tables\Columns\BadgeColumn::make('urgency')
                    ->colors(['gray' => 'low', 'info' => 'medium', 'warning' => 'high', 'danger' => 'urgent']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending', 'info' => 'accepted',
                        'warning' => 'in_progress', 'success' => 'completed',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'disputed']),
                    ]),
                Tables\Columns\TextColumn::make('final_price')->label('Price')
                    ->money('KES')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'accepted' => 'Accepted', 'in_progress' => 'In Progress',
                        'completed' => 'Completed', 'cancelled' => 'Cancelled', 'disputed' => 'Disputed']),
                Tables\Filters\SelectFilter::make('urgency')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve_dispute')
                    ->icon('heroicon-o-shield-check')->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'disputed')
                    ->action(function ($record) {
                        $record->update(['status' => 'completed']);
                        Notification::make()->title('Dispute resolved')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => Excel::download(new ServiceRequestsExport(), 'requests-' . now()->format('Y-m-d') . '.xlsx')),
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(route('admin.reports.requests-pdf'))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = ServiceRequest::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
