<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>دسترسی غیرمجاز</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white rounded-2xl shadow-xl p-10 max-w-md text-center">

    <div class="text-7xl mb-4">🚫</div>

    <h1 class="text-4xl font-extrabold text-red-600 mb-4">
        403
    </h1>

    <p class="text-lg text-gray-700 mb-6">
        شما اجازه دسترسی به این صفحه را ندارید
    </p>

    <div class="flex justify-center gap-3">
        <a href="/register"
           class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
            ثبت نام
        </a>
        <a href="/login"
           class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
            ورود
        </a>

        @auth
            <a href="{{ url('/dashboard') }}"
               class="px-5 py-2 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition">
                داشبورد
            </a>
        @endauth
    </div>

    <p class="text-sm text-gray-400 mt-6">
        اگر فکر می‌کنید این خطا اشتباه است، با پشتیبانی تماس بگیرید
    </p>

</div>

</body>
</html>
