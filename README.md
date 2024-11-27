# Laravel API Project - Company Management

## Deskripsi Proyek

Proyek ini adalah API yang dibangun menggunakan **Laravel** untuk **manajemen perusahaan**, termasuk fitur-fitur seperti:
- Membuat perusahaan baru.
- Menambahkan dan mengelola employee.
- Membuat akun manager secara otomatis saat perusahaan dibuat.
- Menggunakan **JWT-Auth** untuk autentikasi.

### Fitur Utama

1. **Super Admin**:
   - Membuat perusahaan baru dan otomatis membuat akun **manager** untuk perusahaan tersebut.
   
2. **Manager**:
   - Melihat daftar **employees** di perusahaan yang sama.
   - Melihat detail **employee**.
   - Menambah, mengubah, dan menghapus **employee**.
   
3. **Employee**:
   - Melihat rekan **employee** di perusahaan yang sama.
   - Melihat detail informasi **employee**.

4. **Autentikasi** menggunakan **JWT-Auth**.

---



---
# Setup Proyek

Proyek ini adalah aplikasi Laravel yang mengintegrasikan otentikasi JWT, dokumentasi Swagger, dan PHPUnit untuk pengujian.

## Prasyarat

Sebelum Anda memulai, pastikan Anda telah menginstal hal-hal berikut:

- **PHP 8.0+**
- **Composer**
- **MySQL atau database lainnya**
- **Laravel 9+** (kompatibel dengan Laravel 11)

---

## 1. Install Dependensi

Proyek ini menggunakan paket-paket berikut:

- **`tymon/jwt-auth`** untuk otentikasi JSON Web Token (JWT).
- **`phpunit/phpunit`** untuk pengujian.
- **`darkaonline/l5-swagger`** untuk dokumentasi API Swagger.

### Install dengan Composer

Jalankan perintah berikut untuk menginstal semua dependensi yang diperlukan:

- `composer install`

Selanjutnya, install paket tambahan berikut jika belum terinstal:

- `composer require tymon/jwt-auth:^2.1`
- `composer require phpunit/phpunit:^11.0.1`
- `composer require darkaonline/l5-swagger:^8.6`

---

## 2. Konfigurasi JWT Authentication

Setelah menginstal **`tymon/jwt-auth`**, Anda perlu mempublikasikan file konfigurasi:

- `php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"`

Ini akan membuat file `config/jwt.php`. Anda dapat menyesuaikan pengaturan di sini jika perlu.

### Update `.env` untuk JWT

Tambahkan baris berikut ke file `.env` Anda:

- `JWT_SECRET=your-secret-key`

Hasilkan kunci JWT dengan perintah berikut:

- `php artisan jwt:secret`

Ini akan mengatur kunci `JWT_SECRET` di file `.env` Anda.

---

## 3. Konfigurasi Dokumentasi Swagger

Setelah menginstal **`l5-swagger`**, Anda perlu mempublikasikan asetnya:

- `php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"`

Ini akan membuat file konfigurasi yang diperlukan di `config/l5-swagger.php`.

### Hasilkan Dokumentasi Swagger

Untuk menghasilkan dokumentasi Swagger UI untuk API Anda, jalankan perintah berikut:

- `php artisan l5-swagger:generate`

Sekarang Anda dapat mengakses dokumentasi Swagger UI di `http://localhost:8000/api/documentation`.

---

## 4. Jalankan Migrasi

Jalankan migrasi untuk menyiapkan skema database Anda:

- `php artisan migrate`

---

## 5. Pengujian

Untuk menjalankan pengujian menggunakan PHPUnit, pastikan Anda telah menginstal **PHPUnit**, kemudian jalankan perintah berikut:

- `./vendor/bin/phpunit`

Ini akan menjalankan semua pengujian yang ada di direktori `tests`.

---

## 6. Penggunaan Contoh

### JWT Authentication

Untuk mengautentikasi menggunakan JWT di API Anda, Anda perlu mengirimkan permintaan POST ke endpoint login (misalnya, `/api/v1/login`) dengan kredensial pengguna (email dan password). Server akan mengembalikan token JWT.

**Contoh Permintaan**:

- `POST /api/v1/login`
- Content-Type: application/json

- `{
    "email": "user@example.com",
    "password": "password123"
}`

**Contoh Respons**:

- `{
    "token": "your-jwt-token-here"
}`

Gunakan token ini di header Authorization untuk permintaan selanjutnya:

- `Authorization: Bearer your-jwt-token-here`

---

## 7. Dokumentasi Swagger

Swagger UI dapat diakses di:

- `http://localhost:8000/api/documentation`

Di sini, Anda dapat melihat dan berinteraksi dengan endpoint API Anda. Pastikan Anda telah menjalankan perintah pembuatan Swagger (`php artisan l5-swagger:generate`) untuk pembaruan terbaru.

---

## 8. Kesimpulan

Sekarang Anda telah menyiapkan **JWT Authentication**, **Swagger API Documentation**, dan **PHPUnit** untuk pengujian di proyek Laravel Anda.

Jika Anda mengalami masalah atau membutuhkan kustomisasi lebih lanjut, Anda dapat merujuk ke dokumentasi paket terkait:
- **JWT-Auth:** [https://github.com/tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- **Swagger:** [https://github.com/DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- **PHPUnit:** [https://phpunit.de/manual/current/en/](https://phpunit.de/manual/current/en/)

## 9. Test 

File Test Berada di file dengan nama test_result.txt

- test_result.txt

