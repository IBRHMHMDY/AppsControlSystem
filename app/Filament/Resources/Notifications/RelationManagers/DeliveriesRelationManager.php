<?php

namespace App\Filament\Resources\Notifications\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Device Deliveries';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device.id')
                    ->label('Device')
                    ->searchable(),

                // TextColumn::make('device.fcm_token')
                //     ->label('FCM Token')
                //     ->limit(30)
                //     ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('attempts')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(60)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('queued_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('processing_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}