# GCM Learning — Community Learning Platform for Colleges

A practical, community-driven learning platform built for a college environment (not an enterprise LMS).

- **Admin-approved access** (pending/approved/rejected)
- **Courses** with participant limits and lifecycle (apply → accept → start → end)
- **Tutors per course** (admin-assigned or via tutor applications)
- **Evaluations + grading** (tutors-only)
- **Certificates** (single-page A4 landscape PDF) with **QR → public verification page**
- **Profiles + achievements** (Steam/PS/Xbox-style badges with “how to unlock” hints)

## Key Docs

- `AGENTS.md` — project rules of thumb (keep agents grounded)
- `docs/PRODUCT_WORKPLAN.md` — full product + architecture plan
- `docs/IMPLEMENTATION_STATUS.md` — what’s implemented vs planned
- `docs/VERIFY_IN_BROWSER.md` — required browser smoke checklist
- `docs/WHAT_WE_BUILT.md` — step-by-step build summary (Steps 1–10)

## Local Setup (Dev)

Prereqs: PHP 8.2.x, Composer, Node.js, and npm.

Deployment target: shared hosting with PHP 8.2.x.

1) Install PHP deps
- `composer install`

2) Configure env
- `cp .env.example .env`
- `php artisan key:generate`

3) Install and build assets
- `npm install`
- `npm run build`

4) Create DB + run migrations + seed
- `php artisan migrate --seed`

5) Run the app
- `php artisan serve`

## Admin User

This app uses a simple admin flag: `users.is_admin` (plus per-course staff/tutor assignments).

To seed an initial admin user, set these in your `.env` then run `php artisan db:seed`:
- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

## Useful Routes (Quick Map)

- App:
  - `GET /dashboard`
  - `GET /courses`
  - `GET /my-learning`
  - `GET /my-tutoring`
  - `GET /my-certificates`
  - `GET /me` (profile + achievements)
- Admin:
  - `GET /admin`
  - `GET /admin/user-approvals`
  - `GET /admin/courses`
  - `GET /admin/settings`
  - `GET /admin/certificates`
  - `GET /admin/achievements`
  - `GET /admin/search?q=...`
- Public verification (unauthenticated):
  - `GET /verify/certificate/{uuid}`

## Notes

- Auth is session-based (no API tokens stored in the database).
- New users must be approved by an admin before accessing the platform.
- Certificates and verification pages intentionally avoid leaking student email.
