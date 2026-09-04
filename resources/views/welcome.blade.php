<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>السجل المدني | البوابة الإلكترونية</title>

    {{-- الخط --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    @php
        use App\Services\SettingService;
        use App\Support\Themes;

        $settings = app(SettingService::class);

        /*
        |--------------------------------------------------------------------------
        | الثيم الحالي
        |--------------------------------------------------------------------------
        | السماوي هو الافتراضي.
        */

        $themeKey = $settings->get('theme', 'cyan');

        $themes = Themes::all();

        $currentTheme = $themes[$themeKey] ?? $themes['cyan'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | الحصول على ألوان الثيم
        |--------------------------------------------------------------------------
        */

        $primary = $currentTheme['css']['primary'] ?? '#0891b2';
        $primaryDark = $currentTheme['css']['primary-dark'] ?? '#0e7490';
        $primaryLight = $currentTheme['css']['primary-light'] ?? '#22d3ee';

        /*
        |--------------------------------------------------------------------------
        | لون الخلفية الداكنة للواجهة
        |--------------------------------------------------------------------------
        */

        $dark = $currentTheme['css']['sidebar'] ?? '#0f172a';

        /*
        |--------------------------------------------------------------------------
        | درجات اللون الأساسي
        |--------------------------------------------------------------------------
        */

        $primary50 = $currentTheme['css']['primary-50'] ?? '#ecfeff';
        $primary100 = $currentTheme['css']['primary-100'] ?? '#cffafe';
        $primary200 = $currentTheme['css']['primary-200'] ?? '#a5f3fc';
        $primary300 = $currentTheme['css']['primary-300'] ?? '#67e8f9';
        $primary400 = $currentTheme['css']['primary-400'] ?? '#22d3ee';
        $primary500 = $currentTheme['css']['primary-500'] ?? $primary;
        $primary600 = $currentTheme['css']['primary-600'] ?? $primary;
        $primary700 = $currentTheme['css']['primary-700'] ?? $primaryDark;
        $primary800 = $currentTheme['css']['primary-800'] ?? '#155e75';
        $primary900 = $currentTheme['css']['primary-900'] ?? '#164e63';
    @endphp

    <style>
        :root {
            --app-primary: {{ $primary }};
            --app-primary-dark: {{ $primaryDark }};
            --app-primary-light: {{ $primaryLight }};

            --app-primary-50: {{ $primary50 }};
            --app-primary-100: {{ $primary100 }};
            --app-primary-200: {{ $primary200 }};
            --app-primary-300: {{ $primary300 }};
            --app-primary-400: {{ $primary400 }};
            --app-primary-500: {{ $primary500 }};
            --app-primary-600: {{ $primary600 }};
            --app-primary-700: {{ $primary700 }};
            --app-primary-800: {{ $primary800 }};
            --app-primary-900: {{ $primary900 }};

            --app-dark: {{ $dark }};
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {

                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },

                    colors: {

                        primary: {
                            50: 'var(--app-primary-50)',
                            100: 'var(--app-primary-100)',
                            200: 'var(--app-primary-200)',
                            300: 'var(--app-primary-300)',
                            400: 'var(--app-primary-400)',
                            500: 'var(--app-primary-500)',
                            600: 'var(--app-primary-600)',
                            700: 'var(--app-primary-700)',
                            800: 'var(--app-primary-800)',
                            900: 'var(--app-primary-900)',
                        },

                    }
                }
            }
        }
    </script>

</head>


<body class="bg-slate-50 text-slate-800 font-sans antialiased">


{{-- =========================================================
    NAVBAR
========================================================= --}}

<header
    class="sticky top-0 z-50 border-b border-white/10"
    style="background-color: var(--app-dark);"
>

    <div class="max-w-7xl mx-auto px-6">

        <div class="h-20 flex items-center justify-between">

            {{-- الشعار --}}

            <div class="flex items-center gap-4">

                <div
                    class="w-11 h-11 rounded-xl
                           flex items-center justify-center
                           shadow-lg"
                    style="background-color: var(--app-primary);"
                >

                    <svg
                        class="w-6 h-6 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>

                </div>

                <div>

                    <h1 class="text-white text-lg font-bold">
                        السجل المدني
                    </h1>

                    <p class="text-slate-400 text-xs">
                        البوابة الإلكترونية
                    </p>

                </div>

            </div>


            {{-- زر الدخول --}}

            @auth

                <a
                    href="{{ url('/dashboard') }}"
                    class="flex items-center gap-2
                           text-white
                           px-5 py-2.5
                           rounded-lg
                           font-semibold text-sm
                           transition
                           shadow-md
                           hover:opacity-90"
                    style="background-color: var(--app-primary);"
                >

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"
                        />
                    </svg>

                    لوحة التحكم

                </a>

            @else

                <a
                    href="{{ url('/dashboard/login') }}"
                    class="flex items-center gap-2
                           text-white
                           px-5 py-2.5
                           rounded-lg
                           font-semibold text-sm
                           transition
                           shadow-md
                           hover:opacity-90"
                    style="background-color: var(--app-primary);"
                >

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4m-5-4l5-5-5-5m5 5H3"
                        />
                    </svg>

                    تسجيل الدخول

                </a>

            @endauth

        </div>

    </div>

</header>



{{-- =========================================================
    HERO
========================================================= --}}

<section
    class="relative overflow-hidden"
    style="background-color: var(--app-dark);"
>

    {{-- زخرفة خلفية --}}

    <div
        class="absolute top-0 right-1/2
               w-[500px] h-[500px]
               rounded-full blur-3xl
               translate-x-1/2
               -translate-y-1/2
               opacity-20"
        style="background-color: var(--app-primary);"
    ></div>


    <div
        class="absolute bottom-0 left-0
               w-[400px] h-[400px]
               rounded-full blur-3xl
               -translate-x-1/3
               translate-y-1/3
               opacity-10"
        style="background-color: var(--app-primary-light);"
    ></div>


    {{-- المحتوى --}}

    <div
        class="relative max-w-5xl mx-auto
               px-6 py-6 md:py-10
               text-center"
    >

        {{-- الشعار --}}

        <div class="flex justify-center mb-7">

            <div
                class="w-20 h-20
                       rounded-2xl
                       flex items-center justify-center
                       shadow-2xl
                       ring-8 ring-white/5"
                style="background-color: var(--app-primary);"
            >

                <svg
                    class="w-10 h-10 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                    />
                </svg>

            </div>

        </div>


        {{-- الشارة --}}

        <div
            class="inline-flex items-center gap-2
                   px-4 py-2
                   rounded-full
                   bg-white/10
                   border border-white/10
                   text-sm font-semibold
                   mb-6"
            style="color: var(--app-primary-400);"
        >

            <span
                class="w-2 h-2
                       rounded-full
                       animate-pulse"
                style="background-color: var(--app-primary-400);"
            ></span>

            منظومة إلكترونية متكاملة

        </div>


        {{-- العنوان --}}

        <h2
            class="text-4xl
                   md:text-5xl
                   lg:text-6xl
                   font-extrabold
                   text-white
                   leading-tight
                   mb-7"
        >

            بوابة

            <span style="color: var(--app-primary-400);">
                السجل المدني
            </span>

            <br>

            <span class="text-white">
                لإدارة البيانات المدنية
            </span>

        </h2>


        {{-- الوصف --}}

        <p
            class="max-w-3xl mx-auto
                   text-lg
                   md:text-xl
                   text-slate-300
                   leading-relaxed
                   mb-10"
        >
            منظومة رقمية حديثة لإدارة وتنظيم البيانات المدنية
            وتسهيل الإجراءات والخدمات المرتبطة بالسجل المدني
            بطريقة آمنة وموثوقة.
        </p>


        {{-- الأزرار --}}

        <div
            class="flex flex-col
                   sm:flex-row
                   justify-center
                   items-center
                   gap-4
                   mb-12"
        >

            @auth

                <a
                    href="{{ url('/dashboard') }}"
                    class="inline-flex
                           items-center
                           justify-center
                           gap-2
                           min-w-52
                           text-white
                           px-7 py-3.5
                           rounded-xl
                           font-bold
                           transition
                           shadow-lg
                           hover:opacity-90"
                    style="background-color: var(--app-primary);"
                >

                    الدخول إلى لوحة التحكم

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 12h14m-6-6l6 6-6 6"
                        />
                    </svg>

                </a>

            @else

                <a
                    href="{{ url('/dashboard/login') }}"
                    class="inline-flex
                           items-center
                           justify-center
                           gap-2
                           min-w-52
                           text-white
                           px-7 py-3.5
                           rounded-xl
                           font-bold
                           transition
                           shadow-lg
                           hover:opacity-90"
                    style="background-color: var(--app-primary);"
                >

                    الدخول إلى النظام

                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 12h14m-6-6l6 6-6 6"
                        />
                    </svg>

                </a>

            @endauth


            <a
                href="#services"
                class="inline-flex
                       items-center
                       justify-center
                       min-w-52
                       px-7 py-3.5
                       rounded-xl
                       font-semibold
                       text-white
                       border border-white/20
                       hover:bg-white/10
                       transition"
            >
                استكشف الخدمات
            </a>

        </div>

    </div>

</section>



{{-- =========================================================
    STATISTICS
========================================================= --}}

<section class="relative -mt-10 z-10">

    <div class="max-w-6xl mx-auto px-6">

        <div
            class="bg-white
                   rounded-2xl
                   shadow-xl
                   border border-slate-100
                   grid grid-cols-2
                   md:grid-cols-4
                   overflow-hidden"
        >

            <div
                class="p-7 text-center
                       border-b
                       md:border-b-0
                       md:border-l
                       border-slate-100"
            >

                <div
                    class="text-3xl
                           font-extrabold
                           mb-1"
                    style="color: var(--app-primary-900);"
                >
                    —
                </div>

                <div class="text-sm text-slate-500">
                    المواطنون
                </div>

            </div>


            <div
                class="p-7 text-center
                       border-b
                       md:border-b-0
                       md:border-l
                       border-slate-100"
            >

                <div
                    class="text-3xl
                           font-extrabold
                           mb-1"
                    style="color: var(--app-primary-900);"
                >
                    —
                </div>

                <div class="text-sm text-slate-500">
                    الفروع
                </div>

            </div>


            <div
                class="p-7 text-center
                       border-l
                       border-slate-100"
            >

                <div
                    class="text-3xl
                           font-extrabold
                           mb-1"
                    style="color: var(--app-primary-900);"
                >
                    —
                </div>

                <div class="text-sm text-slate-500">
                    المستخدمون
                </div>

            </div>


            <div class="p-7 text-center">

                <div
                    class="text-3xl
                           font-extrabold
                           mb-1"
                    style="color: var(--app-primary-900);"
                >
                    4+
                </div>

                <div class="text-sm text-slate-500">
                    الخدمات
                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    SERVICES
========================================================= --}}

<section
    id="services"
    class="py-24 bg-slate-50"
>

    <div class="max-w-7xl mx-auto px-6">

        {{-- العنوان --}}

        <div
            class="text-center
                   max-w-2xl
                   mx-auto
                   mb-14"
        >

            <span
                class="font-bold text-sm"
                style="color: var(--app-primary-600);"
            >
                خدمات المنظومة
            </span>

            <h2
                class="text-3xl
                       md:text-4xl
                       font-extrabold
                       mt-3 mb-5"
                style="color: var(--app-primary-900);"
            >
                منظومة متكاملة لإدارة السجل المدني
            </h2>

            <p
                class="text-slate-500
                       leading-relaxed"
            >
                توفر المنظومة مجموعة من الأدوات لإدارة البيانات
                والخدمات المدنية بكفاءة وتنظيم.
            </p>

        </div>


        {{-- الخدمات --}}

        <div
            class="grid
                   grid-cols-1
                   sm:grid-cols-2
                   lg:grid-cols-4
                   gap-6"
        >


            {{-- المواطنون --}}

            <div
                class="bg-white
                       rounded-2xl
                       p-7
                       border border-slate-100
                       shadow-sm
                       hover:shadow-xl
                       hover:-translate-y-1
                       transition duration-300"
            >

                <div
                    class="w-12 h-12
                           rounded-xl
                           flex items-center
                           justify-center
                           mb-6"
                    style="
                        background-color: var(--app-primary-50);
                        color: var(--app-primary-700);
                    "
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>

                </div>

                <h3
                    class="text-lg
                           font-bold
                           mb-3"
                    style="color: var(--app-primary-900);"
                >
                    إدارة المواطنين
                </h3>

                <p
                    class="text-sm
                           text-slate-500
                           leading-relaxed"
                >
                    إدارة بيانات المواطنين والملفات المدنية
                    بطريقة منظمة وآمنة.
                </p>

            </div>


            {{-- الفروع --}}

            <div
                class="bg-white
                       rounded-2xl
                       p-7
                       border border-slate-100
                       shadow-sm
                       hover:shadow-xl
                       hover:-translate-y-1
                       transition duration-300"
            >

                <div
                    class="w-12 h-12
                           rounded-xl
                           flex items-center
                           justify-center
                           mb-6"
                    style="
                        background-color: var(--app-primary-50);
                        color: var(--app-primary-700);
                    "
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 21h18M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16M9 7h2m-2 4h2m2-4h2m-2 4h2"
                        />
                    </svg>

                </div>

                <h3
                    class="text-lg
                           font-bold
                           mb-3"
                    style="color: var(--app-primary-900);"
                >
                    إدارة الفروع
                </h3>

                <p
                    class="text-sm
                           text-slate-500
                           leading-relaxed"
                >
                    إدارة فروع السجل المدني والموظفين
                    التابعين لكل فرع.
                </p>

            </div>


            {{-- الجوازات --}}

            <div
                class="bg-white
                       rounded-2xl
                       p-7
                       border border-slate-100
                       shadow-sm
                       hover:shadow-xl
                       hover:-translate-y-1
                       transition duration-300"
            >

                <div
                    class="w-12 h-12
                           rounded-xl
                           flex items-center
                           justify-center
                           mb-6"
                    style="
                        background-color: var(--app-primary-50);
                        color: var(--app-primary-700);
                    "
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>

                </div>

                <h3
                    class="text-lg
                           font-bold
                           mb-3"
                    style="color: var(--app-primary-900);"
                >
                    الجوازات
                </h3>

                <p
                    class="text-sm
                           text-slate-500
                           leading-relaxed"
                >
                    إدارة بيانات الجوازات والطلبات
                    والوثائق المرتبطة بها.
                </p>

            </div>


            {{-- الوثائق --}}

            <div
                class="bg-white
                       rounded-2xl
                       p-7
                       border border-slate-100
                       shadow-sm
                       hover:shadow-xl
                       hover:-translate-y-1
                       transition duration-300"
            >

                <div
                    class="w-12 h-12
                           rounded-xl
                           flex items-center
                           justify-center
                           mb-6"
                    style="
                        background-color: var(--app-primary-50);
                        color: var(--app-primary-700);
                    "
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <h3
                    class="text-lg
                           font-bold
                           mb-3"
                    style="color: var(--app-primary-900);"
                >
                    الوثائق والسجلات
                </h3>

                <p
                    class="text-sm
                           text-slate-500
                           leading-relaxed"
                >
                    تنظيم الوثائق والسجلات المدنية
                    والوصول إليها بسهولة.
                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    ABOUT
========================================================= --}}

<section class="py-24 bg-white">

    <div
        class="max-w-7xl mx-auto
               px-6
               grid lg:grid-cols-2
               gap-16
               items-center"
    >

        {{-- النص --}}

        <div>

            <span
                class="font-bold text-sm"
                style="color: var(--app-primary-600);"
            >
                عن المنظومة
            </span>

            <h2
                class="text-3xl
                       md:text-4xl
                       font-extrabold
                       mt-3 mb-6"
                style="color: var(--app-primary-900);"
            >
                إدارة حديثة للبيانات المدنية
            </h2>

            <p
                class="text-slate-600
                       leading-loose
                       mb-6"
            >
                تم تصميم منظومة السجل المدني لتوفير بيئة
                إلكترونية موحدة تساعد على إدارة البيانات المدنية
                وتنظيم العمليات اليومية للفروع والموظفين.
            </p>

            <p
                class="text-slate-600
                       leading-loose"
            >
                تعتمد المنظومة على تنظيم الصلاحيات وإدارة المستخدمين
                والفروع، مع توفير أدوات تساعد على الوصول إلى البيانات
                وإدارتها بكفاءة.
            </p>

        </div>


        {{-- المميزات --}}

        <div
            class="grid
                   grid-cols-1
                   sm:grid-cols-2
                   gap-5"
        >

            <div
                class="p-6
                       rounded-2xl
                       border border-slate-100"
                style="background-color: var(--app-primary-50);"
            >

                <div
                    class="mb-4"
                    style="color: var(--app-primary-600);"
                >

                    <svg
                        class="w-7 h-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>

                </div>

                <h3
                    class="font-bold mb-2"
                    style="color: var(--app-primary-900);"
                >
                    أمان البيانات
                </h3>

                <p class="text-sm text-slate-500">
                    حماية وتنظيم الوصول إلى بيانات النظام.
                </p>

            </div>


            <div
                class="p-6
                       rounded-2xl
                       border border-slate-100"
                style="background-color: var(--app-primary-50);"
            >

                <div
                    class="mb-4"
                    style="color: var(--app-primary-600);"
                >

                    <svg
                        class="w-7 h-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                    </svg>

                </div>

                <h3
                    class="font-bold mb-2"
                    style="color: var(--app-primary-900);"
                >
                    سرعة الوصول
                </h3>

                <p class="text-sm text-slate-500">
                    الوصول إلى المعلومات بطريقة سهلة ومنظمة.
                </p>

            </div>


            <div
                class="p-6
                       rounded-2xl
                       border border-slate-100"
                style="background-color: var(--app-primary-50);"
            >

                <div
                    class="mb-4"
                    style="color: var(--app-primary-600);"
                >

                    <svg
                        class="w-7 h-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

                <h3
                    class="font-bold mb-2"
                    style="color: var(--app-primary-900);"
                >
                    دقة البيانات
                </h3>

                <p class="text-sm text-slate-500">
                    تنظيم البيانات وتقليل الأخطاء في العمليات.
                </p>

            </div>


            <div
                class="p-6
                       rounded-2xl
                       border border-slate-100"
                style="background-color: var(--app-primary-50);"
            >

                <div
                    class="mb-4"
                    style="color: var(--app-primary-600);"
                >

                    <svg
                        class="w-7 h-7"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"
                        />
                    </svg>

                </div>

                <h3
                    class="font-bold mb-2"
                    style="color: var(--app-primary-900);"
                >
                    إدارة المستخدمين
                </h3>

                <p class="text-sm text-slate-500">
                    تنظيم المستخدمين والأدوار والصلاحيات.
                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    LOGIN CTA
========================================================= --}}

<section
    class="py-20"
    style="background-color: var(--app-dark);"
>

    <div
        class="max-w-4xl
               mx-auto
               px-6
               text-center"
    >

        <h2
            class="text-3xl
                   md:text-4xl
                   font-extrabold
                   text-white
                   mb-5"
        >
            جاهز للدخول إلى النظام؟
        </h2>

        <p
            class="text-slate-400
                   mb-8"
        >
            قم بتسجيل الدخول للوصول إلى لوحة التحكم وإدارة
            بيانات النظام.
        </p>


        @auth

            <a
                href="{{ url('/dashboard') }}"
                class="inline-flex
                       items-center
                       gap-2
                       text-white
                       px-8 py-3.5
                       rounded-lg
                       font-bold
                       transition
                       hover:opacity-90"
                style="background-color: var(--app-primary);"
            >
                فتح لوحة التحكم
            </a>

        @else

            <a
                href="{{ url('/dashboard/login') }}"
                class="inline-flex
                       items-center
                       gap-2
                       text-white
                       px-8 py-3.5
                       rounded-lg
                       font-bold
                       transition
                       hover:opacity-90"
                style="background-color: var(--app-primary);"
            >

                تسجيل الدخول للنظام

                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 12h14m-6-6l6 6-6 6"
                    />
                </svg>

            </a>

        @endauth

    </div>

</section>



{{-- =========================================================
    FOOTER
========================================================= --}}

<footer
    class="text-slate-400"
    style="background-color: #020617;"
>

    <div class="max-w-7xl mx-auto px-6 py-10">

        <div
            class="flex
                   flex-col
                   md:flex-row
                   justify-between
                   items-center
                   gap-5"
        >

            <div>

                <h3
                    class="text-white
                           font-bold
                           mb-1"
                >
                    السجل المدني
                </h3>

                <p class="text-sm">
                    نظام إدارة الأحوال المدنية
                </p>

            </div>


            <div
                class="text-sm
                       text-center
                       md:text-left"
            >

                <p>
                    &copy; {{ date('Y') }}
                    جميع الحقوق محفوظة.
                </p>

            </div>

        </div>

    </div>

</footer>


</body>

</html>
