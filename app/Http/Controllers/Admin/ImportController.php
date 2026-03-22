<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\User;
use App\Traits\FiltraPorColegio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    use FiltraPorColegio;

    public function index()
    {
        return view('admin.importar.index');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('archivo');
        $rows = $this->parseCsv($file->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'El archivo está vacío o no tiene el formato correcto.');
        }

        $headers = array_keys($rows[0]);
        $requiredHeaders = ['nombre', 'apellidos', 'email'];
        $missing = array_diff($requiredHeaders, array_map('strtolower', $headers));

        if (!empty($missing)) {
            return back()->with('error', 'Faltan columnas requeridas: ' . implode(', ', $missing));
        }

        // Validate each row
        $colegioId = $this->colegioId();
        $existingEmails = User::where('colegio_id', $colegioId)->pluck('email')->toArray();
        $validated = [];
        $errors = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // +2 because row 1 is header
            $rowErrors = [];

            $email = trim($row['email'] ?? '');
            $nombre = trim($row['nombre'] ?? '');
            $apellidos = trim($row['apellidos'] ?? '');

            if (empty($nombre)) {
                $rowErrors[] = 'Nombre vacío';
            }
            if (empty($apellidos)) {
                $rowErrors[] = 'Apellidos vacío';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $rowErrors[] = 'Email inválido';
            }
            if (in_array($email, $existingEmails)) {
                $rowErrors[] = 'Email ya registrado';
            }

            $row['_errors'] = $rowErrors;
            $row['_row'] = $rowNum;
            $validated[] = $row;
        }

        // Store CSV data in session for final import
        session(['import_data' => $validated]);

        $totalErrors = collect($validated)->filter(fn ($r) => !empty($r['_errors']))->count();

        return view('admin.importar.preview', compact('validated', 'headers', 'totalErrors'));
    }

    public function store(Request $request)
    {
        $data = session('import_data', []);

        if (empty($data)) {
            return redirect()->route('admin.importar.index')
                ->with('error', 'No hay datos para importar. Suba el archivo nuevamente.');
        }

        $colegioId = $this->colegioId();
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if (!empty($row['_errors'])) {
                    $skipped++;
                    continue;
                }

                $email = trim($row['email']);

                // Double-check email doesn't exist
                if (User::where('colegio_id', $colegioId)->where('email', $email)->exists()) {
                    $skipped++;
                    continue;
                }

                $password = Str::random(8);

                $user = User::create([
                    'colegio_id' => $colegioId,
                    'nombre' => trim($row['nombre']),
                    'apellidos' => trim($row['apellidos']),
                    'email' => $email,
                    'password' => Hash::make($password),
                    'rol' => 'alumno',
                    'dni' => trim($row['dni'] ?? ''),
                    'telefono' => trim($row['telefono'] ?? ''),
                    'activo' => true,
                ]);

                Alumno::create([
                    'colegio_id' => $colegioId,
                    'user_id' => $user->id,
                    'codigo_alumno' => trim($row['codigo_alumno'] ?? '') ?: ('ALU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT)),
                    'fecha_nacimiento' => !empty($row['fecha_nacimiento']) ? $row['fecha_nacimiento'] : null,
                    'genero' => trim($row['genero'] ?? '') ?: null,
                    'direccion' => trim($row['direccion'] ?? '') ?: null,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.importar.index')
                ->with('error', 'Error durante la importación: ' . $e->getMessage());
        }

        session()->forget('import_data');

        return redirect()->route('admin.importar.index')
            ->with('success', "Importación completada: {$imported} alumnos creados, {$skipped} omitidos.");
    }

    public function plantilla()
    {
        $headers = ['nombre', 'apellidos', 'email', 'dni', 'telefono', 'codigo_alumno', 'fecha_nacimiento', 'genero', 'direccion'];
        $example = ['Juan', 'Pérez García', 'juan.perez@ejemplo.com', '12345678', '999888777', 'ALU-00001', '2010-03-15', 'M', 'Av. Principal 123'];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_alumnos.csv"',
        ]);
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        $file = fopen($path, 'r');

        if (!$file) {
            return [];
        }

        // Detect BOM and skip it
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $headers = fgetcsv($file);
        if (!$headers) {
            fclose($file);
            return [];
        }

        // Normalize headers: lowercase, trim
        $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) !== count($headers)) {
                continue;
            }
            $rows[] = array_combine($headers, $data);
        }

        fclose($file);
        return $rows;
    }
}
