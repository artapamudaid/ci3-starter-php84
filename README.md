# CI3 Starter

CI3 Starter adalah template **CodeIgniter 3** yang sudah siap pakai untuk proyek modern dengan PHP 8.4.
Project ini mengutamakan kemudahan pengembangan CRUD, UI responsif, dan manajemen konfigurasi yang fleksibel.

## Fitur Utama

- **PHP 8.4 ready** -- Fully compatible dengan versi PHP terbaru.
- **Frontend interaktif**
 - CRUD menggunakan **jQuery** + **modal**
 - **DataTables** untuk tabel dinamis
 - **SweetAlert** untuk notifikasi interaktif
- **Support Dark Mode** -- UI dapat otomatis beradaptasi dengan mode gelap.
- **Konfigurasi mudah dengan `.env`** -- Menggunakan library **vlucas/phpdotenv** untuk konfigurasi environment.
- Struktur project bersih dan siap dikembangkan untuk berbagai proyek.

## Instalasi

1. **Clone repository**

```
git clone https://github.com/username/ci3-starter-php84.git
cd ci3-starter-php84
```

2.  **Install dependencies**

Jika menggunakan Composer untuk `.env`:

`composer install`

3.  **Konfigurasi `.env`**

Buat file `.env` di root project:

```
APP_NAME=CI3 Starter
APP_ENV=development
APP_DEBUG=true

DB_HOST=localhost
DB_USER=root
DB_PASS=password
DB_NAME=ci3_starter
```

4.  **Import database**

Import file SQL di folder /database -> `ci3_starter.sql` ke MySQL:

5.  **Jalankan project**

Buka browser contoh:

```
http://localhost/ci3-starter-php84/
```

6. **Akses Login Admin**

```
Username : admin
Password : admin123
```

Lisensi
-------

MIT License. Bebas digunakan dan dikembangkan.
