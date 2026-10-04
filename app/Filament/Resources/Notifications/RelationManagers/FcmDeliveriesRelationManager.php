<?php

namespace App\Filament\Resources\Notifications\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FcmDeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'fcmDeliveries';

    protected static ?string $title = 'FCM Native Deliveries';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target_type')
                    ->label('Target Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('target_value')
                    ->label('Target')
                    ->limit(80)
                    ->tooltip(fn ($state): ?string => $state)
                    ->searchable(),

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
            ->actions([
                ViewAction::make(),
            ]);
    }
}