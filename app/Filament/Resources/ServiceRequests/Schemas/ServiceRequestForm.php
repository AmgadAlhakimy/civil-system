<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Models\Branch;
use App\Models\ServiceRequest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ═══════════════════════════════════════════════
                // معلومات الطلب (للقراءة فقط)
                // ═══════════════════════════════════════════════
                Section::make('معلومات الطلب')
                    ->description('بيانات الطلب الأساسية (للقراءة فقط)')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        TextInput::make('request_number')
                            ->label('رقم الطلب')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('service_name')
                            ->label('الخدمة')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('title')
                            ->label('العنوان')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ═══════════════════════════════════════════════
                // الحالة والمراجعة
                // ═══════════════════════════════════════════════
                Section::make('الحالة والمراجعة')
                    ->description('يمكن تعديل حالة الطلب والملاحظات هنا')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Select::make('status')
                            ->label('حالة الطلب')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المراجعة',
                                'approved' => 'مقبول',
                                'rejected' => 'مرفوض',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending'),

                        Select::make('payment_status')
                            ->label('حالة الدفع')
                            ->options([
                                'pending' => 'بانتظار الدفع',
                                'paid' => 'مدفوع',
                                'failed' => 'فشل',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending'),

                        Select::make('branch_id')
                            ->label('الفرع')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('اختر الفرع'),

                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض (إن وُجد)')
                            ->placeholder('اذكر سبب رفض الطلب...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}