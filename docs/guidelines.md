# Desafío Técnico: Backend Senior (Laravel)

El objetivo es desarrollar una **API modular, mantenible y escalable** para gestionar candidaturas y evaluadoresoco principal es la **arquitectura, el diseño limpio, los patrones, el *testing*** y la **escalabilidad**.

### Requerimientos Funcionales ✔️

| # | Funcionalidad | Descripción | Enfoque Senior Clave |
|:---:|:---|:---|:---|
| 1 | **Registro de Candidaturas** | Guardar Nombre, Email, Años Exp., CV (texto), Fecha de creación. Opcional: Historial de acciones/bitácora. . |
| 2 | **Validación de Candidaturas** | Endpoint para validar si una candidatura es válida (CV, Email válido, $\ge 2$ años de experiencia). **Debe ser extensible sin modificar reglas existentes**. ara las reglas de validación (sugerido en el reto) |
| 3 | **Asignación de Evaluadores** | Endpoint para asignar evaluadores a candidaturas (1 evaluador $\to$ M candidaturas). Registrar fecha de asignación. . |
| 4 | **Listado Consolidado** | Endpoint con paginación que lista candidaturas asignadas. Muestra: Candidato (Nombre, Email, Años Exp.), Evaluador (Nombre, Fecha Asignación), Total de candidaturas asignadas al evaluador, Emails concatenados de candidatos evaluados por él. or defecto (Años Exp.). Filtrado por cualquier columna. **Eficiencia** a cargas altas.  |
| 5 | **Resumen de Candidatura** | Datos completos, Validaciones superadas/falladas, Evaluador, Estado, Cálculos derivados útiles. . |
| 6 | **Informe en Excel** | Generar Excel del Listado Consolidado (punto 4), 50 candidatos/página. **Uso de librería externa**. **Notificación por email** al generarse. 2]. |

---

### Escalabilidad y Arquitectura 🚀

Se busca un **alto rendimiento y escalabilidad horizontal**.

* **Arquitectura:** Se recomienda **Arquitectura Hexagonal** o **Limpia**.
* **Desacoplamiento:** Separación clara entre **lógica de negocio**, **infraestructura** y **framework (Laravel)**.
    * La capa de datos debe ser reemplazable sin reescribir la lógica de negocio.
* **Estrategias de Escalabilidad:**
    * Implementar **Caché**.
    * Realizar acciones costosas mediante **Colas**.
    * Añadir **Idempotencia**.
    * Manejar **Concurrencia** en asignaciones masivas.

---

### Requisitos Técnicos y Entrega 📦

* **Tecnología:** Laravel 10+, PHP 8.1+.
* **Código:** Modular, escalable, mantenible, **limpio y SOLID**.

#### Lo que se debe entregar:

1.  **Repositorio Git**.
2.  **README.md** con:
    * Decisiones arquitectónicas y Justificación técnica.
    * **Diagrama de capas**.
    * Descripción de la estructura, Escalabilidad y Patrones usados.
    * Instrucciones de ejecución (colas, caché, etc.).
3.  **Tests Automáticos**:
    * Mínimo **4 Unitarios** (debe incluir CoR y lógica de validación).
    * Mínimo **1 de Endpoint** (funcionalidad compleja).
    * Mínimo **1 de Integración** (DB real).
4.  Base de datos y *seeders* básicos.
