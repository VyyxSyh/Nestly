# Nestly - Project System

## 1. Project Overview

**Nestly** adalah web application untuk membantu mahasiswa mengelola aktivitas perkuliahan, tugas, dan keuangan pribadi dalam satu dashboard.

Project ini dirancang untuk membantu pengguna memantau tugas, deadline, progress, jadwal kuliah, serta kondisi keuangan bulanan secara lebih terorganisir.

Nestly dibangun sebagai **full stack application** sejak awal pengembangan, menggunakan **Laravel** (Backend & Frontend melalui Blade, Tailwind CSS, dan Livewire) dengan **MySQL** sebagai database. Aplikasi dijalankan secara lokal menggunakan **Laragon** sebagai development server, dengan **HeidiSQL** untuk pengelolaan database.

---

## 2. Project Goals

Tujuan utama project:

* Membantu mahasiswa mengorganisir tugas dan aktivitas kuliah.
* Memudahkan pengguna memantau progress setiap tugas.
* Membantu mahasiswa mengontrol keuangan pribadi agar tidak boros.
* Menampilkan informasi akademik dan finansial dalam satu dashboard.
* Membuat pengalaman pengelolaan tugas yang sederhana dan mudah digunakan.
* Menjadi project pembelajaran full stack development menggunakan Laravel & Livewire.

---

## 3. Target Users

Target utama:

* Mahasiswa.
* Kelompok belajar.
* Circle atau kelompok kecil mahasiswa.
* Kelas yang ingin menggunakan dashboard bersama.

Versi awal digunakan secara individual (single-user, data tersimpan di database lokal).

Pada versi berikutnya, sistem dapat dikembangkan agar mendukung multi-user dengan authentication, sehingga setiap pengguna memiliki akun dan datanya masing-masing.

---

## 4. Core Features

### 4.1 Task Management

Pengguna dapat mengelola tugas kuliah.

Fitur:

* Menambahkan tugas.
* Melihat daftar tugas.
* Mengubah informasi tugas.
* Menghapus tugas.
* Mengubah progress tugas.
* Menentukan deadline.
* Menentukan status tugas.

Contoh status:

* Not Started
* In Progress
* Completed

---

### 4.2 Task Progress

Setiap tugas memiliki progress yang dapat diperbarui.

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

Informasi yang dapat ditampilkan:

* Jumlah tugas per status (Total, Not Started, In Progress, Completed).
* Deadline terdekat.
* Ringkasan keuangan bulan berjalan.
* Greeting personal (sapaan + nama panggilan yang bisa dikustomisasi).
* Profil singkat (foto profil, nama, kelas).

Dashboard disusun menggunakan **card grid** dengan prioritas visual: Jadwal ditempatkan di posisi paling menonjol, diikuti ringkasan tugas, list tugas terdekat deadline, dan list transaksi finance bulan berjalan.

> Detail lengkap struktur grid, posisi tiap section, dan perilaku responsive (Desktop/Tablet/Mobile) didokumentasikan di `PRD.md` section 8.6.

Contoh konsep (statistik tugas di card List Tugas):

```text
Total Task          8
Not Started         1
In Progress         4
Completed           3
```

---

### 4.4 Subjects (Mata Kuliah)

Pengguna dapat mengelola data mata kuliah sebagai **data referensi**, terpisah dari halaman Schedule.

Fitur:

* Menambahkan mata kuliah baru (nama mata kuliah).
* Melihat, mengedit, dan menghapus data mata kuliah.

Data mata kuliah menjadi referensi (relasi) yang dipakai di **Task** dan **Schedule** — dipilih lewat dropdown, bukan diketik ulang manual tiap kali. Halaman ini sengaja dipisah dari Schedule karena sifatnya beda: Subjects adalah data yang jarang diubah (diisi sekali di awal semester), sementara Schedule adalah halaman operasional yang sering dicek sehari-hari.

---

### 4.5 Schedule

Pengguna dapat melihat jadwal perkuliahan atau aktivitas belajar.

Informasi dapat mencakup:

* Mata kuliah (relasi ke Subjects).
* Hari.
* Waktu.
* Ruangan.
* Dosen.

---

### 4.6 Deadline Tracking

Dashboard dapat menampilkan tugas berdasarkan deadline dan tingkat urgensinya.

Contoh kategori:

- Due Today
- Due Tomorrow
- Upcoming
- Overdue

Setiap tugas dapat memiliki indikator warna berdasarkan tingkat urgensi deadline:

- 🟢 **Green** — Deadline masih cukup jauh dan tugas dalam kondisi aman.
- 🟡 **Yellow** — Deadline mulai mendekat dan perlu diperhatikan.
- 🟠 **Orange** — Deadline sudah dekat dan tugas perlu segera diprioritaskan.
- 🔴 **Red** — Deadline sangat mendesak, sudah jatuh tempo, atau telah melewati deadline.

Tujuannya agar pengguna dapat mengetahui tugas mana yang perlu diprioritaskan hanya dengan melihat indikator visual.

---

### 4.7 Finance Tracker

Dashboard menyediakan fitur pencatatan keuangan pribadi mahasiswa untuk membantu mengontrol pengeluaran bulanan.

Fitur:

* Mencatat pemasukan (contoh: uang saku, kiriman orang tua, penghasilan sampingan).
* Mencatat pengeluaran dengan kategori (contoh: makan, transport, jajan, kebutuhan kuliah, lainnya).
* Menentukan budget bulanan.
* Melihat ringkasan sisa saldo secara real-time.
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

Contoh filter:

* Status.
* Deadline.
* Mata kuliah.
* Progress.

Contoh sorting:

* Deadline terdekat.
* Progress tertinggi.
* Progress terendah.
* Tugas terbaru.

---

### 4.9 Theme

Website mendukung:

* Light mode.
* Dark mode.

Preferensi theme dapat disimpan sehingga pilihan pengguna tetap digunakan ketika website dibuka kembali.

---

### 4.10 Navigation

Nestly menggunakan pendekatan navigasi yang tidak konvensional untuk web — menempatkan menu navigasi utama di **bagian bawah layar (Bottom Nav)**, alih-alih navbar konvensional di atas. Pendekatan ini dipilih sebagai eksplorasi desain personal, terinspirasi dari pola navigasi aplikasi mobile native.

Garis besar konsepnya:

* Menu navigasi: `Home`, `Task`, `Schedule`, `Subjects`, `Finance`.
* **Bottom Nav** tampil konsisten di seluruh halaman, dengan isi dan perilaku yang berbeda antar breakpoint (Desktop, Tablet, Mobile) — termasuk animasi transisi icon saat berpindah menu.
* Pada **Mobile**, terdapat tambahan **Top Bar** (sticky, transparan-lalu-muncul-background saat di-scroll) sebagai tempat logo dan toggle Light/Dark, karena Bottom Nav di Mobile berbentuk icon-only tanpa ruang untuk elemen tersebut.

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
* Data keuangan (income, expense, budget).
* Theme preference.
* Pengaturan pengguna.

Karena data tersimpan di database (bukan `localStorage`), data lebih terstruktur, dapat direlasikan antar tabel (contoh: Task terhubung ke Mata Kuliah), dan siap dikembangkan menjadi multi-user di tahap berikutnya.

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

### Phase 1 — Core Full Stack Build

Fokus:

* Desain struktur database (migration & relasi antar tabel).
* Task management (CRUD).
* Schedule management (CRUD).
* Finance Tracker (CRUD + kalkulasi budget).
* Dashboard ringkasan.
* Integrasi Livewire untuk interaktivitas real-time.
* UI/UX dengan Tailwind CSS, responsive design.

### Phase 2 — Refinement & UX Polish

Fokus:

* Search, filter & sorting.
* Deadline urgency indicator.
* Light/Dark mode.
* Multi color theme — tambahan tema **Blue** dan **Monochrome** (masing-masing Light Mode + Dark Mode), selain tema Pink default dari Phase 1.
* Perbaikan validasi data & UX secara keseluruhan.

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
| Local Development Server | Laragon |
| Database Management Tool | HeidiSQL |

Tidak menggunakan framework/library JavaScript terpisah — seluruh interaktivitas (real-time update, filter, progress bar dinamis, indikator budget) ditangani oleh **Livewire**.

---

## 9. Deployment

Untuk saat ini, aplikasi dijalankan secara lokal melalui **Laragon** sebagai bagian dari project pembelajaran/portofolio pribadi.

Rencana deployment ke hosting/cloud dapat dipertimbangkan pada tahap pengembangan berikutnya (Phase 3), menyesuaikan kebutuhan (contoh: shared hosting PHP atau cloud VPS) apabila project ingin diakses publik atau multi-user.

---

## 10. Project Scope

### Included

* Student dashboard.
* Task management.
* Progress tracking.
* Deadline management.
* Subjects (data referensi mata kuliah).
* Schedule.
* Finance tracker.
* Search, filter & sorting.
* Theme preference.
* Responsive interface.

### Future Scope

* User authentication.
* Multi-user support.
* Cross-device synchronization.
* User profile.
* Role management.
* Notifications.
* Advanced analytics (akademik & keuangan).

---

## 11. Expected Result

Project diharapkan menjadi web application full stack yang dapat membantu mahasiswa mengelola tugas, jadwal kuliah, dan keuangan pribadi dengan lebih terorganisir.

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

### Future Scope — Multi Color Theme (Phase 2)

> Tidak dikerjakan di Phase 1. Direncanakan masuk **Phase 2 (Refinement & UX Polish)**.

Selain tema Pink (default, sudah termasuk Light Mode & Dark Mode di Phase 1), akan ditambahkan pilihan tema warna:
- **Blue Theme** (Light Mode + Dark Mode)
- **Monochrome Theme** — hitam, putih, abu-abu (Light Mode + Dark Mode)

Setiap tema tambahan mengikuti struktur 12-token yang sama seperti tema Pink. Implementasi ini membutuhkan mekanisme penyimpanan preferensi tema per pengguna (terpisah dari preferensi Light/Dark mode).

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