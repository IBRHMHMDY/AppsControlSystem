<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Application')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('package_name')
                        ->label('Package Name')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('platform')
                        ->default('android')
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    Textarea::make('description')
                        ->rows(4)
                        ->maxLength(2000)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Firebase')
                ->schema([
                    TextInput::make('firebase_project_id')
                        ->label('Firebase Project ID')
                        ->maxLength(255),
                ]),

            Section::make('Version')
                ->schema([
                    TextInput::make('current_version')
                        ->label('Current Version')
                        ->maxLength(50),

                    TextInput::make('current_build_number')
                        ->label('Current Build Number')
                        ->numeric()
                        ->integer()
                        ->minValue(1),
                ])
                ->columns(2),
        ]);
    }
}
