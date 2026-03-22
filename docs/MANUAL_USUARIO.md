# Manual de Usuario — Sistema Colegios

## Tabla de Contenidos

1. [Primeros Pasos](#primeros-pasos)
2. [Roles del Sistema](#roles-del-sistema)
3. [Panel de Administrador](#panel-de-administrador)
4. [Panel del Docente](#panel-del-docente)
5. [Panel del Alumno](#panel-del-alumno)
6. [Panel del Padre](#panel-del-padre)
7. [SuperAdmin (SaaS)](#superadmin)
8. [Guía de Despliegue](#guía-de-despliegue)

---

## Primeros Pasos

### Registro de Colegio

1. Ingresa a la página principal del sistema.
2. Haz clic en **"Prueba Gratis"** o **"Comenzar Prueba Gratuita"**.
3. Completa el formulario en 3 pasos:
   - **Paso 1**: Datos del colegio (nombre, dirección, teléfono).
   - **Paso 2**: Datos del administrador (nombre, email, contraseña).
   - **Paso 3**: Selección de plan.
4. Tu colegio se crea automáticamente con **30 días de prueba gratuita**.
5. Inicia sesión con el email y contraseña registrados.

### Inicio de Sesión

- Accede a `/login`.
- Ingresa tu correo electrónico y contraseña.
- Serás redirigido automáticamente al panel correspondiente a tu rol.

---

## Roles del Sistema

| Rol | Descripción |
|-----|-------------|
| **Admin** | Gestiona todo el colegio: usuarios, períodos, niveles, grados, secciones, matrículas, pagos, avisos |
| **Docente** | Registra notas, asistencia, gestiona tareas y aula virtual |
| **Alumno** | Consulta notas, tareas, entrega trabajos, ve calendario |
| **Padre** | Visualiza notas, asistencia y pagos de sus hijos |
| **SuperAdmin** | Administra colegios, planes y suscripciones (nivel SaaS) |

---

## Panel de Administrador

### Dashboard
- Vista general con estadísticas: total alumnos, matrículas activas, asistencia del día, pagos pendientes, ingresos del mes.

### Estructura Académica
- **Períodos**: Crear años/semestres académicos. Activar uno como período vigente.
- **Niveles**: Inicial, Primaria, Secundaria (o personalizados).
- **Grados**: Dentro de cada nivel (ej: 1° Primaria, 2° Primaria).
- **Secciones**: Divisiones dentro de un grado (ej: Sección "A", "B").
- **Asignaciones**: Vincular docentes a cursos en secciones específicas.

### Usuarios
- Crear y gestionar usuarios con roles: docente, alumno, padre.
- Al crear un alumno, se crea automáticamente la cuenta de usuario.

### Matrículas
- Matricular alumnos en secciones para el período activo.
- Filtrar por sección; ver estado (activa/retirada).

### Pagos
- Crear conceptos de pago (matrícula, mensualidad, etc.).
- Registrar pagos por alumno.
- Marcar como pagado con fecha y método de pago.
- Los padres reciben notificación automática.

### Avisos
- Publicar comunicados dirigidos a: todos, docentes, alumnos, padres o secciones específicas.

### Mensajería
- Enviar y recibir mensajes internos entre usuarios del colegio.

---

## Panel del Docente

### Dashboard
- Lista de cursos asignados con acceso rápido a cada función.

### Asistencia
- Seleccionar curso → Registrar asistencia diaria (presente, falta, tardanza, justificado).
- No se puede duplicar asistencia del mismo día.

### Notas
- Seleccionar curso → Abrir planilla de notas por bimestre y competencia.
- Modificar notas en tabla editable; guardar todas juntas.

### Tareas (Aula Virtual)
- Crear tareas con descripción, fecha límite, puntaje máximo y archivo adjunto.
- Publicar/despublicar tareas.
- Ver entregas de alumnos y calificar.

---

## Panel del Alumno

### Dashboard
- Información de matrícula, avisos recientes, tareas próximas.

### Notas
- Consultar notas por curso y bimestre.
- Descargar boleta de notas en PDF.

### Tareas
- Ver tareas publicadas de sus cursos.
- Entregar tareas con texto y/o archivo adjunto.
- Ver calificación y comentario del docente.

### Calendario
- Vista mensual de tareas con fecha límite.
- Navegación por meses.

### Historial de Entregas
- Lista paginada de todas las entregas realizadas.

---

## Panel del Padre

### Dashboard
- Lista de hijos con acceso rápido a notas, asistencia y pagos.

### Notas del Hijo
- Seleccionar hijo → Ver notas por curso y bimestre.

### Asistencia del Hijo
- Resumen de presentes, faltas y tardanzas.
- Detalle de los últimos 30 registros.

### Pagos del Hijo
- Historial de pagos y total pendiente.

---

## SuperAdmin

### Acceso
- URL: `/superadmin/dashboard`
- Email por defecto: `superadmin@sistema.com` / Contraseña: `password`

### Dashboard
- Métricas generales: colegios activos, ingresos mensuales, nuevos registros.

### Gestión de Colegios
- Listar todos los colegios con estado de suscripción.
- Ver detalle: plan actual, suscripción, estadísticas, administrador.
- Activar/desactivar colegios.
- Cambiar plan de un colegio.

### Gestión de Planes
- CRUD completo de planes de suscripción.
- Configurar: nombre, precio mensual/anual, máximo de alumnos, características.

---

## Guía de Despliegue

### Requisitos
- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Node.js >= 18 y npm
- Extensiones PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

### Instalación

```bash
# 1. Clonar repositorio
git clone <repo-url> sistema-colegios
cd sistema-colegios

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Editar .env con datos de producción
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://tu-dominio.com
# DB_*, MAIL_*, MERCADOPAGO_*

# 5. Migraciones y datos iniciales
php artisan migrate --force
php artisan db:seed --force

# 6. Optimizaciones de producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Permisos
chmod -R 775 storage bootstrap/cache
```

### Monitoreo

- **Health check**: `GET /health` — Retorna `{"status": "ok"}` si todo funciona.
- **Logs**: `storage/logs/laravel-YYYY-MM-DD.log` (rotación diaria, 14 días).
- **Logs de seguridad**: `storage/logs/security.log` (30 días).

### Mantenimiento

```bash
# Limpiar caches
php artisan cache:clear
php artisan config:clear

# Reconstruir assets
npm run build

# Actualizar
git pull
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```
