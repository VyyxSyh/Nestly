# Changelog — Nestly

Dokumentasi perubahan & rencana rilis per-versi. Format mengikuti gaya [Keep a Changelog](https://keepachangelog.com/), disesuaikan untuk kebutuhan project pribadi.

> Catatan: Versi (v1.0, v1.1, dst) berbeda dari konsep **Phase** di `PROJECT.md`/`TODO.md`. Phase = tahapan besar pengembangan; Versi = milestone rilis konkret. Mapping: **v1.0** = fondasi fungsional dalam Phase 1, **v1.1** = Phase 1 selesai penuh (termasuk UI final), **v1.2** = Phase 2, **v1.3** = Phase 3.

---

## [v1.0] — Functional Foundation *(In Progress)*

Seluruh fitur utama sudah berfungsi penuh (CRUD + logic) di semua halaman, namun **UI/styling masih apa adanya** (belum melalui tahap desain visual final).

### Added
- Setup project: Laravel + Livewire + Tailwind CSS, database MySQL via Laragon, HeidiSQL
- Task Management: migration, model, CRUD, deskripsi, mode progress (Manual/Checklist)
- Task Progress: sub-tugas/checklist, kalkulasi progress otomatis, status `Completed` otomatis saat 100%
- Subjects (Mata Kuliah): CRUD, dipakai sebagai referensi dropdown di Task & Schedule
- Schedule: CRUD, relasi ke Subjects, accent color per card (fungsi ada, styling belum final)
- Deadline Tracking: kalkulasi level urgensi otomatis (`safe`/`approaching`/`urgent`/`critical`/`overdue`/`done`)
- Finance Tracker: pencatatan income/expense, budget bulanan, kalkulasi saldo real-time
- Search, Filter & Sorting: pencarian judul tugas, filter status/mata kuliah, 5 opsi sorting

---

## [v1.1] — Phase 1 Complete *(Planned)*

Melengkapi v1.0 dengan seluruh tampilan visual final, sehingga Phase 1 (Core Full Stack Build) dianggap selesai sepenuhnya.

### Planned
- Dashboard: layout card grid 6×4 (Jadwal, Ringkasan, List Tugas, List Transaksi, Profil, Greeting)
- Navigation: Bottom Nav (Desktop/Tablet/Mobile) + Top Bar khusus Mobile, animasi icon, badge notifikasi
- Theme: implementasi token warna Pink (Light/Dark), termasuk token baru `Caution` & `Neutral`
- Task Card: styling neo-brutalist (border tebal + shadow offset), badge nempel border, layout 2 kolom, Todo List
- Task List Page: layout 2 kolom masonry (Desktop/Tablet), 1 kolom (Mobile)
- Schedule Card: styling neo-brutalist, badge Hari, layout "boarding pass"
- Responsive testing (Mobile/Tablet/Desktop), aksesibilitas kontras warna
- Loading & empty state di setiap halaman
- Data persistence: seeder data dummy, handling edge case, verifikasi relasi antar tabel
- Manual testing seluruh CRUD, testing lintas browser, bug fixing
- Finalisasi README.md/PROJECT.md/PRD.md, dokumentasi setup project
- Tag/commit milestone `v1.1 — Phase 1 Complete`

---

## [v1.2] — Refinement & UX Polish *(Future — Phase 2)*

### Planned
- Penyempurnaan UX search/filter/sorting & optimasi performa Livewire
- Penyempurnaan indikator urgensi & aksesibilitas warna
- Multi color theme: tema **Blue** dan **Monochrome** (masing-masing Light/Dark), selain Pink default
- Mekanisme pilih & simpan preferensi tema warna (terpisah dari preferensi Light/Dark mode)
- **Calendar View di Dashboard** — section baru (ke-7, terpisah dari grid 6×4 yang sudah ada) di bagian paling bawah Dashboard, menampilkan kalender bulanan. Tanggal yang punya deadline tugas ditandai dengan titik indikator. Klik tanggal akan expand inline di bawah kalender, menampilkan jadwal kuliah dan tugas dengan deadline pada tanggal tersebut.

---

## [v1.3] — Enhancement *(Future — Phase 3)*

### Planned
- User authentication & multi-user support (Laravel Breeze/Fortify)
- Role management
- Migrasi struktur data untuk mendukung multi-user (`user_id` di tabel relevan)
- Cross-device synchronization
- Notifications system
- Advanced analytics (laporan akademik & keuangan)
- Deployment ke hosting/cloud