<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool { return false; }
    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')->label('Student')->searchable(),
                Tables\Columns\TextColumn::make('provider.business_name')->label('Provider')->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state)),
                Tables\Columns\TextColumn::make('comment')->limit(60)->searchable(),
                Tables\Columns\IconColumn::make('is_flagged')->boolean()->label('Flagged'),
                Tables\Columns\TextColumn::make('flagged_reason')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_flagged')->label('Flagged Reviews'),
            ])
            ->actions([
                Tables\Actions\Action::make('flag')
                    ->icon('heroicon-o-flag')->color('warning')
                    ->visible(fn ($record) => !$record->is_flagged)
                    ->form([
                        \Filament\Forms\Components\TextInput::make('reason')->required()->label('Reason'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['is_flagged' => true, 'flagged_reason' => $data['reason']]);
                        Notification::make()->title('Review flagged')->warning()->send();
                    }),
                Tables\Actions\Action::make('unflag')
                    ->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => $record->is_flagged)
                    ->action(function ($record) {
                        $record->update(['is_flagged' => false, 'flagged_reason' => null]);
                        Notification::make()->title('Review cleared')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()->label('Remove'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListReviews::route('/')];
    }

    public static function getNavigationBadge(): ?string
    {
        $flagged = Review::where('is_flagged', true)->count();
        return $flagged > 0 ? (string) $flagged : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }
}
