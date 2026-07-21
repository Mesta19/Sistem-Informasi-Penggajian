# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- **Run Dev Server**: `php spark serve` (starts development server at http://localhost:8080)
- **Run Tests (All)**: `vendor/bin/phpunit`
- **Run Test (Single Directory)**: `vendor/bin/phpunit tests/<directory>` (e.g., `vendor/bin/phpunit tests/session`)
- **Run Database Migrations**: `php spark migrate`
- **Rollback Migrations**: `php spark migrate:rollback`
- **Run Database Seeders**: `php spark db:seed <SeederName>`
- **CodeIgniter CLI Utilities**: `php spark` (lists all available commands)

## High-Level Architecture & Structure

This codebase is built on **CodeIgniter 4** (MVC pattern):

- **Routes (`app/Config/Routes.php`)**: Manages URI routing. Note that the root `/` maps to authentication, and admin routes are grouped under the `admin` prefix.
- **Controllers (`app/Controllers/`)**:
  - `AuthController.php` handles login/logout and session creation.
  - Admin functionalities are located in the `app/Controllers/admin/` subdirectory (e.g., `KaryawanController`, `GajiController`, `UserController`).
- **Models (`app/Models/`)**: CodeIgniter Models mapping to corresponding database tables (e.g., `KaryawanModel`, `GajiModel`). Handles payroll component snapshotting logic.
- **Views (`app/Views/`)**: Templates and HTML components. Uses global layouts with headers, footers, and sidebars (e.g., in `app/Views/templates/`).
- **Authentication & Security Helper (`app/Common.php`)**: 
  - Contains procedural helper functions like `cekLogin()` and `cekRole(string $role)` that intercept requests in admin controllers to enforce authorization without relying on complex middlewares.
