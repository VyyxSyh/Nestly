<p align="center"><a href="#" target="_blank"><img src="Logo.png" width="200" alt="Laravel Logo"></a></p>

<h1 align="center">
Nestly
</h1>

<p align="center">
<a href=""><img src="https://img.shields.io/badge/Status-In%20Development-blue" alt="Status"></a>
<a href=""><img src="https://img.shields.io/badge/Stack-Laravel%20%2B%20Livewire-red" alt="Stack"></a>
<a href=""><img src="https://img.shields.io/badge/Data-MySQL-4479A1" alt="Data"></a>
<a href=""><img src="https://img.shields.io/badge/Theme-Pink-FF4D8D" alt="Theme"></a>
</p>


> 🎓 **All-in-one academic dashboard for students.** Track tasks, monitor progress, manage class schedules, and keep your student finances in check — all in one place.

**Live Demo :** Coming Soon

**Current Status :** Full Stack Development (Laravel + Livewire) — Phase 1 in progress

---

## 🚀 About Nestly

Nestly is a web application designed to help students manage their academic life without the clutter. From tracking assignment deadlines to keeping monthly finances under control, Nestly turns a chaotic student life into a clear, actionable dashboard.

Nestly is built as a **full stack application** using **Laravel** for both backend and frontend (via Blade + Livewire), with data stored in a **MySQL database**. The app runs locally using **Laragon** as the local development server, and the database is managed through **HeidiSQL**.

> ℹ️ The current version runs as a **single-user** application (no full authentication system yet), but all data is already persisted in MySQL — not in the browser (`localStorage`).

---

## ✨ Core Features

### 📝 Task Management
- Create, edit, delete, and categorize academic tasks
- Track status: `Not Started` → `In Progress` → `Completed` (auto-completed once progress reaches 100%)
- Two progress modes: **Manual** (adjust in 5% increments) or **Checklist** (auto-calculated from subtask completion)
- Set deadlines (date required, time optional) and add a description

### 📊 Progress & Dashboard
- Card-grid dashboard layout: Schedule highlight, quick summary, upcoming task deadlines, recent finance transactions, personal greeting, and profile card
- Visual progress bars for each task
- Real-time statistics powered by Livewire — no full page reload

### 🧭 Navigation
- Unconventional bottom navigation (instead of a traditional top navbar) — an intentional design exploration inspired by mobile app patterns
- **Desktop & Tablet:** 3-section bottom bar (logo | menu labels | account icon + theme toggle), with an animated icon reveal on the active menu item
- **Mobile:** icon-only bottom nav (label appears only on the active item) plus a separate sticky top bar for the logo and theme toggle
- Notification badge on the Task icon (e.g. overdue task count) across all breakpoints

### ⏰ Deadline Tracking
- Smart urgency levels, recalculated automatically based on the current date. Checked in order: a task at 100% progress is always `done`, otherwise a passed deadline is `overdue`, otherwise the level is based on days remaining:
  - 🟢 **Safe** — more than 20 days left
  - 🟡 **Approaching** — 13–20 days left
  - 🟠 **Urgent** — 6–12 days left
  - 🔴 **Critical** — 0–5 days left
  - 🔴 **Overdue** — past the deadline
  - ⚪ **Done** — progress at 100%, regardless of the deadline

### 📚 Subjects
- Manage course/subject reference data (add, edit, delete)
- Kept separate from Schedule — Subjects is rarely-changed reference data, while Schedule is checked daily/weekly
- Used as a dropdown reference across Task and Schedule forms

### 🗓️ Schedule Management
- Log class schedules (linked to Subjects, Day, Time, Room, Lecturer)
- Quick reference for weekly academic planning
- Accent color per schedule card for visual variety

### 💰 Finance Tracker
- Record income & expenses with categories (food, transport, allowance, academic needs, etc.)
- Set monthly budget and track remaining balance in real-time
- Visual indicator when spending is approaching or exceeding budget

### 🔍 Search, Filter & Sort
- Filter by status, deadline, subject, or progress
- Sort by nearest deadline, highest/lowest progress, or creation date

### 🎨 Theme & UX
- **Pink theme** (default) with Light / Dark mode toggle — preference saved and applied automatically
- Fully responsive design (Mobile, Tablet, Desktop)

---

## 🛠️ Tech Stack & Architecture

| Component | Technology |
|-----------|------------|
| **Frontend & Backend** | Laravel (Blade + Livewire) |
| **Styling** | Tailwind CSS |
| **Database** | MySQL |
| **Local Dev Server** | Laragon |
| **Database Management Tool** | HeidiSQL |
| **Deployment** | Localhost (personal project / portfolio) |

> ℹ️ No separate JavaScript framework is used — all dynamic/interactive features (real-time updates, filters, progress indicators) are handled through **Livewire**.

---

## 🎨 Color System

Nestly's default theme is **Pink**, with dedicated Light Mode and Dark Mode token sets (12 tokens each: Primary, Secondary, Tertiary, Background, Surface, Text, Text Muted, Border, Success, Danger, Warning, Caution, Neutral, Info).

Full color tokens are documented in [`PROJECT.md`](./PROJECT.md#12-color-system).

---

## 🗺️ Development Roadmap

| Phase | Focus | Key Deliverables |
|-------|-------|------------------|
| **Phase 1** 🟢 *(Current)* | **Core Full Stack Build** | Database design & migrations, Subjects & Schedule CRUD, Task CRUD, Finance Tracker (income/expense/budget), card-grid Dashboard (greeting, profile, summaries), Bottom Nav + Top Bar navigation, deadline urgency indicators, Pink theme (Light/Dark mode), responsive Tailwind UI, Livewire integration |
| **Phase 2** 🟡 | **Refinement & UX Polish** | Search/filter/sorting refinement, multi color theme (**Blue** & **Monochrome**, each with Light + Dark mode) in addition to the default Pink theme, overall data validation & UX improvements |
| **Phase 3** 🔵 | **Enhancements (Future)** | User authentication & multi-user support, role management, notifications, advanced academic/financial analytics, potential cloud/hosting deployment |

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