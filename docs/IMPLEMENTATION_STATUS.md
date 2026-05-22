# Implementation Status (Living Document)

Last updated: 2026-05-22

This file tracks what is implemented vs planned so agents don’t guess.

## Current State

- Codebase: Laravel project scaffolded with Blade auth (Bootstrap UI).
- Docs: product workplan + implementation tracker present.

## Decisions Log

- Stack fixed: Laravel + MySQL + Blade + Bootstrap; shared hosting deployment.
- Non-goals: no microservices; avoid enterprise infra.
- Auth approach: session-based Laravel auth (no API tokens stored in DB).
- Admin approach (initial): `users.is_admin` boolean + `admin` middleware.
- Admin UI layout: sidebar + topbar for navigation (Blade + Bootstrap).
- Admin can promote/demote admins via `/admin/users`.

## Phase Checklist

Status legend: `Not started` / `In progress` / `Done` / `Deferred`

1. Planning — Done (initial)
2. Setup — Done (Laravel scaffold + Bootstrap assets)
3. Authentication + Admin Approval — Done (session auth + admin approval gate + admin approvals UI)
4. Admin Panel (incl. settings) — In progress (settings table + branding UI added; full admin panel TBD)
5. Courses — Done (admin CRUD + published browsing)
6. Enrollments & Assignments — Done (applications, selection, course start/end, completion marking, tutor→student assignments, My learning/My tutoring pages)
7. Tutor Workflow — Done (student tutor applications + admin approve/reject creates course tutors)
8. Evaluations & Grading — Done (evaluations CRUD; tutor-only grading; student+tutor results views)
9. Certificates + Verification — Done (auto-issue on completion; my certificates page; admin certificates management; public verification + QR; 1-page PDF)
10. Profiles + Achievements — Done (profiles; progress; certificates; game-style achievement badges with unlock hints; admin-defined rules)
11. Testing & QA — Not started
12. Deployment — Not started

## Notes for Agents

- If you add tables, update the DB plan section in `docs/PRODUCT_WORKPLAN.md`.
- If you implement a phase, mark it `In progress`/`Done` here and note any deviations.
