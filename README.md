# ER Medics — Student Certification &amp; Awards Management System

A secure, modern web application for ER Medics to digitise student registration, course management, training completion tracking, and automated certificate generation.

Built with **Laravel 11**, **PHP 8.3**, **MySQL**, **Blade**, and **Bootstrap 5**.

---

## Features

- **Student management** — profiles, IDs, contacts, emergency contacts, profile photos, training history, archive/restore.
- **Course management** — courses, modules, categories, schedules, locations, fees.
- **Training & assessment** — enrolments, attendance, assessment recording with auto pass/fail outcome.
- **Certificates & awards** — auto-generated PDF certificates with ER Medics branding, unique numbers, QR-code public verification, issuance and revocation log.
- **Trainers & staff** — trainer profiles, assignments, schedules.
- **Reports & dashboard** — registrations, completions, certificates, top courses.
- **Roles & permissions** — Spatie-powered (`Admin`, `Account`, `Customer`); permission-aware sidebar generated from a `app_modules` / `app_submodules` data table.
- **REST API** — `/api/v1/*` with Sanctum bearer tokens, including `/api/v1/me` (user + roles + permissions + dynamic navigation).

## Architecture

Strict layered API design — every module follows:

```
Controller  →  Service  →  Interface  →  Repository
```

- Controllers only validate (FormRequest) and call services.
- Services contain business logic and orchestrate repositories.
- Repositories implement contracts in `App\Repositories\Contracts`.
- All interfaces are bound in `App\Providers\RepositoryServiceProvider`.

The sidebar menu is **data-driven**: stored in `app_modules` / `app_submodules` and filtered per user using Spatie permissions via `App\Services\Navigation\NavigationService`. The same tree is exposed at `GET /api/v1/me`.

## Tech stack

| Layer | Library |
| --- | --- |
| Framework | Laravel 11 |
| PHP | 8.3 |
| Database | MySQL (SQLite supported for quick start) |
| Auth | Laravel Breeze (Blade) + Sanctum (bearer for API) |
| Permissions | spatie/laravel-permission |
| PDF | barryvdh/laravel-dompdf |
| QR codes | simplesoftwareio/simple-qrcode |
| UI | Blade + Bootstrap 5 + Bootstrap Icons (loaded via CDN) |

## Local development

Working directory: `c:\laragon\www\medapp`

### 1. Install

```bash
composer install
php artisan key:generate
```

### 2. Configure database

The bundled `.env` is set up for MySQL on `127.0.0.1:3306`, database `medapp`, user `root`, empty password (Laragon default).

Create the database (Laragon shell or any MySQL client):

```sql
CREATE DATABASE medapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

> Alternatively, edit `.env` to switch to SQLite for a quick start:
> `DB_CONNECTION=sqlite` and run `touch database/database.sqlite`.

### 3. Migrate & seed

```bash
php artisan migrate --seed
```

Seeders that run:

- `RolesAndPermissionsSeeder` — creates `Admin`, `Account`, `Customer` roles and all permissions.
- `ModulesSeeder` — seeds the data-driven sidebar (`app_modules` / `app_submodules`).
- `AdminUserSeeder` — creates the bundled admin account.
- `DemoDataSeeder` — creates demo categories, locations, courses, trainers, and a few students.

### 4. Run

```bash
php artisan serve
# or simply visit the Laragon-served URL:
# http://medapp.test (Laragon auto vhost)
```

App URL: <http://localhost:8000>

### Seeded admin account

| Email | Password |
| --- | --- |
| `superadmin@me.local` | `Admin12345!` |
| `account@me.local`    | `Account12345!` |

> **Self-registration is disabled.** New users are created by an administrator from the **Users** page (`/users`).

## Public certificate verification

Anyone with a verification code or certificate number can verify it without logging in:

- `GET /verify` — verification form
- `GET /verify/{code}` — verification result page
- `GET /api/v1/certificates/verify/{code}` — JSON response

Each certificate's PDF includes a QR code linking directly to its verification URL.

## REST API (Sanctum)

```
POST /api/v1/auth/login           { email, password, device_name? } → { token, user }
POST /api/v1/auth/logout
GET  /api/v1/me                   user + roles + permissions + sidebar
GET  /api/v1/students             list (with filters: q, status, per_page)
GET  /api/v1/courses              list
GET  /api/v1/certificates         list
GET  /api/v1/certificates/verify/{code}   public
```

All authenticated endpoints require `Authorization: Bearer <token>`. Permission enforcement is delegated to controllers via Spatie's gate.

## Project layout (selected)

```
app/
  Http/
    Controllers/         web controllers (resource-style)
      Api/V1/            JSON API controllers
    Requests/<Module>/   FormRequests with authorize() permission checks
  Models/                Eloquent models (Student, Course, Enrolment, Certificate, ...)
  Providers/
    RepositoryServiceProvider.php   binds interfaces → concrete repos
  Repositories/
    BaseRepository.php
    Contracts/           one interface per module
  Services/
    StudentService.php
    CourseService.php
    EnrolmentService.php
    CertificateService.php
    TrainerService.php
    UserService.php
    Navigation/NavigationService.php   builds the permission-filtered sidebar

resources/views/
  layouts/{app,guest}.blade.php
  layouts/partials/sidebar.blade.php   data-driven sidebar partial
  students/, courses/, trainers/, enrolments/, certificates/, users/, reports/
  certificates/pdf/certificate.blade.php   ER Medics-branded PDF template

routes/
  web.php     web routes (resource + permission middleware)
  api.php     /api/v1/* (Sanctum)
  auth.php    Breeze auth (register removed)
```

## Permissions reference

| Resource | Actions |
| --- | --- |
| dashboard, reports, modules | view (update for modules) |
| students, courses, trainers, enrolments, certificates, users, roles | view, create, update, delete |

Roles out of the box:

- **Admin** — all permissions.
- **Account** — view + create/update for students/enrolments/certificates, view-only on courses/trainers/reports.
- **Customer** — view dashboard and certificates only.

## Notes for production

- Run `php artisan storage:link` to expose student photos / cert PDFs in `/storage`.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and a strong `APP_KEY`.
- Set `SANCTUM_STATEFUL_DOMAINS` and `SESSION_DOMAIN` to your real domain(s).
- Cache config and routes: `php artisan config:cache route:cache view:cache`.
