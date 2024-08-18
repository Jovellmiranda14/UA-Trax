<?php
namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Button;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;
use Filament\Actions;

class Login extends BaseAuth
{
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(), 
                $this->getPasswordFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): \Filament\Forms\Components\TextInput 
    {
        return TextInput::make('Email')
            ->label('Email')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): \Filament\Forms\Components\TextInput
    {
        return TextInput::make('password')
            ->label('Password')
            ->required()
            ->password()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            Actions\Action::make('clear')
            ->action(function () {
                $this->form->fill(); 
                }) ,
            ],
        );
    }
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
    protected function getCredentialsFromFormData(array $data): array
    { 
        return [
            'email' => $data['email'],
            'password'  => $data['password'],
        ];
    }
}
