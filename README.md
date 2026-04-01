# AI Resume Analyzer System

AI Resume Analyzer System is a Laravel mini project for the **Advanced Web Technology** subject. It is built using **PHP, MySQL, Laravel, Blade, Bootstrap, JavaScript, AJAX, phpMyAdmin, and MAMP**. The project supports separate user and admin login, resume upload, rule-based resume analysis, score generation, job-role matching, admin analytics, and AJAX-based live status simulation without Node.js.

## Abstract

The AI Resume Analyzer System is a web application that helps students upload resumes and receive automated feedback using rule-based analysis. The system extracts important details such as name, email, phone, education, projects, experience, and skills from the uploaded resume. It compares the extracted skills with predefined job-role requirements and generates a transparent score out of 100 along with missing skills, strengths, improvements, and job-role match percentage. An admin panel is included to manage users, resumes, job roles, reports, top skills, missing skills, and activity logs. The project demonstrates advanced web technologies through Laravel MVC, OOP service classes, MySQL relationships, file uploads, sessions, middleware, AJAX polling, and analytics dashboards.

## Objectives

1. Build a complete Laravel-based mini project for Advanced Web Technology.
2. Allow users to register, log in, upload resumes, and view analysis results.
3. Implement rule-based resume parsing without using paid APIs.
4. Match resume skills with job-role skills and calculate job-role compatibility.
5. Provide an admin panel for CRUD operations, reporting, and analytics.
6. Simulate real-time progress updates using AJAX polling in a MAMP environment.
7. Demonstrate PHP OOP, MySQL joins, MVC architecture, middleware, and form/file handling.

## Technology Stack

- PHP
- Laravel 13
- MySQL
- Blade
- Bootstrap 5
- JavaScript
- AJAX polling using `fetch()`
- phpMyAdmin
- MAMP on macOS

## Important Note

- This project does **not** use Node.js, React, Vue, MongoDB, Firebase, or WebSockets.
- Front-end assets are loaded directly from the `public/assets` folder and Bootstrap CDN.
- Live updates are implemented using **AJAX polling** with Laravel JSON responses.

## Demo Credentials

- User Login:
  `student@example.com` / `password`
- Admin Login:
  `admin@resumeanalyzer.com` / `password`

## Phase-Wise Implementation

### Phase 1: Laravel Setup and MAMP Environment

- Laravel application placed in the root folder: `ai_resume_analyzer`
- MAMP-friendly `.env.example` included with default MySQL port `8889`
- No Node/Vite workflow is required
- Bootstrap loaded from CDN
- Public CSS and JS files placed in:
  - `public/assets/css/app.css`
  - `public/assets/js/notification-badge.js`
  - `public/assets/js/resume-status.js`

### Phase 2: Migrations, Models, and Seeders

- Created full relational schema for:
  - `users`
  - `admins`
  - `job_roles`
  - `skills`
  - `job_role_skill`
  - `resumes`
  - `extracted_resume_data`
  - `resume_skill_maps`
  - `resume_scores`
  - `analysis_reports`
  - `notifications`
  - `activity_logs`
- Added Eloquent relationships and sample seeders

### Phase 3: Authentication, Roles, and Middleware

- Separate login for user and admin
- Session-based authentication using Laravel guards:
  - `web`
  - `admin`
- Protected route groups:
  - `auth`
  - `auth:admin`
- Guest-only route groups for login/register pages

### Phase 4: Dashboard UI and Layouts

- Bootstrap-based responsive layout
- Separate:
  - user layout
  - admin layout
- Added navbar, admin sidebar, footer, dashboard cards, and clean student-project styling

### Phase 5: Resume Upload Module

- Upload PDF, DOC, and DOCX resume files
- Validate title, role, file type, and size
- Store files in Laravel storage using public disk
- Save resume metadata into the database

### Phase 6: OOP Service Classes

Created reusable service classes in `app/Services/Resume`:

- `BaseResumeService`
- `ResumeParserService`
- `SkillMatcherService`
- `ResumeScorerService`
- `ReportGeneratorService`

These demonstrate:

- classes and objects
- constructor
- inheritance
- encapsulation
- reusable service design

### Phase 7: Analysis Result Pages

- Resume history page
- Live progress page
- Detailed report page
- Job match page
- Print-friendly report button

### Phase 8: Admin CRUD

- Manage users
- Manage job roles
- Manage resumes
- View reports
- View activity logs

### Phase 9: AJAX Live Status Tracking

- Resume processing stages:
  - Uploaded
  - Parsing
  - Analyzing
  - Completed
- Dynamic progress bar updates using `fetch()` polling every few seconds
- Notification badge polling for user and admin layouts

### Phase 10: Final Polishing

- Added sample seed data
- Added SQL schema file
- Added README for setup and viva
- Removed starter Node/Vite files to stay within project constraints

## Recommended Folder Structure

```text
ai_resume_analyzer/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── User/
│   │   └── HomeController.php
│   ├── Models/
│   ├── Services/Resume/
│   └── Support/ActivityLogger.php
├── config/
│   ├── auth.php
│   └── resume_analyzer.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── sql/ai_resume_analyzer_schema.sql
├── public/
│   └── assets/
│       ├── css/app.css
│       └── js/
├── resources/views/
│   ├── admin/
│   ├── auth/
│   ├── layouts/
│   ├── public/
│   └── user/
├── routes/web.php
└── README.md
```

## Controller List

- `app/Http/Controllers/HomeController.php`
- `app/Http/Controllers/Auth/UserAuthController.php`
- `app/Http/Controllers/Auth/AdminAuthController.php`
- `app/Http/Controllers/User/DashboardController.php`
- `app/Http/Controllers/User/ProfileController.php`
- `app/Http/Controllers/User/ResumeController.php`
- `app/Http/Controllers/User/NotificationController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/UserManagementController.php`
- `app/Http/Controllers/Admin/JobRoleController.php`
- `app/Http/Controllers/Admin/ResumeManagementController.php`
- `app/Http/Controllers/Admin/ReportController.php`
- `app/Http/Controllers/Admin/ActivityLogController.php`
- `app/Http/Controllers/Admin/NotificationController.php`

## Model List

- `app/Models/User.php`
- `app/Models/Admin.php`
- `app/Models/JobRole.php`
- `app/Models/Skill.php`
- `app/Models/Resume.php`
- `app/Models/ExtractedResumeData.php`
- `app/Models/ResumeSkillMap.php`
- `app/Models/ResumeScore.php`
- `app/Models/AnalysisReport.php`
- `app/Models/AppNotification.php`
- `app/Models/ActivityLog.php`

## Migration List

- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_03_30_161527_create_admins_table.php`
- `database/migrations/2026_03_30_161527_create_job_roles_table.php`
- `database/migrations/2026_03_30_161527_create_skills_table.php`
- `database/migrations/2026_03_30_161600_create_job_role_skill_table.php`
- `database/migrations/2026_03_30_161527_create_resumes_table.php`
- `database/migrations/2026_03_30_161527_create_extracted_resume_data_table.php`
- `database/migrations/2026_03_30_161527_create_resume_skill_maps_table.php`
- `database/migrations/2026_03_30_161527_create_resume_scores_table.php`
- `database/migrations/2026_03_30_161527_create_analysis_reports_table.php`
- `database/migrations/2026_03_30_161528_create_app_notifications_table.php`
- `database/migrations/2026_03_30_161528_create_activity_logs_table.php`

## Seeder List

- `database/seeders/SkillSeeder.php`
- `database/seeders/JobRoleSeeder.php`
- `database/seeders/AdminSeeder.php`
- `database/seeders/DemoUserSeeder.php`
- `database/seeders/DatabaseSeeder.php`

## Route List

### Public Routes

- `GET /`
- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`
- `GET /admin/login`
- `POST /admin/login`

### User Routes

- `GET /user/dashboard`
- `GET /user/profile`
- `PUT /user/profile`
- `GET /user/resumes`
- `GET /user/resumes/upload`
- `POST /user/resumes`
- `GET /user/resumes/{resume}/progress`
- `GET /user/resumes/{resume}/status`
- `GET /user/resumes/{resume}`
- `GET /user/resumes/{resume}/job-match`
- `DELETE /user/resumes/{resume}`
- `GET /user/notifications/unread-count`
- `POST /user/notifications/mark-all-read`
- `POST /user/logout`

### Admin Routes

- `GET /admin/dashboard`
- `GET /admin/users`
- `GET /admin/users/{user}/edit`
- `PUT /admin/users/{user}`
- `DELETE /admin/users/{user}`
- `GET /admin/job-roles`
- `GET /admin/job-roles/create`
- `POST /admin/job-roles`
- `GET /admin/job-roles/{job_role}`
- `GET /admin/job-roles/{job_role}/edit`
- `PUT /admin/job-roles/{job_role}`
- `DELETE /admin/job-roles/{job_role}`
- `GET /admin/resumes`
- `GET /admin/resumes/{resume}`
- `DELETE /admin/resumes/{resume}`
- `GET /admin/reports`
- `GET /admin/reports/{resume}`
- `GET /admin/activity-logs`
- `GET /admin/notifications/unread-count`
- `POST /admin/notifications/mark-all-read`
- `POST /admin/logout`

## Page List

### User Side

- Home Page
- User Login Page
- User Register Page
- User Dashboard
- Upload Resume Page
- Resume History Page
- Live Analysis Status Page
- Resume Result Page
- Job Match Page
- Profile Page

### Admin Side

- Admin Login Page
- Admin Dashboard
- Manage Users Page
- Manage Job Roles Page
- Create Job Role Page
- Edit Job Role Page
- Manage Resumes Page
- Resume Detail Page
- Report List Page
- Report Detail Page
- Activity Log Page

## Database Relationship Explanation

- One user can upload many resumes.
- One job role can be linked with many resumes.
- One job role can have many skills.
- One skill can belong to many job roles.
- One resume has one extracted data record.
- One resume has one score record.
- One resume has one analysis report.
- One resume can have many skill map rows.
- Notifications can belong to either a user or an admin.
- Activity logs can belong to either a user or an admin.

## Resume Analysis Logic

### File Parsing

- PDF: basic text extraction from file content
- DOCX: reads `word/document.xml` using PHP `ZipArchive`
- DOC: basic binary text cleanup

### Detection

- Name: first valid heading-like text line
- Email: regex
- Phone: regex
- Skills: keyword matching from skill master table
- Education, Experience, Projects: section headings and keyword fallback

### Scoring System

- Contact Details = 10
- Education = 15
- Skills = 25
- Experience = 20
- Projects = 10
- Formatting/Completeness = 10
- Job-Role Match = 10
- Total = 100

## Local Setup Instructions for MAMP on Mac

### Step 1: Create MySQL Database

1. Open MAMP.
2. Start Apache and MySQL.
3. Open phpMyAdmin from MAMP.
4. Create a new database named `ai_resume_analyzer`.

### Step 2: Copy Environment File

```bash
cp .env.example .env
```

### Step 3: Update `.env`

Use these MAMP values:

```env
APP_NAME="AI Resume Analyzer System"
APP_URL=http://localhost:8888

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=ai_resume_analyzer
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file
```

### Step 4: Install Dependencies

```bash
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

### Step 5: Run with MAMP

Option A:
- Set MAMP document root to this project’s `public` folder.

Option B:
- Keep MAMP as it is and use:

```bash
php artisan serve
```

### Step 6: Open the Project

- User side:
  [http://localhost:8888](http://localhost:8888)
- If using `php artisan serve`:
  [http://127.0.0.1:8000](http://127.0.0.1:8000)

## SQL Schema File

The manual MySQL schema is available at:

- `database/sql/ai_resume_analyzer_schema.sql`

## Key File References

- Routes:
  [`routes/web.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/routes/web.php)
- Auth config:
  [`config/auth.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/config/auth.php)
- Analyzer config:
  [`config/resume_analyzer.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/config/resume_analyzer.php)
- Resume parser:
  [`app/Services/Resume/ResumeParserService.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Services/Resume/ResumeParserService.php)
- Score logic:
  [`app/Services/Resume/ResumeScorerService.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Services/Resume/ResumeScorerService.php)
- Report generator:
  [`app/Services/Resume/ReportGeneratorService.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Services/Resume/ReportGeneratorService.php)
- User upload and live status flow:
  [`app/Http/Controllers/User/ResumeController.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Http/Controllers/User/ResumeController.php)
- Admin dashboard:
  [`app/Http/Controllers/Admin/DashboardController.php`](/Users/manavendrasinh/Desktop/ai_resume_analyzer/app/Http/Controllers/Admin/DashboardController.php)

## Features List

- User registration and login
- Admin login and dashboard
- Session-based authentication
- Resume upload with validation
- Resume metadata storage
- Resume parsing using PHP logic
- Skill detection from master skill list
- Job-role matching
- Score calculation out of 100
- Missing skill detection
- Strength and improvement suggestions
- Resume history
- Admin CRUD for job roles and users
- Resume and report management
- Activity logs
- Notification badge polling
- Responsive Bootstrap UI
- Print report option

## Future Scope

- Better PDF text extraction using advanced libraries
- Download report as true PDF
- Email report delivery
- Resume templates and comparison
- Interview question recommendations
- Skill-wise charts and export reports
- Resume ranking for recruiters

## Viva Questions and Answers

### 1. What is the main purpose of this project?

It helps users upload resumes and automatically analyze them for score, missing skills, and job-role match.

### 2. Which technologies are used in this project?

PHP, Laravel, MySQL, Blade, Bootstrap, JavaScript, AJAX, phpMyAdmin, and MAMP.

### 3. Why did you use Laravel?

Laravel provides MVC architecture, routing, middleware, validation, storage handling, and Eloquent ORM, which make development organized and secure.

### 4. How is resume analysis done without AI APIs?

The project uses rule-based logic such as regex, keyword matching, predefined skill lists, string handling, and section detection.

### 5. How is live progress implemented without WebSocket?

JavaScript sends repeated AJAX requests to a Laravel route. The server updates status step-by-step and returns JSON for the progress bar.

### 6. What are the main database relationships?

Users have many resumes, job roles have many skills through a pivot table, and each resume has one extracted data record, one score record, and one report.

### 7. What OOP concepts are used?

Classes, objects, constructor, inheritance, encapsulation, and reusable service classes.

### 8. How is authentication separated for user and admin?

Laravel session guards are used: `web` for users and `admin` for admins.

### 9. How is the score calculated?

It is calculated transparently using fixed weights for contact details, education, skills, experience, projects, formatting, and job-role match.

### 10. What advanced web technology concepts are covered?

Laravel MVC, middleware, AJAX polling, dashboard analytics, file upload, sessions, relational schema, CRUD operations, and OOP service classes.

## Submission Checklist

- Laravel project ready
- MySQL schema ready
- Seeders ready
- User and admin modules ready
- AJAX live status ready
- README with viva content ready
- SQL file ready
- No Node.js dependency for runtime
