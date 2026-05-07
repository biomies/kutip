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

## 📄 Lisensi

MIT — bebas dipakai, dimodif, dan di-deploy untuk project apapun.

---

<div align="center">

**Dibuat dengan ☕ dan Laravel**

*fork, star, atau kasih feedback — semua welcome*

</div>
