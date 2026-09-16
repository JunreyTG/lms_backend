<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin Login | ClassIQ Backend</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink font-sans">
    <main class="flex min-h-screen">
        <section class="relative hidden w-[46%] overflow-hidden bg-[#162236] px-12 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-20">
            <div class="absolute -right-32 -top-24 h-96 w-96 rounded-full border-[52px] border-coral/10"></div>
            <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full border-[42px] border-white/5"></div>
            <div class="relative z-10 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-coral text-lg font-extrabold shadow-lg shadow-coral/20">A</span>
                <span class="font-display text-lg font-bold tracking-tight">ClassIQ<span class="text-coral">.</span></span>
            </div>
            <div class="relative z-10 max-w-md pb-8">
                <p class="mb-5 text-xs font-extrabold uppercase tracking-[0.24em] text-coral">Control room / 01</p>
                <h1 class="font-display text-5xl font-bold leading-[1.08] tracking-[-0.04em] xl:text-6xl">Make every<br><span class="text-coral">lesson count.</span></h1>
                <p class="mt-7 max-w-sm text-sm leading-7 text-slate-400">A focused workspace for the people shaping what your learners see next.</p>
                <div class="mt-10 flex items-center gap-3 text-xs font-semibold text-slate-400"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> All systems operational</div>
            </div>
            <p class="relative z-10 text-xs text-slate-500">Atlas admin console <span class="mx-2 text-slate-700">/</span> v1.0</p>
        </section>

        <section class="flex flex-1 items-center justify-center bg-[#fbfcfe] px-6 py-12 sm:px-10">
            <div class="w-full max-w-[410px]">
                <div class="mb-10 flex items-center gap-3 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-coral text-lg font-extrabold text-white">A</span>
                    <span class="font-display text-lg font-bold tracking-tight text-ink">ClassIQ<span class="text-coral">.</span></span>
                </div>
                <div class="mb-9">
                    <p class="mb-3 text-xs font-extrabold uppercase tracking-[0.2em] text-coral">Super admin</p>
                    <h2 class="font-display text-3xl font-bold tracking-[-0.03em] text-ink">Backend control room.</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Sign in to manage the LMS backend and its data.</p>
                </div>

                <div id="login-error" class="mb-5 hidden rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold leading-5 text-rose-600" role="alert"></div>
                <form id="login-form" class="space-y-5">
                    <div>
                        <label for="email" class="mb-2 block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required placeholder="admin@example.com" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-ink outline-none transition placeholder:text-slate-300 focus:border-coral focus:ring-4 focus:ring-coral/10">
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password" class="block text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Password</label>
                            <span class="text-xs font-semibold text-slate-400">Sanctum secured</span>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Enter your password" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-ink outline-none transition placeholder:text-slate-300 focus:border-coral focus:ring-4 focus:ring-coral/10">
                    </div>
                    <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-coral px-4 text-sm font-extrabold text-white shadow-lg shadow-coral/20 transition hover:bg-[#f15e3d] focus:outline-none focus:ring-4 focus:ring-coral/20 disabled:cursor-wait disabled:opacity-70">
                        <span data-submit-label>Sign in to control room</span>
                        <svg data-submit-spinner class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-30" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    </button>
                </form>
                <div class="mt-8 flex items-center gap-3 text-xs font-semibold text-slate-400"><svg class="h-4 w-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 4.5-3 7.8-7 10-4-2.2-7-5.5-7-10V6l7-3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg> Your connection is encrypted end to end</div>
            </div>
        </section>
    </main>
</body>
</html>
