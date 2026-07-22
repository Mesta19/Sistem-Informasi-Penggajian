# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

* **Run Dev Server**: `php spark serve` (Server runs on `http://localhost:8080`)
* **Run Tests**: `vendor/bin/phpunit` or `composer test`
* **Run Single Test File**: `vendor/bin/phpunit tests/unit/KaryawanProfileTest.php`
* **Run Database Migrations**: `php spark migrate`
* **Seed Database**: `php spark db:seed ExampleSeeder`

## Codebase Architecture

### MVC Structure (CodeIgniter 4)
* **Controllers**: 
  * `app/Controllers/AuthController.php` handles authentication and session setup.
  * `app/Controllers/admin/` contains administration panel controllers for Karyawan, Jabatan, Komponen Gaji, Penggajian, and Users.
* **Models**: `app/Models/` (e.g. `KaryawanModel`, `JabatanModel`, `GajiModel`, `DetailGajiModel`, `KomponenGajiModel`, `UserModel`).
* **Views**: `app/Views/` (layouts split into templates in `app/Views/templates/` and modular subdirectories for views).
* **Routes**: Defined inside `app/Config/Routes.php`.

### Key Design Aspects & DB Constraints
* **Authentication**: Role-based access control (RBAC) supporting `Admin` and `Karyawan`. The `user` session holds information. CSRF protection is active globally via `Filters.php`.
* **Database Updates**: `KaryawanModel` has custom `ensureColumnsExist()` method to automatically manage DB schema differences dynamically when initialized.
* **Database Script**: Core DB structure resides in `database/penggajian_db.sql` and `database/import.php`.
