# Plan de Desarrollo y Modelo de Negocio — SistemaColegios

## Plan de Desarrollo (6 Meses)

### Fase 1: MVP Core (Semanas 1-4)
**Objetivo**: Sistema funcional mínimo para 1 colegio piloto

- [x] Estructura de proyecto y base de datos
- [x] Autenticación y roles (admin, docente, alumno, padre)
- [x] CRUD de usuarios con perfiles automáticos
- [x] Estructura académica (niveles, grados, secciones, cursos)
- [x] Matrículas
- [x] Registro de notas por bimestre (planilla)
- [x] Registro de asistencia diaria
- [x] Dashboard por rol
- [x] Seeder con datos demo
- [x] Testing básico (Feature tests para flujos críticos)
- [ ] Despliegue en VPS de prueba

**Entregable**: Demo funcional para mostrar a colegios piloto

### Fase 2: Comunicación y Pagos (Semanas 5-8)
**Objetivo**: Completar módulos de valor para padres

- [x] Avisos/anuncios con filtro por destinatario
- [x] Mensajería interna entre roles
- [x] Gestión de pagos (conceptos, registro, estado de cuenta)
- [x] Boleta de notas en PDF
- [x] Vista padre completa (notas, asistencia, pagos de hijos)
- [x] Notificaciones por email (Laravel Notifications)
- [x] Validación y manejo de errores robusto

**Entregable**: Sistema completo para prueba con colegio piloto real

### Fase 3: Aula Virtual (Semanas 9-12)
**Objetivo**: Diferenciador competitivo

- [x] Tareas con archivo adjunto
- [x] Entrega de tareas por alumnos
- [x] Calificación y retroalimentación
- [x] Calendario de tareas pendientes
- [x] Historial de entregas
- [x] Mejoras UX basadas en feedback del piloto

**Entregable**: Aula virtual funcional integrada al ERP

### Fase 4: Multi-Tenancy y SaaS (Semanas 13-16) ✅
**Objetivo**: Preparar para vender como servicio

- [x] Panel super-admin para gestionar colegios
- [x] Registro de nuevos colegios (onboarding)
- [x] Planes de suscripción (básico, estándar, premium)
- [x] Verificación de suscripción vigente
- [x] Subdominios por colegio (opcional)
- [x] Página de landing/marketing
- [x] Pasarela de pago para suscripciones (Mercado Pago / PayPal)

**Entregable**: Plataforma SaaS lista para venta ✅

**Implementación técnica**:
- SuperAdmin: Dashboard con métricas, CRUD de colegios y planes, cambio de plan, toggle activo
- Onboarding: Formulario multi-step con AlpineJS, trial 30 días automático
- 3 Planes: Básico ($19/mes), Estándar ($35/mes), Premium ($55/mes) con límites de alumnos
- Middlewares: VerificarSuscripcion (suscripcion.vigente), VerificarLimitePlan (plan.limite)
- Landing page con pricing, features, CTA
- MercadoPago integrado con modo demo (sin token activa directamente)
- Tests: 113 tests, 261 assertions pasando

### Fase 5: Pulido y Lanzamiento (Semanas 17-20)
**Objetivo**: Calidad de producción

- [ ] Optimización de queries (N+1, eager loading)
- [ ] Caché estratégico (Redis)
- [ ] Compilar Tailwind con Vite (quitar CDN)
- [ ] Tests automatizados (PHPUnit + Pest)
- [ ] Documentación de usuario (manual en PDF/web)
- [ ] SEO de landing page
- [ ] Setup de monitoreo (logs, uptime, errores)

**Entregable**: Producto listo para mercado

### Fase 6: Crecimiento (Semanas 21-24)
**Objetivo**: Funciones avanzadas y crecimiento

- [ ] App móvil o PWA
- [ ] API REST para integraciones
- [ ] Reportes avanzados con gráficos
- [ ] Exportación a Excel
- [ ] Importación masiva de alumnos (CSV)
- [ ] Módulo de bibliotecas/recursos digitales
- [ ] WhatsApp Business API para notificaciones

---

## Modelo de Negocio SaaS

### Planes de Suscripción

| Plan | Precio (USD/mes) | Alumnos | Características |
|---|---|---|---|
| **Básico** | $29 | Hasta 100 | Notas, asistencia, usuarios, boleta PDF |
| **Estándar** | $49 | Hasta 300 | + Pagos, mensajería, avisos, tareas |
| **Premium** | $79 | Hasta 600 | + Aula virtual completa, reportes avanzados, soporte prioritario |
| **Enterprise** | Cotización | Ilimitado | + Personalización, API, capacitación |

### Precios Latinoamérica (ajustados)
| Plan | Precio (USD/mes) |
|---|---|
| Básico | $19 |
| Estándar | $35 |
| Premium | $55 |

### Métricas clave
- **CAC objetivo**: < $50 (adquisición vía redes + referidos)
- **Churn objetivo**: < 5% mensual
- **LTV mínimo**: $420 (12 meses × $35 promedio)
- **Break-even**: ~15 colegios en plan estándar

### Canales de venta
1. **Directo**: Visitas a colegios con demo en vivo
2. **Digital**: Landing page + Google Ads segmentado por ciudad
3. **Referidos**: 1 mes gratis por cada colegio referido que se suscriba
4. **Redes**: Contenido educativo en Facebook/Instagram/TikTok
5. **Alianzas**: Asociaciones de colegios privados

### Estrategia de precios
- **Trial gratis 30 días** con datos demo precargados
- **Descuento anual**: 2 meses gratis (paga 10, obtiene 12)
- **Onboarding gratuito**: Migración de datos asistida para los primeros 20 clientes
- **Garantía**: Reembolso total si cancela en los primeros 60 días

---

## Recomendaciones de Negocio

### Diferenciación vs. competencia (Cubicol, Sieweb, etc.)
1. **Simplicidad**: 3 clics máximo para cualquier acción frecuente
2. **Velocidad**: Carga < 2 segundos en cualquier pantalla
3. **Precio accesible**: 50-60% más barato que soluciones existentes
4. **Soporte humano**: Chat en vivo con respuesta en < 2 horas
5. **Onboarding express**: Colegio funcionando en < 24 horas

### Primeros pasos comerciales
1. Conseguir 1-2 colegios piloto (amigos/conocidos) para validar
2. Recoger feedback intensivo durante 30 días
3. Iterar rápido basado en feedback real
4. Crear caso de éxito con testimonios
5. Lanzar venta activa con 3-5 colegios meta por mes

### Métricas a monitorear
- Colegios activos / churn rate
- Usuarios activos diarios (DAU) por colegio
- Funciones más usadas vs. menos usadas
- Tiempo promedio de respuesta de soporte
- NPS (Net Promoter Score) trimestral

---

## Roadmap Técnico Post-Lanzamiento

| Prioridad | Feature | Impacto |
|---|---|---|
| Alta | PWA / App móvil | Adopción masiva (padres/docentes) |
| Alta | WhatsApp notifications | Engagement de padres |
| Media | Reportes con gráficos | Valor para directores |
| Media | Importación CSV alumnos | Reducir fricción onboarding |
| Baja | API REST pública | Integraciones terceros |
| Baja | Multi-idioma | Expansión regional |
