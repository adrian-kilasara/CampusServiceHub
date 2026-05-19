<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Communications';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
            Forms\Components\RichEditor::make('body')->required()->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->options(['info' => 'Info', 'warning' => 'Warning', 'urgent' => 'Urgent', 'success' => 'Success'])
                ->required(),
            Forms\Components\Select::make('audience')
                ->options(['all' => 'Everyone', 'students' => 'Students Only', 'providers' => 'Providers Only', 'admins' => 'Admins Only'])
                ->required(),
            Forms\Components\Toggle::make('send_email')->label('Also send via Email'),
            Forms\Components\DateTimePicker::make('published_at')->label('Publish At')->default(now()),
            Forms\Components\Hidden::make('created_by')->default(fn () => auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors(['info' => 'info', 'warning' => 'warning', 'danger' => 'urgent', 'success' => 'success']),
                Tables\Columns\BadgeColumn::make('audience')->color('gray'),
                Tables\Columns\IconColumn::make('send_email')->boolean(),
                Tables\Columns\TextColumn::make('creator.name')->label('By'),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
