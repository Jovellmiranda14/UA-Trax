<?php
namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                ->email()
                ->rules([
                    'required',
                    'email',
                    Rule::exists('users', 'email'),
                ]),
                // ->helperText('Please provide a valid email address.'),
                $this->getPasswordFormComponent(),
                // $this->getRememberMeFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): \Filament\Forms\Components\TextInput 
    {
        return TextInput::make('email')
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
            ->revealable(true)
            ->required()
            ->password()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberMeFormComponent(): \Filament\Forms\Components\Checkbox
    {
        return Checkbox::make('remember')
            ->label('Remember Me')
            ->extraInputAttributes(['tabindex' => 3]);
    }

    protected function getFormActions(): array
    {
        return parent::getFormActions();
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
            // 'remember' => $data['remember'] ?? false, 
        ];
    }
}