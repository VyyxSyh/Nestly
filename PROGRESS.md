# PROGRESS.md — Nestly Development Log

## 🕒 Sesi Terakhir
**Tanggal:** 10/9/2026
**Akun Claude yang dipakai:** Akun Utama

---

## ✅ Sudah Selesai (Checklist yang sudah dicentang di TODO.md)
### 🏗️ Setup & Foundation (9/9 — SELESAI)
(sama seperti sebelumnya — Laravel, Livewire, Tailwind, MySQL, Git, skema 6 tabel, struktur folder)

### ✅ Task Management (hampir selesai)
- Migration `tasks` (title, description, subject_id, deadline, progress_mode, progress — kolom `status` & `priority` DIHAPUS dari migration, sekarang dihitung otomatis via accessor di Model, bukan disimpan)
- Model `Task` + relasi ke `Subject` (belongsTo) dan `TaskChecklistItem` (hasMany) — diverifikasi lewat Tinker
- Livewire component `task.task-list` (`resources/views/components/task/task-list.blade.php`) — form tambah tugas modal, daftar tugas, edit, hapus (dengan konfirmasi), semua real-time tanpa reload
- Validasi input pakai Laravel Validation (title required, deadline required date, dst)
- Status tugas (Not Started/In Progress/Completed) — OTOMATIS dari `progress` (0% = not started, 1-99% = in progress, 100% = completed), bukan pilihan manual
- Priority — OTOMATIS dari sisa hari ke deadline (5 level, lihat Keputusan Teknis)
- Progress mode "manual": tombol +/− (increment 5% per klik), progress bar segmented 10 kotak dengan efek diagonal untuk nilai setengah
- Progress mode "checklist": toggle checkbox per item sudah jalan (progress dihitung otomatis dari rasio item selesai, dibulatkan ke kelipatan 5) — **TAPI belum ada UI untuk menambah checklist item baru**, ini masih PR
- Icon Edit (pen) & Hapus (trash) pakai Font Awesome via CDN (`cdnjs.cloudflare.com/.../font-awesome/6.5.2/css/all.min.css`), bukan kit.js — supaya tidak perlu daftar akun & tidak ada JS tambahan yang jalan
- Route `/tasks` → `resources/views/tasks-page.blade.php` (layout dasar yang manggil komponen)

## ~ Sedang Dikerjakan (belum selesai total)
- Task Management: butuh UI untuk **menambah checklist item** ke sebuah task (form input + tombol tambah, di dalam modal edit atau terpisah) — belum dibangun sama sekali

## 🧠 Keputusan Teknis Penting
- Livewire v4 — single-file component di `resources/views/components/**/*.blade.php` (BUKAN `resources/views/livewire/`)
- **Status & Priority tidak lagi kolom database** — keduanya jadi Eloquent accessor (`getStatusAttribute()`, `getPriorityAttribute()`, `getUrgencyColorAttribute()`) di `app/Models/Task.php`, dihitung live setiap diakses, supaya tidak "basi" seiring waktu berjalan
- Priority — 5 level berdasarkan sisa hari ke deadline: `done` (progress 100%), `overdue` (lewat deadline), `critical` (0-5 hari), `urgent` (6-12 hari), `approaching` (13-20 hari), `safe` (≥21 hari). Warna: done=gray, overdue/critical=red, urgent=orange, approaching=yellow, safe=green (sesuai Color System PROJECT.md)
- Form tambah/edit tugas: modal popup di atas Task List (bukan halaman terpisah)
- Progress manual: kelipatan 5% per klik tombol +/−, bukan slider drag bebas
- `task_checklist_items` cascade delete kalau task dihapus
- `schedules.accent_color` dipilih via PHP/Livewire (random `array_rand()` atau swatch klik Blade) — TIDAK pakai JS
- `finance_records` & `budgets` TIDAK ada foreign key — dihubungkan via query `date`/`month`/`year`, status "lebih dari budget" dihitung on-the-fly
- **Rencana:** authentication pakai Laravel Fortify, dikerjakan SETELAH Task/Schedule/Finance selesai — `user_id` ditambah lewat migration baru terpisah nanti. PRD.md/PROJECT.md/TODO.md belum direvisi untuk mencantumkan ini
- Icon pakai Font Awesome CDN (CSS-only, bukan kit.js) — `<link>` di `<head>` tasks-page.blade.php
- **Mode kerja berubah sejak pertengahan sesi:** dari mentoring step-by-step jadi "executor" — kode langsung lengkap diberikan, keputusan teknis diambil langsung tanpa banyak opsi, kecuali dampaknya besar/butuh preferensi personal

## 🐛 Kendala / Belum Terselesaikan
- (tidak ada bug aktif — sempat ada error file ke-paste terpotong & accessor status belum ke-update, sudah diperbaiki)

## ➡️ Next Step (harus dikerjakan di sesi berikutnya)
1. Bangun UI tambah checklist item (untuk task dengan progress_mode = checklist)
2. Setelah itu, section "✅ Task Management" bisa dianggap selesai penuh — centang semua item terkait di TODO.md
3. Lanjut ke section "🏠 Dashboard" atau "🗓️ Schedule" (sesuai urutan TODO.md)
4. **Jangan lupa:** revisi PRD.md/PROJECT.md/TODO.md untuk mencantumkan rencana Authentication (Fortify) dan perubahan skema (status/priority jadi accessor, bukan kolom)
5. 4 file dokumentasi (PRD/PROJECT/TODO) masih perlu direvisi & di-push ulang ke GitHub

## 📁 Referensi Cepat
- Lokasi project: `D:\laragon\www\nestly`
- Repo GitHub: `github.com/VyyxSyh/Nestly` (private)
- Route Task List: `http://127.0.0.1:8000/tasks`
- Section TODO.md yang sedang dikerjakan: "✅ Task Management" (hampir selesai, tinggal checklist-add-item)