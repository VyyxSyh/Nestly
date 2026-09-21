# Changelog — Nestly

Dokumentasi perubahan & rencana rilis per-versi. Format mengikuti gaya [Keep a Changelog](https://keepachangelog.com/), disesuaikan untuk kebutuhan project pribadi.

> Catatan: Versi (v1.0, v1.1, dst) berbeda dari konsep **Phase** di `PROJECT.md`/`TODO.md`. Phase = tahapan besar pengembangan; Versi = milestone rilis yang lebih granular di dalam sebuah Phase.

---

## [v1.0] — Core Data Layer *(In Progress)*

Fondasi backend & CRUD dasar untuk seluruh fitur utama.

### Added
- Setup project: Laravel + Livewire + Tailwind CSS, database MySQL via Laragon, HeidiSQL
- Task Management: migration, model, CRUD, deskripsi, mode progress (Manual/Checklist)
- Task Progress: sub-tugas/checklist, kalkulasi progress otomatis, status `Completed` otomatis saat 100%
- Subjects (Mata Kuliah): CRUD, dipakai sebagai referensi dropdown di Task & Schedule
- Schedule: CRUD, relasi ke Subjects, accent color per card
- Deadline Tracking: kalkulasi level urgensi otomatis (`safe`/`approaching`/`urgent`/`critical`/`overdue`/`done`)
- Finance Tracker: pencatatan income/expense, budget bulanan, kalkulasi saldo real-time
- Search, Filter & Sorting: pencarian judul tugas, filter status/mata kuliah, 5 opsi sorting

### Status
Sebagian besar checklist di atas sudah selesai (`[x]` di `TODO.md`); sisanya jadi acuan v1.1.

---

## [v1.1] — UI/UX Layer *(Planned)*

Membangun tampilan visual di atas fondasi data yang sudah ada di v1.0.

### Planned
- Dashboard: layout card grid 6×4 (Jadwal, Ringkasan, List Tugas, List Transaksi, Profil, Greeting)
- Navigation: Bottom Nav (Desktop/Tablet/Mobile) + Top Bar khusus Mobile, animasi icon, badge notifikasi
- Theme: implementasi token warna Pink (Light/Dark), termasuk token baru `Caution` & `Neutral`
- Task Card: styling neo-brutalist (border tebal + shadow offset), badge nempel border, layout 2 kolom, Todo List
- Task List Page: layout 2 kolom masonry (Desktop/Tablet), 1 kolom (Mobile)
- Schedule Card: styling neo-brutalist, badge Hari, layout "boarding pass"

---

## [v1.2] — Polish & QA *(Planned)*

Penyempurnaan responsive, robustness data, dan pengujian sebelum Phase 1 dianggap selesai.

### Planned
- Testing tampilan di Mobile, Tablet, Desktop
- Review aksesibilitas kontras warna (Light/Dark & indikator urgensi/budget)
- Loading & empty state di setiap halaman
- Data persistence: seeder data dummy, handling edge case, verifikasi relasi antar tabel
- Manual testing seluruh CRUD, testing lintas browser, bug fixing
- Finalisasi README.md/PROJECT.md/PRD.md, dokumentasi setup project
- Tag/commit milestone `v1.2 — Phase 1 Complete`

---

## [v2.0] — Refinement & UX Polish *(Future — Phase 2)*

### Planned
- Penyempurnaan UX search/filter/sorting & optimasi performa Livewire
- Multi color theme: tema **Blue** dan **Monochrome** (masing-masing Light/Dark), selain Pink default
- Mekanisme pilih & simpan preferensi tema warna

---

## [v3.0] — Enhancement *(Future — Phase 3)*

### Planned
- User authentication & multi-user support (Laravel Breeze/Fortify)
- Role management
- Migrasi struktur data untuk mendukung multi-user (`user_id` di tabel relevan)
- Cross-device synchronization
- Notifications system
- Advanced analytics (laporan akademik & keuangan)
- Deployment ke hosting/cloud