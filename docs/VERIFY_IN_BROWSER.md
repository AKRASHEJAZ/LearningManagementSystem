# Browser Verification Checklist (Required)

Any change that affects routes, UI, middleware, auth, or admin workflows must be verified in a browser before considering the work complete.

## Minimum Smoke Tests

1. Public:
   - `/` loads
   - Login/Register pages render

2. Auth + Approval:
   - Register a new user → redirected to `/approval`
   - Admin can see the user in `/admin/user-approvals?status=pending`
   - Admin approves user → user can access `/dashboard`

3. Admin:
   - `/admin` loads (sidebar + topbar visible)
   - Use admin search box (top right) → search a user/course/certificate → confirm `/admin/search?q=...` shows results
   - `/admin/user-approvals` list works
   - `/admin/users` loads; promote a user to admin; refresh to confirm admin badge
   - `/admin/settings` loads
   - Update institute name + primary color, refresh another page to confirm changes apply
   - Upload logo, refresh to confirm it shows in navbar and admin sidebar

4. Courses (Phase 5):
   - As admin: open `/admin/courses`, create a draft course, then publish it
   - As approved non-admin: open `/courses` and confirm only published courses appear
   - Open a published course `/courses/{slug}` and confirm details load
   - Confirm course shows `Duration` and `Completion criteria` when set

5. Enrollments & selection (Phase 6):
   - As admin: set course `Max participants` (e.g. 2), ensure course is `Published`
   - As student A: open `/courses/{slug}` → Apply → see `Applied`
   - As student B: Apply → see `Applied`
   - As admin/tutor: open `/courses/{slug}/manage` → Accept A and B → see them in Accepted list
   - Click `Mark started` → verify applications are closed on course page
   - Mark selected accepted students as completed → verify status changes to `Completed`

5b. Tutor↔student assignments (Phase 6):
   - As course staff: open `/courses/{slug}/assignments` → assign a tutor to an accepted student
   - As tutor: open `/my-tutoring` and confirm assigned student appears

5c. Student dashboard (Phase 6):
   - As student: open `/my-learning` and confirm your applications/enrollments list correctly

6. Tutor assignment (Phase 6):
   - As admin: open `/admin/courses` → click `Tutors` on a course (or open `/admin/courses/{id}/edit` → `Tutors`)
   - Search user by name/email → click `Add` → verify they appear in the tutors table
   - Log in as that tutor → open `/courses/{slug}` → `Manage participants` is visible and `/courses/{slug}/manage` loads
   - Confirm tutors do not see the student `Apply` form for the same course
   - Confirm `/courses/{slug}/manage` uses normal app layout for tutors (no admin sidebar)

7. Tutor applications (Phase 7):
   - As approved user: open `/courses/{slug}` → apply to be tutor → see status `pending`
   - As admin: open `/admin/tutor-applications` → approve → user becomes tutor (can open `/courses/{slug}/manage`)

8. Evaluations & grading (Phase 8):
   - As course staff: open `/courses/{slug}/evaluations` → create 1–2 evaluations
   - As tutor (not admin): open `/courses/{slug}/grading` → grade an accepted student → set status `learning/passed/failed`
   - From grading: set overall status to `passed` → click `Complete` to mark the student completed
   - As admin (not assigned tutor): confirm `/courses/{slug}/grading` is forbidden
   - As accepted student: open `/courses/{slug}/results` → confirm grades/feedback and overall status are visible
   - As tutor: click `Results` next to a student in grading and confirm you can view that student’s results

9. Certificates + verification (Phase 9):
   - Complete a student (Phase 8 step above) → open `/courses/{slug}/results` as that student
   - Confirm `Certificate` card shows `View verification` + `Download PDF`
   - Click `View verification` → confirm public page loads without login and shows:
     - Student name (no email)
     - Course title
     - Assessment table (status/score/feedback)
     - QR block
   - Click `Download PDF` → confirm a PDF downloads and contains a QR code
   - Scan the PDF QR code → confirm it opens `/verify/certificate/{uuid}`
   - As student: open `/my-certificates` → confirm certificates list + download/verify work
   - As admin: open `/admin/certificates` → revoke a certificate → refresh public verification page to confirm it shows `Revoked`
   - As admin: reactivate → confirm public page shows `Valid` again

10. Profiles + achievements (Phase 10):
   - As approved user: open `/me` → confirm profile loads (progress + certificates + achievements)
   - Approve a pending user (Phase 3) → log in as that user → open `/me` → confirm `Verified Member` badge is unlocked
   - Complete a course (Phase 8/9) → open `/me` → confirm `First Completion` badge is unlocked
   - As admin: open `/admin/achievements` → create `Git Guru` achievement
     - Rule type: `Specific courses`
     - Select `Git Basics` + `Git Advanced`
   - Complete both courses for a student → open `/me` → confirm `Git Guru` unlocks
   - Click any achievement badge → confirm a modal opens with “How to unlock” instructions
   - As admin: open `/admin/achievements` on mobile viewport → confirm Achievements link exists in the menu

## Notes

- If you cannot run a browser in your environment, you must:
  - add/extend feature tests, and
  - provide the exact manual steps above for a human to validate.
