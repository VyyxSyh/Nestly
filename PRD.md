# Product Requirements Document (PRD)
## Nestly — Student Task & Study Dashboard

**Version:** 1.0 (Full Stack)
**Author:** Syukron Raffiansyah (Vyy)
**Status:** In Development
**Last Updated:** 2026

---

## 1. Problem Statement

Pelajar termasuk siswa SMA/SMK dan mahasiswa sering mengelola tugas, deadline, jadwal belajar, dan keuangan pribadi secara terpisah, atau hanya mengandalkan ingatan. Akibatnya:

- Tugas dan deadline mudah terlupakan karena tidak tercatat di satu tempat.
- Sulit menentukan tugas mana yang harus diprioritaskan lebih dulu.
- Progress pengerjaan tugas sulit dipantau.
- Jadwal belajar dan tugas tidak terorganisir dalam satu tampilan.
- Pengeluaran pribadi tidak terpantau.

Pelajar membutuhkan satu dashboard terpusat yang membantu mengatur tugas, memantau progress, menyusun prioritas berdasarkan deadline, melihat jadwal, dan mencatat keuangan dengan rapi.

---

## 2. Goals & Objectives

**Tujuan Utama:**
- Membantu siswa SMA/SMK dan mahasiswa mengorganisir tugas serta jadwal belajar dalam satu dashboard.
- Membantu User mengingat deadline dan menentukan tugas yang perlu diprioritaskan.
- Memudahkan User memantau progress setiap tugas secara visual.
- Membantu User mengontrol pengeluaran pribadi.
- Menampilkan informasi akademik dan finansial dalam satu tampilan yang ringkas dan mudah dipahami.
- Membuat pengalaman pengelolaan tugas yang sederhana dan cepat digunakan.
- Membangun aplikasi full stack yang solid sejak awal (Laravel + Livewire + MySQL) sebagai fondasi pengembangan fitur lanjutan di masa depan.

**Objectives (Terukur):**
- User dapat menambah, mengedit, menghapus, dan melacak status tugas dalam <3 langkah interaksi.
- Dashboard menampilkan ringkasan aktivitas (total tugas, status, progress keseluruhan, deadline terdekat, ringkasan keuangan) tanpa perlu reload halaman (real-time via Livewire).
- Sistem memberi indikator urgensi visual (warna) otomatis berdasarkan kedekatan deadline.
- Sistem memberi indikator visual otomatis ketika pengeluaran mendekati/melebihi budget bulanan yang ditentukan.
- Data tugas, jadwal, Subjects, dan keuangan tersimpan di MySQL; preferensi tema saat ini tersimpan di `localStorage` browser (lihat FR-9.2).

---

## 3. Target Users

| Segmen | Deskripsi |
|---|---|
| **Siswa SMA/SMK** | User yang perlu mencatat tugas sekolah, mengingat deadline, dan menentukan prioritas pengerjaan. |
| **Mahasiswa** | User yang mengelola tugas kuliah, jadwal, deadline, progress, dan keuangan pribadi. |
| **Kelompok belajar / study circle** | Kelompok pelajar atau mahasiswa yang ingin mengelola aktivitas belajar masing-masing. |
| **Kelas** | Berpotensi menggunakan dashboard bersama pada pengembangan lanjutan (Phase 2) saat multi-user/authentication sudah tersedia. |

**Karakteristik pengguna:**
- Memiliki banyak tugas sekolah atau kuliah dengan deadline berbeda-beda.
- Sering lupa deadline atau kesulitan menentukan tugas yang harus dikerjakan lebih dulu.
- Ingin mengatur progress belajar dan, bila diperlukan, keuangan pribadi.
- Menginginkan solusi cepat pakai dengan tampilan yang jelas.
- Terbiasa menggunakan aplikasi berbasis web/browser.
- Ingin memantau progress belajar, pengerjaan tugas, dan kondisi keuangan secara visual.

> Catatan: Pada versi saat ini, aplikasi berjalan sebagai single-user tanpa login. Data tugas, jadwal, Subjects, dan keuangan tersimpan di MySQL; preferensi tema tersimpan di `localStorage` browser.

---

## 4. Functional Requirements

### FR-1 — Task Management
- FR-1.1 User dapat menambahkan tugas baru dengan informasi: judul, mata pelajaran/mata kuliah (relasi ke Subjects), deskripsi, deadline, dan mode progress.
- FR-1.2 User dapat melihat daftar seluruh tugas.
- FR-1.3 User dapat mengubah informasi tugas (judul, mata pelajaran/mata kuliah terkait, deskripsi, deadline, status, dll).
- FR-1.4 User dapat menghapus tugas.
- FR-1.5 User dapat menentukan status tugas: `Not Started`, `In Progress`, `Completed`.
- FR-1.6 Input deadline hanya meminta tanggal (hari/bulan/tahun); jam bersifat opsional.
- FR-1.7 Deadline ditampilkan di UI dengan format lengkap termasuk nama hari, contoh: "Rabu, 16 September 2026" (ditambah jam jika diisi saat input).
- FR-1.8 Ketika progress tugas mencapai 100%, status otomatis menjadi `Completed` dan badge status menampilkan `Completed`; badge urgensi deadline juga menampilkan `Done` (lihat FR-2.5 dan FR-6.1) — keduanya bukan dipilih manual.
- FR-1.9 Deskripsi tugas ditampilkan di halaman Task (tidak ditampilkan di ringkasan Dashboard, sesuai FR-3).
- FR-1.10 Halaman Task List menampilkan Task Card dalam layout 2 kolom masonry (Desktop & Tablet) atau 1 kolom stack (Mobile), lihat detail visual di 8.7.

### FR-2 — Task Progress
- FR-2.1 Setiap tugas memiliki **Mode Progress**, dipilih salah satu:
  - **Manual** — progress diatur manual oleh User, bertambah/berkurang dalam kelipatan 5% menggunakan tombol `+`/`-`.
  - **Checklist** — progress dihitung otomatis berdasarkan proporsi sub-tugas yang sudah dicentang selesai (contoh: 1 dari 3 sub-tugas selesai = 33%).
- FR-2.2 Mode **Checklist** memerlukan User menambahkan minimal 1 sub-tugas; setiap sub-tugas punya judul singkat dan status selesai/belum (checkbox).
- FR-2.3 Setiap tugas menampilkan progress bar visual berdasarkan persentase penyelesaian (baik dari mode Manual maupun Checklist).
- FR-2.4 Progress tersimpan otomatis ke database setiap kali diperbarui, secara real-time via Livewire.
- FR-2.5 Ketika progress mencapai 100% (baik lewat Manual maupun Checklist), status tugas otomatis berubah menjadi `Completed`.
- FR-2.6 Halaman Task menampilkan progress keseluruhan (agregat dari seluruh tugas) sebagai indikator ringkasan di halaman tersebut.

### FR-3 — Dashboard
- FR-3.1 Menampilkan jumlah tugas per status (Total Task / Not Started / In Progress / Completed) dalam container statistik di card List Tugas.
- FR-3.2 Menampilkan tugas dengan deadline terdekat (3–5 item: nama tugas, aksen warna mata pelajaran/mata kuliah terkait, deadline) di card List Tugas.
- FR-3.3 Menampilkan ringkasan keuangan bulan berjalan (total pemasukan, pengeluaran, sisa saldo).
- FR-3.4 *(Phase 2)* Menampilkan card Profil (foto profil, nama, kelas).
- FR-3.5 *(Phase 2)* Menampilkan Greeting dengan format "[Kata Sapaan], [Nama Panggilan]!".
- FR-3.6 *(Phase 2)* User dapat mengkustomisasi Kata Sapaan (dipilih dari daftar pilihan, misal: Hai, Hii, Halo, Alloww, Heyy) melalui tombol edit pada section Greeting.
- FR-3.7 *(Phase 2)* User dapat mengkustomisasi Nama Panggilan yang ditampilkan pada Greeting.
- FR-3.8 *(Phase 2)* Preferensi Kata Sapaan dan Nama Panggilan tersimpan di database dan diterapkan otomatis setiap kali Dashboard dibuka.
- FR-3.9 *(Phase 2)* Menampilkan Calendar View di bawah ringkasan Dashboard.
- FR-3.10 *(Phase 2)* Kalender bulanan menandai tanggal yang memiliki tugas deadline.
- FR-3.11 *(Phase 2)* User dapat memilih tanggal untuk melihat jadwal pelajaran/kuliah hari itu dan tugas yang deadline-nya jatuh pada tanggal tersebut.
- FR-3.12 *(Phase 2)* Detail tanggal terpilih ditampilkan expand inline di bawah kalender.

### FR-4 — Subjects (Mata Pelajaran/Mata Kuliah)
- FR-4.1 User dapat menambahkan data mata pelajaran (SMA/SMK) atau mata kuliah (perguruan tinggi) sebagai data referensi.
- FR-4.2 User dapat melihat, mengedit, dan menghapus data Subjects.
- FR-4.3 Data Subjects menjadi referensi (relasi) untuk Task (FR-1) dan Schedule (FR-5), dipilih lewat dropdown.
- FR-4.4 Halaman Subjects terpisah dari halaman Schedule karena Subjects merupakan data referensi yang jarang diubah, sedangkan Schedule rutin diperiksa.

### FR-5 — Schedule
- FR-5.1 User dapat menambahkan jadwal pelajaran/kuliah dengan informasi: mata pelajaran/mata kuliah (relasi ke Subjects), hari, waktu, ruangan, dan guru/dosen.
- FR-5.2 User dapat melihat, mengedit, dan menghapus jadwal.
- FR-5.3 Jadwal ditampilkan dalam bentuk Schedule Card (gaya "boarding pass"), dikelompokkan/ditandai berdasarkan hari lewat badge Hari pada tiap card, lihat detail visual di 8.8.
- FR-5.4 Setiap Schedule Card memiliki accent color (lihat Schedule Card Accent Colors di `PROJECT.md` section 12) yang diterapkan pada border dan badge Hari.

### FR-6 — Deadline Tracking
- FR-6.1 Sistem menghitung urgensi tugas secara otomatis, dengan urutan pengecekan sebagai berikut (dari prioritas tertinggi):
  1. Jika progress tugas ≥ 100% → level `done` dan badge urgensi menampilkan `Done` (mengabaikan kondisi lain).
  2. Jika tanggal sekarang sudah melewati deadline → level `overdue`.
  3. Jika belum, hitung sisa hari menuju deadline dan tentukan level berdasarkan tabel berikut:

  | Level | Rentang Sisa Hari | Warna |
  |---|---|---|
  | `critical` | 0–5 hari | Danger (merah) |
  | `urgent` | 6–12 hari | Warning (oranye) |
  | `approaching` | 13–20 hari | Caution (kuning) |
  | `safe` | > 20 hari (default) | Success (hijau) |
  | `overdue` | Tanggal sekarang sudah melewati deadline | Danger (merah) |
  | `done` | Progress ≥ 100% (dicek pertama, override semua level lain) | Neutral (abu-abu) |

- FR-6.2 Level `done` menggantikan/override level urgensi lain begitu progress tugas mencapai 100%, terlepas dari sisa hari atau status deadline-nya.
- FR-6.3 Level `overdue` dan `critical` menggunakan warna yang sama (Danger/merah), namun secara logis tetap dua kondisi berbeda (sudah lewat deadline vs mendekati deadline).
- FR-6.4 Level urgensi ditampilkan di UI sebagai label "Priority" pada Task Card (lihat 8.4), meskipun secara teknis ini adalah hasil kalkulasi urgensi deadline, bukan field prioritas manual yang diinput User.
- FR-6.5 Level urgensi dihitung ulang secara otomatis (real-time) berdasarkan tanggal sistem saat ini setiap kali halaman dibuka/direfresh.

### FR-7 — Finance Tracker
- FR-7.1 User dapat mencatat pemasukan (contoh: uang saku, kiriman orang tua, penghasilan sampingan) beserta nominal dan tanggal.
- FR-7.2 User dapat mencatat pengeluaran dengan kategori (contoh: makan, transport, jajan, kebutuhan sekolah/kuliah, lainnya) beserta nominal dan tanggal.
- FR-7.3 User dapat menentukan budget bulanan.
- FR-7.4 Sistem menghitung dan menampilkan sisa saldo secara real-time berdasarkan pemasukan dan pengeluaran yang tercatat.
- FR-7.5 Sistem menampilkan indikator visual (progress bar/warna) ketika total pengeluaran mendekati atau melebihi budget bulanan.
- FR-7.6 User dapat mengedit dan menghapus catatan pemasukan/pengeluaran.

### FR-8 — Search, Filter & Sorting
- FR-8.1 User dapat mencari tugas berdasarkan judul (real-time search).
- FR-8.2 Filter Status: `Semua Status`, `Not Started`, `In Progress`, `Completed`.
- FR-8.3 Filter Subjects: pilihan semua atau daftar dinamis mata pelajaran/mata kuliah yang sudah diinput User.
- FR-8.4 Sorting: `Deadline Terdekat`, `Deadline Terjauh`, `Progress Tertinggi`, `Progress Terendah`, `Terbaru Dibuat`, dan `Subjects (A-Z)`.
- FR-8.5 Tugas berstatus `Completed` selalu ditampilkan di urutan paling bawah setelah sorting utama diterapkan; urutan dalam kelompok masing-masing tetap mengikuti sorting yang dipilih.
- FR-8.6 Search, filter, dan sorting dapat dikombinasikan sekaligus (bukan saling eksklusif).

### FR-9 — Theme
- FR-9.1 User dapat beralih antara Light mode dan Dark mode.
- FR-9.2 *(Phase 2)* Preferensi tema disimpan pada akun User di database dan diterapkan otomatis saat login, termasuk saat diakses dari browser atau perangkat lain. Pada versi single-user saat ini, preferensi tema disimpan sementara di `localStorage` browser.

### FR-10 — Data Persistence
- FR-10.1 Data task, progress, deadline, schedule, Subjects, dan finance disimpan di database MySQL melalui Laravel. Preferensi tema lokal dikecualikan (FR-9.2).
- FR-10.2 Data database tetap tersedia lintas browser/perangkat selama database tidak dihapus atau diubah; backup dan pemulihan tidak termasuk scope versi ini.

### FR-11 — Navigation
- FR-11.1 Bottom Nav tampil konsisten pada halaman Dashboard, Task, Schedule, Subjects, dan Finance.
- FR-11.2 Navigasi menggunakan gaya floating liquid-glass pill (`bg-surface/20`, `backdrop-blur-xs`, border, rounded penuh).
- FR-11.3 Pada Desktop & Tablet, Bottom Nav menampilkan logo+teks di kiri, menu berlabel di tengah, dan toggle Light/Dark di kanan.
- FR-11.4 Pada Desktop & Tablet, menu aktif ditandai sliding pill ber-border Primary, teks berwarna Primary, dan icon yang muncul dengan animasi; menu nonaktif menampilkan label rapat tanpa icon.
- FR-11.5 Pada Mobile, Bottom Nav berupa floating pill dengan icon untuk semua menu; label hanya ditampilkan pada menu aktif dan sliding pill mengikuti item aktif.
- FR-11.6 Pada Mobile, Top Bar terpisah dan fixed di atas, berisi logo+teks di kiri dan toggle Light/Dark di kanan, dengan gaya liquid glass.
- FR-11.7 Badge Task menampilkan jumlah tugas yang belum selesai; badge Schedule menampilkan jumlah jadwal pada hari ini. Keduanya tampil pada semua breakpoint dan diperbarui setelah perubahan data terkait.

---

## 5. Non-Functional Requirements

| Kategori | Kebutuhan |
|---|---|
| **Usability** | Antarmuka harus intuitif dan dapat digunakan tanpa onboarding/tutorial; alur menambah tugas maksimal 3 langkah. |
| **Performance** | Interaksi CRUD tugas, update progress, dan kalkulasi keuangan harus terasa responsif, memanfaatkan update real-time Livewire tanpa reload halaman penuh. |
| **Responsiveness** | Tampilan harus responsif dan berfungsi baik di perangkat Mobile, Tablet, dan Desktop. |
| **Reliability** | Data aplikasi tersimpan di database dan backend memvalidasi input; backup dan pemulihan data belum termasuk scope versi ini. |
| **Availability** | Aplikasi berupa web application; selama pengembangan diakses melalui local development server Laragon. |
| **Maintainability** | Struktur kode mengikuti pola Laravel dan Livewire agar mudah dipelihara serta dikembangkan, termasuk penambahan authentication pada Phase 2. |
| **Scalability** | Struktur database (migration & relasi antar tabel) dirancang agar mudah diperluas, misalnya menambahkan relasi user saat multi-user diimplementasikan. |
| **Security (Future)** | Saat Phase 2 (Authentication) diimplementasikan, data per-user harus terlindungi dan tervalidasi di sisi server (backend Laravel). |
| **Portability** | Aplikasi saat ini dijalankan secara lokal melalui Laragon; struktur project memungkinkan deployment ke hosting PHP/cloud di tahap berikutnya bila dibutuhkan. |
| **Accessibility** | Kontras warna (termasuk indikator urgensi & dark/light mode) harus tetap terbaca dan sesuai standar aksesibilitas dasar. |

---

## 6. Product Scope

### 6.1 In Scope — Phase 1 (Core Full Stack Build)
- Dashboard pelajar (jadwal hari ini, ringkasan tugas dan budget, deadline terdekat, transaksi bulan ini).
- Task management (CRUD tugas).
- Progress tracking per tugas.
- Deadline management & indikator urgensi warna.
- Subjects (CRUD data mata pelajaran/mata kuliah sebagai referensi).
- Schedule (jadwal pelajaran/kuliah).
- Finance tracker (pemasukan, pengeluaran, budget bulanan, indikator saldo).
- Search, filter & sorting tugas.
- Theme preference (Light/Dark mode).
- Data persistence via database MySQL.
- Responsive interface (Mobile, Tablet, Desktop).

### 6.2 Out of Scope — Future Phases

**Phase 2 — Refinement, Enhancement, & UX Polish:**
- Calendar View bulanan pada Dashboard, penanda deadline, dan detail tanggal expand inline.
- Penyempurnaan UX dan aksesibilitas yang belum selesai.
- Profil Dashboard (foto profil, nama, kelas) dan Greeting yang dapat dikustomisasi.
- Penyimpanan preferensi Profil/Greeting per pengguna; dikerjakan bersama authentication dan multi-user.
- User authentication (login/register).
- Role management & multi-user support.
- Notifikasi.
- Advanced analytics (laporan akademik & keuangan lebih mendalam).
- Kemungkinan deployment ke hosting/cloud untuk akses publik.

> Catatan: Item Future di atas belum termasuk scope versi saat ini. Struktur database dapat dikembangkan untuk mendukungnya.

---

## 7. Features & Requirements Summary

| # | Fitur | Deskripsi Singkat | Prioritas |
|---|---|---|---|
| 1 | Task Management | CRUD tugas, status, deadline, prioritas | Must Have |
| 2 | Task Progress | Progress bar per tugas, update real-time | Must Have |
| 3 | Dashboard | Ringkasan total tugas, status, progress, deadline terdekat, ringkasan keuangan | Must Have |
| 4 | Subjects | CRUD mata pelajaran/mata kuliah sebagai data referensi (terpisah dari Schedule) | Must Have |
| 5 | Schedule | Jadwal pelajaran/kuliah (relasi ke Subjects, hari, waktu, ruangan, guru/dosen) | Must Have |
| 6 | Deadline Tracking | Kategori urgensi + indikator warna otomatis | Must Have |
| 7 | Finance Tracker | Pemasukan, pengeluaran, budget bulanan, indikator saldo | Must Have |
| 8 | Search, Filter & Sorting | Cari & atur tugas berdasarkan kriteria | Should Have |
| 9 | Theme (Light/Dark) | Toggle tema; simpan preferensi lokal sekarang, sinkronisasi akun Phase 2 | Should Have |
| 10 | Data Persistence | Simpan semua data ke database MySQL | Must Have |
| 11 | Responsive Design | Optimal di Mobile, Tablet, Desktop | Must Have |
| 12 | Navigation | Floating Bottom Nav semua device, Top Bar mobile, sliding active state, badge Task dan Schedule | Must Have |

---

## 8. Design System

### 8.1 Prinsip Desain
- **Simple & Clean** — fokus pada keterbacaan informasi, minim clutter.
- **Visual-first for urgency** — pengguna dapat langsung mengenali prioritas tugas hanya lewat warna/indikator visual, tanpa perlu membaca detail.
- **Consistency** — komponen UI (card, button, progress bar) konsisten di seluruh halaman (Dashboard, Task List, Schedule, Finance Tracker).
- **Distraction-free** — tampilan dibuat minim elemen yang tidak perlu agar pengguna tetap fokus pada informasi penting.

### 8.2 Tema (Theming)
- Mendukung **Light Mode** dan **Dark Mode**.
- Preferensi tema disimpan di `localStorage` pada versi single-user saat ini; penyimpanan lintas perangkat melalui akun direncanakan pada Phase 2 (FR-9.2).

### 8.3 Sistem Warna — Urgency Indicator
Warna berikut digunakan secara konsisten sebagai bahasa visual utama untuk deadline tracking (dan indikator budget pada Finance Tracker):

| Warna | Makna |
|---|---|
| 🟢 Green | Aman, deadline masih jauh / pengeluaran masih jauh dari budget |
| 🟡 Yellow | Mulai mendekat, perlu diperhatikan |
| 🟠 Orange | Mendesak, perlu diprioritaskan / pengeluaran mendekati budget |
| 🔴 Red | Overdue / sangat kritis / pengeluaran melebihi budget |

### 8.4 Komponen Utama

- **Navigation Bar (Bottom Nav)** — floating liquid-glass pill, fixed di bagian bawah halaman Dashboard, Task, Schedule, Subjects, dan Finance.

  **Desktop & Tablet** — layout terbagi jadi 3 section:
  - **Section kiri:** Logo Nestly + teks "Nestly".
  - **Section tengah:** Menu navigasi (`Home`, `Task`, `Schedule`, `Subjects`, `Finance`).
  - **Section kanan:** Toggle switch Light/Dark Mode.
  - Tablet mengikuti layout yang sama persis seperti Desktop, hanya dengan ukuran elemen yang diperkecil (scaled down).
  - **Perilaku menu aktif:** Setiap item menu defaultnya hanya menampilkan **label teks**. Saat sebuah menu menjadi aktif (halaman sedang dibuka):
    - Sebuah **icon muncul dari belakang label**, bergeser ke posisi **sebelah kiri label** (slide-in dari arah kanan/belakang teks ke kiri).
    - Saat berpindah ke halaman lain (menu tersebut jadi tidak aktif lagi), icon tersebut **menghilang dengan arah sebaliknya** — bergerak ke kanan, masuk ke belakang label lagi (slide-out ke kanan).
    - Menu yang aktif juga dibedakan lewat **warna border** dan **warna label + icon** (menggunakan token warna `Primary` dari Color System).

  **Mobile** — layout lebih ringkas, kebalikan dari perilaku Desktop/Tablet:
  - Isi menu: `Home`, `Task`, `Schedule`, `Subjects`, `Finance` — seluruhnya dalam bentuk **icon saja** secara default (tanpa label).
  - **Perilaku menu aktif:** kebalikan dari Desktop/Tablet — icon selalu tampil, dan **label baru muncul saat menu tersebut aktif** (icon lain tetap icon-only, hanya menu aktif yang melebar menampilkan label di sebelah iconnya).
  - Logo Nestly **tidak ditampilkan** di Bottom Nav pada Mobile — ditampilkan pada Top Bar khusus Mobile.

  Referensi visual perilaku "icon-only default, melebar dengan label saat aktif" mengikuti pola floating pill navigation seperti pada aplikasi mobile modern (icon-only nav yang salah satu itemnya melebar menampilkan label saat dipilih).

  **Badge:** Menu `Task` menampilkan jumlah tugas belum selesai; menu `Schedule` menampilkan jumlah jadwal hari ini. Badge muncul di semua breakpoint.

- **Theme Toggle (Light/Dark Mode)** — button toggle switch untuk beralih Light/Dark mode. Posisinya berbeda antar breakpoint:
  - **Desktop & Tablet:** berada di section kanan Bottom Nav.
  - **Mobile:** berada di Top Bar (lihat komponen di bawah), karena Bottom Nav Mobile berbentuk icon-only tanpa ruang untuk toggle.

- **Top Bar** — komponen navigasi tambahan di bagian atas layar, **khusus tampil pada breakpoint Mobile** (karena Bottom Nav di Mobile berupa icon-only tanpa slot logo). Pada Desktop & Tablet, Top Bar ini **tidak digunakan** — logo tetap berada di section kiri Bottom Nav (lihat spesifikasi Desktop & Tablet di atas).
  - **Posisi:** fixed di bagian atas layar.
  - **Isi:** 2 elemen saja —
    - **Kiri:** Logo Nestly + teks "Nestly".
    - **Kanan:** Toggle switch Light/Dark Mode (tanpa hamburger menu).
  - **Tampilan:** liquid-glass (`bg-surface/20`, blur, border bawah, shadow) dengan sudut bawah membulat.

- **Task Card (halaman Task)** — desain lengkap & detail visual ada di section 8.7.
- **Task Card (ringkas, khusus di Dashboard)** — versi mini dari Task Card, hanya menampilkan nama tugas, aksen warna Subjects terkait, dan deadline (lihat 8.6).
- **Progress Bar** — representasi visual progress (persentase) per tugas dan progress keseluruhan.
- **Dashboard Summary Widget** — kartu ringkasan statistik (total tugas, status, deadline terdekat, ringkasan keuangan).
- **Schedule Card/List** — menampilkan jadwal pelajaran/kuliah secara terstruktur, dengan accent color per kartu.
- **Finance Summary Widget** — kartu ringkasan pemasukan, pengeluaran, dan sisa saldo, dengan indikator progress terhadap budget.
- **Filter & Sort Bar** — kontrol untuk pencarian, filter, dan pengurutan tugas.

Referensi warna lengkap (hex code untuk Light & Dark mode, serta accent color untuk Schedule Card) mengikuti **Color System** yang telah ditetapkan di `PROJECT.md`.

### 8.5 Layout & Responsiveness

- Layout adaptif untuk 3 breakpoint utama: **Mobile**, **Tablet**, **Desktop**.
- **Top Bar** (lihat 8.4) hanya muncul pada **Mobile** — fixed di atas, berisi logo+teks (kiri) dan toggle Light/Dark (kanan).
- **Navigation Bar** (lihat 8.4) tetap berada di **bottom** pada seluruh breakpoint, namun kontennya berubah:
  - **Desktop:** 3-section layout penuh (logo+teks | menu | toggle Light/Dark), menu default menampilkan label, icon muncul saat aktif.
  - **Tablet:** identik dengan Desktop, ukuran elemen diperkecil (scaled down).
  - **Mobile:** icon nav (`Home`, `Task`, `Schedule`, `Subjects`, `Finance`), tanpa logo/teks dan toggle; label hanya muncul pada menu aktif. Logo dan toggle ada di Top Bar.
- Dashboard sebagai halaman utama (landing) setelah aplikasi dibuka.
- Konten utama halaman (Task List, Schedule, Finance) perlu diberi padding/margin agar tidak tertutup oleh Top Bar maupun Bottom Nav yang fixed.

### 8.6 Dashboard Page Layout

Dashboard menggunakan grid 6 kolom pada Tablet/Desktop. Konten utama terdiri dari jadwal, ringkasan singkat, tugas, dan transaksi; Profil dan Greeting direncanakan untuk Phase 2:

| # | Section | Posisi Grid (kolom, baris) | Isi |
|---|---|---|---|
| 1 | **Jadwal** | Kolom 1–5, Baris 1 | 4 card jadwal sejajar di dalam section ini |
| 2 | **Ringkasan Singkat** | Kolom 6, Baris 1 | Jumlah tugas belum selesai + persentase budget terpakai bulan ini |
| 3 | **List Tugas Terdekat Deadline** | Kolom 1–4, Baris 2–4 | Lihat detail sub-struktur di bawah |
| 4 | **List Transaksi Finance** | Kolom 5–6, Baris 2–4 | Maksimal 5 transaksi bulan berjalan (dengan empty state jika belum ada) |
| 5 | **Profil** *(Phase 2)* | Kolom 5–6, Baris 3–4 | Foto profil (bentuk lingkaran), Nama, Kelas |
| 6 | **Greeting** *(Phase 2)* | Kolom 5–6, Baris 2 | Sapaan custom (lihat detail di bawah) |

**Detail Section 3 — List Tugas Terdekat Deadline:**

Section ini dipecah jadi 2 container tersusun vertikal:

- **Container atas — Statistik Tugas:**
  - Layout: `display: flex; justify-content: space-evenly;`
  - Berisi 4 item statistik yang dihitung otomatis dari data tugas:
    1. **Total Task** — jumlah seluruh tugas.
    2. **Not Started** — jumlah tugas berstatus `Not Started`.
    3. **In Progress** — jumlah tugas berstatus `In Progress`.
    4. **Completed** — jumlah tugas berstatus `Completed`.
  - Setiap item terdiri dari label (nama status) + angka (dihitung real-time via Livewire dari database, bukan manual).

- **Container bawah — List Tugas:**
  - Menampilkan 3–5 tugas dengan deadline paling mendekat.
  - Setiap item hanya menampilkan: **nama tugas**, **aksen warna dari mata kuliah terkait**, dan **deadline**.

**Detail Section 6 — Greeting:**
- Menampilkan format: `[Kata Sapaan], [Nama Panggilan]!`
- **Kata sapaan** dapat dikustomisasi lewat pilihan dropdown/select, contoh opsi: `Hai`, `Hii`, `Halo`, `Alloww`, `Heyy`, dll.
- **Nama panggilan** juga dapat dikustomisasi oleh pengguna.
- Sebuah **tombol edit** ditempatkan di sisi **paling kanan** section ini (tetap berada di dalam batas section 6) untuk membuka pengaturan kustomisasi kata sapaan & nama panggilan.

**Responsiveness:**
- **Desktop:** grid 6 kolom seperti tabel di atas.
  - **Tablet:** mengikuti grid 6 kolom dengan susunan section yang sama seperti Desktop.
- **Mobile:** grid ditata ulang total menjadi **1 kolom vertikal (stacked)**, dengan urutan dari atas ke bawah:
  1. Jadwal
  2. Ringkasan Singkat
  3. List Tugas Terdekat Deadline
  4. List Transaksi Finance

  Greeting + Profil akan ditambahkan di bagian paling atas setelah fitur Phase 2 tersedia.

> Catatan: Section Jadwal (poin 1 di atas) di dalamnya sendiri berisi 4 card jadwal — perilaku responsivenya (grid 2×2 di Tablet/Mobile) mengikuti spesifikasi yang sudah ditetapkan sebelumnya, tetap berlaku sebagai bagian dari section ini.

**Section 7 — Calendar View *(Phase 2)*:**
- Ditempatkan terpisah di bagian paling bawah Dashboard, di luar grid konten utama.
- Tampilan kalender bulanan (month view).
- Tanggal dengan deadline tugas ditandai indikator titik.
- Klik tanggal → **expand inline di bawah kalender** (mendorong konten lain ke bawah, bukan modal/popup), menampilkan:
  - Jadwal pelajaran/kuliah pada hari yang sesuai dengan tanggal tersebut.
  - Tugas dengan deadline pada tanggal tersebut.

### 8.7 Task Card Design (Halaman Task)

Task Card di halaman Task (bukan versi ringkas di Dashboard) menggunakan gaya **Neo-brutalist**: border tebal berwarna aksen + hard shadow (offset, bukan blur).

**Struktur luar card:**
- Border: `2px solid`, warna mengikuti aksen (bisa warna mata kuliah, ditentukan lebih lanjut saat implementasi).
- `border-radius: 14px`.
- `box-shadow: 6px 6px 0px [warna aksen yang sama dengan border]` — shadow keras/solid, bukan blur, khas gaya neo-brutalist.
- Background card mengikuti token `Surface`.

**Badge yang "nempel" di garis border atas card:**
- **Badge Subjects** — posisi tengah atas, bentuk pill, background sama dengan `Surface` card (supaya seolah "memotong" garis border), border & warna teks mengikuti aksen mata pelajaran/mata kuliah.
- **Badge Status** (`Not Started` / `In Progress` / `Completed`) — posisi kanan atas, bentuk pill sama seperti badge Mata Kuliah, namun warna mengikuti token status masing-masing.
- Kedua badge diposisikan `position: absolute`, sedikit naik ke atas garis border (setengah tinggi badge berada di luar card, setengah di dalam).

**Body card — 2 kolom:**
- **Kolom kiri (lebih lebar, ± 55–60%):**
  - Judul tugas (bold, ukuran lebih besar dari teks lain).
  - Deskripsi tugas (teks kecil, warna `Text Muted`, multi-baris/wrap).
- **Kolom kanan (± 40–45%):**
  - Label "Progress" (bold).
  - Progress bar (untuk Mode **Manual**) dengan angka persentase di sisi kanan bar.
  - Baris tombol `−` dan `+` tepat di bawah progress bar — **lebar baris tombol ini mengikuti lebar progress bar saja** (tidak melebar sampai ke kolom angka persentase); tombol `−` menempel di ujung kiri, tombol `+` menempel di ujung kanan (`justify-content: space-between` dalam container selebar progress bar).
  - Untuk Mode **Checklist**, bagian ini menampilkan progress read-only (dihitung otomatis dari sub-tugas), tanpa tombol `+`/`−`.
  - Label "Deadline" (bold), diikuti tanggal terformat sesuai FR-1.7 (contoh: "Rabu, 16 September 2026").

**Todo List (khusus Mode Progress Checklist) — full-width di bawah 2 kolom:**
- Label "Todo List" (bold, rata tengah).
- Daftar sub-tugas, masing-masing dengan checkbox (kotak, bukan bulat) di kiri dan teks sub-tugas di kanan (wrap multi-baris jika perlu).
- Sub-tugas yang sudah dicentang: teks berubah warna `Text Muted` dengan style *strikethrough*.
- Section Todo List ini **tidak muncul** jika Mode Progress tugas adalah **Manual** (karena tidak ada sub-tugas).

**Icon edit & hapus:** tetap tersedia di card ini (lihat FR-1), penempatan detail (misal di pojok kanan atas, dekat badge status) disesuaikan saat implementasi visual agar tidak bertabrakan dengan badge.

**Layout Halaman Task List (susunan antar-card):**
- **Desktop & Tablet:** 2 kolom sejajar, masing-masing kolom berisi beberapa Task Card tersusun ke bawah (gaya masonry — tinggi tiap card bisa berbeda-beda tergantung panjang deskripsi dan jumlah sub-tugas, tidak harus seragam).
- **Mobile:** 1 kolom, seluruh card di-stack vertikal ke bawah.

---

### 8.8 Schedule Card Design (Halaman Schedule)

Schedule Card menggunakan gaya visual yang konsisten dengan Task Card (neo-brutalist: border tebal + hard shadow offset), dengan layout "boarding pass" khas untuk info jadwal.

**Struktur luar card:**
- Border: `2px solid`, warna mengikuti accent color card (lihat Schedule Card Accent Colors di `PROJECT.md` section 12).
- `border-radius: 14px`.
- `box-shadow: 5px 5px 0px [warna aksen yang sama dengan border]`.

**Badge Hari — nempel di garis border atas:**
- Posisi: kiri atas (bukan tengah/kanan).
- Bentuk pill dengan border (outline, bukan solid fill), background sama dengan `Surface` card (supaya "memotong" garis border, konsisten dengan pola badge di Task Card).
- Warna border & teks mengikuti accent color card.
- Ukuran cukup besar/jelas terbaca (tidak mini).

**Body card:**
- **Kolom kiri (blok waktu, "boarding pass style"):**
  - Jam mulai ditampilkan besar & bold (contoh: "08").
  - Menit ditampilkan kecil di bawahnya (contoh: "00").
  - Garis vertikal pemisah pendek.
  - Jam selesai ditampilkan kecil di bawah garis (contoh: "09:40").
- **Kolom kanan (info Subjects):**
  - Nama mata pelajaran/mata kuliah — font besar & bold (lebih besar dari teks lain di card).
  - Ruangan — baris tersendiri.
  - Nama guru/dosen — baris tersendiri, **di bawah** ruangan.

---

## Appendix — Referensi Tambahan (dari PROJECT.md & README.md)

- **Data yang disimpan di database (MySQL):** Task, Progress, Deadline, Status, Schedule, Data keuangan (income, expense, budget), Theme preference, Pengaturan pengguna.
- **Roadmap Pengembangan:**
  - Phase 1 (🟢 Current) — Core Full Stack Build, including Task, Schedule, Finance, Dashboard, search/filter/sorting, urgency indicators, theme toggle, responsive navigation, and UX polish.
  - Phase 2 (🟡 Future) — Calendar View Dashboard (monthly calendar, deadline markers, inline date details), Enhancement (Authentication, multi-user, notifications, advanced analytics, kemungkinan deployment ke hosting/cloud).
- **Deployment (Saat Ini):** Local development via Laragon, database dikelola via HeidiSQL — digunakan sebagai project pembelajaran/portofolio pribadi.
- **Tech Stack:** Laravel (Backend & Frontend melalui Blade + Livewire), Tailwind CSS untuk styling, MySQL sebagai database. Tidak menggunakan JavaScript framework terpisah — seluruh interaktivitas ditangani oleh Livewire.
