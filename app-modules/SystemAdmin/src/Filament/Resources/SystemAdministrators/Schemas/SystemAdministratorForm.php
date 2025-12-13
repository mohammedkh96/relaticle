<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\SystemAdministrators\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Relaticle\SystemAdmin\Enums\SystemAdministratorRole;

final class SystemAdministratorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Administrator Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('role')
                            ->options(
                                collect(SystemAdministratorRole::cases())
                                    ->mapWithKeys(fn(SystemAdministratorRole $role): array => [
                                        $role->value => $role->getLabel(),
                                    ])
                            )
                            ->default(SystemAdministratorRole::SuperAdministrator->value)
                            ->required(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->displayFormat('M j, Y \a\t g:i A')
                            ->nullable(),

                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->confirmed()
                            ->helperText(fn(string $context): ?string => $context === 'edit' ? 'Leave blank to keep current password' : null),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn(string $context, Get $get): bool => $context === 'create' || filled($get('password')))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Permissions')
                    ->description('Select granular permissions for each resource.')
                    ->hidden(fn(Get $get): bool => $get('role') === SystemAdministratorRole::SuperAdministrator->value)
                    ->columns(3)
                    ->schema([
                        self::makeResourcePermissions('Companies', 'companies'),
                        self::makeResourcePermissions('People', 'people'),
                        self::makeResourcePermissions('Invoices', 'invoices'),
                        self::makeResourcePermissions('Payments', 'payments'),
                        self::makeResourcePermissions('Events', 'events'),
                        self::makeResourcePermissions('Opportunities', 'opportunities'),
                        self::makeResourcePermissions('Tasks', 'tasks'),
                        self::makeResourcePermissions('Participations', 'participations'),
                        self::makeResourcePermissions('Notes', 'notes'),
                        self::makeResourcePermissions('App Users', 'users'),
                    ]),
            ]);
    }

    private static function makeResourcePermissions(string $label, string $resource): \Filament\Schemas\Components\Component
    {
        return Section::make($label)
            ->schema([
                \Filament\Forms\Components\CheckboxList::make('permissions')
                    ->hiddenLabel()
                    ->options([
                        "view_{$resource}" => 'View',
                        "create_{$resource}" => 'Create',
                        "edit_{$resource}" => 'Edit',
                        "delete_{$resource}" => 'Delete',
                    ])
                    ->bulkToggleable()
                    ->columns(2),
            ])
            ->collapsible()
            ->compact();
    }
}
