@extends('layouts.app')
@section('title', $mensaje->asunto)

@section('content')
<div class="mb-6">
    <a href="{{ route('mensajes.inbox') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver a bandeja</a>
</div>

<div class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl">
    <div class="border-b pb-4 mb-4">
        <h1 class="text-xl font-bold text-gray-800">{{ $mensaje->asunto }}</h1>
        <div class="flex justify-between items-center mt-2">
            <div class="text-sm text-gray-500">
                @if($mensaje->remitente_id === auth()->id())
                <span>Para: <strong class="text-gray-700">{{ $mensaje->destinatario->nombre }} {{ $mensaje->destinatario->apellidos }}</strong></span>
                @else
                <span>De: <strong class="text-gray-700">{{ $mensaje->remitente->nombre }} {{ $mensaje->remitente->apellidos }}</strong></span>
                @endif
            </div>
            <span class="text-xs text-gray-400">{{ $mensaje->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $mensaje->contenido }}</div>
</div>
@endsection
