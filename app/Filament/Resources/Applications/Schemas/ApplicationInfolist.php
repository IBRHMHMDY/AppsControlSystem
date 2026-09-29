<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Application Name'),

                TextEntry::make('slug'),

                TextEntry::make('package_name')
                    ->label('Package Name'),

                TextEntry::make('platform')
                    ->badge(),

                TextEntry::make('status')
                    ->badge(),

                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('firebase_project_id')
                    ->label('Firebase Project ID')
                    ->placeholder('-'),

                TextEntry::make('current_version')
                    ->label('Current Version')
                    ->placeholder('-'),

                TextEntry::make('current_build_number')
                    ->label('Current Build Number')
                    ->numeric()
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
