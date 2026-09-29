<?php

namespace App\Filament\Resources\Applications\RelationManagers;

use App\Actions\Application\CreateApplicationTokenAction;
use App\Actions\Application\RevokeApplicationTokenAction;
use App\Data\ApplicationTokenData;
use App\Enums\ApplicationTokenAbility;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Laravel\Sanctum\PersonalAccessToken;

class TokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    protected static ?string $title = 'API Tokens';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('abilities')
                    ->formatStateUsing(
                        fn (mixed $state): string => is_array($state)
                            ? implode(', ', $state)
                            : (string) $state,
                    )
                    ->wrap(),

                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('issueToken')
                    ->label('Issue API Token')
                    ->icon('heroicon-o-key')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        CheckboxList::make('abilities')
                            ->options(ApplicationTokenAbility::options())
                            ->default([
                                ApplicationTokenAbility::APPLICATION_READ->value,
                            ])
                            ->required()
                            ->columns(1),

                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->minDate(now()),
                    ])
                    ->action(function (array $data): void {
                        $token = app(CreateApplicationTokenAction::class)->handle(
                            application: $this->getOwnerRecord(),
                            data: new ApplicationTokenData(
                                name: $data['name'],
                                abilities: $data['abilities'],
                                expiresAt: isset($data['expires_at'])
                                    ? new \DateTimeImmutable($data['expires_at'])
                                    : null,
                            ),
                        );

                        Notification::make()
                            ->success()
                            ->title('API Token Generated')
                            ->body($token->plainTextToken)
                            ->persistent()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (PersonalAccessToken $record): void {
                        app(RevokeApplicationTokenAction::class)->handle(
                            application: $this->getOwnerRecord(),
                            token: $record,
                        );

                        Notification::make()
                            ->success()
                            ->title('API Token Revoked')
                            ->send();
                    }),
            ]);
    }
}
