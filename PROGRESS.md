# PROGRESS.md — Nestly Development Log

## 🕒 Sesi Terakhir
**Tanggal:** (3/9/26)
**Akun Claude yang dipakai:** (Akun utama)

---

## ✅ Sudah Selesai (Checklist yang sudah dicentang di TODO.md)
### 🏗️ Setup & Foundation (9/9 — SELESAI)
- Install Laravel project baru (v13.29.0) di `D:\laragon\www\nestly`
- Install & konfigurasi Livewire (v4.4, single-file component)
- Install & konfigurasi Tailwind CSS (via Vite, terverifikasi jalan)
- Setup database MySQL `nestly` via Laragon + HeidiSQL
- Koneksi `.env` ke MySQL, tested via `php artisan migrate`
- Setup Git repository, `.gitignore` sudah benar (`.env`, `/vendor`, `/node_modules` ter-exclude), repo di-push ke `github.com/VyyxSyh/Nestly` (private)
- Skema database dirancang & di-migrate: 6 tabel — `subjects`, `tasks`, `task_checklist_items`, `schedules`, `finance_records`, `budgets` (lihat detail keputusan di bawah)
- Struktur folder disepakati: `app/Livewire/{Tasks,Schedules,Finance,Dashboard}`, `app/Models/`, mengikuti konvensi Laravel + Livewire v4

## ~ Sedang Dikerjakan (belum selesai total)
- (belum mulai section berikutnya)

## 🧠 Keputusan Teknis Penting
- Livewire v4 (bukan v3) — single-file component di `resources/views/components/*.blade.php`
- Skema `tasks`: `status` & `priority` string biasa (bukan enum), ada `progress_mode` (manual/checklist) + `progress` (0-100)
- `task_checklist_items` (sub-tugas per task) — cascade delete kalau task dihapus
- `schedules.accent_color` (hex code) dipilih via PHP/Livewire (random `array_rand()` atau user klik swatch Blade `wire:click`) — TIDAK pakai JS
- `finance_records` & `budgets` sengaja TIDAK ada foreign key — dihubungkan via query `date`/`month`/`year` yang cocok, bukan relasi database. Status "lebih dari budget" dihitung on-the-fly (`amount budget - total expense`), tidak disimpan sebagai kolom terpisah
- Dokumentasi (README/PRD/PROJECT/TODO/PROGRESS) di root `D:\laragon\www\nestly`; PRD.md/PROJECT.md/TODO.md 4 file (PRD, PROJECT, TODO — belum PROGRESS) masih revisi/**belum ikut di-push ke GitHub**
- **Rencana baru (belum masuk PRD/PROJECT/TODO):** authentication akan dimajukan dari Phase 3 — pakai **Laravel Fortify**, dikerjakan SETELAH fitur utama (Task/Schedule/Finance) jalan. `user_id` akan ditambah ke tabel-tabel terkait lewat migration baru terpisah nanti, bukan edit migration yang sudah ada

## 🐛 Kendala / Belum Terselesaikan
- (belum ada)

## ➡️ Next Step (harus dikerjakan di sesi berikutnya)
1. Mulai section "✅ Task Management" di TODO.md: bikin Model `Task`, `Subject`, `TaskChecklistItem` + relasinya
2. Bikin Livewire component form tambah tugas
3. Bikin Livewire component daftar tugas (Task List)
4. **Jangan lupa:** revisi PRD.md/PROJECT.md/TODO.md buat masukin rencana Authentication (Fortify) sebelum lupa detailnya

## 📁 Referensi Cepat
- Lokasi project: `D:\laragon\www\nestly`
- Repo GitHub: `github.com/VyyxSyh/Nestly` (private)
- Section TODO.md yang sedang dikerjakan: "✅ Task Management" (baru mulai)