<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ChangePassword extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'change-password';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'تغيير كلمة المرور';

    protected string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('تغيير كلمة المرور')
                    ->description('يجب تغيير كلمة المرور المؤقتة قبل متابعة استخدام النظام.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('off')
                            ->validationMessages([
                                'required' => 'يرجى إدخال كلمة المرور الحالية يدويًا.',
                            ]),

                        TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(128)
                            ->rules([
                                'string',
                                'different:data.current_password',
                                Password::min(8)
                                    ->letters()
                                    ->numbers(),
                            ])
                            ->validationMessages([
                                'required' => 'يرجى إدخال كلمة المرور الجديدة.',
                                'min' => 'يجب ألا تقل كلمة المرور الجديدة عن 8 أحرف.',
                                'max' => 'يجب ألا تتجاوز كلمة المرور الجديدة 128 حرفًا.',
                                'different' => 'يجب أن تختلف كلمة المرور الجديدة عن الحالية.',
                            ])
                            ->autocomplete('new-password'),

                        TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('new-password')
                            ->validationMessages([
                                'required' => 'يرجى تأكيد كلمة المرور الجديدة.',
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $validated = $this->form->getState();

        $user = auth()->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'data.current_password' => 'انتهت جلسة تسجيل الدخول، يرجى تسجيل الدخول مجددًا.',
            ]);
        }

        $currentPassword = $validated['current_password'] ?? '';
        $password = $validated['password'] ?? '';
        $confirmation = $validated['password_confirmation'] ?? '';

        if (! is_string($currentPassword) || $currentPassword === '') {
            throw ValidationException::withMessages([
                'data.current_password' => 'يرجى إدخال كلمة المرور الحالية.',
            ]);
        }

        if (! Hash::check($currentPassword, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'data.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ]);
        }

        if (
            ! is_string($password) ||
            ! is_string($confirmation) ||
            $password !== $confirmation
        ) {
            throw ValidationException::withMessages([
                'data.password_confirmation' => 'تأكيد كلمة المرور غير مطابق لكلمة المرور الجديدة.',
            ]);
        }

        if ($password === $currentPassword) {
            throw ValidationException::withMessages([
                'data.password' => 'يجب أن تختلف كلمة المرور الجديدة عن الحالية.',
            ]);
        }

        $user->password = $password;
        $user->must_change_password = false;
        $user->save();

        request()->session()->regenerate();

        Notification::make()
            ->title('تم تغيير كلمة المرور بنجاح')
            ->success()
            ->send();

        $this->redirect(filament()->getCurrentPanel()->getUrl());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
