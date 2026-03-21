@extends('layouts.app')
@section('title', 'Registrar Pago')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.pagos.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Registrar Pago</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="POST" action="{{ route('admin.pagos.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="alumno_id" class="block text-sm font-medium text-gray-700 mb-1">Alumno</label>
                    <select name="alumno_id" id="alumno_id" required
                            class="w-full border rounded-lg px-3 py-2 text-sm @error('alumno_id') border-red-500 @enderror">
                        <option value="">Seleccionar alumno...</option>
                        @foreach($alumnos as $alumno)
                        <option value="{{ $alumno->id }}" @selected(old('alumno_id') == $alumno->id)>
                            {{ $alumno->user->apellidos }}, {{ $alumno->user->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('alumno_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="concepto_pago_id" class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                        <select name="concepto_pago_id" id="concepto_pago_id" required
                                class="w-full border rounded-lg px-3 py-2 text-sm @error('concepto_pago_id') border-red-500 @enderror">
                            <option value="">Seleccionar concepto...</option>
                            @foreach($conceptos as $concepto)
                            <option value="{{ $concepto->id }}" data-monto="{{ $concepto->monto }}"
                                    @selected(old('concepto_pago_id') == $concepto->id)>
                                {{ $concepto->nombre }} — S/ {{ number_format($concepto->monto, 2) }}
                            </option>
                            @endforeach
                        </select>
                        @error('concepto_pago_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="periodo_id" class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                        <select name="periodo_id" id="periodo_id" required
                                class="w-full border rounded-lg px-3 py-2 text-sm @error('periodo_id') border-red-500 @enderror">
                            @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}" @selected(old('periodo_id') == $periodo->id)>
                                {{ $periodo->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('periodo_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto (S/)</label>
                        <input type="number" name="monto" id="monto" step="0.01" min="0.01" required
                               value="{{ old('monto') }}"
                               class="w-full border rounded-lg px-3 py-2 text-sm @error('monto') border-red-500 @enderror">
                        @error('monto')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" id="estado" required
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="pendiente" @selected(old('estado') === 'pendiente')>Pendiente</option>
                            <option value="pagado" @selected(old('estado') === 'pagado')>Pagado</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ show: false }" x-init="show = document.getElementById('estado').value === 'pagado'"
                     x-show="show" x-effect="document.getElementById('estado').addEventListener('change', e => show = e.target.value === 'pagado')">
                    <div>
                        <label for="fecha_pago" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Pago</label>
                        <input type="date" name="fecha_pago" id="fecha_pago" value="{{ old('fecha_pago', date('Y-m-d')) }}"
                               class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo" @selected(old('metodo_pago') === 'efectivo')>Efectivo</option>
                            <option value="transferencia" @selected(old('metodo_pago') === 'transferencia')>Transferencia</option>
                            <option value="tarjeta" @selected(old('metodo_pago') === 'tarjeta')>Tarjeta</option>
                            <option value="yape" @selected(old('metodo_pago') === 'yape')>Yape/Plin</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="observacion" class="block text-sm font-medium text-gray-700 mb-1">Observación (opcional)</label>
                    <textarea name="observacion" id="observacion" rows="2"
                              class="w-full border rounded-lg px-3 py-2 text-sm"
                              placeholder="Notas adicionales...">{{ old('observacion') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition">
                    Registrar Pago
                </button>
                <a href="{{ route('admin.pagos.index') }}" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-fill monto when selecting concepto
    document.getElementById('concepto_pago_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const monto = option.dataset.monto;
        if (monto) document.getElementById('monto').value = monto;
    });
</script>
@endpush
@endsection
