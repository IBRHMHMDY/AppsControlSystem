<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class NotificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('application.name')
                    ->label('Application'),

                TextEntry::make('title'),

                TextEntry::make('body')
                    ->columnSpanFull(),

                ImageEntry::make('image')
                    ->placeholder('-'),

                TextEntry::make('data_payload')
                    ->label('Data Payload')
                    ->formatStateUsing(
                        fn ($state): string => empty($state)
                            ? '-'
                            : json_encode(
                                $state,
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                            ),
                    )
                    ->columnSpanFull(),

                TextEntry::make('target_type')
                    ->label('Target Type')
                    ->badge(),

                TextEntry::make('target_value')
                    ->label('Target Value')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('status')
                    ->badge(),

                TextEntry::make('scheduled_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('sent_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('creator.name')
                    ->label('Created By')
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}