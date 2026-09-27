<nav x-data="{ sidebarOpen: false }">
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:opacity
         x-transition:duration-200
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside x-cloak
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-900 text-slate-100 shadow-2xl transition-transform duration-200 ease-in-out lg:transform-none">
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-800 px-6">
            <a href="{{ route('super-admin.dashboard') }}" class="flex min-w-0 flex-col">
                <span class="text-base font-bold tracking-tight text-white">Super Admin</span>
                <span class="truncate text-[11px] font-medium uppercase tracking-wider text-slate-400">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden" aria-label="Close sidebar">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Platform</p>
                <div class="mt-2 space-y-1">
                    <x-sidebar-link :href="route('super-admin.dashboard')" :active="request()->routeIs('super-admin.dashboard')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <span>Dashboard</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('super-admin.pharmacies.index')" :active="request()->routeIs('super-admin.pharmacies.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        <span>Pharmacies</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('super-admin.pharmacies.create')" :active="request()->routeIs('super-admin.pharmacies.create')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Add Pharmacy</span>
                    </x-sidebar-link>
                </div>
            </div>
        </div>

        <div class="shrink-0 border-t border-slate-800 px-4 py-4">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-500 text-sm font-bold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-slate-400">Super Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3 border-t border-slate-800 pt-3">
                @csrf
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition duration-150 ease-in-out">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm lg:hidden">
        <button @click="sidebarOpen = true" class="text-slate-600 hover:text-slate-900" aria-label="Open sidebar">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <span class="text-base font-bold tracking-tight text-slate-900">Super Admin</span>
        <div class="w-6"></div>
    </div>
</nav>
