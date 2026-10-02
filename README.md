<p align="center"><a href="#" target="_blank"><img src="public/logo.png" width="200" alt="Laravel Logo"></a></p>

<h1 align="center">
Nestly
</h1>

<p align="center">
<a href=""><img src="https://img.shields.io/badge/Status-In%20Development-blue" alt="Status"></a>
<a href=""><img src="https://img.shields.io/badge/Stack-Laravel%20%2B%20Livewire-red" alt="Stack"></a>
<a href=""><img src="https://img.shields.io/badge/Data-MySQL-4479A1" alt="Data"></a>
<a href=""><img src="https://img.shields.io/badge/Theme-Pink-FF4D8D" alt="Theme"></a>
</p>


> **Dashboard belajar untuk siswa SMA/SMK dan mahasiswa.** Catat tugas dan deadline, tentukan prioritas, pantau progress, atur jadwal, serta kelola keuangan pribadi.

**Live Demo :** Coming Soon

**Current Status :** Phase 1 — Core Full Stack Build

---

## 🚀 About Nestly

Nestly helps students manage school or university tasks in one dashboard. Track deadlines and progress, prioritize upcoming work, organize schedules, and monitor personal finances.

Nestly is built as a **full stack application** using **Laravel** for both backend and frontend (via Blade + Livewire), with data stored in a **MySQL database**. The app runs locally using **Laragon** as the local development server, and the database is managed through **HeidiSQL**.

> The current version is single-user with no login. Tasks, schedules, subjects, and finance data are stored in MySQL. Theme preference is stored in browser `localStorage`; account-based cross-device sync is planned for Phase 3.

---

## ✨ Core Features

### 📝 Task Management
- Create, edit, delete, and categorize academic tasks
- Track status: `Not Started` → `In Progress` → `Completed` (auto-completed once progress reaches 100%)
- Two progress modes: **Manual** (adjust in 5% increments) or **Checklist** (auto-calculated from subtask completion)
- Set deadlines (date required, time optional) and add a description

### 📊 Progress & Dashboard
- Dashboard: today's schedule, task statistics, nearest unfinished deadlines, current-month finance summary and transactions
- Visual progress bars for each task
- Real-time statistics powered by Livewire — no full page reload
- Calendar View planned for Phase 2; profile and customizable greeting planned for Phase 3

### 🧭 Navigation
- Unconventional bottom navigation (instead of a traditional top navbar) — an intentional design exploration inspired by mobile app patterns
- Floating liquid-glass Bottom Nav on Dashboard, Task, Schedule, Subjects, and Finance
- **Desktop & Tablet:** logo, labeled menu with sliding active indicator, and theme toggle
- **Mobile:** icon menu with active label, plus fixed Top Bar with logo and theme toggle
- Task badge shows unfinished task count; Schedule badge shows today's schedule count

### ⏰ Deadline Tracking
- Smart urgency levels, recalculated automatically based on the current date. Checked in order: a task at 100% progress is always `done`, otherwise a passed deadline is `overdue`, otherwise the level is based on days remaining:
  - 🟢 **Safe** — more than 20 days left
  - 🟡 **Approaching** — 13–20 days left
  - 🟠 **Urgent** — 6–12 days left
  - 🔴 **Critical** — 0–5 days left
  - 🔴 **Overdue** — past the deadline
  - ⚪ **Done** — progress at 100%, regardless of the deadline

### 📚 Subjects
- Manage school subjects or university courses (add, edit, delete)
- Kept separate from Schedule — Subjects is rarely-changed reference data, while Schedule is checked daily/weekly
- Used as a dropdown reference across Task and Schedule forms

### 🗓️ Schedule Management
- Log school/university schedules (linked to Subjects, day, time, room, teacher/lecturer)
- Quick reference for weekly academic planning
- Accent color per schedule card for visual variety

### 💰 Finance Tracker
- Record income & expenses with categories (food, transport, allowance, academic needs, etc.)
- Set a monthly budget and track net balance in real-time; net balance carries over from previous months, budget remains monthly
- Visual indicator when spending is approaching or exceeding budget

### 🔍 Search, Filter & Sort
- Search tasks by title; filter by status and dynamic Subjects list
- Sort by deadline, progress, creation date, or Subjects (A–Z); completed tasks stay at the bottom
- Combine search, filters, and sorting

### 🎨 Theme & UX
- **Pink theme** with Light / Dark mode toggle — preference saved in browser `localStorage`
- Fully responsive design (Mobile, Tablet, Desktop)

---

## 🛠️ Tech Stack & Architecture

| Component | Technology |
|-----------|------------|
| **Frontend & Backend** | Laravel (Blade + Livewire) |
| **Styling** | Tailwind CSS |
| **Database** | MySQL (SQLite also available for local/testing use) |
| **Local Dev Server** | Laragon |
| **Database Management Tool** | HeidiSQL |
| **Deployment** | Localhost (personal project / portfolio) |

> ℹ️ No separate JavaScript framework is used — all dynamic/interactive features (real-time updates, filters, progress indicators) are handled through **Livewire**.

## Local Setup

Requirements: PHP 8.3+, Composer, Node.js/npm, and MySQL (or SQLite).

1. Clone the repository and enter the project directory.
2. Install dependencies and build assets:

   ```bash
   composer install
   npm install
   ```

3. Create `.env` from `.env.example`, set `APP_NAME=Nestly`, and configure `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your database.
4. Generate the application key, migrate, and build:

   ```bash
   php artisan key:generate
   php artisan migrate
   npm run build
   ```

5. Start the app:

   ```bash
   php artisan serve
   ```

Open the URL printed by Artisan. For Laragon, start Apache and MySQL, create a database, then use its credentials in `.env`.

---

## 🎨 Color System

Nestly's default theme is **Pink**, with dedicated Light Mode and Dark Mode token sets (12 tokens each: Primary, Secondary, Tertiary, Background, Surface, Text, Text Muted, Border, Success, Danger, Warning, Caution, Neutral, Info).

Full color tokens are documented in [`PROJECT.md`](./PROJECT.md#12-color-system).

---

## 🗺️ Development Roadmap

| Phase | Focus | Key Deliverables |
|-------|-------|------------------|
| **Phase 1** 🟢 *(Current)* | **Core Full Stack Build** | Task/progress, Subjects/Schedule, Finance, dashboard summaries, search/filter/sorting, urgency indicators, Light/Dark toggle, responsive liquid-glass navigation, UX polish |
| **Phase 2** 🟡 | **Calendar View** | Monthly Dashboard calendar, deadline markers, inline date details |
| **Phase 3** 🔵 | **Enhancements (Future)** | Profile and customizable greeting, authentication, multi-user, account-based theme sync, roles, notifications, analytics, potential deployment |

---

## 🤝 Contributing & Feedback

Nestly is a personal academic project aimed at solving real student pain points. Feedback, feature requests, and collaboration are highly welcome!
- 🐛 Found a bug? Open an [Issue](#)
- 💡 Have an idea? Start a [Discussion](#)
- 🛠️ Want to contribute? Check the [Roadmap](#️-development-roadmap)


> ⚠️ **Disclaimer:** Nestly is currently in active development. Features and architecture may evolve as the project progresses.

---

<div align="center">
  <sub>Built with ❤️ by <strong>Syukron Raffiansyah (Vyy)</strong> • 2026</sub>
</div>
