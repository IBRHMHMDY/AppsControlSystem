<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Actions\Notification\CancelNotificationAction;
use App\Actions\Notification\RetryNotificationAction;
use App\Actions\Notification\ScheduleNotificationAction;
use App\Actions\Notification\SendNotificationAction;
use App\Enums\NotificationStatus;
use App\Enums\NotificationTargetType;
use App\Models\Notification;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application.name')
                    ->label('Application')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('target_type')
                    ->label('Target Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('application_id')
                    ->label('Application')
                    ->relationship('application', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options(NotificationStatus::options()),

                SelectFilter::make('target_type')
                    ->label('Target Type')
                    ->options(NotificationTargetType::options()),
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->authorize('update'),

                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Notification $record): bool =>
                            $record->status === NotificationStatus::DRAFT
                            && $record->scheduled_at === null,
                    )
                    ->authorize('update')
                    ->action(
                        fn (
                            Notification $record,
                            SendNotificationAction $action,
                        ): Notification => $action->handle($record),
                    )
                    ->successNotificationTitle('Notification queued for delivery.'),

                Action::make('schedule')
                    ->label('Schedule')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Notification $record): bool =>
                            $record->status === NotificationStatus::DRAFT
                            && $record->scheduled_at !== null,
                    )
                    ->authorize('update')
                    ->action(
                        fn (
                            Notification $record,
                            ScheduleNotificationAction $action,
                        ): Notification => $action->handle(
                            $record,
                            $record->scheduled_at,
                        ),
                    )
                    ->successNotificationTitle('Notification scheduled.'),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Notification $record): bool => in_array(
                            $record->status,
                            [
                                NotificationStatus::DRAFT,
                                NotificationStatus::SCHEDULED,
                                NotificationStatus::PENDING,
                            ],
                            true,
                        ),
                    )
                    ->authorize('update')
                    ->action(
                        fn (
                            Notification $record,
                            CancelNotificationAction $action,
                        ): Notification => $action->handle($record),
                    )
                    ->successNotificationTitle('Notification cancelled.'),

                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Notification $record): bool =>
                            $record->status === NotificationStatus::SENT,
                    )
                    ->authorize('update')
                    ->action(
                        fn (
                            Notification $record,
                            RetryNotificationAction $action,
                        ): Notification => $action->handle($record),
                    )
                    ->successNotificationTitle('Notification queued for retry.'),
            ]);
    }
}