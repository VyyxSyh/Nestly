# PROGRESS.md — Nestly Development Log

## 🕒 Sesi Terakhir
**Tanggal:** 16 September 2026
**Akun Claude yang dipakai:** Akun Utama (sesi ini mendekati limit, lanjut sesi/akun baru berikutnya)

---

## ✅ Sudah Selesai (Checklist yang sudah dicentang di TODO.md)

### 🏗️ Setup & Foundation (9/9 — SELESAI)
Laravel v13.29, Livewire v4.4, Tailwind via Vite, MySQL `nestly`, Git repo di `github.com/VyyxSyh/Nestly` (private, dokumentasi PRD/PROJECT/TODO belum di-push versi terbaru).

### ✅ Task Management (SELESAI PENUH)
- Migration `tasks` — title, description, subject_id (nullable), deadline, progress_mode, progress. Kolom `status`/`priority` TIDAK ada di database — dihitung live via accessor
- Model `Task`: `getStatusAttribute()`, `getPriorityAttribute()` (5 level: done/overdue/critical/urgent/approaching/safe), `getUrgencyColorAttribute()`
- CRUD lengkap via modal (`resources/views/components/task/task-list.blade.php`): tambah, edit, hapus (dengan konfirmasi)
- Progress manual (+/- 5% per klik, progress bar 10-segmen dengan efek diagonal) DAN progress checklist (sub-tugas, dikelola di dalam modal Edit, staged changes — batal beneran membatalkan, baru permanen saat Simpan)
- Search real-time (`wire:model.live`), filter by Status & Mata Kuliah, sorting (deadline/progress/terbaru) — BARU DITAMBAHKAN, perlu re-verifikasi (lihat Kendala)
- Mata kuliah di form Task pakai INPUT TEKS bebas (bukan dropdown), auto `firstOrCreate` ke tabel `subjects`

### ✅ Subjects/Mata Kuliah (SELESAI)
- Migration, Model `Subject` (relasi `hasMany` ke Task & Schedule)
- CRUD via `/subjects` — tambah, hapus
- Delete SEKARANG mengecek relasi dulu (tasks_count + schedules_count) — kalau masih dipakai, hapus ditolak dengan pesan error, BARU DITAMBAHKAN

### ✅ Schedule (SELESAI)
- Migration, Model `Schedule` (belongsTo Subject)
- CRUD via `/schedules` — tambah, edit, hapus
- Accent color: 8 warna dari PROJECT.md, bisa klik pilih manual atau tombol "Acak" (`array_rand()`, PHP murni, tanpa JS)
- Mata kuliah juga pakai input teks + `firstOrCreate`, sama seperti Task

### ✅ Finance Tracker (SELESAI)
- Migration `finance_records` & `budgets` (TIDAK ada foreign key, dihubungkan via query `date`/`month`/`year`)
- CRUD transaksi (income/expense) via `/finance`
- Budget bulanan dengan "warisan otomatis" — kalau bulan berjalan belum diisi budget, otomatis pakai nominal dari budget terakhir yang pernah di-set (label "(dari bulan sebelumnya)")
- Summary card: Pemasukan, Pengeluaran, Sisa Saldo (= Pemasukan − Pengeluaran, INDEPENDEN dari budget)
- Progress bar Budget terpisah (cuma bandingin Pengeluaran vs Budget), warna berubah saat mendekati/melebihi
- Riwayat transaksi DIKELOMPOKKAN PER BULAN (bukan cuma bulan berjalan) — semua transaksi historis tetap terlihat, di-scroll ke bawah, dengan header pemisah per bulan

### ✅ Dashboard (versi awal — AKAN DIROMBAK, lihat catatan besar di bawah)
- Route `/` diarahkan ke Dashboard (bukan welcome Laravel lagi)
- Widget: total tugas, breakdown status, progress keseluruhan, deadline terdekat, jadwal hari ini, ringkasan keuangan bulan berjalan
- **PENTING: versi ini akan diganti total** karena ada revisi PRD besar (lihat bawah) — jangan dikembangkan lebih lanjut sampai keputusan final

## ~ Sedang Dikerjakan / Belum Selesai
- Verifikasi ulang fitur search/filter/sort Task — user melaporkan list task kosong ("Belum ada tugas") padahal toolbar filter sudah muncul benar; BELUM DIKONFIRMASI apakah ini bug beneran atau cuma filter nyangkut/data ke-reset. **CEK INI DULU DI SESI BERIKUTNYA.**
- Navigasi antar halaman: sempat mau ditambahkan nav bar sederhana (link Dashboard/Tugas/Jadwal/Mata Kuliah/Keuangan di tiap halaman), TAPI DIBATALKAN karena user mau revisi PRD dulu (akan diganti Bottom Nav, lihat bawah) — saat ini navigasi antar halaman TIDAK ADA sama sekali kecuali ketik URL manual

## 🧠 Keputusan Teknis Penting
- Livewire v4 — single-file component di `resources/views/components/**/*.blade.php`
- Semua nilai turunan (status, priority, urgency_color) dihitung via accessor, TIDAK disimpan sebagai kolom — prinsip: jangan simpan data yang bisa dihitung ulang
- Mata kuliah di form Task & Schedule: input teks bebas + `Subject::firstOrCreate()`, BUKAN dropdown — mengurangi friksi input
- `task_checklist_items` cascade delete; `schedules→subjects` cascade delete; `tasks→subjects` nullOnDelete
- Subject TIDAK BISA dihapus kalau masih dipakai task/schedule (relation guard)
- Icon pakai Font Awesome CDN (CSS-only via `<link>`, bukan kit.js) — tidak perlu daftar akun, tidak ada JS tambahan
- **Mode kerja "executor"** sejak pertengahan sesi sebelumnya: kode langsung lengkap, keputusan teknis diambil langsung, bug diperbaiki langsung tanpa penjelasan panjang kecuali diminta
- **Prioritas kerja:** functional end-to-end dulu di semua fitur, styling/aesthetic belakangan sebagai final pass

## 🔮 REVISI BESAR PRD/README — BELUM DIEKSEKUSI, PERLU DIBACA ULANG DI SESI BERIKUTNYA
User sudah merombak README.md & TODO.md dengan scope baru yang signifikan (PRD.md & PROJECT.md juga direvisi tapi isi lengkapnya belum sempat terbaca di chat — user perlu share ulang):
1. **Dashboard baru total**: CSS Grid 6 kolom × 4 baris (Desktop) berisi section Jadwal, Ringkasan Singkat, List Tugas Terdekat, List Transaksi, **Profil** (foto lingkaran, nama, kelas), dan **Greeting** ("[Kata Sapaan], [Nama Panggilan]!" — dropdown sapaan + nama panggilan bisa diedit, disimpan ke tabel BARU `user_settings`). Responsive: Tablet scale-down, Mobile jadi 1 kolom vertikal dengan urutan khusus.
2. **Bottom Navigation baru** menggantikan nav bar biasa — Desktop/Tablet: 3 section (logo | menu label | akun+toggle tema) dengan animasi icon slide-in/out saat menu aktif; Mobile: icon-only + Top Bar sticky terpisah. Badge notifikasi di icon Task.
3. **Sistem tema warna**: Pink sebagai default (12 token: Primary/Secondary/Tertiary/BG/Surface/Text/Text Muted/Border/Success/Danger/Warning/Info) × Light/Dark mode — beda total dari palet teal yang dipakai di semua form sekarang. Blue & Monochrome direncanakan di Phase 2 (belum sekarang).
4. **Subjects sudah dianggap selesai di TODO baru** (kita memang sudah bangun ini duluan) tapi ditambah requirement: cek relasi sebelum hapus — SUDAH DIKERJAKAN sesi ini ✅.
5. **Search/Filter/Sorting untuk Task** — SUDAH DIKERJAKAN sesi ini (perlu verifikasi ulang, lihat bagian Kendala).

**Keputusan user:** kerjakan dulu semua functional gap (search/filter/sort, subject delete-guard — SUDAH), BARU masuk ke Dashboard grid + Bottom Nav + Tema Pink setelahnya. Item Dashboard/Nav/Tema BELUM DIMULAI SAMA SEKALI dalam bentuk barunya.

## 🐛 Kendala / Belum Terselesaikan
- List Task tampil kosong setelah penambahan fitur search/filter/sort — perlu dicek apakah bug asli atau cuma state filter/data sementara

## ➡️ Next Step (harus dikerjakan di sesi berikutnya)
1. **Cek dulu bug "Belum ada tugas"** di halaman `/tasks` — apakah data beneran hilang atau cuma filter nyangkut
2. Minta user share ulang isi lengkap PRD.md & PROJECT.md (belum sempat terbaca lengkap sesi ini) untuk detail spesifikasi Dashboard grid & Bottom Nav & tema Pink
3. Setelah dapat detail lengkap, mulai kerjakan: migration+model `user_settings` (Greeting/Profile), lalu Dashboard grid baru, lalu Bottom Nav, lalu sistem tema Pink Light/Dark
4. PRD.md/PROJECT.md/TODO.md perlu direvisi ulang untuk mencantumkan rencana Authentication (Fortify) — masih belum tercantum sejak beberapa sesi lalu
5. 5 file dokumentasi (README/PRD/PROJECT/TODO/PROGRESS) perlu di-push ulang ke GitHub setelah semua stabil

## 📁 Referensi Cepat
- Lokasi project: `D:\laragon\www\nestly`
- Repo GitHub: `github.com/VyyxSyh/Nestly` (private)
- Routes: `/` (Dashboard), `/tasks`, `/schedules`, `/subjects`, `/finance`
- Section TODO.md yang sedang dikerjakan: baru selesai "Search/Filter/Sorting" + "Subjects" delete-guard, berikutnya "🏠 Dashboard" (versi grid baru) dan "🧭 Navigation" (Bottom Nav)