<p align="center"><a href="#" target="_blank"><img src="logo.png" width="200" alt="Laravel Logo"></a></p>

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
- Track status: `Not Started` → `In Progress` → `Completed`
- Set deadlines, priority levels, and progress percentage (0–100%)

### 📊 Progress & Dashboard
- Visual progress bars for each task
- Dashboard overview: total tasks, status breakdown, overall progress, nearest deadlines, monthly finance summary
- Real-time statistics powered by Livewire — no full page reload

### ⏰ Deadline Tracking
- Smart urgency indicators, updated automatically based on the current date:
  - 🟢 **Green** — Safe & on track
  - 🟡 **Yellow** — Approaching deadline
  - 🟠 **Orange** — High priority, needs attention soon
  - 🔴 **Red** — Overdue / Critical

### 🗓️ Schedule Management
- Log class schedules (Subject, Day, Time, Room, Lecturer)
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

Nestly's default theme is **Pink**, with dedicated Light Mode and Dark Mode token sets (12 tokens each: Primary, Secondary, Tertiary, Background, Surface, Text, Text Muted, Border, Success, Danger, Warning, Info).

Full color tokens are documented in [`PROJECT.md`](./PROJECT.md#12-color-system).

---

## 🗺️ Development Roadmap

| Phase | Focus | Key Deliverables |
|-------|-------|------------------|
| **Phase 1** 🟢 *(Current)* | **Core Full Stack Build** | Database design & migrations, Task CRUD, Schedule CRUD, Finance Tracker (income/expense/budget), Dashboard summary, deadline urgency indicators, Pink theme (Light/Dark mode), responsive Tailwind UI, Livewire integration |
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