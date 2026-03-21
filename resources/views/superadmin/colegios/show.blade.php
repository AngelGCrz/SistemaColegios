@extends('layouts.app')
@section('title', $colegio->nombre . ' - Super Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.colegios.index') }}" class="text-sm text-primary-600 hover:underline">&larr; Volver a Colegios</a>
    <div class="flex items-center justify-between mt-2">
        <h1 class="text-2xl font-bold text-gray-800">{{ $colegio->nombre }}</h1>
        <div class="flex items-center space-x-2">
            <a href="{{ route('superadmin.colegios.edit', $colegio) }}"
               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 text-sm font-medium">Editar</a>
            <form method="POST" action="{{ route('superadmin.colegios.toggle', $colegio) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="{{ $colegio->activo ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} px-4 py-2 rounded-lg text-sm font-medium">
                    {{ $colegio->activo ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info del colegio --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Datos generales --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Email</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->email ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Teléfono</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->telefono ?: '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Dirección</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->direccion ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Contacto</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->contacto_nombre ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Email Contacto</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->contacto_email ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Registrado</dt>
                    <dd class="font-medium text-gray-800">{{ $colegio->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Estado</dt>
                    <dd>
                        @if($colegio->activo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Inactivo</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Historial de pagos --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Historial de Pagos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Fecha</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Monto</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Método</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($pagos as $pago)
                        <tr>
                            <td class="px-6 py-3 text-gray-700">{{ $pago->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 font-medium">${{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $pago->metodo_pago ?: '—' }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $color = match($pago->estado) {
                                        'aprobado' => 'green',
                                        'pendiente' => 'yellow',
                                        'rechazado' => 'red',
                                        default => 'gray',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700">
                                    {{ ucfirst($pago->estado) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">Sin pagos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar info --}}
    <div class="space-y-6">
        {{-- Stats --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Usuarios totales</span>
                    <span class="font-semibold text-gray-800">{{ $stats['usuarios'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Alumnos</span>
                    <span class="font-semibold text-gray-800">{{ $stats['alumnos'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Docentes</span>
                    <span class="font-semibold text-gray-800">{{ $stats['docentes'] }}</span>
                </div>
                @if($stats['admin'])
                <div class="pt-2 border-t">
                    <span class="text-gray-500 text-xs">Admin:</span>
                    <p class="font-medium text-gray-800">{{ $stats['admin']->nombreCompleto() }}</p>
                    <p class="text-xs text-gray-400">{{ $stats['admin']->email }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Suscripción activa --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Suscripción Activa</h2>
            @if($colegio->suscripcionActiva)
                @php $sub = $colegio->suscripcionActiva; @endphp
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Plan</span>
                        <span class="font-semibold text-primary-700">{{ $sub->plan->nombre }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estado</span>
                        <span class="font-semibold">{{ ucfirst($sub->estado) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ciclo</span>
                        <span>{{ ucfirst($sub->ciclo) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Vence</span>
                        <span>{{ $sub->fecha_fin?->format('d/m/Y') ?: '—' }}</span>
                    </div>
                    @if($sub->estado === 'trial')
                        <div class="flex justify-between">
                            <span class="text-gray-500">Trial hasta</span>
                            <span class="text-orange-500 font-medium">{{ $sub->trial_hasta?->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Monto</span>
                        <span class="font-semibold">${{ number_format($sub->monto, 2) }}</span>
                    </div>
                </div>

                {{-- Cambiar plan --}}
                <form method="POST" action="{{ route('superadmin.colegios.cambiarPlan', $colegio) }}" class="mt-4 pt-4 border-t">
                    @csrf
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cambiar Plan</label>
                    <select name="plan_id" class="w-full border rounded-lg px-3 py-2 text-sm mb-2">
                        @foreach(\App\Models\Plan::where('activo', true)->orderBy('orden')->get() as $plan)
                            <option value="{{ $plan->id }}" @selected($plan->id === $sub->plan_id)>{{ $plan->nombre }}</option>
                        @endforeach
                    </select>
                    <select name="ciclo" class="w-full border rounded-lg px-3 py-2 text-sm mb-2">
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                    </select>
                    <button type="submit" class="w-full bg-primary-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-primary-700">
                        Cambiar Plan
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-400">Sin suscripción activa.</p>
            @endif
        </div>

        {{-- Historial de suscripciones --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Historial Suscripciones</h2>
            <div class="space-y-2">
                @foreach($colegio->suscripciones as $sub)
                <div class="text-sm p-2 rounded {{ $sub->estaVigente() ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $sub->plan->nombre }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded {{ $sub->estaVigente() ? 'bg-green-200 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ ucfirst($sub->estado) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        {{ $sub->fecha_inicio?->format('d/m/Y') }} — {{ $sub->fecha_fin?->format('d/m/Y') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
