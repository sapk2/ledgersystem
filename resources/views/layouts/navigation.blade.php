<nav x-data="{ sidebarOpen: false }" class="print:hidden">
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
        {{-- Brand --}}
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-800 px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <x-application-logo class="block h-8 w-auto" />
                <span class="text-base font-bold tracking-tight text-white">Ledger Book</span>
            </a>
            <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white lg:hidden" aria-label="Close sidebar">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Overview</p>
                <div class="mt-2 space-y-1">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M9 21v-6h6v6" />
                        </svg>
                        <span>Dashboard</span>
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Transactions</p>
                <div class="mt-2 space-y-1">
                    <x-sidebar-link :href="route('purchases.create')" :active="request()->routeIs('purchases.create')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New Bill</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Payments</span>
                    </x-sidebar-link>
                </div>
            </div>

            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Masters</p>
                <div class="mt-2 space-y-1">
                    <x-sidebar-link :href="route('distributors.index')" :active="request()->routeIs('distributors.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>Ledger (A–Z)</span>
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('medicines.index')" :active="request()->routeIs('medicines.*')">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        <span>Items</span>
                    </x-sidebar-link>
                </div>
            </div>
        </div>

        {{-- User --}}
        <div class="shrink-0 border-t border-slate-800 px-4 py-4">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-sm font-bold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 border-t border-slate-800 pt-3">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition duration-150 ease-in-out">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
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
        </div>
    </aside>

    {{-- Mobile top bar --}}
    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm lg:hidden">
        <button @click="sidebarOpen = true" class="text-slate-600 hover:text-slate-900" aria-label="Open sidebar">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <x-application-logo class="block h-7 w-auto" />
            <span class="text-base font-bold tracking-tight text-slate-900">Ledger Book</span>
        </div>
        <div class="w-6"></div>
    </div>
</nav>
