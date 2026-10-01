# TODO — Nestly (Phase 1: Core Full Stack Build)

Checklist pengembangan Nestly, dikelompokkan per fitur sesuai PRD.md. Centang item yang sudah selesai.

> Legend: `[ ]` belum dikerjakan · `[~]` sedang dikerjakan · `[x]` selesai

---

## 🏗️ Setup & Foundation
- [x] Install Laravel project baru
- [x] Install & konfigurasi Livewire
- [x] Install & konfigurasi Tailwind CSS
- [x] Setup database MySQL via Laragon
- [x] Buat koneksi database di `.env` & test koneksi
- [x] Setup HeidiSQL untuk akses & inspeksi database
- [x] Rancang skema database awal (tabel: tasks, mata_kuliah/subjects, schedules, finance_records, budgets, user_settings/preferensi Greeting & Profil)
- [x] Setup struktur folder project (Livewire components, views, routes)
- [x] Setup Git repository & `.gitignore`

## ✅ Task Management
- [x] Migration tabel `tasks` (judul, deskripsi, subject_id, deadline, status, progress_mode, progress)
- [x] Model `Task` + relasi ke `Subject`
- [x] Livewire component: form tambah tugas (judul, dropdown mata kuliah, deskripsi/textarea, deadline, dropdown mode progress)
- [x] Input deadline: date picker (wajib) + time picker (opsional)
- [x] Livewire component: daftar tugas (Task List) dengan Task Card sesuai desain PRD.md 8.7
- [x] Layout Task List: 2 kolom masonry (Desktop & Tablet), 1 kolom stack (Mobile) — lihat PRD.md FR-1.10
- [x] Fitur edit tugas (icon edit di card)
- [x] Fitur hapus tugas (icon hapus + konfirmasi)
- [x] Logika status `Completed` otomatis ter-set saat progress mencapai 100% (bukan field manual terpisah)
- [x] Format tampilan deadline dengan nama hari (contoh: "Rabu, 16 September 2026")
- [x] Validasi input form (judul wajib, deadline valid, dll) menggunakan Laravel Validation

## 📊 Task Progress
- [x] Migration tabel `subtasks` (task_id, judul, is_completed)
- [x] Model `Subtask` + relasi ke `Task`
- [x] Field `progress_mode` pada tabel `tasks` (enum: manual, checklist)
- [x] Livewire component: progress bar + tombol `+`/`-` (kelipatan 5%) — khusus Mode Manual
- [x] Livewire component: Todo List/checklist sub-tugas — khusus Mode Checklist
- [x] Logika kalkulasi otomatis progress dari proporsi sub-tugas selesai (Mode Checklist)
- [x] Progress tersimpan otomatis ke database setiap perubahan, real-time via Livewire
- [x] Widget/indikator progress keseluruhan (agregat semua tugas) di halaman Task

## 🏠 Dashboard
- [x] Layout halaman utama (Dashboard) sebagai route utama
- [x] Setup CSS Grid 6 kolom × 4 baris (gap 10px) untuk Desktop, sesuai spesifikasi PRD.md 8.6
- [x] Livewire component: section Jadwal (kolom 1-5, baris 1) — 4 card jadwal sejajar
- [x] Livewire component: section Ringkasan Singkat (kolom 6, baris 1) — jumlah tugas belum selesai + % budget terpakai
- [x] Livewire component: section List Tugas Terdekat Deadline (kolom 1-2, baris 2-4) — nama, aksen warna mata kuliah, deadline (3-5 item)
- [ ] Livewire component: section List Transaksi Finance (kolom 3-4, baris 2-4) — 3-5 transaksi bulan berjalan
- [ ] Empty state untuk List Transaksi Finance jika belum ada transaksi bulan ini
- [ ] Livewire component: section Profil (kolom 5-6, baris 3-4) — foto profil (lingkaran), nama, kelas
- [ ] Livewire component: section Greeting (kolom 5-6, baris 2) — format "[Kata Sapaan], [Nama Panggilan]!"
- [ ] Fitur edit Kata Sapaan (dropdown/select: Hai, Hii, Halo, Alloww, Heyy, dll) + tombol edit di kanan section Greeting
- [ ] Fitur edit Nama Panggilan, tersimpan ke database
- [ ] Migration & model untuk menyimpan preferensi Greeting (Kata Sapaan + Nama Panggilan) per pengguna
- [ ] Responsive: Tablet — scale down grid 6x4 tanpa ubah susunan
- [ ] Responsive: Mobile — restrukturisasi jadi 1 kolom vertikal (urutan: Greeting+Profil → Jadwal → Ringkasan → List Tugas → List Transaksi)

## 🧭 Navigation
- [x] Livewire/Blade component: Bottom Nav (tampil di semua halaman)
- [x] Desktop & Tablet — 3 section: logo+teks (kiri), menu label Home/Task/Schedule/Subjects/Finance (tengah), icon akun + toggle Light/Dark (kanan)
- [x] Animasi icon slide-in dari belakang label saat menu aktif (dan slide-out saat pindah halaman)
- [x] Styling border & warna berbeda untuk menu yang sedang aktif (pakai token Primary)
- [x] Tablet — scaled down version dari layout Desktop
- [x] Mobile — Bottom Nav icon-only (Home, Task, Schedule, Subjects, Finance), label muncul saat menu aktif
- [x] Badge notifikasi (angka) di icon Task & Schedule — semua breakpoint
- [x] Mobile — Top Bar terpisah: logo+teks (kiri), toggle Light/Dark (kanan)
- [x] Top Bar `position: fixed`, border-radius hanya di bottom-left & bottom-right
- [x] Animasi Top Bar: transparan di posisi awal, muncul background saat halaman di-scroll
- [x] Routing/state management untuk menandai menu mana yang sedang aktif
- [x] Pastikan konten halaman punya padding cukup agar tidak tertutup Bottom Nav/Top Bar (fixed/sticky)

## 📚 Subjects (Mata Kuliah)
- [x] Migration tabel `subjects` (nama mata kuliah)
- [x] Model `Subject`
- [x] Livewire component: form tambah mata kuliah
- [x] Tampilan daftar mata kuliah
- [x] Fitur edit mata kuliah
- [x] Fitur hapus mata kuliah (cek dulu relasi ke Task/Schedule sebelum hapus, hindari data yatim)
- [x] Dropdown pilih mata kuliah dipakai ulang di form Task & form Schedule

## 🗓️ Schedule
- [x] Migration tabel `schedules` (subject_id, hari, jam_mulai, jam_selesai, ruangan, dosen, accent_color)
- [x] Model `Schedule` + relasi ke `Subject`
- [x] Livewire component: form tambah jadwal (pilih mata kuliah dari dropdown Subjects)
- [x] Tampilan daftar/tabel jadwal
- [x] Fitur edit jadwal
- [x] Fitur hapus jadwal
- [x] Tampilan jadwal per hari/minggu
- [x] Random/pilih accent color per schedule card
- [x] Styling Schedule Card: border 2px + border-radius 14px + box-shadow offset 5px 5px 0px (neo-brutalist, sesuai PRD.md 8.8)
- [x] Badge Hari — posisi kiri atas, nempel di garis border (background = Surface), bentuk pill outline
- [x] Layout body: blok waktu "boarding pass" (jam mulai besar, menit kecil, garis pemisah, jam selesai) di kiri; nama matkul (font besar) + ruangan + dosen (2 baris terpisah) di kanan

## ⏰ Deadline Tracking
- [x] Logika kalkulasi sisa hari menuju deadline
- [x] Logika penentuan level urgensi (`safe` ≥25 hari, `approaching` 17-24 hari, `urgent` 10-16 hari, `critical` 0-9 hari, `overdue` sudah lewat, `done` progress 100%)
- [x] Mapping warna per level: safe=Success, approaching=Caution, urgent=Warning, critical/overdue=Danger, done=Neutral
- [x] Tampilkan level urgensi sebagai label "Priority" di Task Card
- [x] Update otomatis level & warna secara real-time (Livewire), dihitung ulang tiap load halaman

## 💰 Finance Tracker
- [x] Migration tabel `finance_records` (tipe: income/expense, kategori, nominal, tanggal, catatan)
- [x] Migration tabel `budgets` (bulan, tahun, nominal_budget)
- [x] Model `FinanceRecord` & `Budget`
- [x] Livewire component: form tambah pemasukan
- [x] Livewire component: form tambah pengeluaran (dengan kategori)
- [x] Fitur edit & hapus catatan pemasukan/pengeluaran
- [x] Fitur set budget bulanan
- [x] Kalkulasi otomatis: total income, total expense, sisa saldo (real-time via Livewire)
- [x] Indikator visual/progress bar saat pengeluaran mendekati/melebihi budget
- [x] Tampilan riwayat transaksi (list income & expense)

## 🔍 Search, Filter & Sorting
- [x] Fitur search tugas berdasarkan judul (Livewire real-time search)
- [x] Filter Status: Semua Status / Not Started / In Progress / Completed
- [x] Filter Mata Kuliah: Semua Mata Kuliah / daftar dinamis dari Subjects
- [x] Sorting: Deadline Terdekat
- [x] Sorting: Deadline Terjauh
- [x] Sorting: Progress Tertinggi
- [x] Sorting: Progress Terendah
- [x] Sorting: Terbaru Dibuat
- [x] Pastikan search + filter + sort bisa dikombinasikan sekaligus

## 🎨 Theme (Tema Pink — default)
- [x] Implementasi Light mode tema Pink (Tailwind color tokens sesuai PROJECT.md)
- [x] Implementasi Dark mode tema Pink (Tailwind `dark:` variant)
- [x] Konfigurasi token warna tambahan `Caution` (kuning) dan `Neutral` (abu-abu) di Tailwind config, sesuai Color System PROJECT.md
- [x] Toggle switch Light/Dark mode
- [x] Simpan preferensi Light/Dark mode (localStorage)
- [x] Terapkan preferensi otomatis saat aplikasi dibuka kembali

## 🎴 Task Card Styling (Neo-brutalist)
- [x] Card: border 2px + border-radius 14px + box-shadow offset (6px 6px 0px, warna sesuai aksen)
- [x] Badge Mata Kuliah — posisi absolute, tengah atas, "nempel" di garis border (background = Surface)
- [x] Badge Status — posisi absolute, kanan atas, warna sesuai token status
- [x] Layout body 2 kolom: kiri (judul + deskripsi), kanan (Progress + Deadline)
- [x] Baris tombol +/- lebarnya mengikuti lebar progress bar saja (bukan lebar penuh termasuk kolom persentase)
- [x] Todo List full-width di bawah 2 kolom, khusus Mode Checklist (checkbox kotak + strikethrough saat selesai)
- [x] Sembunyikan section Todo List jika Mode Progress = Manual
- [x] Pastikan komponen ini reusable/konsisten dipakai di semua card Task List

## 💾 Data Persistence
- [x] Pastikan seluruh migration sudah mencerminkan relasi antar tabel dengan benar
- [ ] Jalankan `php artisan migrate` & verifikasi struktur tabel di HeidiSQL
- [ ] Seeder untuk data dummy/testing (opsional, mempermudah development)
- [ ] Handling error/edge case (validasi gagal, data tidak ditemukan, dll)
- [ ] Testing CRUD memastikan data konsisten setelah refresh/reload halaman

## 📱 Responsive & UX Polish
- [ ] Uji tampilan di Mobile
- [ ] Uji tampilan di Tablet
- [ ] Uji tampilan di Desktop
- [ ] Review aksesibilitas kontras warna (light/dark & indikator urgensi/budget)
- [ ] Loading/empty state untuk setiap halaman (Task List kosong, Schedule kosong, Finance kosong, dll)

## 🧪 Testing & QA
- [ ] Manual testing seluruh fitur CRUD (Task, Schedule, Finance)
- [ ] Testing relasi antar tabel (Task ↔ Subject, Schedule ↔ Subject, Finance ↔ Budget)
- [ ] Testing across browser (Chrome, Firefox, Edge)
- [ ] Bug fixing round sebelum dianggap Phase 1 selesai

## 🚀 Wrap-up Phase 1
- [ ] Finalisasi README.md, PROJECT.md, PRD.md (pastikan semua konsisten dengan implementasi akhir)
- [ ] Dokumentasikan cara setup project (clone → composer install → migrate → npm run dev/build)
- [ ] Tag/commit milestone `v1.0 — Core Full Stack Build`

---

## 🔮 Future (Phase 2 & 3 — Reference Only)
> Tidak dikerjakan di Phase 1, dicatat untuk konteks roadmap ke depan.

**Phase 2 — Refinement & UX Polish**
- [ ] Penyempurnaan UX search/filter/sorting
- [ ] Penyempurnaan indikator urgensi & aksesibilitas warna
- [ ] Optimasi query & performa Livewire component
- [ ] Buat tema warna **Blue** (Light Mode + Dark Mode) mengikuti struktur 12-token yang sama
- [ ] Buat tema warna **Monochrome** (Light Mode + Dark Mode) mengikuti struktur 12-token yang sama
- [ ] Buat mekanisme pilih & simpan preferensi tema warna (terpisah dari preferensi Light/Dark mode)
- [ ] Livewire component: Calendar View — section ke-7 di bagian paling bawah Dashboard (terpisah dari grid 6x4)
- [ ] Tampilan kalender bulanan (month view)
- [ ] Logika penandaan tanggal yang punya deadline tugas dengan indikator titik
- [ ] Fitur klik tanggal → expand inline di bawah kalender (bukan modal/popup)
- [ ] Query jadwal kuliah (berdasarkan hari yang sesuai) dan tugas dengan deadline pada tanggal yang diklik

**Phase 3 — Enhancement**
- [ ] Implementasi User Authentication (Laravel Breeze/Fortify)
- [ ] Role management
- [ ] Migrasi struktur data agar mendukung multi-user (`user_id` di setiap tabel relevan)
- [ ] Cross-device synchronization (otomatis, karena sudah berbasis database & auth)
- [ ] Notifications system
- [ ] Advanced analytics (laporan akademik & keuangan)
- [ ] Deployment ke hosting/cloud (jika ingin diakses publik)