<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acesso')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('E-mail de login')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Deixe em branco para manter a senha atual (ao editar).'),

                        Select::make('roles')
                            ->label('Perfil')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Contato do corretor (exibido nos anúncios)')
                    ->columns(2)
                    ->schema([
                        Select::make('locale')
                            ->label('Idioma preferido')
                            ->options([
                                'pt' => 'Português',
                                'en' => 'English',
                                'es' => 'Español',
                                'it' => 'Italiano',
                            ])
                            ->default('pt'),

                        TextInput::make('email_public')
                            ->label('E-mail público')
                            ->email(),

                        TextInput::make('phone')
                            ->label('Telefone'),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->helperText('Apenas dígitos com código do país. Ex.: 5573999998888'),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->url(),

                        TextInput::make('facebook')
                            ->label('Facebook')
                            ->url(),

                        TextInput::make('website')
                            ->label('Website')
                            ->url(),
                    ]),
            ]);
    }
}
