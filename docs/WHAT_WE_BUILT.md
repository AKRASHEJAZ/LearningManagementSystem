# What We Built (Steps 1–10)

Last updated: 2026-05-22

This document summarizes what has been implemented so far so a new developer/agent can understand the current product quickly.

## Product in One Line

A lightweight, community-driven college learning platform where **approved students can learn**, **any user can tutor per-course**, tutors can **grade**, and completions generate **publicly verifiable certificates** (QR → verification page), plus **game-style achievements**.

## Tech & Constraints (As Implemented)

- Laravel + Blade + Bootstrap (server-rendered, mobile-first)
- MySQL target (local dev may use SQLite)
- Shared-hosting friendly (no microservices, no Redis clusters, no heavy infra)
- Session auth (no API token storage)

## Step-by-Step Summary

### Step 1–2: Planning + Setup

- Laravel app scaffolded with Breeze (Blade).
- Bootstrap-based UI + consistent layouts:
  - App layout
  - Admin layout (sidebar + topbar)
  - Public layout for certificate verification pages

### Step 3: Authentication + Admin Approval Gate

- Registration/login works, but new users are **blocked until admin approval**.
- User approval states: `pending`, `approved`, `rejected`.
- Non-approved users are redirected to `/approval`.
- Admin approval UI:
  - View pending/approved/rejected lists
  - Approve/reject with reason

### Step 4: Admin Panel + Settings (MVP)

Implemented admin areas:
- Dashboard with real stats + recent activity
- User approvals
- User management (promote/demote admins with safety constraints)
- Courses CRUD (admin)
- Tutor applications moderation
- Certificates management (revoke/reactivate, regenerate PDF cache)
- Achievements management (create/edit + awarding rules)
- Admin search (users, courses, certificates, achievements)

Settings system:
- DB-backed settings and branding (institute name, primary color, logo upload)
- Settings are injected into all views

Note:
- There are no standalone “Enrollments overview” / “Evaluations overview” admin pages; work is done via per-course routes.

### Step 5: Courses

- Admin can create/edit/publish/archive courses.
- Course fields include duration, completion criteria, participant limit, and lifecycle (started/ended).
- Approved users can browse published courses and view course detail pages.

### Step 6: Enrollments + Capacity + Assignments

- Students apply to courses.
- Admin/course staff can accept/reject applications.
- Course can be marked started → applications close.
- Completion can be marked after assessment workflow.
- Tutor/student roles are enforced per-course (a user cannot be both tutor and student for the same course).
- Tutor ↔ student assignment mapping per course.
- Student pages:
  - `/my-learning`
- Tutor pages:
  - `/my-tutoring`

### Step 7: Tutor Workflow

- Users can apply to become tutor for a course.
- Admin can approve/reject tutor applications.
- Admin can also assign tutors directly to courses.

### Step 8: Evaluations + Grading + Results

- Course staff can define evaluations (assessment items).
- Tutors-only grading workflow:
  - Grade per evaluation: status `learning/passed/failed`, score, feedback
  - Overall enrollment result: `learning/passed/failed`
- Students can view their results for accepted/completed enrollments.
- Tutors can view results for students they can grade.

### Step 9: Certificates + Public Verification

- Certificates are issued per (student, course) on completion.
- Public verification:
  - `/verify/certificate/{uuid}` (no auth)
  - Shows student name (no email), course, assessment performance table, status (active/revoked), QR
- PDF certificate:
  - Single A4 landscape page
  - Includes student name, course, assessment table, QR linking to verification page
  - Cached in storage; regenerate supported
- Student access:
  - `/my-certificates`

### Step 10: Profiles + Achievements (Badges)

Profiles:
- `/me` (current user profile)
- `/users/{user}` (public profile for approved users)
- Shows:
  - course progress summary
  - recent certificates
  - achievements grid

Achievements:
- Game-style badges with tiers and points.
- Admin-managed achievements and rules:
  - Rule type: course completion count (min N completions)
  - Rule type: specific required courses (e.g. Git Basics + Git Advanced ⇒ Git Guru)
- Students can click a badge to open a modal explaining “How to unlock”.

## Where to Verify Flows

- Manual browser checklist: `docs/VERIFY_IN_BROWSER.md`
- Implementation tracker: `docs/IMPLEMENTATION_STATUS.md`

## What’s Next

- Step 11: Testing & QA (expand browser checklist + more feature tests, edge cases, regression coverage)
- Step 12: Deployment planning for shared hosting (env, storage, backups, migrations, performance)

