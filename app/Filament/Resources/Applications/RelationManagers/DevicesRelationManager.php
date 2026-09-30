<?php

namespace App\Filament\Resources\Applications\RelationManagers;

use App\Actions\Device\ActivateDeviceAction;
use App\Actions\Device\DeactivateDeviceAction;
use App\Models\Device;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DevicesRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    protected static ?string $recordTitleAttribute = 'device_identifier';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_identifier')
                    ->label('Device Identifier')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('platform')
                    ->badge()
                    ->sortable(),

                TextColumn::make('user_identifier')
                    ->label('User Identifier')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('app_version')
                    ->label('App Version')
                    ->placeholder('—'),

                TextColumn::make('os_version')
                    ->label('OS Version')
                    ->placeholder('—'),

                TextColumn::make('locale')
                    ->placeholder('—'),

                TextColumn::make('timezone')
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_seen_at')
                    ->label('Last Seen')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Device $record): bool => $record->is_active
                    )
                    ->action(
                        fn (Device $record) =>
                            app(DeactivateDeviceAction::class)->handle($record)
                    ),

                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Device $record): bool => ! $record->is_active
                    )
                    ->action(
                        fn (Device $record) =>
                            app(ActivateDeviceAction::class)->handle($record)
                    ),
            ]);
    }
}