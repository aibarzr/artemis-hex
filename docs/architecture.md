# Arquitectura Hexagonal - Artemis

## Estructura de Capas

```
app/
├── Domain/              # Capa de Dominio (lógica de negocio pura)
│   ├── Application/     # Contexto de Candidaturas
│   ├── Evaluator/       # Contexto de Evaluadores
│   ├── Validation/      # Contexto de Validación
│   ├── Reporting/       # Contexto de Reportes
│   └── Shared/          # Compartido entre dominios
├── Application/         # Casos de Uso (Use Cases)
└── Infrastructure/      # Adaptadores (DB, Cache, Queues, APIs externas)
```

## Dominios Definidos

### 1. Application Domain (Candidaturas)
**Responsabilidad:** Gestión del ciclo de vida de las candidaturas.

**Ports (Interfaces):**
- `ApplicationRepositoryInterface`: Persistencia de candidaturas
  - `create()`: Registrar nueva candidatura
  - `findById()`: Buscar por ID
  - `update()`: Actualizar candidatura
  - `getConsolidatedList()`: Listado paginado con filtros
  - `countByEvaluator()`: Contar candidaturas por evaluador

### 2. Evaluator Domain (Evaluadores)
**Responsabilidad:** Gestión de evaluadores y asignaciones.

**Ports (Interfaces):**
- `EvaluatorRepositoryInterface`: Persistencia de evaluadores
  - `create()`: Crear evaluador
  - `findById()`: Buscar por ID
  - `assignToApplication()`: Asignar a candidatura
  - `getApplicationsByEvaluator()`: Obtener candidaturas asignadas

### 3. Validation Domain (Validación)
**Responsabilidad:** Validación extensible de candidaturas usando Chain of Responsibility.

**Ports (Interfaces):**
- `ValidationRuleInterface`: Contrato para reglas de validación
  - `validate()`: Ejecutar validación
  - `getErrorMessage()`: Obtener mensaje de error
  - `getRuleName()`: Nombre de la regla
  
- `ValidationServiceInterface`: Servicio orquestador de validaciones
  - `addRule()`: Añadir regla al chain (Open/Closed Principle)
  - `validate()`: Ejecutar todas las reglas
  - `isValid()`: Verificar si es válida

### 4. Reporting Domain (Reportes)
**Responsabilidad:** Generación de informes y notificaciones.

**Ports (Interfaces):**
- `ReportGeneratorInterface`: Generación de reportes (Excel, PDF, etc.)
  - `generate()`: Generar reporte
  - `getFormat()`: Formato del reporte
  
- `NotificationServiceInterface`: Envío de notificaciones
  - `sendReportGenerated()`: Notificar cuando el reporte está listo

### 5. Shared Domain (Compartido)
**Responsabilidad:** Servicios transversales y value objects.

**Ports (Interfaces):**
- `CacheServiceInterface`: Gestión de caché
  - `get()`, `put()`, `remember()`: Operaciones de caché
  
- `QueueServiceInterface`: Gestión de colas
  - `dispatch()`: Encolar trabajo
  - `dispatchSync()`: Ejecutar sincrónicamente
  - `dispatchAfterResponse()`: Ejecutar después de la respuesta

## Principios Aplicados

### Hexagonal Architecture (Ports & Adapters)
- **Ports:** Interfaces que definen contratos (en `Domain/*/Ports/`)
- **Adapters:** Implementaciones concretas (en `Infrastructure/`)
- **Domain:** Lógica de negocio independiente del framework

### SOLID Principles
- **Single Responsibility:** Cada dominio tiene una responsabilidad clara
- **Open/Closed:** ValidationService permite añadir reglas sin modificar código existente
- **Liskov Substitution:** Todas las implementaciones cumplen sus contratos
- **Interface Segregation:** Interfaces específicas por funcionalidad
- **Dependency Inversion:** Dependemos de abstracciones (ports), no de implementaciones

### DDD (Domain-Driven Design)
- **Bounded Contexts:** Cada dominio es un contexto acotado
- **Ubiquitous Language:** Nombres del dominio reflejan el negocio
- **Value Objects:** En `Domain/Shared/ValueObjects/`
- **Domain Exceptions:** En `Domain/Shared/Exceptions/`

## Flujo de Dependencias

```
HTTP/API Controller → Use Case (Application Layer) → Domain Service → Repository Port
                                                                           ↓
                                                            Infrastructure Adapter
```

**Regla de Oro:** Las dependencias siempre apuntan hacia adentro (Domain nunca depende de Infrastructure)

## Ventajas de esta Arquitectura

1. **Testabilidad:** Lógica de negocio totalmente independiente del framework
2. **Mantenibilidad:** Separación clara de responsabilidades
3. **Escalabilidad:** Fácil añadir nuevas funcionalidades sin modificar existentes
4. **Flexibilidad:** Cambiar implementaciones (DB, cache, queues) sin afectar la lógica
5. **Reusabilidad:** Domain services reutilizables en diferentes contextos

## Análisis Estático de Tipos

El proyecto usa **Larastan (PHPStan nivel 6)** para análisis estático:

```bash
vendor/bin/sail composer analyse
```
