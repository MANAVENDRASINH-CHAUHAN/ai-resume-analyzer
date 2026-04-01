# Phase 2 Database Layer

This document contains the complete Phase 2 database-layer deliverables for the **AI Resume Analyzer System**.

## Artisan Commands

```bash
php artisan make:migration create_users_table
php artisan make:migration create_job_roles_table
php artisan make:migration create_skills_table
php artisan make:migration create_resumes_table
php artisan make:migration create_extracted_resume_data_table
php artisan make:migration create_resume_skill_map_table
php artisan make:migration create_resume_scores_table
php artisan make:migration create_analysis_reports_table
php artisan make:migration create_notifications_table
php artisan make:migration create_activity_logs_table

php artisan make:model User
php artisan make:model Admin
php artisan make:model JobRole
php artisan make:model Skill
php artisan make:model Resume
php artisan make:model ExtractedResumeData
php artisan make:model ResumeSkillMap
php artisan make:model ResumeScore
php artisan make:model AnalysisReport
php artisan make:model AppNotification
php artisan make:model ActivityLog

php artisan make:seeder AdminUserSeeder
php artisan make:seeder CandidateUserSeeder
php artisan make:seeder SkillSeeder
php artisan make:seeder JobRoleSeeder
php artisan make:seeder DatabaseSeeder

php artisan make:factory UserFactory --model=User
php artisan make:factory JobRoleFactory --model=JobRole
php artisan make:factory ResumeFactory --model=Resume
```

## File Locations

- Migrations:
  `/Users/manavendrasinh/Desktop/ai_resume_analyzer/database/migrations`
- Models:
  `/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Models`
- Seeders:
  `/Users/manavendrasinh/Desktop/ai_resume_analyzer/database/seeders`
- Factories:
  `/Users/manavendrasinh/Desktop/ai_resume_analyzer/database/factories`
- SQL backup:
  `/Users/manavendrasinh/Desktop/ai_resume_analyzer/database/sql/ai_resume_analyzer_schema.sql`

## Final ER Relationship Explanation

- One `user` can upload many `resumes`.
- One `job_role` can be selected in many `resumes`.
- One `resume` belongs to one `user`.
- One `resume` belongs to one `job_role`.
- One `resume` has one `extracted_resume_data` record.
- One `resume` has one `resume_score`.
- One `resume` has one `analysis_report`.
- One `resume` has many `activity_logs`.
- One `resume` belongs to many `skills` through `resume_skill_map`.
- One `skill` belongs to many `resumes` through `resume_skill_map`.
- One `user` has many `notifications`.
- One `user` can generate many `analysis_reports`.

## Migration Order Explanation

1. `users`
   Reason: required by resumes, reports, notifications, and activity logs.
2. `job_roles`
   Reason: required by resumes.
3. `skills`
   Reason: required by resume_skill_map.
4. `resumes`
   Reason: required by extracted data, scores, reports, logs, and skill map.
5. `extracted_resume_data`
6. `resume_skill_map`
7. `resume_scores`
8. `analysis_reports`
9. `notifications`
10. `activity_logs`

## Run Commands

```bash
php artisan migrate:fresh --seed
php artisan db:seed
```

## Notes

- This Phase 2 design uses a **single `users` table** with a `role` column.
- The `Admin` model is kept as a convenience wrapper over the same `users` table for later auth phases.
- Soft deletes are enabled on:
  - `users`
  - `job_roles`
  - `resumes`
