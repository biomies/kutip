# 📣 Koar — Forum Anonim

> *Ngomong bebas, ga perlu daftar, ga ada yang tau siapa lo.*

**Koar** adalah forum diskusi anonim berbasis web yang dibangun dengan **Laravel 12**. Ga perlu login, ga perlu email, langsung bisa posting. Setiap pengunjung otomatis dapet identitas unik via browser cookie — vibe-nya kayak nge-post di Reddit tapi lebih santai.

---

## ✨ Fitur Utama

### 🗣️ Forum & Diskusi
- **2 jenis forum**: Global (semua bisa langsung post) dan Niche (ada subforum-nya, mirip subreddit)
- **Subforum buatan user** — siapa aja bisa bikin subforum di forum niche
- **Threaded replies** sampai 5 level kedalaman biar diskusinya rapi kayak Twitter thread
- **Edit & hapus** post/reply sendiri via dropdown menu yang clean

### 🖼️ Image Embed Otomatis
- Paste link gambar di post → gambar langsung muncul (Discord-style)
- Support **Google Drive** (tinggal share link, auto detect)
- Support **Imgur**, dan semua URL gambar langsung
- Image proxy server-side buat bypass CORS, ada domain whitelist buat keamanan

### 💬 Direct Message
- Chat 1-on-1 langsung ke user lain
- Badge unread count di navbar/bottom bar yang update real-time-ish (60 detik cache)
- Pesan dari akun yang sudah dihapus tetap muncul dengan label `[deleted]`

### 👤 Profil & Akun
- Username otomatis format `user-42` (angka sequential, unik tiap user)
- Bisa ganti username kapan aja
- **Hapus akun** dengan konfirmasi (ketik "hapus") — data tetap ada, nama jadi `[deleted]`
- Profil publik dengan statistik post, reply, dan subforum

### 🔒 URL Obfuscation
- ID numerik di-encode jadi hash acak — `/post/k3Zx9m` bukan `/post/1`
- Pakai **Hashids** dengan salt unik per tipe resource biar ga bisa di-enumerate
- Forum & subforum tetap pakai slug yang human-readable

### ⚡ Performance
- User lookup, unread count, dan activity tracking semuanya di-cache
- Image proxy di-cache 6 jam di server + 1 jam immutable di browser
- DB writes buat activity tracking dibatasi 1x per jam per user

### 📱 Mobile-First
- **Bottom navigation bar** di smartphone (Beranda, Forum, Chat, Profil)
- Sidebar otomatis hidden di layar kecil, layout stack jadi single column
- Touch target yang proper, modal jadi bottom sheet, support safe area iPhone
- Add to home screen ready (apple-mobile-web-app-capable)

---

## 🛠️ Tech Stack

| Layer | Tech |
|---|---|
| Framework | Laravel 12 |
| PHP | ^8.2 |
| Database | SQLite (dev) / MySQL (prod) |
| ID Obfuscation | vinkla/hashids ^13.0 |
| CSS | Custom design system (no Tailwind runtime) |
| Auth | Cookie-based browser token (no login) |

---

## 🚀 Setup

### Requirements
- PHP 8.2+
- Composer
- SQLite atau MySQL

### Install

```bash
git clone https://github.com/username/koar.git
cd koar

composer install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

php artisan serve
```

Buka `http://localhost:8000` dan langsung bisa pake — ga perlu setup apapun lagi.

### Konfigurasi Database (MySQL)

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koar
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📁 Struktur Proyek

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ForumController.php       # Feed beranda + semua forum
│   │   ├── PostController.php        # CRUD post
│   │   ├── ReplyController.php       # CRUD reply (threaded)
│   │   ├── ChatController.php        # DM + unread tracking
│   │   ├── ProfileController.php     # Profil publik
│   │   ├── SettingsController.php    # Ganti username, hapus akun
│   │   ├── SubforumController.php    # Subforum buatan user
│   │   └── ImageProxyController.php  # Proxy gambar (Drive, Imgur)
│   └── Middleware/
│       └── IdentifyAnonymousUser.php # Auto-create sesi anonim
├── Models/
│   ├── Concerns/
│   │   └── HasEmbeds.php   # Trait: URL extraction & image embedding
│   ├── User.php            # Soft deletes, browser token
│   ├── Forum.php           # Global vs Niche
│   ├── Subforum.php        # User-created categories
│   ├── Post.php            # nullable user_id (support [deleted])
│   ├── Reply.php           # Self-referential, depth 0-5
│   ├── Chat.php            # Normalized pair (smaller ID = user_one)
│   └── ChatMessage.php     # is_read tracking
└── Services/
    └── Hashid.php  # ID obfuscation service
```

---

## 🗄️ Database Schema

```
users            → uuid, browser_token, username, user_number, soft_delete
forums           → name, slug, type (global|niche), icon, order
subforums        → forum_id, user_id (creator), name, slug, post_count
posts            → user_id (nullable), forum_id, subforum_id, content, reply_count
replies          → post_id, user_id (nullable), parent_id (self-ref), depth, content
chats            → user_one_id, user_two_id (unique pair)
chat_messages    → chat_id, user_id (nullable), content, is_read
```

> `user_id` di posts, replies, dan chat_messages nullable dengan `nullOnDelete` — konten tetap ada kalau user hapus akun.

---

## 🔑 Cara Kerja Autentikasi

Ga ada login tradisional. Begini alurnya:

```
Request masuk
    ↓
Middleware cek cookie koar_token
    ↓
Ada token? → Lookup user (cached 5 menit)
Ga ada?    → Buat user baru + set cookie (1 tahun)
    ↓
User tersedia di semua controller via currentUser()
```

Setiap user punya `browser_token` (64 karakter random) yang disimpan sebagai HTTP-only cookie. Aman dari XSS, cukup buat identifikasi anonim jangka panjang.

---

## 🖼️ Cara Embed Gambar

Paste link ini langsung di kolom post/reply:

```
# Gambar langsung (JPG, PNG, GIF, WEBP)
https://example.com/foto.jpg

# Google Drive (file harus di-share "Anyone with the link")
https://drive.google.com/file/d/FILE_ID/view?usp=sharing

# Imgur
https://imgur.com/abc1234
```

Maksimal 4 gambar per post. Gambar muncul otomatis di bawah teks.

---

## 🎨 Design System

CSS kustom tanpa Tailwind runtime, pakai CSS custom properties:

```css
--bg-base   #0d0f1a   /* Background utama */
--bg-1      #12151f   /* Card */
--ac-1      #8b5cf6   /* Violet aksen */
--tx-1      #eef0f6   /* Teks heading */
```

Palet violet/navy dipilih berdasarkan psikologi warna untuk meningkatkan fokus, kepercayaan, dan kenyamanan pemakaian lama (mirip Discord & Linear).

---

## 🌐 Deploy ke cPanel / Shared Hosting

```bash
# Upload semua file ke public_html atau subdomain folder
# Pastikan document root pointing ke /public

# Di server
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> Untuk storage gambar skala besar, rekomendasi pakai **Cloudflare R2** (10GB gratis, egress gratis).

---

## 📄 Lisensi

MIT — bebas dipakai, dimodif, dan di-deploy untuk project apapun.

---

<div align="center">

**Dibuat dengan ☕ dan Laravel**

*fork, star, atau kasih feedback — semua welcome*

</div>
