# Product Requirements Document (PRD)
## Nestly — Student Task & Study Dashboard

**Version:** 1.0 (Full Stack)
**Author:** Syukron Raffiansyah (Vyy)
**Status:** In Development
**Last Updated:** 2026

---

## 1. Problem Statement

Mahasiswa umumnya mengelola tugas kuliah, deadline, jadwal, dan keuangan pribadi secara terpisah — lewat catatan manual, aplikasi to-do generik, kalender, dan pencatatan keuangan seadanya yang tidak saling terhubung. Akibatnya:

- Tugas dan deadline mudah terlewat karena tidak ada satu tempat yang menampilkan urgensi secara jelas.
- Progress pengerjaan tugas sulit dipantau secara visual.
- Jadwal kuliah tidak terintegrasi dengan daftar tugas.
- Pengeluaran bulanan tidak terpantau, sehingga mahasiswa rentan boros dan kehabisan uang sebelum waktunya.

Mahasiswa membutuhkan satu dashboard terpusat yang dapat menampilkan tugas, progress, jadwal, tingkat urgensi deadline, dan kondisi keuangan secara jelas dalam satu aplikasi yang mudah digunakan.

---

## 2. Goals & Objectives

**Tujuan Utama:**
- Membantu mahasiswa mengorganisir tugas dan aktivitas kuliah dalam satu dashboard terpusat.
- Memudahkan pengguna memantau progress setiap tugas secara visual.
- Membantu mahasiswa mengontrol pengeluaran bulanan agar tidak boros.
- Menampilkan informasi akademik dan finansial dalam satu tampilan yang ringkas dan mudah dipahami.
- Membuat pengalaman pengelolaan tugas yang sederhana dan cepat digunakan.
- Membangun aplikasi full stack yang solid sejak awal (Laravel + Livewire + MySQL) sebagai fondasi pengembangan fitur lanjutan di masa depan.

**Objectives (Terukur):**
- Pengguna dapat menambah, mengedit, menghapus, dan melacak status tugas dalam <3 langkah interaksi.
- Dashboard menampilkan ringkasan aktivitas (total tugas, status, progress keseluruhan, deadline terdekat, ringkasan keuangan) tanpa perlu reload halaman (real-time via Livewire).
- Sistem memberi indikator urgensi visual (warna) otomatis berdasarkan kedekatan deadline.
- Sistem memberi indikator visual otomatis ketika pengeluaran mendekati/melebihi budget bulanan yang ditentukan.
- Seluruh data (tugas, progress, jadwal, keuangan, tema) tersimpan secara persisten di database (MySQL), tidak bergantung pada penyimpanan browser.

---

## 3. Target Users

| Segmen | Deskripsi |
|---|---|
| **Mahasiswa (individu)** | Pengguna utama — menggunakan Nestly secara personal untuk mengelola tugas kuliah, jadwal, dan keuangan masing-masing. |
| **Kelompok belajar / study circle** | Kelompok kecil mahasiswa yang ingin memakai konsep dashboard yang sama untuk masing-masing anggota. |
| **Kelas** | Berpotensi menggunakan dashboard bersama pada pengembangan lanjutan (Phase 3) saat multi-user/authentication sudah tersedia. |

**Karakteristik pengguna:**
- Memiliki banyak tugas kuliah dengan deadline berbeda-beda dalam satu waktu.
- Sering kesulitan mengatur keuangan bulanan sebagai mahasiswa.
- Menginginkan solusi cepat pakai dengan tampilan yang jelas.
- Terbiasa menggunakan aplikasi berbasis web/browser.
- Ingin memantau progress belajar, pengerjaan tugas, dan kondisi keuangan secara visual.

> Catatan: Pada versi saat ini, aplikasi berjalan sebagai single-user (belum ada sistem login/authentication penuh), namun seluruh data sudah tersimpan di database MySQL, bukan di browser.

---

## 4. Functional Requirements

### FR-1 — Task Management
- FR-1.1 Pengguna dapat menambahkan tugas baru.
- FR-1.2 Pengguna dapat melihat daftar seluruh tugas.
- FR-1.3 Pengguna dapat mengubah informasi tugas (judul, mata kuliah terkait, deadline, status, dll).
- FR-1.4 Pengguna dapat menghapus tugas.
- FR-1.5 Pengguna dapat memperbarui progress tugas (dalam persentase).
- FR-1.6 Pengguna dapat menentukan deadline tugas.
- FR-1.7 Pengguna dapat menentukan status tugas: `Not Started`, `In Progress`, `Completed`.
- FR-1.8 Pengguna dapat menentukan prioritas tugas.

### FR-2 — Task Progress
- FR-2.1 Setiap tugas menampilkan progress bar visual berdasarkan persentase penyelesaian.
- FR-2.2 Progress dapat diperbarui secara manual oleh pengguna dan langsung ter-update di tampilan (real-time via Livewire).
- FR-2.3 Progress tersimpan otomatis ke database setiap kali diperbarui.

### FR-3 — Dashboard
- FR-3.1 Menampilkan total jumlah tugas.
- FR-3.2 Menampilkan jumlah tugas per status (Not Started / In Progress / Completed).
- FR-3.3 Menampilkan progress keseluruhan (agregat semua tugas).
- FR-3.4 Menampilkan deadline terdekat.
- FR-3.5 Menampilkan ringkasan keuangan bulan berjalan (total pemasukan, pengeluaran, sisa saldo).

### FR-4 — Schedule
- FR-4.1 Pengguna dapat menambahkan jadwal kuliah dengan informasi: nama mata kuliah, hari, waktu, ruangan, dosen.
- FR-4.2 Pengguna dapat melihat, mengedit, dan menghapus jadwal.
- FR-4.3 Jadwal ditampilkan dalam format yang mudah dibaca (mis. per hari/minggu).

### FR-5 — Deadline Tracking
- FR-5.1 Tugas dikategorikan otomatis berdasarkan urgensi deadline: `Due Today`, `Due Tomorrow`, `Upcoming`, `Overdue`.
- FR-5.2 Setiap tugas memiliki indikator warna urgensi otomatis:
  - 🟢 Green — deadline masih jauh, aman.
  - 🟡 Yellow — deadline mulai mendekat.
  - 🟠 Orange — deadline dekat, perlu diprioritaskan.
  - 🔴 Red — deadline mendesak / overdue.
- FR-5.3 Indikator warna diperbarui otomatis berdasarkan tanggal sistem saat ini.

### FR-6 — Finance Tracker
- FR-6.1 Pengguna dapat mencatat pemasukan (contoh: uang saku, kiriman orang tua, penghasilan sampingan) beserta nominal dan tanggal.
- FR-6.2 Pengguna dapat mencatat pengeluaran dengan kategori (contoh: makan, transport, jajan, kebutuhan kuliah, lainnya) beserta nominal dan tanggal.
- FR-6.3 Pengguna dapat menentukan budget bulanan.
- FR-6.4 Sistem menghitung dan menampilkan sisa saldo secara real-time berdasarkan pemasukan dan pengeluaran yang tercatat.
- FR-6.5 Sistem menampilkan indikator visual (progress bar/warna) ketika total pengeluaran mendekati atau melebihi budget bulanan.
- FR-6.6 Pengguna dapat mengedit dan menghapus catatan pemasukan/pengeluaran.

### FR-7 — Search, Filter & Sorting
- FR-7.1 Pengguna dapat mencari tugas berdasarkan kata kunci.
- FR-7.2 Pengguna dapat memfilter tugas berdasarkan: status, deadline, mata kuliah, progress.
- FR-7.3 Pengguna dapat mengurutkan tugas berdasarkan: deadline terdekat, progress tertinggi, progress terendah, tugas terbaru.

### FR-8 — Theme
- FR-8.1 Pengguna dapat beralih antara Light mode dan Dark mode.
- FR-8.2 Preferensi tema disimpan dan diterapkan otomatis saat website dibuka kembali.

### FR-9 — Data Persistence
- FR-9.1 Seluruh data (task, progress, deadline, status, schedule, data keuangan, theme, pengaturan pengguna) disimpan di database MySQL melalui Laravel.
- FR-9.2 Data tetap tersedia secara permanen selama tidak dihapus langsung dari database, tidak bergantung pada browser/perangkat yang digunakan untuk mengakses.

---

## 5. Non-Functional Requirements

| Kategori | Kebutuhan |
|---|---|
| **Usability** | Antarmuka harus intuitif dan dapat digunakan tanpa onboarding/tutorial; alur menambah tugas maksimal 3 langkah. |
| **Performance** | Interaksi CRUD tugas, update progress, dan kalkulasi keuangan harus terasa responsif, memanfaatkan update real-time Livewire tanpa reload halaman penuh. |
| **Responsiveness** | Tampilan harus responsif dan berfungsi baik di perangkat Mobile, Tablet, dan Desktop. |
| **Reliability** | Data tidak boleh hilang selama tidak ada penghapusan manual di database; backend harus menangani validasi input dengan baik. |
| **Availability** | Aplikasi berjalan sebagai web application yang diakses melalui local development server (Laragon) selama tahap pengembangan ini. |
| **Maintainability** | Struktur kode mengikuti konvensi Laravel (MVC) agar mudah dipelihara dan dikembangkan lebih lanjut (mis. penambahan authentication di Phase 3). |
| **Scalability** | Struktur database (migration & relasi antar tabel) dirancang agar mudah diperluas, misalnya menambahkan relasi user saat multi-user diimplementasikan. |
| **Security (Future)** | Saat Phase 3 (Authentication) diimplementasikan, data per-user harus terlindungi dan tervalidasi di sisi server (backend Laravel). |
| **Portability** | Aplikasi saat ini dijalankan secara lokal melalui Laragon; struktur project memungkinkan deployment ke hosting PHP/cloud di tahap berikutnya bila dibutuhkan. |
| **Accessibility** | Kontras warna (termasuk indikator urgensi & dark/light mode) harus tetap terbaca dan sesuai standar aksesibilitas dasar. |

---

## 6. Product Scope

### 6.1 In Scope — Phase 1 (Core Full Stack Build)
- Student dashboard (ringkasan aktivitas & keuangan).
- Task management (CRUD tugas).
- Progress tracking per tugas.
- Deadline management & indikator urgensi warna.
- Schedule (jadwal kuliah).
- Finance tracker (pemasukan, pengeluaran, budget bulanan, indikator saldo).
- Search, filter & sorting tugas.
- Theme preference (Light/Dark mode).
- Data persistence via database MySQL.
- Responsive interface (Mobile, Tablet, Desktop).

### 6.2 Out of Scope — Future Phases

**Phase 2 — Refinement & UX Polish:**
- Penyempurnaan search/filter/sorting.
- Penyempurnaan indikator urgensi deadline.
- Penyempurnaan dark/light mode & aksesibilitas.
- Multi color theme — tambahan tema Blue dan Monochrome (masing-masing Light Mode + Dark Mode), selain tema Pink default dari Phase 1.

**Phase 3 — Enhancement:**
- User authentication (login/register).
- Role management & multi-user support.
- Notifikasi.
- Advanced analytics (laporan akademik & keuangan lebih mendalam).
- Kemungkinan deployment ke hosting/cloud untuk akses publik.

> Catatan: Fitur-fitur di atas TIDAK termasuk dalam scope PRD versi 1.0 ini, namun struktur database & arsitektur Laravel pada Phase 1 dirancang agar kompatibel untuk pengembangan ke fase tersebut (contoh: tabel sudah siap ditambahkan relasi `user_id`).

---

## 7. Features & Requirements Summary

| # | Fitur | Deskripsi Singkat | Prioritas |
|---|---|---|---|
| 1 | Task Management | CRUD tugas, status, deadline, prioritas | Must Have |
| 2 | Task Progress | Progress bar per tugas, update real-time | Must Have |
| 3 | Dashboard | Ringkasan total tugas, status, progress, deadline terdekat, ringkasan keuangan | Must Have |
| 4 | Schedule | Jadwal kuliah (mata kuliah, hari, waktu, ruangan, dosen) | Must Have |
| 5 | Deadline Tracking | Kategori urgensi + indikator warna otomatis | Must Have |
| 6 | Finance Tracker | Pemasukan, pengeluaran, budget bulanan, indikator saldo | Must Have |
| 7 | Search, Filter & Sorting | Cari & atur tugas berdasarkan kriteria | Should Have |
| 8 | Theme (Light/Dark) | Toggle tema + preferensi tersimpan | Should Have |
| 9 | Data Persistence | Simpan semua data ke database MySQL | Must Have |
| 10 | Responsive Design | Optimal di Mobile, Tablet, Desktop | Must Have |

---

## 8. Design System

### 8.1 Prinsip Desain
- **Simple & Clean** — fokus pada keterbacaan informasi, minim clutter.
- **Visual-first for urgency** — pengguna dapat langsung mengenali prioritas tugas hanya lewat warna/indikator visual, tanpa perlu membaca detail.
- **Consistency** — komponen UI (card, button, progress bar) konsisten di seluruh halaman (Dashboard, Task List, Schedule, Finance Tracker).
- **Distraction-free** — tampilan dibuat minim elemen yang tidak perlu agar pengguna tetap fokus pada informasi penting.

### 8.2 Tema (Theming)
- Mendukung **Light Mode** dan **Dark Mode**.
- Preferensi tema disimpan dan diterapkan otomatis saat aplikasi dibuka kembali.

### 8.3 Sistem Warna — Urgency Indicator
Warna berikut digunakan secara konsisten sebagai bahasa visual utama untuk deadline tracking (dan indikator budget pada Finance Tracker):

| Warna | Makna |
|---|---|
| 🟢 Green | Aman, deadline masih jauh / pengeluaran masih jauh dari budget |
| 🟡 Yellow | Mulai mendekat, perlu diperhatikan |
| 🟠 Orange | Mendesak, perlu diprioritaskan / pengeluaran mendekati budget |
| 🔴 Red | Overdue / sangat kritis / pengeluaran melebihi budget |

### 8.4 Komponen Utama
- **Task Card** — menampilkan judul tugas, progress bar, status, dan indikator warna urgensi.
- **Progress Bar** — representasi visual progress (persentase) per tugas dan progress keseluruhan.
- **Dashboard Summary Widget** — kartu ringkasan statistik (total tugas, status, deadline terdekat, ringkasan keuangan).
- **Schedule Card/List** — menampilkan jadwal kuliah secara terstruktur, dengan accent color per kartu.
- **Finance Summary Widget** — kartu ringkasan pemasukan, pengeluaran, dan sisa saldo, dengan indikator progress terhadap budget.
- **Filter & Sort Bar** — kontrol untuk pencarian, filter, dan pengurutan tugas.
- **Theme Toggle** — switch Light/Dark mode.

Referensi warna lengkap (hex code untuk Light & Dark mode, serta accent color untuk Schedule Card) mengikuti **Color System** yang telah ditetapkan di `PROJECT.md`.

### 8.5 Layout & Responsiveness
- Layout harus adaptif untuk 3 breakpoint utama: Mobile, Tablet, Desktop.
- Dashboard sebagai halaman utama (landing) setelah aplikasi dibuka.

---

## Appendix — Referensi Tambahan (dari PROJECT.md & README.md)

- **Data yang disimpan di database (MySQL):** Task, Progress, Deadline, Status, Schedule, Data keuangan (income, expense, budget), Theme preference, Pengaturan pengguna.
- **Roadmap Pengembangan:**
  - Phase 1 (🟢 Current) — Core Full Stack Build (Task, Schedule, Finance Tracker, Dashboard, Livewire integration).
  - Phase 2 (🟡 Future) — Refinement & UX Polish (search/filter/sorting, urgency indicator, dark/light mode, multi color theme Blue & Monochrome).
  - Phase 3 (🔵 Future) — Enhancement (Authentication, multi-user, notifications, advanced analytics, kemungkinan deployment ke hosting/cloud).
- **Deployment (Saat Ini):** Local development via Laragon, database dikelola via HeidiSQL — digunakan sebagai project pembelajaran/portofolio pribadi.
- **Tech Stack:** Laravel (Backend & Frontend melalui Blade + Livewire), Tailwind CSS untuk styling, MySQL sebagai database. Tidak menggunakan JavaScript framework terpisah — seluruh interaktivitas ditangani oleh Livewire.