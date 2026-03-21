# SistemaColegios — Arquitectura del Sistema

## 1. Visión General

**SistemaColegios** es un ERP educativo + aula virtual diseñado como SaaS para colegios pequeños y medianos en Latinoamérica. Prioriza simplicidad, velocidad e intuitividad sobre cantidad de funciones.

### Stack Tecnológico
| Componente | Tecnología |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Base de datos | MySQL 8.0+ |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| PDF | barryvdh/laravel-dompdf |
| Servidor | Ubuntu 22.04 + Nginx (o CyberPanel) |
| Hosting | DigitalOcean / VPS |

### Principios de Diseño
- **Monolito bien organizado** (no microservicios): más simple de desplegar, mantener y debuggear
- **Multi-tenant por colegio_id**: cada tabla lleva `colegio_id` como FK para aislar datos
- **Mobile-first**: UI optimizada para celulares (docentes y padres usan móvil)
- **MVP primero**: funcionalidades mínimas que generen valor, iterar después

---

## 2. Arquitectura Multi-Tenant

```
Colegio A ──┐
             ├── Misma BD, mismo código
Colegio B ──┘
             Aislamiento por colegio_id en cada query
```

### Patrón de aislamiento
- Trait `FiltraPorColegio` aplicado a modelos que necesitan filtrado
- Método `colegioId()` obtiene el colegio del usuario autenticado
- Scope `scopeColegio()` filtra automáticamente por `colegio_id`
- Middleware `CheckColegioActivo` verifica suscripción vigente

### Seguridad multi-tenant
- NUNCA se consultan datos sin filtro de `colegio_id`
- Los controllers siempre usan `->where('colegio_id', ...)` o el trait
- Las rutas usan route model binding + verificación de pertenencia

---

## 3. Diagrama de Base de Datos (ER)

### Tablas principales (17+)

```
colegios
├── users (colegio_id, rol: admin|docente|alumno|padre)
│   ├── alumnos (user_id) ──── alumno_padre ──── padres (user_id)
│   └── docentes (user_id)
├── periodos (colegio_id, activo)
├── niveles (colegio_id)
│   └── grados (nivel_id)
│       └── secciones (grado_id)
│           └── curso_seccion (curso_id, seccion_id, docente_id)
├── cursos (colegio_id)
├── matriculas (alumno_id, periodo_id, seccion_id, estado)
├── bimestres (periodo_id)
├── notas (matricula_id, curso_seccion_id, bimestre_id, nota, nota_letra)
├── asistencias (matricula_id, fecha, estado)
├── tareas (curso_seccion_id)
│   └── entregas_tareas (tarea_id, alumno_id)
├── avisos (colegio_id, destinatario)
├── mensajes (remitente_id, destinatario_id)
├── conceptos_pago (colegio_id)
└── pagos (alumno_id, concepto_pago_id, estado)
```

### Índices clave
- `users`: UNIQUE(colegio_id, email)
- `notas`: UNIQUE(matricula_id, curso_seccion_id, bimestre_id)
- `asistencias`: UNIQUE(matricula_id, fecha)
- Todas las FKs indexadas automáticamente

---

## 4. Módulos del Sistema

### 4.1 Autenticación y Autorización
- Login por email/password
- 4 roles: `admin`, `docente`, `alumno`, `padre`
- Middleware `CheckRole` para proteger rutas por rol
- Redirección post-login según rol

### 4.2 Gestión de Usuarios (Admin)
- CRUD completo de usuarios
- Al crear usuario, auto-genera perfil (Alumno/Docente/Padre) en transacción
- Filtros por rol y estado

### 4.3 Estructura Académica (Admin)
- Niveles → Grados → Secciones (jerarquía)
- Cursos independientes
- Asignaciones: Curso + Sección + Docente (tabla pivote `curso_seccion`)

### 4.4 Matrículas (Admin)
- Vincula alumno → periodo + sección
- Estados: activa, retirada, trasladada
- Filtro por periodo

### 4.5 Notas (Docente)
- Planilla de notas por curso/sección/bimestre
- Nota numérica (0-20) + nota letra calculada (AD/A/B/C)
- Guardado masivo con `updateOrCreate`

### 4.6 Asistencia (Docente)
- Registro diario por sección
- Estados: presente, falta, tardanza, justificada
- Radio buttons para entrada rápida

### 4.7 Tareas / Aula Virtual (Docente + Alumno)
- Docente crea tareas con archivo adjunto
- Alumno entrega con texto/archivo
- Docente califica y comenta

### 4.8 Comunicación
- **Avisos**: Admin publica, filtrable por destinatario (todos/docente/alumno/padre)
- **Mensajes**: Internos entre usuarios del mismo colegio

### 4.9 Pagos (Admin + Padre)
- Conceptos de pago configurables
- Registro de deudas y pagos
- Vista de estado de cuenta por alumno

### 4.10 Boleta de Notas (PDF)
- Generación con DomPDF
- Incluye notas por bimestre, promedio, asistencia
- Descargable por admin, alumno y padre

### 4.11 Dashboard por Rol
- **Admin**: KPIs (alumnos, matrículas, asistencia, pagos)
- **Docente**: Lista de cursos asignados
- **Alumno**: Avisos, tareas pendientes, accesos rápidos
- **Padre**: Lista de hijos con accesos a notas/asistencia/pagos

---

## 5. Estructura de Carpetas

```
SistemaColegios/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── Admin/{Dashboard,Usuario,Periodo,Academico,Matricula,Pago,Aviso}Controller.php
│   │   │   ├── Docente/{Dashboard,Nota,Asistencia,Tarea}Controller.php
│   │   │   ├── Alumno/DashboardController.php
│   │   │   ├── Padre/DashboardController.php
│   │   │   ├── MensajeController.php
│   │   │   └── BoletaController.php
│   │   └── Middleware/{CheckRole,CheckColegioActivo}.php
│   ├── Models/ (20 modelos)
│   └── Traits/FiltraPorColegio.php
├── database/migrations/ (9 archivos)
├── resources/views/
│   ├── layouts/{app,partials/navbar,partials/sidebar}.blade.php
│   ├── components/sidebar-link.blade.php
│   ├── auth/login.blade.php
│   ├── admin/{dashboard,usuarios/*,periodos/*,academico/*,matriculas/*,pagos/*,avisos/*}
│   ├── docente/{dashboard,notas/*,asistencia/*,tareas/*}
│   ├── alumno/{dashboard,notas,tareas}
│   ├── padre/{dashboard,notas,asistencia,pagos}
│   ├── mensajes/{inbox,enviados,create,show}
│   └── pdf/boleta.blade.php
├── routes/web.php
├── composer.json
├── .env.example
└── bootstrap/app.php
```

---

## 6. Flujo de Datos por Rol

### Admin
```
Login → Dashboard → Gestionar {Usuarios, Periodos, Académico, Matrículas, Pagos, Avisos}
```

### Docente
```
Login → Dashboard (mis cursos) → Seleccionar curso →
  ├── Notas: seleccionar bimestre → planilla → guardar
  ├── Asistencia: seleccionar fecha → registrar → guardar
  └── Tareas: crear tarea → ver entregas → calificar
```

### Alumno
```
Login → Dashboard (avisos + tareas pendientes) →
  ├── Mis Notas → descargar boleta
  └── Tareas → ver/entregar
```

### Padre
```
Login → Dashboard (lista de hijos) → Seleccionar hijo →
  ├── Notas → descargar boleta
  ├── Asistencia (resumen + detalle)
  └── Pagos (estado de cuenta)
```
