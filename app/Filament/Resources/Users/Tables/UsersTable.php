<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('profile_photo')
                    ->label('الصورة')
                    ->getStateUsing(
                        fn ($record) => $record->profile_photo
                            ? route(
                                'users.profile-photo',
                                [
                                    'path' => basename($record->profile_photo),
                                ]
                            )
                            : null
                    )
                    ->circular()
                    ->size(40)
                    ->placeholder('لا توجد')
                    ->alignCenter(),

                TextColumn::make('name')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user'),

                TextColumn::make('full_name')
                    ->label('الاسم الكامل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ البريد الإلكتروني')
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف')
                    ->icon('heroicon-o-phone'),

                TextColumn::make('branch.name')
                    ->label('الفرع')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('roles.name')
                    ->label('الدور')
                    ->badge()
                    ->searchable()
                    ->placeholder('بدون دور'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'active' => 'نشط',
                            'inactive' => 'غير نشط',
                            'blocked' => 'محظور',
                            default => $state,
                        }
                    )
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('لم يسجل دخول')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_login_ip')
                    ->label('عنوان IP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ عنوان IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('تاريخ الحذف')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'blocked' => 'محظور',
                    ]),

                SelectFilter::make('roles')
                    ->label('الدور')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make()
                    ->label('سلة المحذوفات'),
            ])

            ->recordActions([

                ViewAction::make()
                    ->label('عرض')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),
            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('حذف المستخدمين المحددين')
                        ->modalDescription(
                            'هل أنت متأكد من حذف المستخدمين المحددين؟ يمكن استعادتها من سلة المحذوفات.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف'),

                    RestoreBulkAction::make()
                        ->label('استعادة المحدد')
                        ->requiresConfirmation()
                        ->modalHeading('استعادة المستخدمين')
                        ->modalDescription(
                            'هل أنت متأكد من استعادة المستخدمين المحددين؟'
                        )
                        ->modalSubmitActionLabel('نعم، استعادة'),

                    ForceDeleteBulkAction::make()
                        ->label('حذف نهائي')
                        ->requiresConfirmation()
                        ->modalHeading('حذف نهائي')
                        ->modalDescription(
                            'تحذير: سيتم حذف المستخدمين نهائيًا ولا يمكن استعادتها.'
                        )
                        ->modalSubmitActionLabel('نعم، حذف نهائي'),

                ])
                    ->label('إجراءات جماعية'),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
