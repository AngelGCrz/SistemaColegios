<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Colegios - Gestión Escolar Integral | Matrículas, Notas, Asistencia</title>
    <meta name="description" content="Plataforma SaaS de gestión escolar integral. Administra matrículas, notas, asistencia, pagos, mensajería y aula virtual desde una sola plataforma. 30 días gratis.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Sistema Colegios - Gestión Escolar Simple y Completa">
    <meta property="og:description" content="Administra matrículas, notas, asistencia, pagos y aula virtual desde una sola plataforma. Prueba gratis 30 días.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Sistema Colegios">
    <meta property="og:locale" content="es_LA">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sistema Colegios - Gestión Escolar Integral">
    <meta name="twitter:description" content="Plataforma todo-en-uno para colegios. Matrículas, notas, asistencia, pagos y aula virtual.">

    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Sistema Colegios",
        "applicationCategory": "EducationalApplication",
        "operatingSystem": "Web",
        "description": "Plataforma SaaS de gestión escolar integral para colegios de Latinoamérica.",
        "offers": {
            "@type": "AggregateOffer",
            "priceCurrency": "USD",
            "lowPrice": "19",
            "highPrice": "55",
            "offerCount": "3"
        }
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white">
    {{-- Navbar --}}
    <nav class="fixed w-full bg-white/90 backdrop-blur-sm z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800">Sistema Colegios</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">Iniciar Sesión</a>
                <a href="{{ route('registro') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 font-medium text-sm">
                    Prueba Gratis
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="pt-32 pb-20 bg-gradient-to-br from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                Gestión Escolar<br>
                <span class="text-primary-600">Simple y Completa</span>
            </h1>
            <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto">
                Administra matrículas, notas, asistencia, pagos, comunicación y aula virtual desde una sola plataforma.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('registro') }}" class="bg-primary-600 text-white px-8 py-4 rounded-xl hover:bg-primary-700 font-bold text-lg shadow-lg shadow-primary-200">
                    Comenzar Prueba Gratuita
                </a>
                <a href="#planes" class="text-primary-600 font-medium text-lg hover:underline">
                    Ver Planes &darr;
                </a>
            </div>
            <p class="mt-4 text-sm text-gray-400">30 días gratis. Sin tarjeta de crédito.</p>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-20" id="funcionalidades">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800">Todo lo que necesitas</h2>
                <p class="text-gray-500 mt-3 text-lg">Herramientas diseñadas para colegios de Latinoamérica</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                $features = [
                    ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197', 'title' => 'Matrículas', 'desc' => 'Gestiona alumnos, docentes y padres. Control completo de matriculación por período.'],
                    ['icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'title' => 'Notas y Calificaciones', 'desc' => 'Registro de notas por competencias, reportería y boletas digitales.'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Asistencia', 'desc' => 'Control diario de asistencia con reportes automáticos para padres.'],
                    ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 1v8m0 0v1', 'title' => 'Pagos y Facturación', 'desc' => 'Gestiona pensiones, conceptos de pago y genera reportes financieros.'],
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Mensajería', 'desc' => 'Comunicación directa entre docentes, padres y administración.'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Aula Virtual', 'desc' => 'Tareas, entregas, calendario académico y seguimiento del alumno.'],
                ];
                @endphp
                @foreach($features as $feature)
                <div class="bg-white rounded-xl border p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-500 text-sm">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Planes --}}
    <section class="py-20 bg-gray-50" id="planes">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800">Planes para cada colegio</h2>
                <p class="text-gray-500 mt-3 text-lg">Elige el plan que mejor se adapte a tu institución</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach($planes as $plan)
                <div class="bg-white rounded-2xl border-2 {{ $loop->iteration === 2 ? 'border-primary-500 shadow-xl relative' : 'border-gray-200' }} p-8">
                    @if($loop->iteration === 2)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-xs font-bold px-4 py-1 rounded-full">
                            Más Popular
                        </div>
                    @endif
                    <h3 class="text-xl font-bold text-gray-800">{{ $plan->nombre }}</h3>
                    <div class="mt-4">
                        <span class="text-4xl font-extrabold text-gray-900">${{ number_format($plan->precio_mensual, 0) }}</span>
                        <span class="text-gray-500">/mes</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">${{ number_format($plan->precio_anual, 0) }}/año</p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Hasta {{ number_format($plan->max_alumnos) }} alumnos
                        </li>
                        @if($plan->caracteristicas)
                            @foreach($plan->caracteristicas as $car)
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $car }}
                            </li>
                            @endforeach
                        @endif
                    </ul>
                    <a href="{{ route('registro', ['plan' => $plan->id]) }}"
                       class="mt-8 block text-center py-3 rounded-lg font-medium transition {{ $loop->iteration === 2 ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Comenzar Prueba
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800">¿Listo para modernizar tu colegio?</h2>
            <p class="text-gray-500 mt-4 text-lg">Únete a instituciones que ya confían en Sistema Colegios.</p>
            <a href="{{ route('registro') }}" class="inline-block mt-8 bg-primary-600 text-white px-8 py-4 rounded-xl hover:bg-primary-700 font-bold text-lg shadow-lg">
                Empezar Ahora — Es Gratis
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="w-6 h-6 bg-primary-600 rounded flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-white font-bold">Sistema Colegios</span>
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} Sistema Colegios. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
