# UAS Administrasi Server - Static CV dan Dynamic App

## Identitas

- Nama: Wildan Fahmi
- NIM: 2388010030
- GitHub: https://github.com/wildanmuhammadfahmi81-blip
- Docker Hub: https://hub.docker.com/u/wildanmfahmi123

## Deskripsi

Project ini menyiapkan dua aplikasi untuk UAS: web statis CV dan web dinamis PHP dengan fitur login serta CRUD sederhana. Aplikasi berjalan menggunakan Docker Compose, Nginx reverse proxy, dan MariaDB. Database otomatis dibuat dan diisi data awal melalui `database/init.sql`.

## Struktur

```text
uas-aws/
├── static-cv/
│   ├── index.html
│   ├── Dockerfile
│   └── gambar CV
├── dynamic-app/
│   ├── public/
│   ├── Dockerfile
│   └── .dockerignore
├── database/
│   └── init.sql
├── nginx/
│   └── default.conf
├── .github/
│   └── workflows/
│       ├── deploy-static.yml
│       └── deploy-dynamic.yml
├── docker-compose.yml
├── .env.example
└── README.md
```

## Login Demo

- Username: `admin`
- Password: `admin123`

## Menjalankan Lokal

```bash
cp .env.example .env
docker compose up -d --build
docker compose ps
```

Web dapat dibuka di:

```text
http://localhost       -> static CV
http://localhost/app/  -> dynamic app via reverse proxy
http://localhost:8080  -> dynamic app direct fallback
```

## Environment

```env
APP_NAME=UAS Administrasi Server
STATIC_IMAGE=wildanmfahmi123/uas-static:latest
DYNAMIC_IMAGE=wildanmfahmi123/uas-dynamic:latest
HTTP_PORT=80
DYNAMIC_PORT=8080
MYSQL_ROOT_PASSWORD=ganti_root_password
MYSQL_DATABASE=uas_db
MYSQL_USER=uas_user
MYSQL_PASSWORD=ganti_password_database
```

## Docker Compose

- `reverse-proxy`: Nginx publik pada port `80`.
- `static-cv`: Nginx static website, diakses dari `/`.
- `dynamic-app`: PHP Apache, diakses dari `/app/` dan fallback port `8080`.
- `mariadb`: database MariaDB internal, memakai volume `mariadb_data`.
- `database/init.sql`: seed otomatis saat volume MariaDB pertama kali dibuat.

## CI/CD

Workflow static `.github/workflows/deploy-static.yml` berjalan saat perubahan terjadi pada:

- `static-cv/**`
- `nginx/**`
- `docker-compose.yml`
- `.env.example`
- `.github/workflows/deploy-static.yml`

Workflow `.github/workflows/deploy-dynamic.yml` berjalan saat perubahan terjadi pada:

- `dynamic-app/**`
- `database/**`
- `docker-compose.yml`
- `.env.example`
- `.github/workflows/deploy-dynamic.yml`

Pipeline melakukan checkout, login Docker Hub, build image ke Docker Hub, lalu SSH ke EC2 untuk pull image dan restart container.

## GitHub Secrets

```text
DOCKERHUB_USERNAME
DOCKERHUB_TOKEN
EC2_HOST
EC2_USER
EC2_SSH_KEY
STATIC_IMAGE
DYNAMIC_IMAGE
MYSQL_ROOT_PASSWORD
MYSQL_DATABASE
MYSQL_USER
MYSQL_PASSWORD
```
