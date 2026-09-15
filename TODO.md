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
- [x] Migration tabel `tasks` (judul, deskripsi, mata_kuliah_id, deadline, status, progress, priority)
- [x] Model `Task` + relasi ke `Subject`/Mata Kuliah
- [ ] Livewire component: form tambah tugas
- [ ] Livewire component: daftar tugas (Task List)
- [ ] Fitur edit tugas
- [ ] Fitur hapus tugas (dengan konfirmasi)
- [ ] Fitur update status tugas (Not Started / In Progress / Completed)
- [ ] Fitur set & update deadline tugas
- [ ] Fitur set prioritas tugas
- [ ] Validasi input form (judul wajib, deadline valid, dll) menggunakan Laravel Validation

## 📊 Task Progress
- [ ] Komponen progress bar per tugas (Blade + Tailwind)
- [ ] Fitur update progress secara real-time via Livewire (slider/input persentase)
- [ ] Progress tersimpan otomatis ke database setiap perubahan
- [ ] Animasi/transisi progress bar (opsional, UX enhancement)

## 🏠 Dashboard
- [ ] Layout halaman utama (Dashboard) sebagai route utama
- [ ] Setup CSS Grid 6 kolom × 4 baris (gap 10px) untuk Desktop, sesuai spesifikasi PRD.md 8.6
- [ ] Livewire component: section Jadwal (kolom 1-5, baris 1) — 4 card jadwal sejajar
- [ ] Livewire component: section Ringkasan Singkat (kolom 6, baris 1) — jumlah tugas belum selesai + % budget terpakai
- [ ] Livewire component: section List Tugas Terdekat Deadline (kolom 1-2, baris 2-4) — nama, aksen warna mata kuliah, deadline (3-5 item)
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
- [ ] Livewire/Blade component: Bottom Nav (tampil di semua halaman)
- [ ] Desktop & Tablet — 3 section: logo+teks (kiri), menu label Home/Task/Schedule/Finance (tengah), icon akun + toggle Light/Dark (kanan)
- [ ] Animasi icon slide-in dari belakang label saat menu aktif (dan slide-out saat pindah halaman)
- [ ] Styling border & warna berbeda untuk menu yang sedang aktif (pakai token Primary)
- [ ] Tablet — scaled down version dari layout Desktop
- [ ] Mobile — Bottom Nav icon-only (Home, Task, Schedule, Finance, Account), label muncul saat menu aktif
- [ ] Badge notifikasi (angka) di icon Task — semua breakpoint
- [ ] Mobile — Top Bar terpisah: logo+teks (kiri), toggle Light/Dark (kanan)
- [ ] Top Bar `position: sticky`, border-radius hanya di bottom-left & bottom-right
- [ ] Animasi Top Bar: transparan di posisi awal, muncul background saat halaman di-scroll
- [ ] Routing/state management untuk menandai menu mana yang sedang aktif
- [ ] Pastikan konten halaman punya padding cukup agar tidak tertutup Bottom Nav/Top Bar (fixed/sticky)

## 🗓️ Schedule
- [ ] Migration tabel `schedules` (mata_kuliah_id, hari, jam_mulai, jam_selesai, ruangan, dosen, accent_color)
- [ ] Model `Schedule` + relasi ke `Subject`/Mata Kuliah
- [ ] Livewire component: form tambah jadwal
- [ ] Tampilan daftar/tabel jadwal
- [ ] Fitur edit jadwal
- [ ] Fitur hapus jadwal
- [ ] Tampilan jadwal per hari/minggu
- [ ] Random/pilih accent color per schedule card

## ⏰ Deadline Tracking
- [ ] Logika kategorisasi otomatis (Due Today / Due Tomorrow / Upcoming / Overdue) di Model/Livewire component
- [ ] Logika indikator warna urgensi (🟢🟡🟠🔴) berdasarkan tanggal sistem (pakai warna dari Color System di PROJECT.md)
- [ ] Update otomatis kategori & warna secara real-time (Livewire)
- [ ] Tampilan filter tugas berdasarkan kategori urgensi

## 💰 Finance Tracker
- [ ] Migration tabel `finance_records` (tipe: income/expense, kategori, nominal, tanggal, catatan)
- [ ] Migration tabel `budgets` (bulan, tahun, nominal_budget)
- [ ] Model `FinanceRecord` & `Budget`
- [ ] Livewire component: form tambah pemasukan
- [ ] Livewire component: form tambah pengeluaran (dengan kategori)
- [ ] Fitur edit & hapus catatan pemasukan/pengeluaran
- [ ] Fitur set budget bulanan
- [ ] Kalkulasi otomatis: total income, total expense, sisa saldo (real-time via Livewire)
- [ ] Indikator visual/progress bar saat pengeluaran mendekati/melebihi budget
- [ ] Tampilan riwayat transaksi (list income & expense)

## 🔍 Search, Filter & Sorting
- [ ] Fitur search tugas berdasarkan kata kunci (Livewire real-time search)
- [ ] Filter berdasarkan status
- [ ] Filter berdasarkan deadline
- [ ] Filter berdasarkan mata kuliah
- [ ] Filter berdasarkan progress
- [ ] Sorting: deadline terdekat
- [ ] Sorting: progress tertinggi / terendah
- [ ] Sorting: tugas terbaru

## 🎨 Theme (Tema Pink — default)
- [ ] Implementasi Light mode tema Pink (Tailwind color tokens sesuai PROJECT.md)
- [ ] Implementasi Dark mode tema Pink (Tailwind `dark:` variant)
- [ ] Toggle switch Light/Dark mode
- [ ] Simpan preferensi Light/Dark mode (cookie/session, atau tabel `user_settings` jika ingin persist ke database)
- [ ] Terapkan preferensi otomatis saat aplikasi dibuka kembali

## 💾 Data Persistence
- [ ] Pastikan seluruh migration sudah mencerminkan relasi antar tabel dengan benar
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

**Phase 3 — Enhancement**
- [ ] Implementasi User Authentication (Laravel Breeze/Fortify)
- [ ] Role management
- [ ] Migrasi struktur data agar mendukung multi-user (`user_id` di setiap tabel relevan)
- [ ] Cross-device synchronization (otomatis, karena sudah berbasis database & auth)
- [ ] Notifications system
- [ ] Advanced analytics (laporan akademik & keuangan)
- [ ] Deployment ke hosting/cloud (jika ingin diakses publik)