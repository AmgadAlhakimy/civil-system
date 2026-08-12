<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بوابة الأحوال المدنية</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- إعدادات الخطوط والألوان الإضافية -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0f172a', // أزرق داكن رسمي
                        secondary: '#d97706', // ذهبي
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

<!-- الشريط العلوي (Navbar) -->
<header class="bg-primary shadow-lg border-b-4 border-secondary">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">

        <!-- الشعار -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-secondary rounded-lg flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-white text-xl font-bold tracking-wide">السجل المدني</h1>
                <p class="text-slate-300 text-xs">نظام إدارة الأحوال المدنية</p>
            </div>
        </div>

        <!-- روابط الدخول (محفوظة من كود Laravel الأصلي) -->
        @if (Route::has('login'))
            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-2 text-sm font-semibold text-primary bg-white hover:bg-slate-100 rounded-md shadow transition">
                        لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-white hover:text-secondary transition">
                        تسجيل الدخول
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold text-primary bg-secondary hover:bg-yellow-600 rounded-md shadow transition">
                            إنشاء حساب
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </div>
</header>

<!-- القسم الرئيسي (Hero) -->
<main class="flex-grow">
    <div class="bg-white">
        <div class="container mx-auto px-6 py-20 lg:py-28 text-center max-w-5xl">
            <span class="text-secondary font-bold tracking-wider mb-4 block">بوابة إلكترونية موثوقة</span>
            <h2 class="text-4xl md:text-6xl font-extrabold text-primary leading-tight mb-8">
                النظام الشامل لإدارة <br/> الأحوال المدنية
            </h2>
            <p class="text-lg md:text-xl text-slate-600 mb-12 leading-relaxed px-4 max-w-3xl mx-auto">
                منصة رقمية متطورة لتسهيل وتوثيق الإجراءات المدنية، وإدارة قواعد البيانات الوطنية بدقة وموثوقية عالية لضمان تقديم أفضل الخدمات للمواطنين.
            </p>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3 text-lg font-bold text-white bg-primary hover:bg-slate-800 rounded-md shadow-lg transition transform hover:-translate-y-1">
                        الانتقال إلى مساحة العمل
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-block px-8 py-3 text-lg font-bold text-white bg-primary hover:bg-slate-800 rounded-md shadow-lg transition transform hover:-translate-y-1">
                        تسجيل الدخول للنظام
                    </a>
                @endauth
            @endif
        </div>
    </div>

    <!-- الخدمات المميزة -->
    <div class="container mx-auto px-6 py-16 -mt-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- خدمة 1 -->
            <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-secondary hover:shadow-xl transition">
                <div class="w-12 h-12 bg-orange-50 text-secondary rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">البطاقات الشخصية</h3>
                <p class="text-slate-600 text-sm leading-relaxed">إدارة شاملة لطلبات الإصدار، التجديد، وبدل الفاقد للبطاقات الوطنية مع أرشفة رقمية للبيانات الحيوية.</p>
            </div>

            <!-- خدمة 2 -->
            <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-primary hover:shadow-xl transition">
                <div class="w-12 h-12 bg-slate-100 text-primary rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">السجل العائلي</h3>
                <p class="text-slate-600 text-sm leading-relaxed">ربط منهجي لبيانات الأسر، إصدار دفاتر العائلة، والمتابعة الدقيقة لتحديثات الحالة الاجتماعية.</p>
            </div>

            <!-- خدمة 3 -->
            <div class="bg-white p-8 rounded-xl shadow-md border-t-4 border-secondary hover:shadow-xl transition">
                <div class="w-12 h-12 bg-orange-50 text-secondary rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-3">الشهادات الحيوية</h3>
                <p class="text-slate-600 text-sm leading-relaxed">تسجيل دقيق لواقعات الولادة، الوفاة، الزواج، والطلاق وتوليد الوثائق الرسمية والشهادات المعتمدة لها.</p>
            </div>
        </div>
    </div>
</main>

<!-- التذييل -->
<footer class="bg-slate-900 text-slate-400 py-6 mt-auto">
    <div class="container mx-auto px-6 text-center">
        <p class="text-sm">&copy; {{ date('Y') }} نظام الأحوال المدنية. جميع الحقوق محفوظة.</p>
    </div>
</footer>

</body>
</html>
