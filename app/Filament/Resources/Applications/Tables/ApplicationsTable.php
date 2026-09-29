<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Actions\Applications\ActivateApplicationAction;
use App\Actions\Applications\DeactivateApplicationAction;
use App\Actions\Applications\DeleteApplicationAction;
use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('package_name')
                    ->searchable(),

                TextColumn::make('platform')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('current_version')
                    ->label('Version'),

                TextColumn::make('current_build_number')
                    ->label('Build'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Application $record): bool => $record->status->value === 'inactive')
                    ->action(function (Application $record): void {
                        app(ActivateApplicationAction::class)
                            ->handle($record);
                    }),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Application $record): bool => $record->status->value === 'active')
                    ->action(function (Application $record): void {
                        app(DeactivateApplicationAction::class)
                            ->handle($record);
                    }),

                DeleteAction::make()
                    ->action(function (Application $record): void {
                        app(DeleteApplicationAction::class)
                            ->handle($record);
                    }),
            ]);
    }
}
