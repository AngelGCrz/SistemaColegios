@extends('layouts.app')
@section('title', 'Nuevo Colegio - Super Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.colegios.index') }}" class="text-sm text-primary-600 hover:underline">&larr; Volver a Colegios</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">Registrar Nuevo Colegio</h1>
</div>

<form method="POST" action="{{ route('superadmin.colegios.store') }}" class="space-y-6 max-w-3xl">
    @csrf

    {{-- Datos del Colegio --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Datos del Colegio</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Colegio *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subdominio *</label>
                <div class="flex items-center">
                    <input type="text" name="subdominio" value="{{ old('subdominio') }}" required maxlength="50"
                           pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?" placeholder="mi-colegio"
                           class="flex-1 border rounded-l-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r-lg text-sm text-gray-500">.{{ config('app.domain') }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Solo letras minúsculas, números y guiones.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email del Colegio</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
    </div>

    {{-- Contacto Principal --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Contacto Principal</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                <input type="text" name="contacto_nombre" value="{{ old('contacto_nombre') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email de Contacto *</label>
                <input type="email" name="contacto_email" value="{{ old('contacto_email') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de Contacto</label>
                <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
    </div>

    {{-- Plan y Ciclo --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Suscripción</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan *</label>
                <select name="plan_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Seleccionar plan</option>
                    @foreach($planes as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                            {{ $plan->nombre }} — ${{ number_format($plan->precio_mensual, 2) }}/mes
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo de Facturación *</label>
                <select name="ciclo" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="mensual" @selected(old('ciclo') === 'mensual')>Mensual</option>
                    <option value="anual" @selected(old('ciclo') === 'anual')>Anual</option>
                </select>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">El colegio comenzará con 30 días de trial gratuito.</p>
    </div>

    {{-- Usuario Administrador --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Usuario Administrador</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="admin_nombre" value="{{ old('admin_nombre') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                <input type="text" name="admin_apellidos" value="{{ old('admin_apellidos') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                <input type="password" name="admin_password" required minlength="8"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium">
            Crear Colegio
        </button>
    </div>
</form>
@endsection
