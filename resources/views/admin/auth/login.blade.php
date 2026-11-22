<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex, nofollow">

    <title>Masuk Admin - Sekar Kawung</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-pine-900 px-4 py-12 font-sans antialiased">
    <main class="w-full max-w-md rounded-3xl bg-foam-50 p-8 shadow-2xl">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/landing/nav-logo.png') }}" alt="Sekar Kawung" class="h-10 w-auto object-contain">
            <div>
                <h1 class="text-lg font-bold leading-6 text-pine-900">Admin Sekar Kawung</h1>
                <p class="text-sm leading-5 text-ink-soft">Masuk untuk mengelola laporan dan faktor emisi.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.login') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-pine-900">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-pine-900/15 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none focus:border-sage-600">
                @error('email')
                    <p class="mt-1 text-sm leading-5 text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-semibold text-pine-900">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-pine-900/15 bg-white px-4 py-2.5 text-sm text-slate-800 outline-none focus:border-sage-600">
                @error('password')
                    <p class="mt-1 text-sm leading-5 text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-soft">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="size-4 rounded border-pine-900/20">
                Ingat saya
            </label>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-pine-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-pine-950">
                Masuk
            </button>
        </form>
    </main>
</body>
</html>
