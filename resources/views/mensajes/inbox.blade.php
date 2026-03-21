@extends('layouts.app')
@section('title', 'Bandeja de Entrada')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Mensajes</h1>
    <a href="{{ route('mensajes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
        + Nuevo Mensaje
    </a>
</div>

<div class="flex gap-2 mb-4">
    <a href="{{ route('mensajes.inbox') }}" class="px-4 py-2 text-sm rounded-lg {{ request()->routeIs('mensajes.inbox') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Recibidos</a>
    <a href="{{ route('mensajes.enviados') }}" class="px-4 py-2 text-sm rounded-lg {{ request()->routeIs('mensajes.enviados') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Enviados</a>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    @forelse($mensajes as $mensaje)
    <a href="{{ route('mensajes.show', $mensaje) }}" class="block p-4 border-b hover:bg-gray-50 transition {{ !$mensaje->leido ? 'bg-blue-50/50' : '' }}">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    @if(!$mensaje->leido)
                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                    @endif
                    <span class="font-medium text-gray-800 text-sm">{{ $mensaje->remitente->nombre }} {{ $mensaje->remitente->apellidos }}</span>
                </div>
                <h3 class="text-sm text-gray-700 mt-1">{{ $mensaje->asunto }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($mensaje->contenido, 80) }}</p>
            </div>
            <span class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $mensaje->created_at->diffForHumans() }}</span>
        </div>
    </a>
    @empty
    <div class="text-center py-12 text-gray-400">No tienes mensajes.</div>
    @endforelse
</div>

@if($mensajes->hasPages())
<div class="mt-4">{{ $mensajes->links() }}</div>
@endif
@endsection
