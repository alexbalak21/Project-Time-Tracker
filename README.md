# 🧪 R&D Lab Time Tracking System  
A lightweight, structured time‑tracking platform designed for **scientific R&D environments** (biochemistry, analytical chemistry, enzymology, QC, method development, etc.).  
This README focuses **exclusively on the data side**: entities, relationships, logic, and examples.

---

# 📌 Overview

This system allows lab personnel to track **time spent per task per day**, with support for:

- **Projects** (research studies, client work, QC batches)
- **Project‑specific tasks**
- **Generic tasks** (usable across all projects)
- **Daily time entries**
- **R&D‑specific workflows** (experiments, analysis, documentation, calibration…)

The data model is optimized for:

- Scientific traceability  
- Regulatory documentation  
- Project‑based reporting  
- Weekly timesheet views  
- Flexible task management  

---

# 🧱 Data Model Summary

The system is built around four core entities:

1. **User** — lab personnel  
2. **Project** — research or client work  
3. **Task** — lab activity (generic or project‑specific)  
4. **TimeEntry** — time spent on a task for a project on a given day  

---

# 👤 User

Represents a scientist, technician, analyst, or manager.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Unique identifier |
| name | string | Full name |
| email | string | Work email |
| role | string | Optional (Scientist, Technician…) |
| is_active | boolean | Soft‑delete / deactivation |

---

# 🧬 Project

Represents a scientific or client project.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Unique identifier |
| name | string | Project name |
| code | string | Optional project code (e.g., PRJ‑ATP‑2026) |
| description | text | Optional |
| status | enum | active / on_hold / completed |
| start_date | date | Optional |
| end_date | date | Optional |
| owner_user_id | FK(User) | Optional project owner |
| client_name | string | Optional |
| color | string | Optional UI color |

---

# 🧪 Task

Represents a lab activity.  
A task can be:

- **Generic** → available for all projects  
- **Project‑specific** → belongs to one project  

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Unique identifier |
| name | string | Task name |
| description | text | Optional |
| user_id | FK(User) | Creator/owner |
| project_id | FK(Project) nullable | NULL = generic task |
| is_generic | boolean | true = generic task |
| is_active | boolean | Soft‑delete |

### Generic task examples
- Documentation  
- Lab cleaning  
- Equipment calibration  
- Reagent preparation  
- Inventory management  
- Safety procedures  
- Internal meeting  
- Reporting  

### Project‑specific task examples
- Sample preparation  
- HPLC/UPLC analysis  
- LC‑MS/MS analysis  
- Enzyme activity assay  
- Protein purification  
- Cell culture maintenance  
- Method development  
- QC batch analysis  
- Data interpretation  

---

# ⏱️ TimeEntry

Represents time spent by a user on a task for a project on a specific date.

| Field | Type | Description |
|-------|------|-------------|
| id | PK | Unique identifier |
| user_id | FK(User) | Who performed the work |
| project_id | FK(Project) | Project worked on |
| task_id | FK(Task) | Task performed |
| date | date | Day of the entry |
| duration_minutes | integer | Time spent (in minutes) |
| notes | text | Optional notes |
| created_at | datetime | Auto |
| updated_at | datetime | Auto |

### Why store `project_id` even though `task_id` implies it?
- Faster queries  
- Simpler reporting  
- Historical consistency  
- Avoids complex joins  

---

# 🔗 Relationships

- **User 1‑N TimeEntry**
- **Project 1‑N TimeEntry**
- **Task 1‑N TimeEntry**
- **Project 1‑N Task** (project‑specific tasks)
- **Task (generic)** → available to all projects

---

# 🔍 Query Examples

### Get all tasks available for a project
```sql
SELECT *
FROM tasks
WHERE project_id = :projectId
   OR is_generic = true;

```

---

# 🌐 API Summary

This project now exposes a simple JSON CRUD API for the four core entities.

| Resource | List/Create | Item Read/Update/Delete |
|----------|-------------|-------------------------|
| users | `GET /api/users` | `GET /api/users/{id}` |
| projects | `GET /api/projects` | `GET /api/projects/{id}` |
| tasks | `GET /api/tasks` | `GET /api/tasks/{id}` |
| time-entries | `GET /api/time-entries` | `GET /api/time-entries/{id}` |

`POST`, `PUT`, `PATCH`, and `DELETE` are supported on the matching item routes. The API now uses MySQL through PDO. See [config/database.php](config/database.php) and [config/schema.sql](config/schema.sql) for setup.
