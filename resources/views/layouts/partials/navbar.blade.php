{{-- Navbar superior --}}
<nav class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 md:px-6">
    {{-- Logo / Toggle sidebar --}}
    <div class="flex items-center space-x-3">
        <button @click="$dispatch('toggle-sidebar')" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="text-lg font-bold text-primary-700">
            {{ auth()->user()->colegio->nombre ?? 'Sistema Colegios' }}
        </span>
    </div>

    {{-- Usuario --}}
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center space-x-2 text-sm hover:bg-gray-100 rounded-lg p-2">
            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
            </div>
            <span class="hidden sm:block">{{ auth()->user()->nombreCompleto() }}</span>
            <span class="hidden sm:block text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full">{{ ucfirst(auth()->user()->rol) }}</span>
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</nav>
