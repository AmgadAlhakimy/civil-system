<?php

namespace App\Filament\Resources\BirthCertificates\Schemas;

use App\Models\Citizen;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BirthCertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('بيانات شهادة الميلاد')
                    ->description('المعلومات الأساسية لشهادة الميلاد')
                    ->icon('heroicon-o-document-text')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('child_id')
                                    ->label('الطفل')
                                    ->relationship(
                                        name: 'child',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: fn ($query) => $query
                                            ->where('verification_status', 'approved')
                                            ->orderBy('first_name')
                                            ->orderBy('father_name')
                                            ->orderBy('middle_name')
                                            ->orderBy('last_name')
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn ($record) => collect([
                                            $record->first_name,
                                            $record->father_name,
                                            $record->middle_name,
                                            $record->last_name,
                                        ])
                                            ->filter()
                                            ->join(' ')
                                    )
                                    ->searchable([
                                        'first_name',
                                        'father_name',
                                        'middle_name',
                                        'last_name',
                                    ])
                                    ->searchPrompt('ابحث باسم الطفل')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (Set $set) => $set('father_id', null)
                                    )
                                    ->disabled(
                                        fn (string $operation): bool => $operation === 'edit'
                                    )
                                    ->dehydrated()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الطفل',
                                        'exists' => 'الطفل المحدد غير صالح',
                                    ]),

                                Select::make('branch_id')
                                    ->label('جهة الإصدار')
                                    ->relationship(
                                        name: 'branch',
                                        titleAttribute: 'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الفرع المُصدِر للشهادة',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('بيانات الوالدين')
                    ->description('بيانات والد ووالدة الطفل')
                    ->icon('heroicon-o-users')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('father_id')
                                    ->label('الأب')
                                    ->relationship(
                                        name: 'father',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query
                                                ->where('verification_status', 'approved')
                                                ->where('gender', 'male');

                                            if (! $childId) {
                                                return $query->whereRaw('1 = 0');
                                            }

                                            $child = Citizen::query()
                                                ->select([
                                                    'id',
                                                    'father_name',
                                                    'middle_name',
                                                ])
                                                ->find($childId);

                                            if (
                                                ! $child ||
                                                blank($child->father_name) ||
                                                blank($child->middle_name)
                                            ) {
                                                return $query->whereRaw('1 = 0');
                                            }

                                            return $query
                                                ->where('id', '!=', $child->id)
                                                ->where('first_name', $child->father_name)
                                                ->where('father_name', $child->middle_name)
                                                ->orderBy('first_name')
                                                ->orderBy('father_name')
                                                ->orderBy('middle_name')
                                                ->orderBy('last_name');
                                        },
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn ($record) => collect([
                                            $record->first_name,
                                            $record->father_name,
                                            $record->middle_name,
                                            $record->last_name,
                                        ])
                                            ->filter()
                                            ->join(' ')
                                    )
                                    ->searchable([
                                        'first_name',
                                        'father_name',
                                        'middle_name',
                                        'last_name',
                                    ])
                                    ->searchPrompt('ابحث باسم الأب')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->rules(
                                        fn (Get $get): array => [
                                            function (
                                                string $attribute,
                                                       $value,
                                                Closure $fail
                                            ) use ($get): void {

                                                $child = Citizen::query()
                                                    ->find($get('child_id'));

                                                $father = Citizen::query()
                                                    ->find($value);

                                                if (! $child || ! $father) {
                                                    $fail('تعذر التحقق من بيانات الطفل أو الأب.');
                                                    return;
                                                }

                                                if (
                                                    $father->verification_status !== 'approved' ||
                                                    $father->gender !== 'male' ||
                                                    $father->id == $child->id ||
                                                    $father->first_name !== $child->father_name ||
                                                    $father->father_name !== $child->middle_name
                                                ) {
                                                    $fail(
                                                        'اسم الأب غير مطابق لبيانات الطفل. يجب أن يتطابق الاسم الأول والثاني للأب مع اسم الأب واسم الجد المسجلين في بيانات الطفل.'
                                                    );
                                                }
                                            },
                                        ]
                                    )
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأب',
                                        'exists' => 'الأب المحدد غير صالح',
                                    ]),

                                Select::make('mother_id')
                                    ->label('الأم')
                                    ->relationship(
                                        name: 'mother',
                                        titleAttribute: 'first_name',
                                        modifyQueryUsing: function ($query, $get) {
                                            $childId = $get('child_id');

                                            $query
                                                ->where('verification_status', 'approved')
                                                ->where('gender', 'female');

                                            if ($childId) {
                                                $query->where('id', '!=', $childId);
                                            }

                                            return $query
                                                ->orderBy('first_name')
                                                ->orderBy('father_name')
                                                ->orderBy('middle_name')
                                                ->orderBy('last_name');
                                        },
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn ($record) => collect([
                                            $record->first_name,
                                            $record->father_name,
                                            $record->middle_name,
                                            $record->last_name,
                                        ])
                                            ->filter()
                                            ->join(' ')
                                    )
                                    ->searchable([
                                        'first_name',
                                        'father_name',
                                        'middle_name',
                                        'last_name',
                                    ])
                                    ->searchPrompt('ابحث باسم الأم')
                                    ->searchDebounce(500)
                                    ->optionsLimit(50)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->validationMessages([
                                        'required' => 'يرجى اختيار الأم',
                                        'exists' => 'الأم المحددة غير صالحة',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('معلومات إضافية')
                    ->description('ملاحظات وبيانات إضافية مرتبطة بالشهادة')
                    ->icon('heroicon-o-information-circle')
                    ->schema([

                        Grid::make(1)
                            ->schema([
                                Textarea::make('notes')
                                    ->label('ملاحظات')
                                    ->rows(4)
                                    ->maxLength(1000),
                            ]),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
