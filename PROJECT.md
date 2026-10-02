# Nestly — Project System

## 1. Project Overview

**Nestly** adalah web application untuk membantu siswa SMA/SMK dan mahasiswa mengelola tugas, jadwal belajar, progress, deadline, serta keuangan pribadi dalam satu dashboard.

Project ini dirancang untuk membantu pengguna memantau tugas, deadline, progress, jadwal belajar, serta kondisi keuangan secara lebih terorganisir.

Nestly dibangun sebagai **full stack application** sejak awal pengembangan, menggunakan **Laravel** (Backend & Frontend melalui Blade, Tailwind CSS, dan Livewire) dengan **MySQL** sebagai database. Aplikasi dijalankan secara lokal menggunakan **Laragon** sebagai development server, dengan **HeidiSQL** untuk pengelolaan database.

---

## 2. Project Goals

Tujuan utama project:

* Membantu pelajar mengorganisir tugas sekolah/kuliah dan jadwal belajar.
* Membantu pengguna mengingat deadline, menentukan prioritas, dan memantau progress tugas.
* Membantu pengguna memantau keuangan pribadi.
* Menampilkan informasi akademik dan finansial dalam satu dashboard.
* Membuat pengalaman pengelolaan tugas yang sederhana dan mudah digunakan.
* Menjadi project pembelajaran full stack development menggunakan Laravel & Livewire.

---

## 3. Target Users

Target utama:

* Siswa SMA/SMK.
* Mahasiswa.
* Kelompok belajar pelajar atau mahasiswa.
* Kelas sebagai kemungkinan penggunaan bersama di fase mendatang.

Versi awal digunakan secara individual (single-user, data tersimpan di database lokal).

Pada versi berikutnya, sistem dapat dikembangkan agar mendukung multi-user dengan authentication, sehingga setiap pengguna memiliki akun dan datanya masing-masing.

---

## 4. Core Features

### 4.1 Task Management

Pengguna dapat mengelola tugas sekolah atau kuliah.

Fitur:

* Menambahkan tugas dengan informasi: judul, mata pelajaran/mata kuliah (relasi ke Subjects), deskripsi, deadline, mode progress.
* Melihat daftar tugas.
* Mengubah informasi tugas.
* Menghapus tugas.
* Menentukan status tugas.

Contoh status:

* Not Started
* In Progress
* Completed

Status `Completed` otomatis ter-set ketika progress tugas mencapai 100% — bukan dipilih manual terpisah dari progress.

Input deadline hanya meminta tanggal (jam bersifat opsional), namun ditampilkan lengkap dengan nama hari, contoh: "Rabu, 16 September 2026". Deskripsi tugas ditampilkan di halaman Task (tidak ikut ditampilkan di ringkasan Dashboard).

> Detail desain visual Task Card (neo-brutalist style, badge, layout 2 kolom, Todo List) didokumentasikan di `PRD.md` section 8.7.

---

### 4.2 Task Progress

Setiap tugas memiliki **Mode Progress**, dipilih salah satu:

* **Manual** — progress diatur sendiri oleh pengguna, bertambah/berkurang dalam kelipatan 5% menggunakan tombol `+`/`-`.
* **Checklist** — progress dihitung otomatis dari proporsi sub-tugas yang sudah dicentang selesai. Contoh: 1 dari 3 sub-tugas selesai = 33%.

Contoh:

```text
Project IoT
Progress: 70%

██████████████░░░░░░
```

Progress dapat diperbarui oleh pengguna secara real-time menggunakan Livewire, dan disimpan langsung ke database.

Halaman Task juga menampilkan **progress keseluruhan** (agregat dari seluruh tugas) sebagai indikator ringkasan di halaman tersebut.

---

### 4.3 Dashboard

Dashboard menjadi halaman utama yang menampilkan ringkasan aktivitas pengguna.

Informasi yang saat ini ditampilkan:

* Jumlah tugas per status (Total, Not Started, In Progress, Completed).
* Jadwal hari ini (maksimal 4 jadwal, sisanya diringkas).
* Jumlah tugas per status dan persentase budget terpakai.
* Maksimal 10 tugas belum selesai terdekat, beserta deadline, urgensi, dan progress.
* Ringkasan pemasukan/pengeluaran/saldo bersih bulan ini dan maksimal 5 transaksi.

Dashboard disusun menggunakan **card grid** dengan prioritas visual: Jadwal ditempatkan di posisi paling menonjol, diikuti ringkasan tugas, list tugas terdekat deadline, dan list transaksi finance bulan berjalan.

> Detail lengkap struktur grid, posisi tiap section, dan perilaku responsive (Desktop/Tablet/Mobile) didokumentasikan di `PRD.md` section 8.6.

**Profil dan Greeting (Phase 3):** foto profil, nama/kelas, dan sapaan yang dapat dikustomisasi.

**Calendar View (Phase 2):** kalender bulanan di bawah ringkasan Dashboard, dengan penanda deadline dan detail tanggal yang expand inline.

Contoh konsep (statistik tugas di card List Tugas):

```text
Total Task          8
Not Started         1
In Progress         4
Completed           3
```

---

### 4.4 Subjects (Mata Pelajaran/Mata Kuliah)

Pengguna dapat mengelola data mata pelajaran atau mata kuliah sebagai **data referensi**, terpisah dari halaman Schedule.

Fitur:

* Menambahkan Subject (nama mata pelajaran/mata kuliah).
* Melihat, mengedit, dan menghapus data Subject.

Data Subjects menjadi referensi (relasi) yang dipakai di **Task** dan **Schedule** — dipilih lewat dropdown, bukan diketik ulang manual tiap kali. Subjects terpisah dari Schedule karena merupakan data referensi, sedangkan Schedule rutin diperiksa.

---

### 4.5 Schedule

Pengguna dapat mengelola jadwal pelajaran atau perkuliahan.

Informasi dapat mencakup:

* Mata pelajaran/mata kuliah (relasi ke Subjects).
* Hari.
* Waktu.
* Ruangan.
* Guru/dosen.

---

### 4.6 Deadline Tracking

Sistem menghitung urgensi tugas secara otomatis, ditampilkan sebagai label "Priority" di Task Card. Urutan pengecekan: progress ≥100% → `done` (prioritas tertinggi, override semua), lalu cek apakah sudah lewat deadline → `overdue`, baru dihitung sisa hari untuk level lainnya:

| Level | Rentang Sisa Hari | Warna |
|---|---|---|
| `critical` | 0–5 hari | Danger (merah) |
| `urgent` | 6–12 hari | Warning (oranye) |
| `approaching` | 13–20 hari | Caution (kuning) |
| `safe` | > 20 hari (default) | Success (hijau) |
| `overdue` | Tanggal sekarang sudah melewati deadline | Danger (merah) |
| `done` | Progress ≥ 100% (dicek pertama, override semua level lain) | Neutral (abu-abu) |

Level `done` menggantikan level urgensi lain begitu progress tugas mencapai 100%, terlepas dari sisa hari atau status deadline-nya. Level dihitung ulang otomatis berdasarkan tanggal sistem setiap kali halaman dibuka/direfresh.

Tujuannya agar pengguna dapat mengetahui tugas mana yang perlu diprioritaskan hanya dengan melihat indikator visual, tanpa perlu field prioritas manual terpisah.

---

### 4.7 Finance Tracker

Fitur Finance membantu pengguna mencatat keuangan pribadi dan mengontrol pengeluaran bulanan.

Fitur:

* Mencatat pemasukan (contoh: uang saku, kiriman orang tua, penghasilan sampingan).
* Mencatat pengeluaran dengan kategori (contoh: makan, transport, jajan, kebutuhan sekolah/kuliah, lainnya).
* Menentukan budget bulanan.
* Melihat sisa saldo bersih secara real-time, termasuk saldo bersih yang terbawa dari bulan sebelumnya; budget tetap bulanan.
* Indikator visual ketika pengeluaran mendekati atau melebihi budget yang ditentukan.

Contoh konsep:

```text
Monthly Budget: Rp1.500.000
Spent: Rp1.100.000

██████████████░░░░░░ 73%

Remaining: Rp400.000
```

Update saldo dan indikator budget dilakukan secara real-time menggunakan Livewire, tanpa perlu reload halaman.

---

### 4.8 Search, Filter & Sorting

Pengguna dapat mencari dan mengatur daftar tugas.

Filter Status: `Semua Status`, `Not Started`, `In Progress`, `Completed`.

Filter Subjects: semua atau daftar dinamis mata pelajaran/mata kuliah dari data Subjects.

Sorting: `Deadline Terdekat`, `Deadline Terjauh`, `Progress Tertinggi`, `Progress Terendah`, `Terbaru Dibuat`, `Subjects (A-Z)`. Tugas `Completed` selalu berada di urutan bawah setelah sorting utama.

Search, filter, dan sorting dapat dikombinasikan sekaligus.

---

### 4.9 Theme

Website mendukung:

* Light mode.
* Dark mode.

Pada single-user saat ini, preferensi tema disimpan di `localStorage` browser dan diterapkan saat website dibuka kembali. Sinkronisasi melalui akun lintas perangkat direncanakan pada Phase 3.

---

### 4.10 Navigation

Nestly menggunakan pendekatan navigasi yang tidak konvensional untuk web — menempatkan menu navigasi utama di **bagian bawah layar (Bottom Nav)**, alih-alih navbar konvensional di atas. Pendekatan ini dipilih sebagai eksplorasi desain personal, terinspirasi dari pola navigasi aplikasi mobile native.

Garis besar konsepnya:

* Menu navigasi: `Home`, `Task`, `Schedule`, `Subjects`, `Finance`.
* Floating liquid-glass Bottom Nav fixed di halaman Dashboard, Task, Schedule, Subjects, dan Finance; menu aktif memakai sliding indicator.
* Pada **Mobile**, Top Bar fixed berisi logo dan toggle Light/Dark. Bottom Nav menampilkan icon semua menu dan label menu aktif.
* Badge Task menunjukkan jumlah tugas belum selesai; badge Schedule menunjukkan jumlah jadwal hari ini.

> Detail lengkap struktur, posisi tiap elemen, dan perilaku animasi didokumentasikan di `PRD.md` section 8.4 dan 8.5.

---

## 5. Data Storage

Nestly menggunakan arsitektur full stack sejak awal pengembangan:

```text
Frontend (Blade + Tailwind CSS + Livewire)
   ↓
Backend (Laravel)
   ↓
Database (MySQL, dikelola via Laragon & HeidiSQL)
```

Data yang disimpan ke database meliputi:

* Task.
* Progress.
* Deadline.
* Status.
* Schedule.
* Subjects.
* Data keuangan (income, expense, budget).

Data inti tersimpan di database dan dapat direlasikan antar tabel (contoh: Task terhubung ke Subject). Preferensi tema browser disimpan terpisah di `localStorage`. Backup dan pemulihan data belum termasuk scope saat ini.

---

## 6. User Account

### Versi Saat Ini

Aplikasi berjalan sebagai single-user, tanpa sistem authentication penuh. Seluruh data disimpan di database lokal.

### Future Version

Sistem dapat dikembangkan menjadi multi-user dengan authentication (login/register).

Contoh:

```text
User
├── Profile
├── Tasks
├── Schedule
├── Finance
└── Progress
```

Setiap user memiliki data masing-masing.

Contoh:

```text
Vyy
├── Project IoT → 80%
└── Mobile Development → 60%

User B
├── Project IoT → 40%
└── Mobile Development → 90%
```

---

## 7. Project Development Strategy

Project dikembangkan secara bertahap, dengan fondasi full stack yang sudah dibangun sejak Phase 1.

### Phase 1 — Core Full Stack Build (Current)

Fokus:

* Desain struktur database (migration & relasi antar tabel).
* Task management (CRUD).
* Schedule management (CRUD).
* Finance Tracker (CRUD + kalkulasi budget).
* Dashboard ringkasan.
* Integrasi Livewire untuk interaktivitas real-time.
* UI/UX dengan Tailwind CSS, responsive design.
* Search/filter/sorting, urgency indicators, theme toggle, responsive navigation, dan UX polish.

### Phase 2 — Calendar View

Fokus:

* Calendar View Dashboard: kalender bulanan, penanda deadline, dan detail tanggal expand inline.

### Phase 3 — Enhancement (Future)

Fokus:

* User authentication & multi-user support.
* Role management.
* Notifications.
* Advanced analytics/laporan keuangan & akademik.
* Kemungkinan deployment ke hosting/cloud.

---

## 8. Technology Stack

| Layer | Technology |
|---|---|
| Frontend & Backend | Laravel (Blade + Livewire) |
| Styling | Tailwind CSS (dipakai di dalam file Blade) |
| Database | MySQL |
| Local Development Server | Laragon / PHP development server |
| Database Management Tool | HeidiSQL |

Tidak menggunakan framework/library JavaScript terpisah — seluruh interaktivitas (real-time update, filter, progress bar dinamis, indikator budget) ditangani oleh **Livewire**.

---

## 9. Deployment

Untuk saat ini, aplikasi dijalankan secara lokal melalui **Laragon** sebagai bagian dari project pembelajaran/portofolio pribadi.

Rencana deployment ke hosting/cloud dapat dipertimbangkan pada tahap pengembangan berikutnya (Phase 3), menyesuaikan kebutuhan (contoh: shared hosting PHP atau cloud VPS) apabila project ingin diakses publik atau multi-user.

---

## 10. Project Scope

### Included

* Dashboard untuk siswa SMA/SMK dan mahasiswa.
* Task management.
* Progress tracking.
* Deadline management.
* Subjects (data referensi mata pelajaran/mata kuliah).
* Schedule pelajaran/kuliah.
* Finance tracker.
* Search, filter & sorting.
* Theme preference (`localStorage` pada versi single-user).
* Responsive interface.

### Future Scope

* User authentication.
* Multi-user support.
* Sinkronisasi preferensi melalui akun lintas perangkat.
* Profil dan Greeting pengguna.
* Role management.
* Notifications.
* Advanced analytics (akademik & keuangan).

---

## 11. Expected Result

Project diharapkan menjadi web application full stack yang membantu siswa SMA/SMK dan mahasiswa mengelola tugas, jadwal belajar, serta keuangan pribadi dengan lebih terorganisir.

Aplikasi dibangun di atas fondasi Laravel + Livewire yang solid sejak awal, sehingga pengembangan fitur lanjutan (authentication, multi-user, notifikasi, dsb.) dapat dilakukan tanpa perlu membangun ulang arsitektur dari awal.

---

## 12. Color System

> Tema warna utama Nestly adalah **Pink** (lihat token di bawah). Token warna mengikuti struktur 12-token (termasuk semantic colors untuk status task, deadline urgency, dan finance tracker).

### Light Mode

| Token | Hex | Usage |
|---|---|---|
| Primary | `#FF4D8D` | Primary buttons, active nav, links, focus rings |
| Secondary | `#FF9DBB` | Secondary buttons, less prominent actions |
| Tertiary | `#FFD6E5` | Subtle backgrounds, badges, chip highlights, hover fills |
| Background | `#FFF7FA` | Main page background |
| Surface | `#FFFFFF` | Cards, navbar, panels, modals |
| Text | `#16141A` | Headings, primary content |
| Text Muted | `#6B6470` | Descriptions, metadata, timestamps |
| Border | `#FFE1EA` | Dividers, input outlines, card borders |
| Success | `#22C55E` | Task status "safe" (urgency terjauh), income entries, "on track" budget |
| Danger | `#EF4444` | Task urgency "critical" & "overdue", expense entries, over-budget alerts |
| Warning | `#F59E0B` | Task urgency "urgent" (oranye), nearing budget limit |
| Caution | `#EAB308` | Task urgency "approaching" (kuning, beda dari Warning yang oranye) |
| Neutral | `#9CA3AF` | Task urgency "done" (abu-abu, progress 100%) |
| Info | `#3B82F6` | In-progress states, neutral notifications |

### Dark Mode

| Token | Hex | Usage |
|---|---|---|
| Primary | `#E879F9` | Primary buttons, active nav, links, focus rings |
| Secondary | `#F0ABFC` | Secondary buttons, less prominent actions |
| Tertiary | `#A855F7` | Subtle backgrounds, badges, chip highlights, hover fills |
| Background | `#1B0F1F` | Main page background |
| Surface | `#2B1830` | Cards, navbar, panels, modals |
| Text | `#F8F0FA` | Headings, primary content |
| Text Muted | `#BFA6C7` | Descriptions, metadata, timestamps |
| Border | `#4A2F52` | Dividers, input outlines, card borders |
| Success | `#4ADE80` | Task status "safe" (urgency terjauh), income entries, "on track" budget |
| Danger | `#F87171` | Task urgency "critical" & "overdue", expense entries, over-budget alerts |
| Warning | `#FBBF24` | Task urgency "urgent" (oranye), nearing budget limit |
| Caution | `#FDE047` | Task urgency "approaching" (kuning, beda dari Warning yang oranye) |
| Neutral | `#A1A1AA` | Task urgency "done" (abu-abu, progress 100%) |
| Info | `#60A5FA` | In-progress states, neutral notifications |

### Color Usage Guidelines

- **Primary** should be used for the main brand identity and important interactive elements.
- **Secondary** should support primary actions without competing with them.
- **Tertiary** should be used for subtle visual emphasis rather than large areas.
- **Background** is reserved for the main application canvas.
- **Surface** is used for elevated UI elements such as cards, panels, and navigation.
- **Text** is used for primary content and headings.
- **Text Muted** is used for secondary information and supporting content.
- **Border** should remain subtle and should not overpower the content.
- **Success / Danger / Warning / Info** are reserved for semantic states (task status, deadline urgency, finance in/out, budget alerts) — should not be reused for generic brand/decorative purposes so their meaning stays consistent across the app.

### Schedule Card Accent Colors

| Token | Color | Hex | Usage |
|---|---|---|---|
| Accent 1 | Coral Red | `#E85D68` | Schedule card left border (random/user-selected) |
| Accent 2 | Vibrant Orange | `#F07845` | Schedule card left border (random/user-selected) |
| Accent 3 | Mustard Yellow | `#E7C23B` | Schedule card left border (random/user-selected) |
| Accent 4 | Emerald Green | `#4CAF72` | Schedule card left border (random/user-selected) |
| Accent 5 | Cyan Blue | `#35B9C4` | Schedule card left border (random/user-selected) |
| Accent 6 | Royal Blue | `#4D83D1` | Schedule card left border (random/user-selected) |
| Accent 7 | Deep Purple | `#8666D5` | Schedule card left border (random/user-selected) |
| Accent 8 | Hot Pink | `#E7659A` | Schedule card left border (random/user-selected) |

### Color Usage Guidelines

- **Schedule Accents** are applied dynamically (randomly or user-selected) to card borders to provide visual variety without relying on hardcoded category mappings.
