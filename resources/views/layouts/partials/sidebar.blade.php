{{-- Sidebar lateral responsive --}}
<aside x-data="{ open: false }"
       @toggle-sidebar.window="open = !open"
       :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed top-16 left-0 z-20 w-64 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 overflow-y-auto transition-transform duration-200">

    {{-- Overlay mobile --}}
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/30 md:hidden z-10" x-cloak></div>

    <nav class="relative z-20 bg-white h-full py-4 px-3 space-y-1">
        @php $rol = auth()->user()->rol; @endphp

        {{-- ========= ADMIN ========= --}}
        @if($rol === 'admin')
            <x-sidebar-link href="{{ route('admin.dashboard') }}" icon="home" :active="request()->routeIs('admin.dashboard')">
                Dashboard
            </x-sidebar-link>

            <p class="text-xs font-semibold text-gray-400 uppercase mt-4 mb-2 px-3">Gestión</p>

            <x-sidebar-link href="{{ route('admin.usuarios.index') }}" icon="users" :active="request()->routeIs('admin.usuarios.*')">
                Usuarios
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.periodos.index') }}" icon="calendar" :active="request()->routeIs('admin.periodos.*')">
                Periodos
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.matriculas.index') }}" icon="clipboard" :active="request()->routeIs('admin.matriculas.*')">
                Matrículas
            </x-sidebar-link>

            <p class="text-xs font-semibold text-gray-400 uppercase mt-4 mb-2 px-3">Académico</p>

            <x-sidebar-link href="{{ route('admin.academico.niveles') }}" icon="layers" :active="request()->routeIs('admin.academico.niveles')">
                Niveles y Grados
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.academico.secciones') }}" icon="grid" :active="request()->routeIs('admin.academico.secciones')">
                Secciones
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.academico.cursos') }}" icon="book" :active="request()->routeIs('admin.academico.cursos')">
                Cursos
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.academico.asignaciones') }}" icon="link" :active="request()->routeIs('admin.academico.asignaciones')">
                Asignaciones
            </x-sidebar-link>

            <p class="text-xs font-semibold text-gray-400 uppercase mt-4 mb-2 px-3">Finanzas</p>

            <x-sidebar-link href="{{ route('admin.pagos.index') }}" icon="dollar" :active="request()->routeIs('admin.pagos.index')">
                Pagos
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('admin.pagos.conceptos') }}" icon="tag" :active="request()->routeIs('admin.pagos.conceptos')">
                Conceptos de Pago
            </x-sidebar-link>

            <p class="text-xs font-semibold text-gray-400 uppercase mt-4 mb-2 px-3">Comunicación</p>

            <x-sidebar-link href="{{ route('admin.avisos.index') }}" icon="megaphone" :active="request()->routeIs('admin.avisos.*')">
                Avisos
            </x-sidebar-link>

        {{-- ========= DOCENTE ========= --}}
        @elseif($rol === 'docente')
            <x-sidebar-link href="{{ route('docente.dashboard') }}" icon="home" :active="request()->routeIs('docente.dashboard')">
                Mis Cursos
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('docente.notas.seleccionar') }}" icon="edit" :active="request()->routeIs('docente.notas.*')">
                Registrar Notas
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('docente.asistencia.seleccionar') }}" icon="check-circle" :active="request()->routeIs('docente.asistencia.*')">
                Asistencia
            </x-sidebar-link>

        {{-- ========= ALUMNO ========= --}}
        @elseif($rol === 'alumno')
            <x-sidebar-link href="{{ route('alumno.dashboard') }}" icon="home" :active="request()->routeIs('alumno.dashboard')">
                Inicio
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('alumno.notas') }}" icon="star" :active="request()->routeIs('alumno.notas')">
                Mis Notas
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('alumno.tareas') }}" icon="clipboard" :active="request()->routeIs('alumno.tareas')">
                Tareas
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('alumno.calendario') }}" icon="calendar" :active="request()->routeIs('alumno.calendario')">
                Calendario
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('alumno.historial') }}" icon="clock" :active="request()->routeIs('alumno.historial')">
                Historial
            </x-sidebar-link>

        {{-- ========= PADRE ========= --}}
        @elseif($rol === 'padre')
            <x-sidebar-link href="{{ route('padre.dashboard') }}" icon="home" :active="request()->routeIs('padre.dashboard')">
                Inicio
            </x-sidebar-link>
        @endif

        {{-- Compartido: Mensajes --}}
        <p class="text-xs font-semibold text-gray-400 uppercase mt-4 mb-2 px-3">Mensajes</p>
        <x-sidebar-link href="{{ route('mensajes.inbox') }}" icon="mail" :active="request()->routeIs('mensajes.*')">
            Mensajes
        </x-sidebar-link>
    </nav>
</aside>
