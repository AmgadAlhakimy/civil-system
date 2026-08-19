<x-filament-panels::page>
    <div class="civil-dashboard" dir="rtl">



        <section class="civil-dashboard-stats">

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>إجمالي المواطنين</span>
                    <strong>{{ number_format(\App\Models\Citizen::count()) }}</strong>
                    <small>إجمالي السجلات المسجلة</small>
                </div>

                <div class="civil-stat-icon blue">
                    <x-heroicon-o-users />
                </div>
            </div>

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>المواطنون النشطون</span>
                    <strong>{{ number_format(\App\Models\Citizen::where('is_active', true)->count()) }}</strong>
                    <small>سجلات نشطة</small>
                </div>

                <div class="civil-stat-icon green">
                    <x-heroicon-o-user-group />
                </div>
            </div>

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>بطاقات الهوية</span>
                    <strong>{{ number_format(\App\Models\IdentityCard::count()) }}</strong>
                    <small>بطاقات مسجلة</small>
                </div>

                <div class="civil-stat-icon indigo">
                    <x-heroicon-o-identification />
                </div>
            </div>

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>الجوازات</span>
                    <strong>{{ number_format(\App\Models\Passport::count()) }}</strong>
                    <small>جوازات مسجلة</small>
                </div>

                <div class="civil-stat-icon purple">
                    <x-heroicon-o-document-text />
                </div>
            </div>

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>شهادات الميلاد</span>
                    <strong>{{ number_format(\App\Models\BirthCertificate::count()) }}</strong>
                    <small>شهادات مسجلة</small>
                </div>

                <div class="civil-stat-icon orange">
                    <x-heroicon-o-document-plus />
                </div>
            </div>

            <div class="civil-stat-card">
                <div class="civil-stat-info">
                    <span>شهادات الوفاة</span>
                    <strong>{{ number_format(\App\Models\DeathCertificate::count()) }}</strong>
                    <small>شهادات مسجلة</small>
                </div>

                <div class="civil-stat-icon red">
                    <x-heroicon-o-document-minus />
                </div>
            </div>

        </section>

        <section class="civil-dashboard-charts">

            <div class="civil-dashboard-card chart-transactions civil-transactions-card">
                <div class="civil-line-chart-area">
                    @livewire(
                    \App\Filament\Widgets\TransactionsChart::class
                    )
                </div>
            </div>

            <div class="civil-dashboard-card chart-medium civil-doughnut-card">
                <div class="civil-doughnut-chart-area">
                    @livewire(
                    \App\Filament\Widgets\GenderChart::class
                    )
                </div>
            </div>

            <div class="civil-dashboard-card chart-medium civil-age-card">
                <div class="civil-age-chart-area">
                    @livewire(
                    \App\Filament\Widgets\AgeDistributionChart::class
                    )
                </div>
            </div>

        </section>

        <section class="civil-dashboard-lists">
            <div class="civil-dashboard-card operations-card">

                <div class="civil-list-header">
                    <h2>آخر العمليات</h2>
                    <span>أحدث النشاطات</span>
                </div>

                <div class="civil-operations-list">

                    @foreach(\Spatie\Activitylog\Models\Activity::latest()->take(5)->get() as $activity)

                        <div class="civil-operation-item">

                            <div class="civil-operation-info">
                                <strong>
                                    {{ $activity->causer?->name ?? 'النظام' }}
                                </strong>

                                <span>
                        @switch($activity->event)
                                        @case('created')
                                            إضافة سجل
                                            @break

                                        @case('updated')
                                            تعديل سجل
                                            @break

                                        @case('deleted')
                                            حذف سجل
                                            @break

                                        @default
                                            {{ $activity->event }}
                                    @endswitch
                    </span>
                            </div>

                            <div class="civil-operation-meta">
                    <span>
                        {{ $activity->created_at?->format('Y-m-d H:i') }}
                    </span>
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            <div class="civil-dashboard-card citizens-card">

                <div class="civil-list-header">
                    <h2>آخر المواطنين المضافين</h2>
                    <span>أحدث السجلات</span>
                </div>

                <div class="civil-citizens-list">

                    @foreach(\App\Models\Citizen::latest()->take(5)->get() as $citizen)

                        <div class="civil-citizen-item">

                            <div class="civil-citizen-info">
                                <strong>
                                    {{ $citizen->first_name }}
                                    {{ $citizen->middle_name }}
                                    {{ $citizen->last_name }}
                                </strong>

                                <span>
                        الرقم الوطني:
                        {{ $citizen->national_id }}
                    </span>
                            </div>

                            <div class="civil-citizen-meta">

                    <span>
                        {{ $citizen->created_at?->format('Y-m-d') }}
                    </span>

                                <span class="civil-citizen-status {{ $citizen->is_active ? 'active' : 'inactive' }}">
                        {{ $citizen->is_active ? 'نشط' : 'غير نشط' }}
                    </span>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            <div class="civil-dashboard-card activities-card">

                <div class="civil-list-header">
                    <h2>النشاطات الأخيرة</h2>
                    <span>آخر النشاطات</span>
                </div>

                <div class="civil-activities-list">

                    @foreach(\Spatie\Activitylog\Models\Activity::latest()->take(5)->get() as $activity)

                        <div class="civil-activity-item">

                            <div class="civil-activity-icon">
                                @if($activity->event === 'created')
                                    <x-heroicon-o-plus />
                                @elseif($activity->event === 'updated')
                                    <x-heroicon-o-pencil />
                                @elseif($activity->event === 'deleted')
                                    <x-heroicon-o-trash />
                                @else
                                    <x-heroicon-o-information-circle />
                                @endif
                            </div>

                            <div class="civil-activity-info">
                                <strong>
                                    {{ $activity->causer?->name ?? 'النظام' }}
                                </strong>

                                <span>
                        @switch($activity->event)
                                        @case('created')
                                            تمت إضافة سجل
                                            @break

                                        @case('updated')
                                            تم تعديل سجل
                                            @break

                                        @case('deleted')
                                            تم حذف سجل
                                            @break

                                        @default
                                            {{ $activity->event }}
                                    @endswitch
                    </span>
                            </div>

                            <div class="civil-activity-time">
                                {{ $activity->created_at?->diffForHumans() }}
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </section>

        <section class="civil-dashboard-bottom">

            <div class="civil-bottom-card">
                <div class="civil-bottom-info">
                    <span>الفروع</span>
                    <strong>{{ number_format(\App\Models\Branch::count()) }}</strong>
                </div>

                <div class="civil-stat-icon blue">
                    <x-heroicon-o-building-office-2 />
                </div>
            </div>
            <div class="civil-bottom-card">
                <div class="civil-bottom-info">
                    <span>الفروع النشطة</span>
                    <strong>{{ number_format(\App\Models\Branch::where('is_active', true)->count()) }}</strong>
                </div>

                <div class="civil-stat-icon green">
                    <x-heroicon-o-building-office-2 />
                </div>
            </div>

            <div class="civil-bottom-card">
                <div class="civil-bottom-info">
                    <span>الموظفون</span>
                    <strong>{{ number_format(\App\Models\User::count()) }}</strong>
                </div>

                <div class="civil-stat-icon purple">
                    <x-heroicon-o-users />
                </div>
            </div>

            <div class="civil-bottom-card">
                <div class="civil-bottom-info">
                    <span>التاريخ</span>
                    <strong>{{ now()->translatedFormat('d F Y') }}</strong>
                    <small>{{ now()->format('H:i') }}</small>
                </div>

                <div class="civil-stat-icon orange">
                    <x-heroicon-o-clock />
                </div>
            </div>

        </section>

    </div>
</x-filament-panels::page>
