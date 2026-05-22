# Product Workplan — Community-Driven Learning Platform (College)

This document is the **end-to-end development plan** for a modern, community-driven learning platform built for a college environment.

## Assumptions (Explicit)

These assumptions guide the plan and should be revised if they become wrong:
- The platform targets **one college instance** initially (single institute branding/settings).
- The platform is **server-rendered** (Blade + Bootstrap) with light progressive enhancement JS only when needed.
- Shared hosting provides: PHP, MySQL, cron access, and ability to set a web root to Laravel `public/`.
- Email delivery is available (SMTP or provider) for approvals and notifications (can be deferred to MVP if needed).

## Non-Goals (Explicit Constraints)

- No microservices.
- No Kubernetes / Docker orchestration / Redis clusters.
- No “enterprise LMS” features like SCORM, LTI, multi-tenant corporate hierarchies.
- No heavy SPA requirement.

---

# 1) Full Product Architecture

## System Structure (High-Level)

The platform is a monolithic Laravel application with clear internal boundaries:
- **Auth & Identity**: registration, login, approval status, profiles
- **Courses**: course creation, visibility, prerequisites (optional future)
- **Tutors**: tutor applications, approvals, tutor assignment per course
- **Learning & Enrollment**: enrollment, tutor-student mapping per course
- **Evaluation**: assessments, grading, feedback, completion status
- **Certificates**: issuance, PDF rendering, QR verification, public pages
- **Settings & Branding**: database-driven institute branding, certificate theme assets
- **Admin**: approvals, moderation, configuration, reporting (lightweight)

## Module Breakdown (Boundaries)

- **Users**
  - Profiles, skill progress display, achievements
  - Approval gate (admin-driven)
- **Courses**
  - Course metadata + publishing state
  - Multiple roles per course (student vs tutor)
- **Tutor Applications**
  - Application to become tutor for a specific course (or general pool if future)
  - Admin review + approval
- **Enrollment & Assignment**
  - Students enroll in courses
  - Tutors assigned to students per course (manual first; semi-automatic later)
- **Evaluations & Grading**
  - Evaluation items per course (e.g., tasks/quizzes/projects)
  - Grade entries and completion criteria
- **Certificates**
  - Issued when completion criteria met
  - Public verification endpoint with QR code link
- **Settings**
  - Stored in DB, cached in app
  - Controls institute name, logo, colors, certificate header/footer text

## MVP Scope (Realistic)

MVP should deliver:
- Admin-approved registration
- Courses CRUD (admin + approved tutors if allowed)
- Enrollment
- Tutor application + assignment per course
- Basic evaluation entries + final completion flag
- Certificate issuance + public verification + QR code
- Profiles showing enrolled courses, tutor roles, certificates
- Dynamic settings for institute branding + certificate appearance

## Future Scalability Planning (Without Overengineering)

- Add “mentor” role later as a specialization (built on top of tutor history)
- Add lightweight notifications (database notifications + optional email)
- Add optional queues (for PDF generation) but keep synchronous fallback
- Add “skills framework” later (skill taxonomy, mapping course outcomes to skills)
- Add moderation tools (reporting, soft deletes, audit trail) only when needed

---

# 2) Full Database Planning

## Core Entity Relationship Summary

- Users have many Enrollments (as students)
- Users have many CourseTutorAssignments (as tutors per course)
- Courses have many Enrollments
- Courses have many Tutor Applications
- Enrollments have many Evaluations/Grades
- Certificates are issued to a user for a course (and optionally per enrollment)
- Settings store branding and system configuration

## Tables (Proposed)

### `users`
Important fields:
- `id`
- `name`
- `email` (unique)
- `password`
- `approval_status` (enum: pending/approved/rejected)
- `approved_at`, `approved_by` (nullable)
- `rejected_at`, `rejected_by`, `rejection_reason` (nullable)
- `profile_photo_path` (optional)
- timestamps

### `roles` (optional) + `role_user` (optional)
Only if global roles are needed beyond “admin”.
Suggested global roles:
- `admin`
- `staff` (optional)

Course-specific roles should **not** be implemented via global roles; use course pivots.

### `courses`
- `id`
- `title`
- `slug` (unique)
- `summary` (short)
- `duration` (short string, e.g. "6 weeks")
- `description` (long)
- `completion_criteria` (long; guidance shown on course page)
- `status` (draft/published/archived)
- `max_participants` (optional integer limit)
- `started_at`, `ended_at` (course lifecycle timestamps; once started, applications close)
- `created_by` (user id)
- timestamps

### `course_enrollments`
Represents a user as a **student** in a course.
- `id`
- `course_id`
- `user_id`
- `status` (applied/accepted/rejected/withdrawn/completed)
- `applied_at`, `accepted_at`, `rejected_at`, `withdrawn_at`, `completed_at`
- `updated_by` (who accepted/rejected/completed)
- `note` (optional application/admin note)
- timestamps
Unique constraint: (`course_id`, `user_id`)

### `tutor_applications`
Request by a user to tutor a course.
- `id`
- `course_id`
- `user_id`
- `motivation` (text)
- `status` (pending/approved/rejected)
- `reviewed_by` (nullable)
- `reviewed_at` (nullable)
- `review_notes` (nullable)
- timestamps
Unique constraint: (`course_id`, `user_id`)

### `course_tutors`
Represents a user as a **tutor** in a course (approved assignment).
- `id`
- `course_id`
- `user_id`
- `assigned_by` (admin)
- `assigned_at`
- `status` (active/inactive)
- timestamps
Unique constraint: (`course_id`, `user_id`)

### `tutor_student_assignments`
Maps students to tutors **per course**.
- `id`
- `course_id`
- `tutor_user_id`
- `student_user_id`
- `assigned_by`
- `assigned_at`
- `status` (active/ended)
- timestamps
Unique constraint: (`course_id`, `tutor_user_id`, `student_user_id`)

### `evaluations`
Defines evaluation items for a course (e.g., “Project 1”).
- `id`
- `course_id`
- `title`
- `description` (nullable)
- `max_score` (nullable, integer)
- `weight` (nullable, decimal) (optional for future weighted grading)
- `is_required` (bool)
- `position` (ordering)
- `created_by`
- timestamps

### `evaluation_grades`
Stores grade/feedback for a student for a given evaluation.
- `id`
- `evaluation_id`
- `course_id` (denormalized for indexing, optional but practical)
- `student_user_id`
- `graded_by_user_id` (tutor/admin)
- `score` (nullable)
- `status` (learning/passed/failed)
- `feedback` (text, nullable)
- `graded_at`
- timestamps
Unique constraint: (`evaluation_id`, `student_user_id`)

### `certificates`
Issued certificates (publicly verifiable).
- `id`
- `uuid` (unique, public identifier)
- `certificate_number` (unique, human-friendly optional)
- `user_id`
- `course_id`
- `issued_by` (admin/system)
- `issued_at`
- `status` (active/revoked)
- `revoked_at`, `revoked_by`, `revocation_reason` (nullable)
- `pdf_path` (nullable if generated on-demand)
- timestamps

### `settings`
Database-driven config.
- `id`
- `key` (unique; e.g. `institute.name`, `brand.primary_color`)
- `value` (text/json)
- `type` (string: text/color/file/json/bool)
- `group` (string; e.g. institute/brand/certificates)
- `is_public` (bool; for public display items only)
- timestamps

### `files` (optional helper)
If you want structured file uploads for logos/backgrounds:
- `id`
- `disk`
- `path`
- `original_name`
- `mime_type`
- `size_bytes`
- `uploaded_by`
- timestamps

## Relationships & Normalization Notes

- Use `course_enrollments` and `course_tutors` instead of a single role column: this enables “user can be tutor and student depending on course”.
- Keep `settings.value` as JSON-capable text to allow structured settings without schema churn.
- Use indexed foreign keys for shared hosting performance (especially on `course_id`, `user_id` pairs).

---

# 3) Full Laravel Architecture

## App Structure (Opinionated but Conventional)

Suggested folders:
- `app/Models` — Eloquent models
- `app/Http/Controllers` — thin controllers
- `app/Http/Requests` — Form Request validation
- `app/Policies` — authorization policies
- `app/Services` — business logic services (certificate issuing, assignment logic)
- `app/Actions` (optional) — single-purpose use cases if it stays clean
- `app/View/Components` — reusable Blade components
- `database/migrations`, `database/seeders`

## Models (Initial Set)

- `User`
- `Course`
- `CourseEnrollment`
- `TutorApplication`
- `CourseTutor`
- `TutorStudentAssignment`
- `Evaluation`
- `EvaluationGrade`
- `Certificate`
- `Setting`
- `FileAsset` (if using a file table)

## Controllers (Initial Set)

Public:
- `Auth/*` (Laravel auth scaffolding choice)
- `PublicCertificateController` (verification page)

Student/Tutor:
- `DashboardController`
- `CourseController` (browse/view)
- `EnrollmentController`
- `TutorApplicationController`
- `EvaluationController` (view evaluation items)
- `GradeController` (tutors grade students)
- `ProfileController`

Admin:
- `Admin/UserApprovalController`
- `Admin/UserManagementController` (promote/demote admins)
- `Admin/CourseAdminController`
- `Admin/TutorAdminController` (applications + course tutors)
- `Admin/AssignmentsController` (tutor-student mapping)
- `Admin/SettingsController`
- `Admin/CertificateAdminController` (revoke/reissue)

## Services (Business Logic)

- `UserApprovalService`
  - Approve/reject users, record audit fields
- `TutorAssignmentService`
  - Assign tutor to course; assign tutor-to-student per course
- `EvaluationService`
  - Determine completion; compute pass/fail
- `CertificateService`
  - Issue certificate, generate UUID/number, generate PDF, build QR payload
- `SettingsService`
  - Read/write settings, caching, validation per type

## Middleware

- `EnsureUserApproved` — blocks non-approved accounts from accessing core app
- `EnsureAdmin` — admin routes only
- Rate limiting for public verification endpoint (optional, lightweight)

## Policies (Authorization)

Core policies:
- `CoursePolicy` (manage course, publish, archive)
- `EnrollmentPolicy` (enroll/drop/view)
- `TutorApplicationPolicy` (apply/view own)
- `EvaluationPolicy` (manage evaluations for course)
- `GradePolicy` (grade students only if tutor for course)
- `CertificatePolicy` (admin revoke; user view own)
- `SettingPolicy` (admin only)

## Validation Strategy

- Use Form Requests for all write operations.
- Validate IDs with `exists:` constraints.
- Validate file uploads (mimes, max size), store on non-public disk when possible.
- Centralize “settings type” validation in `SettingsService`.

---

# 4) Authentication & Authorization Flow

## User Approval Flow

1. User registers.
2. Account status defaults to `pending`.
3. User can log in but is restricted to a “Pending Approval” screen (or limited routes).
4. Admin reviews and approves/rejects.
5. On approval, user gains full access; on rejection, user sees rejection reason (optional) and cannot proceed.

## Role Flow (Global vs Per Course)

- Global role: `admin` (and optionally `staff`).
- Per-course roles:
  - Student: exists via `course_enrollments`
  - Tutor: exists via `course_tutors`

This supports the requirement: one user can be both tutor and student depending on course.

## Tutor Application Flow

1. Approved user applies to tutor a course via `tutor_applications`.
2. Admin approves/rejects.
3. On approval, a `course_tutors` record is created (or activated).

## Course Enrollment Logic

1. Approved user enrolls in a published course.
2. Enrollment record created in `course_enrollments`.
3. If tutor assignment is needed:
   - Admin can assign (MVP), or
   - Simple auto-assignment rules later (e.g., least-loaded tutor).

---

# 5) UI/UX Planning

## Mobile-First Strategy

- Design for small screens first; ensure all dashboards work at 360px width.
- Keep pages lightweight: minimize JS, optimize images, use Bootstrap utilities.
- Prefer server-rendered pagination and filtering (avoid heavy client rendering).

## Core Layout & Navigation

Top-level navigation (approved users):
- Dashboard
- Courses
- My Learning (enrollments)
- Tutoring (if tutor in any course)
- Certificates
- Profile

Admin navigation:
- User Approvals
- Courses
- Tutor Applications
- Assignments
- Evaluations (per course)
- Certificates
- Settings / Branding

## Dashboard Layouts

Student dashboard:
- Current courses (progress + next evaluation)
- Assigned tutor (per course)
- Recent feedback/grades

Tutor dashboard:
- Courses tutoring
- Assigned students list (per course)
- Pending grading items

Admin dashboard:
- Pending approvals count
- Pending tutor applications
- Quick links to settings + recent certificates

## Reusable Blade Components

- Navbar + sidebar (responsive)
- Course card
- Status badges (pending/approved/rejected, active/completed)
- Empty state component
- Pagination + filter toolbar
- Form components (input/select/textarea/file)
- Certificate preview block

## Design Recommendations (Modern but Lightweight)

- Use Bootstrap 5 with:
  - subtle shadows, rounded corners, consistent spacing
  - limited color palette controlled by `settings`
  - accessible contrast and large tap targets
- Prefer system fonts or a single webfont (optional) to reduce payload.

---

# 6) Certificate System Design

## PDF Generation Strategy (Shared Hosting Friendly)

Recommended approach:
- Generate PDFs using a well-supported Laravel-compatible HTML-to-PDF library.
- Keep certificate templates as Blade views rendered to HTML, then converted to PDF.
- Store generated PDFs (optional) for caching; allow regeneration if storage is limited.

## QR Verification System

- Each certificate has a `uuid` (public, unguessable).
- QR code encodes the public verification URL:
  - `/verify/certificate/{uuid}`

## Public Verification Routes

- `GET /verify/certificate/{uuid}`
  - Shows certificate validity, student name (consider privacy), course, issued date, institute branding.

Optional:
- `GET /verify/certificate/{uuid}/pdf` (rate limited; may require CAPTCHA later if abused)

## Certificate Security Considerations

- Use random UUIDs and keep them immutable.
- Support revocation; verification page must reflect revoked status.
- Avoid exposing student email or sensitive identifiers on the public page.
- Consider masking name (configurable) if privacy concerns arise (future).

## Certificate Design Recommendations (FreeCodeCamp-Style)

- Clean typography with a strong hierarchy:
  - institute name + logo
  - “Certificate of Completion”
  - learner name (large)
  - course title
  - issue date + certificate number
  - signatures (optional image placeholders)
- Use a subtle background pattern/watermark (configurable setting).
- Ensure printable A4/Letter layout with margins.

---

# 7) Development Roadmap (Phased)

Complexity legend: Low / Medium / High (relative for student team)

## Phase 1 — Planning

Objectives:
- Confirm MVP scope, flows, and data model.

Tasks:
- Finalize DB schema + migrations list
- Confirm certificate layout requirements and verification page fields
- Confirm admin approval UX

Complexity: Low
Dependencies: None

## Phase 2 — Setup

Objectives:
- Initialize Laravel project and baseline UI.

Tasks:
- Create Laravel app structure
- Configure MySQL connection
- Add Bootstrap layout + base templates
- Set up `.env` examples and shared hosting deployment notes

Complexity: Medium
Dependencies: Phase 1

## Phase 3 — Authentication + Admin Approval

Objectives:
- Working registration/login + approval gate.

Tasks:
- Implement auth scaffolding
- Add `approval_status` to users
- Admin UI to approve/reject
- Middleware to restrict non-approved users

Complexity: Medium
Dependencies: Phase 2

## Phase 4 — Admin Panel (incl. Settings)

Objectives:
- Admin can manage core configuration.

Tasks:
- Admin area layout + navigation
- Implement `settings` CRUD (with types and groups)
- Upload branding assets (logo, certificate background)

Notes (Implemented deviation):
- Admin UI uses a dedicated sidebar + topbar layout for faster navigation (still Blade + Bootstrap; lightweight).

Complexity: Medium
Dependencies: Phase 3

## Phase 5 — Courses

Objectives:
- Courses can be created/published and browsed.

Tasks:
- Course CRUD + slugging
- Public/approved browsing rules
- Course detail page

Implementation note:
- Phase 5 implemented as admin CRUD + published-only browsing for approved users (enrollment comes next phase).

Complexity: Medium
Dependencies: Phase 4 (settings for branding), Phase 3

## Phase 6 — Enrollments & Assignments

Objectives:
- Students enroll; admin assigns tutors to students per course.

Tasks:
 - Student applies to join a course (application)
 - Admin/tutor accepts up to `max_participants`
 - Course start action closes applications
 - Basic completion marking per student (post-course)
 - Course tutor list management
 - Tutor-student assignment CRUD (later in phase if needed)

Complexity: Medium
Dependencies: Phase 5

## Phase 7 — Tutor Workflow

Objectives:
- Tutors can apply and be approved for courses.

Tasks:
- Tutor application submit/view
- Admin approve/reject applications
- Activate `course_tutors` entries

Complexity: Medium
Dependencies: Phase 5

## Phase 8 — Evaluations & Grading

Objectives:
- Tutors can grade students; completion can be computed.

Tasks:
- Evaluation items CRUD per course
- Grade entries per student/evaluation
- Completion rules v1 (simple: all required passed)

Complexity: High
Dependencies: Phase 6, Phase 7

## Phase 9 — Certificates + Verification

Objectives:
- Issue certificates and verify publicly by QR.

Tasks:
- Certificate issuance logic triggered by completion
- Generate PDF and store/serve
- Public verification page
- QR generation integrated into PDF and UI

Complexity: High
Dependencies: Phase 8, Phase 4 (branding)

## Phase 10 — Profiles + Achievements

Objectives:
- Users can showcase progress and certificates.

Tasks:
- Profile page with enrollments + tutoring history
- Certificates list + downloadable PDFs
- Basic achievements (e.g., completed courses) derived from data

Complexity: Medium
Dependencies: Phase 9

## Phase 11 — Testing & QA

Objectives:
- Reduce regressions and ensure correctness on shared hosting.

Tasks:
- Feature tests for approval, enrollment, grading, certificate verification
- Authorization tests for policies
- Manual UX testing on mobile sizes

Complexity: Medium
Dependencies: All prior phases

## Phase 12 — Deployment

Objectives:
- Ship to shared hosting reliably.

Tasks:
- Deployment checklist, env config, storage linking
- Backup plan, migrations process
- Performance pass (caching, indexes, assets)

Complexity: Medium
Dependencies: Phase 11

---

# 8) Security Planning

## Authorization

- Use policies for all resource actions (course manage, grade, issue/revoke certs).
- Use middleware for:
  - authentication
  - approval status
  - admin access

## Validation

- All forms use Form Request validation.
- Use `bail` for critical fields and explicit messages for user clarity.

## Upload Protection

- Validate MIME types and file size.
- Store uploads outside webroot when possible; serve via controller if sensitive.
- Generate safe filenames; never trust client filename.

## Route Protection

- Admin routes under `/admin` with strict middleware.
- Public verification route is public but rate-limited and sanitized.

## Certificate Verification Security

- Verification is by UUID, not sequential IDs.
- Support certificate revocation and reflect status on public page.
- Avoid leaking private user data on verification pages.

## Shared Hosting Precautions

- Turn off debug in production.
- Use proper file permissions.
- Avoid long-running jobs without queue support; keep operations bounded.

---

# 9) Deployment Planning (Shared Hosting)

## Deployment Approach

- Host points web root to Laravel `public/`.
- Upload code via git/zip/FTP depending on hosting capabilities.
- Run migrations via SSH if available; otherwise provide a safe migration procedure.

## Optimization

- Enable config/route/view caching in production.
- Use optimized autoloader.
- Minify assets (Laravel Mix/Vite as appropriate for chosen Laravel version).

## Backups

- Nightly MySQL backups (cron + retention).
- Weekly offsite copy (manual acceptable initially).
- Store certificate PDFs on disk with backup strategy if persisted.

## Migration Handling

- Use Laravel migrations consistently.
- Add indexes early for common lookups (course/user pivots, certificate uuid).

## Environment Configuration

- `.env` production values: APP_KEY, DB, mail, filesystem disk, app url.
- Configure `storage` symlink; if symlink unsupported, fallback to copying public assets or controller serving.

---

# 10) Sustainability Strategy (Community Flywheel)

Goal: a self-sustaining student ecosystem where learners become tutors, and tutors become mentors.

Practical mechanisms (kept lightweight):
- **Progress visibility**: profiles show completed courses and certificates to encourage participation.
- **Tutor pipeline**: students who complete a course can apply to tutor that course.
- **Tutor reputation** (future, simple): count of students helped + course completions supported; avoid complex scoring.
- **Mentorship path** (future): invite trusted tutors to mentor new tutors (operationally an admin-managed label at first).
- **Institution continuity**: settings and branding in DB; admin workflows documented; avoid hardcoding founders.
- **Verifiable outputs**: public certificate verification makes outcomes durable and valued.

---

## What to Do Next

When implementation begins, keep this plan updated and track progress in `docs/IMPLEMENTATION_STATUS.md`.
