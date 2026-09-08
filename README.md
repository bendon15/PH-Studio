# PHStudio — Photography Booking & Client Management Platform

> **Portfolio Demo — Not a real photography booking service.**
> All photographers, clients, bookings, and images in this project are fictional and included for demonstration purposes only.

PHStudio is a full-stack photography studio platform built with **HTML, CSS, vanilla JavaScript, PHP, and MySQL**. It combines a premium public-facing studio website with a client portal, private client galleries, and a full photographer/admin back office — the kind of system a real boutique photography studio could run their business on.

**Live Demo (static, Vercel):** _add your deployed URL here after following the deployment steps below_
**Full Source (PHP + MySQL):** this repository, in `/backend`

---

## Table of Contents

1. [Why two versions of the app?](#why-two-versions-of-the-app)
2. [Feature Overview](#feature-overview)
3. [Tech Stack](#tech-stack)
4. [Project Structure](#project-structure)
5. [Running the Full PHP + MySQL App Locally](#running-the-full-php--mysql-app-locally)
6. [Demo Accounts](#demo-accounts)
7. [Deploying the Static Demo to Vercel](#deploying-the-static-demo-to-vercel)
8. [Database Schema Overview](#database-schema-overview)
9. [Security Notes](#security-notes)
10. [Screenshots / Pages Map](#pages-map)
11. [License](#license)

---

## Why two versions of the app?

Vercel is a static/serverless host — it does not run long-lived PHP processes or a MySQL server. To keep this repository **both a genuine full-stack PHP + MySQL project and a one-click live demo**, the project ships as two parts:

| Folder          | What it is                                                                                     | Where it runs                                   |
|------------------|--------------------------------------------------------------------------------------------------|--------------------------------------------------|
| `/backend`       | The **complete implementation** — PHP 8, PDO/MySQL, sessions, real auth, file uploads, everything described in this README. This is the "real" app you'd deploy to any LAMP/PHP host. | Apache/Nginx + PHP-FPM, or `php -S` locally, or any traditional PHP host (see below) |
| `/vercel-demo`   | A **static, front-end-only** re-creation of the same UI/UX and flows, using vanilla JS + `localStorage` as a mock database instead of PHP + MySQL. This is what Vercel actually serves. | Vercel (or any static host / just opening the files) |
| `/database`      | `schema.sql` + `seed.sql` — the real relational schema and demo data used by `/backend`.        | Any MySQL/MariaDB server                          |

Both versions share the same visual design system, page structure, and user flows, so the Vercel demo is a faithful preview of what the PHP application does — it just persists data to your browser's `localStorage` instead of a real database, so anyone can click through the whole product with zero setup.

**For a portfolio/recruiter/reviewer:** link them to the Vercel demo for an instant interactive preview, and point them at `/backend` + `/database` in this repo to show the real, production-style PHP/MySQL implementation.

---

## Feature Overview

### Public Website
- Editorial hero section, studio introduction, and About page
- Portfolio / gallery with category filtering (Weddings, Portraits, Events, Graduation, Prenatal, Corporate)
- Packages & pricing page
- Testimonials, FAQ accordion, contact form
- Booking call-to-action flow
- Fully responsive (mobile-first breakpoints)

### Client Portal
- Register / log in (bcrypt-hashed passwords)
- Manage profile & change password
- Multi-step booking flow: choose package → live date/time availability → event details → optional reference image upload → review & submit
- Booking history with status filters
- Per-booking detail page with messages/notes thread with the photographer
- Invoices & payment status

### Private Client Galleries
- Photographer creates a named gallery per client/booking (e.g. *"Juan & Maria — Wedding Gallery"*)
- Client can view photos, ❤️ favorite individual photos, download (when enabled), read photographer notes
- Secure shareable link (`gallery-share.php?token=...`) for sharing with family — no login required to view

### Photographer / Admin Dashboard
- Stats dashboard (clients, bookings, revenue, upcoming sessions)
- Client management
- Booking management with full status lifecycle + audit history
- Package management (CRUD)
- Availability / calendar management (define working windows, block dates)
- Gallery management (create galleries, upload photos, publish/unpublish)
- Testimonials management
- Invoice & payment tracking (record partial/full payments)
- Basic reports (bookings by category, top packages, revenue by month, completion rate)

### Booking Logic
- Availability windows are defined per photographer, per date
- `/backend/api/check-availability.php` returns only open (non-booked, non-blocked) 1-hour slots for a chosen date
- A `UNIQUE` constraint on `(photographer_id, session_date, start_time)` plus an application-level check prevents double-booking
- Booking statuses: `pending → confirmed → in_progress → completed`, or `cancelled` at any point, with a full history log

---

## Tech Stack

- **Frontend:** HTML5, CSS3 (custom design system, no framework), vanilla JavaScript (fetch API, no build step)
- **Backend:** PHP 8+ (no framework — plain PHP with PDO, sessions, and small reusable includes)
- **Database:** MySQL 8 / MariaDB 10.4+ (relational schema, foreign keys, InnoDB)
- **Static Demo:** same HTML/CSS, vanilla JS + `localStorage` in place of PHP/MySQL

No Composer packages or npm build tooling are required to run the PHP app — it's dependency-free by design so it's easy to read, review, and deploy anywhere PHP runs.

---

## Project Structure

```
phstudio/
├── backend/                  # Full PHP + MySQL application
│   ├── admin/                 # Photographer/admin dashboard pages
│   ├── api/                   # JSON endpoints (availability check, favorites)
│   ├── assets/                # css/, js/, images/
│   ├── auth/                  # login.php, register.php, logout.php
│   ├── client/                # Client portal pages
│   ├── config/                # config.php (env loader), db.php (PDO)
│   ├── includes/              # functions.php, header/footer/dash layout partials
│   ├── uploads/                # Uploaded reference images & gallery photos
│   ├── index.php, about.php, portfolio.php, packages.php,
│   │   faq.php, contact.php, booking.php, gallery-share.php
├── database/
│   ├── schema.sql             # Full relational schema
│   └── seed.sql                # Demo data (fictional)
├── vercel-demo/                # Static, localStorage-backed live demo for Vercel
│   ├── css/                    # Same design system as /backend/assets/css
│   ├── js/                     # mock-data.js, store.js, app.js
│   └── *.html                  # One file per page/flow
├── .env.example
├── .gitignore
├── vercel.json
└── README.md
```

---

## Running the Full PHP + MySQL App Locally

### Requirements
- PHP 8.0+
- MySQL 8.0+ or MariaDB 10.4+
- (Optional) Apache/Nginx — the PHP built-in server works fine for local dev

### 1. Clone & configure

```bash
git clone https://github.com/<your-username>/phstudio.git
cd phstudio
cp .env.example .env
```

Edit `.env` with your local MySQL credentials.

### 2. Create the database

```bash
mysql -u root -p -e "CREATE DATABASE phstudio CHARACTER SET utf8mb4;"
mysql -u root -p phstudio < database/schema.sql
mysql -u root -p phstudio < database/seed.sql
```

### 3. Verify the demo password hashes

The seed file ships with bcrypt hashes for the password `Demo@1234`. Hash output can vary slightly between PHP builds/cost factors, so if login fails locally, regenerate and update it:

```bash
php -r "echo password_hash('Demo@1234', PASSWORD_BCRYPT), PHP_EOL;"
```

```sql
UPDATE users SET password_hash = '<paste-the-generated-hash>';
```

### 4. Run the app

```bash
php -S localhost:8000 -t backend
```

Visit **http://localhost:8000** — that serves `backend/index.php` as the homepage.

> If you deploy to Apache/Nginx instead, point the document root at `/backend` and make sure `.env` sits **one level above** it (the project root), matching the path used in `backend/config/config.php`.

---

## Demo Accounts

All seeded accounts share the password **`Demo@1234`**.

| Role          | Email                          | Access                                  |
|---------------|----------------------------------|-------------------------------------------|
| Admin         | `admin@phstudio.demo`            | Full admin dashboard                        |
| Photographer  | `marco@phstudio.demo`            | Admin dashboard (booking/gallery/calendar owner) |
| Client        | `juan.delacruz@example.com`      | Client portal, has a confirmed wedding booking & private gallery |
| Client        | `maria.santos@example.com`       | Client portal, has a pending booking |
| Client        | `andrea.reyes@example.com`       | Client portal, has a completed booking + published gallery |
| Client        | `paolo.ramirez@example.com`      | Client portal, has a completed booking + published gallery |

You can also register a brand-new client account from either the PHP app or the static demo — both flows work end-to-end.

---

## Deploying the Static Demo to Vercel

The static demo in `/vercel-demo` needs **no build step and no backend** — it's plain HTML/CSS/JS.

### Option A — Vercel Dashboard
1. Push this repository to GitHub.
2. In Vercel, click **Add New → Project** and import the repo.
3. When asked for the **Root Directory**, this repo's `vercel.json` already sets `outputDirectory: vercel-demo`, so you can leave the root directory as the repo root and Vercel will serve `/vercel-demo`.
4. Framework preset: **Other** (no build command needed).
5. Click **Deploy**.

### Option B — Vercel CLI
```bash
npm i -g vercel
vercel login
vercel --prod
```
Vercel will read `vercel.json` at the project root and serve the contents of `vercel-demo/` directly.

### What the static demo can and can't do
- ✅ Full public site, browsing, filtering, FAQ, contact form (saved to `localStorage`)
- ✅ Register/login (simulated — accounts are stored in your browser only)
- ✅ Full multi-step booking flow with live "availability" pulled from mock data
- ✅ Client dashboard, booking history, messages thread, invoices
- ✅ Private galleries with favoriting, share links, download simulation
- ✅ Admin dashboard, booking status management, gallery creation
- ❌ No real file uploads (the demo shows sample images instead of your uploaded files)
- ❌ Data does not sync between browsers/devices — it's per-browser `localStorage`, reset anytime via your browser's dev tools (`localStorage.clear()`) or by clearing site data

---

## Database Schema Overview

See `database/schema.sql` for the full DDL. Key tables:

- **users** — single table for admin/photographer/client roles
- **categories**, **portfolio_photos** — public portfolio content
- **packages** — pricing & deliverables (JSON array column)
- **availability_slots** — photographer working windows / blocked dates
- **bookings**, **booking_references**, **booking_status_history**, **booking_messages** — the booking lifecycle, uploaded reference images, audit trail, and client↔photographer messaging
- **invoices** — payment tracking per booking
- **galleries**, **gallery_photos**, **gallery_favorites** — private client galleries with share tokens and per-client favorites
- **testimonials**, **faqs**, **contact_messages** — public site content management

Foreign keys and a `UNIQUE (photographer_id, session_date, start_time)` constraint on `bookings` enforce referential integrity and prevent double-booking at the database level (on top of the application-level check in `includes/functions.php`).

---

## Security Notes

This is a portfolio project, but it follows real-world practices:

- Passwords hashed with `password_hash()` / verified with `password_verify()` (bcrypt)
- All queries use **PDO prepared statements** — no string-concatenated SQL
- CSRF tokens on all state-changing forms (`csrf_field()` / `verify_csrf()`)
- Output escaped with `htmlspecialchars()` via the `e()` helper to prevent XSS
- Role-based access control (`require_role()`) gating every client/admin page
- File uploads validated by MIME type (via `finfo`), size-limited, and renamed to random filenames before being stored outside of guessable paths
- Session regenerated on login to mitigate session fixation

For a production deployment you'd want to add: rate limiting on auth endpoints, email verification, HTTPS enforcement, a real mail service for notifications, and a CSP header — intentionally left out to keep this a readable, dependency-free portfolio codebase.

---

## Pages Map

| Public Site | Client Portal | Admin/Photographer |
|---|---|---|
| `/index.php` — Home | `/client/dashboard.php` | `/admin/dashboard.php` |
| `/portfolio.php` | `/client/book-session.php` | `/admin/bookings.php` |
| `/packages.php` | `/client/my-bookings.php` | `/admin/booking-detail.php` |
| `/about.php` | `/client/booking-detail.php` | `/admin/clients.php` |
| `/faq.php` | `/client/invoices.php` | `/admin/packages.php` |
| `/contact.php` | `/client/gallery.php` | `/admin/availability.php` |
| `/booking.php` | `/client/gallery-view.php` | `/admin/galleries.php` |
| `/gallery-share.php?token=` | `/client/profile.php` | `/admin/gallery-manage.php` |
| `/auth/login.php`, `/auth/register.php` | | `/admin/testimonials.php` |
| | | `/admin/invoices.php` |
| | | `/admin/reports.php` |

---

## License

This project is provided as-is for portfolio and educational purposes. All fictional client names, testimonials, and stock photography (sourced from Unsplash) are used for demonstration only and do not represent real people, bookings, or endorsements.
