# Panduan Instalasi Proyek Penjualan Kendaraan REST API Laravel

Proyek Penjualan Kendaraan REST API Laravel adalah sebuah aplikasi berbasis Laravel yang menyediakan API untuk mengelola penjualan kendaraan.

## Daftar Isi
- [Prasyarat](#prasyarat)
- [Langkah-langkah Instalasi](#langkah-langkah-instalasi)
- [Dokumentasi API](#dokumentasi-api)
  - [Autentikasi](#autentikasi)
  - [Kendaraan](#kendaraan)

## Prasyarat

Sebelum memulai instalasi, pastikan Anda telah memenuhi prasyarat berikut:

1. PHP v8.0
2. Composer
3. MongoDB v4.2
4. Git

## Langkah-langkah Instalasi

Berikut adalah langkah-langkah untuk menginstal proyek Penjualan Kendaraan REST API Laravel:

1. Clone Repositori

   Buka terminal atau command prompt dan jalankan perintah berikut untuk mengkloning repositori:

   ```bash
   git clone https://github.com/shofistwn/penjualan-kendaraan.git
   ```

2. Pindah ke Direktori Proyek

   Masuk ke direktori proyek yang telah di-kloning dengan menjalankan perintah:

   ```bash
   cd penjualan-kendaraan
   ```

3. Instal Dependensi

   Jalankan perintah berikut untuk menginstal semua dependensi yang diperlukan oleh proyek:

   ```bash
   composer install
   ```

4. Konfigurasi Lingkungan

   Salin file `.env.example` menjadi `.env` dengan menjalankan perintah:

   ```bash
   cp .env.example .env
   ```

5. Generate Kunci Aplikasi

   Jalankan perintah berikut untuk menghasilkan kunci aplikasi:

   ```bash
   php artisan key:generate
   ```

6. Konfigurasi Database

   Buka file `.env` dan ubah pengaturan database seperti berikut:

   ```bash
    DB_CONNECTION=mongodb
    DB_HOST=127.0.0.1
    DB_PORT=27017
    DB_DATABASE=penjualan_kendaraan
    DB_USERNAME=
    DB_PASSWORD=
   ```

7. Mengimpor Database

    Import file database yang berada di folder `/documents/database/penjualan_kendaraan.agz`
    
8. Mengimpor Collection Postman

    Import file collection yang berada di folder `/documents/postman/`
    
8. Jalankan Server Lokal

   Terakhir, jalankan server lokal dengan perintah:

   ```bash
   php artisan serve
   ```

   Server akan berjalan di `http://localhost:8000`.

## Dokumentasi API

Berikut adalah dokumentasi API yang tersedia dalam proyek ini.

### Autentikasi

1. Register

    **Request**:
    ```bash
    POST /api/v1/auth/register
    
    Body:
    {
        "name" : "John Doe"
        "email": "user@mail.com",
        "password": "password"
    }
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Pendaftaran berhasil",
        "data": {
            "_id": "647949443cac74567d0f4919",
            "email": "user@mail.com",
            "name": "John Doe",
            "password": "$2y$10$W0ttWIhBPd5ULcwWkV6eM..."
        }
    }
    ```

2. Login

    **Request**:
    ```bash
    POST /api/v1/auth/login

    Header:
    Authorization: Bearer <JWT Token>
    
    Body:
    {
        "email": "user@mail.com",
        "password": "password"
    }
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Login berhasil",
        "data": {
            "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ...",
            "token_type": "bearer",
            "expires_in": 3600
        }
    }
    ```

3. Logout

    **Request**:
    ```bash
    POST /api/v1/auth/login

    Header:
    Authorization: Bearer <JWT Token>
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Logout berhasil",
        "data": []
    }
    ```

### Kendaraan

1. Mengambil Stok Kendaraan

    **Request**:
    ```bash
    POST /api/v1/vehicles/stock

    Header:
    Authorization: Bearer <JWT Token>
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Dapatkan stok kendaraan",
        "data": [
            {
                "_id": "64788d11904ea19c110f2425",
                "harga": "25000000",
                "motor": {
                    "mesin": "150cc",
                    "tipe_suspensi": "Telescopic",
                    "tipe_transmisi": "Manual"
                },
                "tahun_keluaran": "2022",
                "terjual": false,
                "tipe_kendaraan": "motor",
                "warna": "Merah"
            },
            {
                "_id": "64788d58904ea19c110f2426",
                "harga": "850000000",
                "mobil": {
                    "mesin": "1200cc",
                    "kapasitas_penumpang": "7",
                    "tipe": "SUV"
                },
                "tahun_keluaran": "2023",
                "terjual": false,
                "tipe_kendaraan": "mobil",
                "warna": "Merah"
            }
        ]
    }
    ```

2. Menjual Kendaraan

    **Request**:
    ```bash
    POST /api/v1/vehicles/sell

    Header:
    Authorization: Bearer <JWT Token>
    
    Body:
    {
        "kendaraan_id": "64788d11904ea19c110f2425"
    }
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Kendaraan terjual",
        "data": {
            "_id": "64788d11904ea19c110f2425",
            "harga": "25000000",
            "motor": {
                "mesin": "150cc",
                "tipe_suspensi": "Telescopic",
                "tipe_transmisi": "Manual"
            },
            "tahun_keluaran": "2022",
            "terjual": true,
            "tipe_kendaraan": "motor",
            "warna": "Merah"
        }
    }
    ```

3. Laporan Penjualan

    **Request**:
    ```bash
    POST /api/v1/vehicles/sales-report

    Header:
    Authorization: Bearer <JWT Token>
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Dapatkan laporan penjualan",
        "data": {
            "mobil": {
                "terjual": 1,
                "tersisa": 1,
                "total": 2
            },
            "motor": {
                "terjual": 1,
                "tersisa": 2,
                "total": 3
            }
        }
    }
    ```

4. Menambahkan Kendaraan Motor

    **Request**:
    ```bash
    POST /api/v1/vehicles/add-motor

    Header:
    Authorization: Bearer <JWT Token>
    
    Body:
    {
        "tahun_keluaran": "2018",
        "warna": "Putih",
        "harga": "40000000",
        "mesin": "400cc",
        "tipe_suspensi": "Mono Shock",
        "tipe_transmisi": "Automatic"
    }
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Kendaraan berhasil ditambahkan",
        "data": {
            "_id": "647948503cac74567d0f4916",
            "harga": "40000000",
            "motor": {
                "mesin": "400cc",
                "tipe_suspensi": "Mono Shock",
                "tipe_transmisi": "Automatic"
            },
            "tahun_keluaran": "2018",
            "terjual": false,
            "tipe_kendaraan": "motor",
            "warna": "Putih"
        }
    }
    ```

5. Menambahkan Kendaraan Mobil

    **Request**:
    ```bash
    POST /api/v1/vehicles/add-car

    Header:
    Authorization: Bearer <JWT Token>
    
    Body:
    {
        "tahun_keluaran": "2022",
        "warna": "Putih",
        "harga": "300000000",
        "mesin": "2000cc",
        "kapasitas_penumpang": "5",
        "tipe": "MPV"
    }
    ```
    
    **Response**:
    ```json
    {
        "success": true,
        "message": "Kendaraan berhasil ditambahkan",
        "data": {
            "_id": "6479487d3cac74567d0f4917",
            "harga": "300000000",
            "motor": {
                "mesin": "2000cc",
                "kapasitas_penumpang": "5",
                "tipe": "MPV"
            },
            "tahun_keluaran": "2022",
            "terjual": false,
            "tipe_kendaraan": "mobil",
            "warna": "Putih"
        }
    }
    ```