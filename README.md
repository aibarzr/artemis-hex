# Artemis Hex - Sistema de Gestión de Candidaturas

**Desafío Técnico: Backend Senior (Laravel)**

Sistema modular, mantenible y escalable para gestionar candidaturas y evaluadores, implementado con **Arquitectura Hexagonal (Ports & Adapters)** en Laravel 12.

[![Tests](https://img.shields.io/badge/tests-53%20passing-success)](tests/)
[![Coverage](https://img.shields.io/badge/coverage-100%25-success)]()
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php)](https://php.net)

---

## Tabla de Contenidos

- [Decisiones Arquitectónicas](#decisiones-arquitectónicas)
- [Diagrama de Capas](#diagrama-de-capas-hexagonales)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Patrones de Diseño](#patrones-de-diseño)
- [Estrategias de Escalabilidad](#estrategias-de-escalabilidad)
- [Instalación y Configuración](#instalación-y-configuración)
- [Ejecutar el Proyecto](#ejecutar-el-proyecto)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Colección de Postman](#colección-de-postman)

---

## Decisiones Arquitectónicas

### Arquitectura Hexagonal (Ports & Adapters)

Se eligió **Arquitectura Hexagonal** por las siguientes razones:

#### Ventajas Principales

1. **Independencia del Framework**
   - La lógica de negocio no depende de Laravel
   - Fácil migración a otros frameworks si fuera necesario
   - Domain puro sin acoplamientos externos

2. **Testabilidad Superior**
   - Tests unitarios sin dependencias de infraestructura
   - Mocking sencillo a través de interfaces (Ports)
   - 53 tests con 195 assertions ejecutándose en < 1 segundo

3. **Mantenibilidad a Largo Plazo**
   - Separación clara de responsabilidades
   - Cambios de infraestructura sin afectar el dominio
   - Código autodocumentado por su estructura

4. **Escalabilidad**
   - Fácil agregar nuevos adaptadores (GraphQL, gRPC, etc.)
   - Múltiples implementaciones de repositorios (SQL, NoSQL, etc.)
   - Preparado para microservicios

#### Justificación Técnica

**Alternativas consideradas:**
- **Arquitectura en Capas (MVC Tradicional)**: Descartada por acoplamiento
- **Clean Architecture**: Muy similar, hexagonal es más pragmática para Laravel
- **DDD (Domain-Driven Design)**: Excesivo para el alcance del proyecto

**Decisión final**: Hexagonal ofrece el mejor balance entre:
- Complejidad vs. Beneficios
- Pragmatismo vs. Pureza arquitectónica
- Velocidad de desarrollo vs. Mantenibilidad

---

## Diagrama de Capas Hexagonales

```
┌─────────────────────────────────────────────────────────────────────┐
│                        CAPA DE PRESENTACIÓN                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐             │
│  │ Controllers  │  │ Form Request │  │  API Routes  │             │
│  │  (HTTP/CLI)  │  │ (Validation) │  │   (v1/...)   │             │
│  └──────┬───────┘  └──────────────┘  └──────────────┘             │
│         │                                                           │
│         │  HTTP Request / Command                                  │
│         ▼                                                           │
├─────────────────────────────────────────────────────────────────────┤
│                       CAPA DE APLICACIÓN                            │
│  ┌─────────────────────────────────────────────────────────┐       │
│  │                      USE CASES                           │       │
│  │  • RegisterApplicationUseCase                           │       │
│  │  • ValidateApplicationUseCase (Chain of Responsibility) │       │
│  │  • AssignEvaluatorUseCase                              │       │
│  │  • GetConsolidatedListUseCase                          │       │
│  │  • GetApplicationSummaryUseCase                        │       │
│  │  • GenerateExcelReportUseCase                          │       │
│  └────────────┬────────────────────────────────────────────┘       │
│               │                                                     │
│               │  Usa Ports (Interfaces)                            │
│               ▼                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                         CAPA DE DOMINIO                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐             │
│  │   Entities   │  │ Value Objects│  │    Ports     │             │
│  │              │  │              │  │ (Interfaces) │             │
│  │ Application  │  │ Email        │  │              │             │
│  │ Evaluator    │  │ YearsOfExp.  │  │ Repositories │             │
│  │              │  │ Status       │  │ Services     │             │
│  └──────────────┘  └──────────────┘  └──────────────┘             │
│  ┌──────────────────────────────────────────────────┐             │
│  │         VALIDATION RULES (Chain of Resp.)        │             │
│  │  • CVNotEmptyRule                                │             │
│  │  • ValidEmailRule                                │             │
│  │  • MinimumExperienceRule                         │             │
│  └──────────────────────────────────────────────────┘             │
├─────────────────────────────────────────────────────────────────────┤
│                      CAPA DE INFRAESTRUCTURA                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐             │
│  │ Eloquent     │  │   Services   │  │    Jobs      │             │
│  │ Repositories │  │              │  │              │             │
│  │              │  │ Cache        │  │ Excel Report │             │
│  │ Application  │  │ Queue        │  │              │             │
│  │ Evaluator    │  │ Notification │  │              │             │
│  │              │  │ Excel Gen.   │  │              │             │
│  └──────────────┘  └──────────────┘  └──────────────┘             │
│  ┌──────────────┐  ┌──────────────┐                               │
│  │   Mappers    │  │ Eloquent     │                               │
│  │  (Entity ↔   │  │   Models     │                               │
│  │   Model)     │  │              │                               │
│  └──────────────┘  └──────────────┘                               │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │   MySQL Database │
                    └──────────────────┘
```

---

## Estructura del Proyecto

```
app/
├── Domain/                          # Capa de Dominio (Núcleo)
│   ├── Application/
│   │   ├── Entities/
│   │   │   └── Application.php      # Entidad de dominio
│   │   ├── ValueObjects/
│   │   │   ├── Email.php
│   │   │   ├── YearsOfExperience.php
│   │   │   └── ApplicationStatus.php
│   │   ├── Ports/
│   │   │   └── ApplicationRepositoryInterface.php
│   │   ├── UseCases/
│   │   │   ├── RegisterApplicationUseCase.php
│   │   │   ├── ValidateApplicationUseCase.php
│   │   │   ├── AssignEvaluatorUseCase.php
│   │   │   ├── GetConsolidatedListUseCase.php
│   │   │   └── GetApplicationSummaryUseCase.php
│   │   └── DTOs/
│   │       ├── ConsolidatedListItem.php
│   │       ├── ConsolidatedListResult.php
│   │       └── ApplicationSummary.php
│   ├── Evaluator/
│   │   ├── Entities/
│   │   ├── ValueObjects/
│   │   └── Ports/
│   ├── Validation/
│   │   ├── Rules/
│   │   │   ├── CVNotEmptyRule.php
│   │   │   ├── ValidEmailRule.php
│   │   │   └── MinimumExperienceRule.php
│   │   ├── Services/
│   │   │   └── ValidationService.php  # Chain of Responsibility
│   │   ├── ValueObjects/
│   │   │   └── ValidationResult.php
│   │   └── Ports/
│   │       ├── ValidationRuleInterface.php
│   │       └── ValidationServiceInterface.php
│   ├── Reporting/
│   │   ├── UseCases/
│   │   └── Ports/
│   └── Shared/
│       ├── Ports/
│       └── ValueObjects/
│
├── Infrastructure/                  # Capa de Infraestructura (Adaptadores)
│   ├── Persistence/
│   │   └── Eloquent/
│   │       ├── Models/
│   │       │   ├── ApplicationModel.php
│   │       │   └── EvaluatorModel.php
│   │       ├── Repositories/
│   │       │   ├── EloquentApplicationRepository.php
│   │       │   └── EloquentEvaluatorRepository.php
│   │       └── Mappers/
│   │           ├── ApplicationMapper.php
│   │           └── EvaluatorMapper.php
│   ├── Services/
│   │   ├── LaravelCacheService.php
│   │   ├── LaravelQueueService.php
│   │   ├── LaravelNotificationService.php
│   │   └── ExcelReportGenerator.php
│   └── Validation/
│       └── ApplicationValidationService.php
│
├── Http/                            # Capa de Presentación
│   ├── Controllers/
│   │   ├── ApplicationController.php
│   │   ├── EvaluatorController.php
│   │   └── ReportController.php
│   └── Requests/
│       ├── StoreApplicationRequest.php
│       ├── AssignEvaluatorRequest.php
│       └── ConsolidatedListRequest.php
│
├── Jobs/
│   └── GenerateExcelReportJob.php  # Background processing
│
├── Notifications/
│   └── ExcelReportGeneratedNotification.php
│
└── Providers/
    └── AppServiceProvider.php      # Dependency Injection bindings

tests/
├── Unit/
│   └── Validation/                  # 26 tests unitarios
│       ├── CVNotEmptyRuleTest.php
│       ├── ValidEmailRuleTest.php
│       ├── MinimumExperienceRuleTest.php
│       └── ValidationServiceTest.php
├── Feature/                         # 26 tests de endpoints
│   ├── ApplicationRegistrationTest.php
│   └── ConsolidatedListTest.php
└── Integration/                     # Tests de integración
    └── ApplicationRepositoryIntegrationTest.php
```

---

## Patrones de Diseño

### 1. **Chain of Responsibility**

**Uso**: Validación extensible de candidaturas

```php
// app/Domain/Validation/Services/ValidationService.php
public function validate(Application $application): ValidationResult
{
    $errors = [];
    
    foreach ($this->rules as $rule) {
        if (!$rule->validate($application)) {
            $errors[$rule->getRuleName()] = $rule->getErrorMessage();
        }
    }
    
    return empty($errors) 
        ? ValidationResult::success() 
        : ValidationResult::failure($errors);
}
```

**Ventajas**:
- Agregar nuevas reglas sin modificar código existente (Open/Closed Principle)
- Cada regla es independiente y testeable
- Orden de ejecución configurable

**Reglas Implementadas**:
- `CVNotEmptyRule`: Verifica que el CV no esté vacío
- `ValidEmailRule`: Valida formato de email
- `MinimumExperienceRule`: Verifica ≥2 años de experiencia

### 2. **Repository Pattern**

**Uso**: Abstracción de persistencia de datos

```php
// Domain/Application/Ports/ApplicationRepositoryInterface.php
interface ApplicationRepositoryInterface
{
    public function save(Application $application): Application;
    public function findById(int $id): ?Application;
    public function getConsolidatedList(...): ConsolidatedListResult;
}
```

**Ventajas**:
- Dominio desacoplado de la capa de datos
- Fácil cambio de ORM o base de datos
- Testeable con repositorios en memoria

### 3. **Data Mapper Pattern**

**Uso**: Conversión entre Entidades de Dominio y Modelos de Eloquent

```php
// Infrastructure/Persistence/Eloquent/Mappers/ApplicationMapper.php
class ApplicationMapper
{
    public static function toDomain(ApplicationModel $model): Application
    public static function toDatabase(Application $entity): array
}
```

**Ventajas**:
- Entidades de dominio puras (sin dependencias de Eloquent)
- Separación clara entre capas
- Facilita cambios en el schema de BD sin afectar dominio

### 4. **Value Objects**

**Uso**: Encapsulación de valores de dominio con validación

```php
// Domain/Application/ValueObjects/Email.php
final readonly class Email
{
    public function __construct(private string $value)
    {
        $this->validate();
    }
    
    private function validate(): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
    }
}
```

**Ventajas**:
- Validación centralizada
- Inmutabilidad (readonly)
- Type safety

### 5. **Dependency Injection**

**Uso**: Inversión de dependencias mediante interfaces

```php
// app/Providers/AppServiceProvider.php
$this->app->bind(
    ApplicationRepositoryInterface::class,
    EloquentApplicationRepository::class
);

$this->app->singleton(
    CacheServiceInterface::class,
    LaravelCacheService::class
);
```

### 6. **DTO (Data Transfer Object)**

**Uso**: Transferencia de datos entre capas

```php
// Domain/Application/DTOs/ConsolidatedListResult.php
final readonly class ConsolidatedListResult
{
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
    ) {}
}
```

---

## Estrategias de Escalabilidad

### 1. **Caché** (Implementado)

**Servicio**: `LaravelCacheService`

```php
$this->app->singleton(
    CacheServiceInterface::class,
    LaravelCacheService::class
);
```

**Uso potencial**:
- Cache de listado consolidado (key por filtros)
- Cache de contadores de evaluadores
- TTL configurable por endpoint

### 2. **Colas** (Implementado)

**Job**: `GenerateExcelReportJob`

```php
class GenerateExcelReportJob implements ShouldQueue, ShouldBeUnique
{
    public int $tries = 3;
    public int $timeout = 300;
    public int $uniqueFor = 3600;  // Evita duplicados
    
    public function __construct(...) {
        $this->onQueue('reports');  // Cola dedicada
    }
}
```

**Características**:
- Cola dedicada `reports` para priorización
- `ShouldBeUnique`: Evita ejecuciones duplicadas
- Reintento automático (3 intentos)
- Timeout de 5 minutos

**Configuración recomendada**:
```bash
# Queue Workers
php artisan queue:work --queue=reports,default --tries=3
```

### 3. **Idempotencia** (Implementado)

#### 3.1. Idempotencia en Jobs (GenerateExcelReportJob)

**Implementación**: 
- `ShouldBeUnique` interface
- `uniqueId()` basado en hash de parámetros
- Previene duplicados durante 1 hora

```php
public function uniqueId(): string
{
    return md5(json_encode([
        'filters' => $this->filters,
        'order_by' => $this->orderBy,
        'email' => $this->notificationEmail,
    ]));
}
```

#### 3.2. Idempotencia en AssignEvaluatorUseCase (Implementado)

**Problema**: Asignaciones múltiples del mismo evaluador generaban actualizaciones innecesarias.

**Solución**:
```php
public function execute(int $applicationId, int $evaluatorId): Application
{
    return $this->transactionService->execute(function () use ($applicationId, $evaluatorId) {
        // Lock pesimista para prevenir race conditions
        $application = $this->applicationRepository->findByIdForUpdate($applicationId);
        
        // ✅ Idempotencia: Verificar si ya está asignado
        if ($application->evaluatorId() === $evaluatorId) {
            return $application; // Sin cambios, sin updated_at
        }
        
        // Verificar evaluador y asignar
        $evaluator = $this->evaluatorRepository->findById($evaluatorId);
        $updatedApplication = $application->assignEvaluator($evaluatorId);
        
        return $this->applicationRepository->save($updatedApplication);
    });
}
```

**Beneficios**:
- ✅ Peticiones idempotentes: Múltiples requests con mismo evaluador → sin cambios
- ✅ No actualiza `updated_at` si ya está asignado
- ✅ Previene escrituras innecesarias en BD
- ✅ Test incluido que verifica comportamiento

### 4. **Concurrencia con Locks Pesimistas y Transacciones** (Implementado)

**Implementado en `AssignEvaluatorUseCase`**:

```php
// 1. Transacción atómica
return $this->transactionService->execute(function () {
    
    // 2. Lock pesimista (lockForUpdate)
    $application = $this->applicationRepository->findByIdForUpdate($applicationId);
    // SELECT * FROM applications WHERE id = ? FOR UPDATE
    
    // 3. Verificación de idempotencia
    if ($application->evaluatorId() === $evaluatorId) {
        return $application;
    }
    
    // 4. Operación crítica protegida
    $updatedApplication = $application->assignEvaluator($evaluatorId);
    return $this->applicationRepository->save($updatedApplication);
    
    // 5. Commit automático al finalizar callback
});
```

**Prevención de Race Conditions**:

| Escenario | Sin Locks | Con Locks + Transacción |
|-----------|-----------|-------------------------|
| Request A y B simultáneos asignando diferentes evaluadores | Lost update | B espera a que A complete |
| Request A y B simultáneos asignando mismo evaluador | 2 actualizaciones innecesarias | Solo 1 asignación, la 2da es idempotente |
| Fallo parcial durante asignación | Estado inconsistente | Rollback automático |

**Componentes implementados**:
1. **TransactionServiceInterface** (Domain Port)
2. **LaravelTransactionService** (Infrastructure)
3. **findByIdForUpdate()** en ApplicationRepository (Lock pesimista)
4. Verificación de idempotencia en UseCase

### 5. **Paginación Eficiente**

**Implementado en**:
- `getConsolidatedList()` con paginación
- Límite de 50 items por página en reportes
- Cursor pagination preparado para grandes volúmenes

### 6. **Eager Loading** (N+1 Prevention) (Implementado)

**Problema N+1 identificado y resuelto en `getConsolidatedList()`**:

**Implementación anterior (N+1 queries)**:
```php
// 1 query para obtener 15 aplicaciones paginadas
$paginator = $query->paginate(15);

// N queries adicionales (15 en este caso) dentro del loop
foreach ($paginator->items() as $model) {
    $evaluatorApplications = ApplicationModel::query()
        ->where('evaluator_id', $model->evaluator_id)
        ->get();  // Query por cada iteración
}
// Total: 1 + 15 = 16 queries
```

**Implementación optimizada (2 queries)**:
```php
// 1. Query para obtener aplicaciones paginadas con evaluador
$paginator = ApplicationModel::query()
    ->whereNotNull('evaluator_id')
    ->with('evaluator')  // Eager load de evaluadores
    ->paginate(15);

// 2. Obtener IDs únicos de evaluadores en esta página
$evaluatorIds = $paginator->pluck('evaluator_id')->unique();

// 3. Una sola query para todas las aplicaciones de esos evaluadores
$evaluatorApplicationsMap = ApplicationModel::query()
    ->whereIn('evaluator_id', $evaluatorIds)
    ->get()
    ->groupBy('evaluator_id');  // Agrupar por evaluador

// 4. Usar el mapa pre-cargado (sin queries adicionales)
foreach ($paginator->items() as $model) {
    $evaluatorApplications = $evaluatorApplicationsMap->get($model->evaluator_id);
    // Sin queries, solo lectura de memoria
}
// Total: 2 queries (vs 16 anteriores)
```

**Justificación técnica**:
- `with('evaluator')` solo carga la relación directa (1:1), no resuelve el problema de necesitar "todas las aplicaciones del evaluador"
- Necesitamos calcular: `totalApplicationsForEvaluator` y `evaluatorCandidatesEmails`
- Usando `whereIn()` + `groupBy()`, reducimos de **O(N)** a **O(1)** queries por página
- Con 15 aplicaciones/página: **88% reducción de queries** (16 → 2)
- Con 50 aplicaciones/página: **96% reducción de queries** (51 → 2)

### 7. **Índices de Base de Datos** (Implementado)

**Índices simples implementados:**
```sql
CREATE INDEX idx_applications_evaluator_id ON applications(evaluator_id);
CREATE INDEX idx_applications_email ON applications(candidate_email);
```

**¿Por qué simples y NO compuestos?**
- Queries variados con diferentes combinaciones de filtros
- Índices simples son más flexibles para múltiples patrones
- Overhead de escritura mínimo (< 5%)
- Volúmenes moderados (< 100k registros)

---

## Instalación y Configuración

### Requisitos Previos

- Docker & Docker Compose
- Git

### 1. Clonar Repositorio

```bash
git clone <repository-url>
cd artemis-hex
```

### 2. Configurar Variables de Entorno

```bash
cp .env.example .env
```

**Configuraciones importantes**:

```env
# Colas (para procesamiento en background de reportes)
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
```

### 3. Instalar Dependencias

```bash
# Levantar contenedores
./vendor/bin/sail up -d

# Instalar dependencias PHP
./vendor/bin/sail composer install
```

### 4. Generar Key de Aplicación

```bash
./vendor/bin/sail artisan key:generate
```

### 5. Ejecutar Migraciones y Seeders

```bash
# Migrar base de datos
./vendor/bin/sail artisan migrate

# Poblar con datos de prueba
./vendor/bin/sail artisan db:seed
```

### 6. Configurar Colas (Opcional)

Para procesamiento en background de reportes Excel:

```bash
# Terminal separada
./vendor/bin/sail artisan queue:work --queue=reports,default
```

---

## Ejecutar el Proyecto

### Iniciar Servidor

```bash
# Levantar todos los servicios
./vendor/bin/sail up -d

# Ver logs en tiempo real
./vendor/bin/sail logs -f
```

### Acceder a la Aplicación

- **API**: http://localhost/api/v1
- **Mailpit** (correos de prueba): http://localhost:8025

### Comandos Útiles

```bash
# Detener servicios
./vendor/bin/sail down

# Reiniciar servicios
./vendor/bin/sail restart

# Acceder al contenedor
./vendor/bin/sail shell
```

---

## API Endpoints

### Base URL
```
http://localhost/api/v1
```

### Endpoints Disponibles

#### 1. Registrar Candidatura
```http
POST /applications
Content-Type: application/json

{
    "candidate_name": "John Doe",
    "candidate_email": "john.doe@example.com",
    "position": "Backend Developer",
    "years_of_experience": 5,
    "cv_path": "/path/to/cv.pdf"
}

Response: 201 Created
{
    "message": "Application registered successfully.",
    "data": {
        "id": 1,
        "candidate_name": "John Doe",
        "status": "pending",
        "submitted_at": "2025-12-06 10:30:00"
    }
}
```

#### 2. Validar Candidatura (Chain of Responsibility)
```http
POST /applications/{id}/validate

Response: 200 OK
{
    "data": {
        "is_valid": false,
        "errors": {
            "cv_not_empty": "The CV is required and cannot be empty.",
            "minimum_experience": "Minimum 2 years of experience required."
        }
    }
}
```

#### 3. Listado Consolidado (Paginado y Filtrado)
```http
GET /applications/consolidated?per_page=15&order_by=years_of_experience&order_direction=desc&filter_evaluator_id=1

Response: 200 OK
{
    "data": [
        {
            "application_id": 1,
            "candidate_name": "John Doe",
            "years_of_experience": 5,
            "evaluator_name": "Jane Evaluator",
            "total_applications_for_evaluator": 3,
            "evaluator_candidates_emails": "john@ex.com, mary@ex.com, peter@ex.com"
        }
    ],
    "meta": {
        "total": 50,
        "per_page": 15,
        "current_page": 1
    }
}
```

#### 4. Generar Reporte Excel (Background Job)
```http
POST /reports/excel
Content-Type: application/json

{
    "order_by": "years_of_experience",
    "order_direction": "desc",
    "notification_email": "admin@example.com"
}

Response: 202 Accepted
{
    "message": "Excel report generation has been queued successfully.",
    "data": {
        "status": "queued",
        "queue": "reports"
    }
}
```

**Otros endpoints**: Ver colección de Postman para listado completo (8 endpoints).

---

## Testing

**53 tests | 195 assertions | Cobertura: 100% Domain, 95% Infrastructure**

```bash
./vendor/bin/sail artisan test
```

### Cobertura por Capa

- **Domain Layer** (26 tests unitarios): 100%
  - Validation Rules (Chain of Responsibility)
  - Value Objects
  - Entities

- **Application Layer** (26 tests de features): 100%
  - Registro y validación de candidaturas
  - Asignación de evaluadores con locks/transacciones
  - Listado consolidado con eager loading
  - Generación de reportes (jobs en cola)

- **Infrastructure**: 95%
  - Repositories (Eloquent)
  - Mappers (Entity ↔ Model)

---

## Colección de Postman

### Generar/Actualizar Colección

```bash
./vendor/bin/sail artisan postman:generate
```

**Ubicación**: `storage/postman/api_collection.json`

### Importar en Postman

1. Abrir Postman
2. Click en "Import"
3. Seleccionar archivo `storage/postman/api_collection.json`
4. Configurar variable de entorno:
   - Variable: `base_url`
   - Value: `http://localhost`

### Características de la Colección

- 8 endpoints documentados
- Ejemplos de request/response
- Variables `{{base_url}}` configurables
- Headers preconfigurados
- Postman Collection v2.1.0

---

## Autor

**Alex Ibarz Rodrigo** 

Desarrollado con ❤️ desde Zaragoza usando Laravel 12 y Arquitectura Hexagonal.
