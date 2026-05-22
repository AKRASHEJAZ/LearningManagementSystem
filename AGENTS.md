# Learning Management System (College Community Platform) — Agent Guide

This repository will evolve into a **community-driven learning platform** for a college environment (not an enterprise LMS).

If you are an agent working on this codebase, **treat this file as the source of truth** for what the project is, what it is not, and the rules of thumb to follow. Update it when reality changes.

## What We’re Building (Product Summary)

A practical platform where students can **learn, teach, mentor, track skills, and receive verifiable certificates**.

Core principles:
- Keep architecture **simple but scalable**
- Keep UI **modern, mobile-first, lightweight**
- Avoid overengineering
- Prioritize maintainability for student developers
- Make the ecosystem sustainable beyond initial founders

## Tech Stack (Hard Constraints)

- Backend: **Laravel**
- Database: **MySQL**
- Server-rendered UI: **Blade templates**
- UI framework: **Bootstrap**
- Deployment: **Shared hosting**

Explicit non-goals:
- No microservices
- No Kubernetes/Docker orchestration/Redis clusters/enterprise infrastructure
- No heavy SPA requirement

## Feature Scope (Current Roadmap)

The end-to-end plan lives in:
- `docs/PRODUCT_WORKPLAN.md`
- `docs/IMPLEMENTATION_STATUS.md`

Primary features to be implemented progressively:
1. Authentication with admin approval
2. Course management
3. Tutor applications
4. Student/tutor assignment system
5. Evaluations and grading
6. Public verifiable certificates with QR codes
7. User profiles showing progress/achievements
8. Dynamic settings system stored in database
9. Mobile-first clean UI
10. Public certificate verification pages

Important rule:
- A user can be **both tutor and student** depending on course.

## Rules of Thumb (Do This / Avoid This)

### Product & Requirements
- **Do** stick to the features listed above; **avoid** inventing extra modules.
- **Do** label assumptions explicitly in docs/PRs; **avoid** implying “requirements” that were never stated.
- **Do** optimize for community sustainability (handoff-friendly workflows); **avoid** founder-only admin workflows.

### Architecture
- **Do** prefer Laravel conventions (Eloquent models, policies, form requests).
- **Do** keep modules cohesive and boundaries clear.
- **Avoid** premature abstractions, event-driven overuse, or complex CQRS patterns.
- **Do** design for shared hosting: minimal dependencies, predictable filesystem usage, queue optional.

### Database
- **Do** normalize core entities (users, courses, enrollments, evaluations, certificates).
- **Do** use pivot tables for “user can be tutor/student per course”.
- **Avoid** storing derived totals that can drift unless you also implement reconciliation.

### UI/UX
- **Do** mobile-first layouts, low-end device performance, accessible color contrast.
- **Do** reuse Blade components and Bootstrap utilities.
- **Avoid** heavy JS frameworks unless there is a clear ROI.

### Verification (No Surprises)
- **Do** manually smoke-test UI changes in a browser before marking work “done”.
  - Minimum check: key pages affected by the change load without errors and the primary action works.
  - If you can’t run a browser in your environment, add/extend feature tests and provide exact manual steps for a human to verify.

### Certificates
- **Do** generate professional-looking PDFs (FreeCodeCamp-style inspiration).
- **Do** use **public verification pages** and **QR codes** that link to those pages.
- **Avoid** “secret URL only” security; verification should be stable and durable.

### Security
- **Do** enforce authorization via policies/gates and per-route middleware.
- **Do** validate all inputs via Form Requests.
- **Do** protect file uploads and sanitize filenames.
- **Avoid** leaking PII on public verification pages.

## Documentation Maintenance Policy (Anti-Hallucination)

When you implement or change anything significant:
- Update `docs/IMPLEMENTATION_STATUS.md` (what’s done vs planned).
- Update the relevant section(s) in `docs/PRODUCT_WORKPLAN.md` if the plan changes.
- If a new assumption becomes a requirement (or is rejected), record it explicitly.

If you are unsure whether something is already implemented:
- Check the codebase and `docs/IMPLEMENTATION_STATUS.md` first.
- If it isn’t in code or docs, treat it as **not implemented**.

## Assumptions (Current, Can Change)

- Single college instance (one institute brand) at first; multi-college support is a future consideration.
- Certificates are issued per course completion (exact completion rules will be defined during evaluations phase).
- Shared hosting environment supports typical Laravel requirements (PHP + extensions, cron, MySQL).
