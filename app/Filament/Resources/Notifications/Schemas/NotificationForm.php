<?php

namespace App\Filament\Resources\Notifications\Schemas;

use App\Enums\NotificationTargetType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('application_id')
                    ->label('Application')
                    ->relationship('application', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('body')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->image()
                    ->imageEditor(),

                KeyValue::make('data_payload')
                    ->label('Data Payload')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->columnSpanFull(),

                Select::make('target_type')
                    ->label('Target Type')
                    ->options(NotificationTargetType::options())
                    ->required()
                    ->live(),

                Textarea::make('target_value')
                    ->label('Target Value')
                    ->rows(3)
                    ->required(fn (Get $get): bool => filled($get('target_type')))
                    ->visible(fn (Get $get): bool => filled($get('target_type')))
                    ->columnSpanFull(),

                DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->seconds(false)
                    ->nullable(),
            ]);
    }
}