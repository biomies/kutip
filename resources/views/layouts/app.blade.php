<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0d0f1a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Koar') — Koar</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('home') }}" class="navbar-logo">
                <span class="navbar-logo-mark">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M2 4C2 2.895 2.895 2 4 2h10c1.105 0 2 .895 2 2v7c0 1.105-.895 2-2 2H6l-4 3V4z" fill="currentColor" opacity=".95"/>
                    </svg>
                </span>
                <span class="navbar-logo-text">Koar</span>
            </a>

            <div class="navbar-links">
                <a href="{{ route('home') }}"
                   class="navbar-link {{ request()->routeIs('home') ? 'navbar-link--active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
                        <path d="M9 21V12h6v9"/>
                    </svg>
                    <span>Beranda</span>
                </a>
                <a href="{{ route('forums.all') }}"
                   class="navbar-link {{ request()->routeIs('forums.*') ? 'navbar-link--active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Forum</span>
                </a>
                <a href="{{ route('chat.index') }}"
                   class="navbar-link {{ request()->routeIs('chat.*') ? 'navbar-link--active' : '' }}">
                    <span class="navbar-chat-wrap">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        @if(($unreadCount ?? 0) > 0)
                        <span class="navbar-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </span>
                    <span>Chat</span>
                </a>
            </div>

            @if(isset($user))
            <div class="dropdown-wrap" style="margin-left:auto;">
                <button onclick="openMenu('navbar-user-menu', this)" class="navbar-user" style="border:none;cursor:pointer;">
                    <span class="navbar-user-avatar">{{ strtoupper(substr($user->username, 5, 2)) }}</span>
                    <span class="navbar-user-name">{{ $user->username }}</span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="navbar-user-chevron">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </button>
                <div id="navbar-user-menu" class="dropdown-menu" style="min-width:13rem;right:0;top:calc(100% + 8px);">
                    {{-- Info user --}}
                    <div style="padding:.625rem .875rem .5rem;border-bottom:1px solid var(--bd-1);margin-bottom:.25rem;">
                        <p style="font-size:.6875rem;color:var(--tx-4);">Masuk sebagai</p>
                        <p style="font-size:.8125rem;font-weight:600;color:var(--tx-1);margin-top:.125rem;">{{ $user->username }}</p>
                    </div>
                    {{-- Menu items --}}
                    <a href="{{ route('profile.show', $user->username) }}" class="dropdown-item">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil
                    </a>
                    <a href="{{ route('settings.show') }}" class="dropdown-item {{ request()->routeIs('settings.*') ? 'dropdown-item--active' : '' }}">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pengaturan
                    </a>
                </div>
            </div>
            @endif
        </div>
        <div class="navbar-glow"></div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success') || $errors->any())
    <div style="max-width:64rem;margin:0 auto;padding:0 1.25rem;">
        @if(session('success'))
        <div class="flash-success" style="margin-top:.875rem;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="flash-error" style="margin-top:.875rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif
    </div>
    @endif

    <main style="flex:1;max-width:64rem;margin:0 auto;padding:1.75rem 1.25rem;width:100%;">
        @yield('content')
    </main>

    <footer style="border-top:1px solid var(--bd-1);padding:1.5rem;text-align:center;font-size:.75rem;color:var(--tx-4);">
        Koar &mdash; Forum Anonim
    </footer>

    {{-- Bottom navigation (mobile only) --}}
    <nav class="bottom-nav" aria-label="Navigasi utama">
        <a href="{{ route('home') }}"
            class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ request()->routeIs('home') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
                @if(!request()->routeIs('home'))<path d="M9 21V12h6v9"/>@endif
            </svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('forums.all') }}"
            class="bottom-nav-item {{ request()->routeIs('forums.*') || request()->routeIs('forum.*') || request()->routeIs('subforum.*') ? 'active' : '' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Forum</span>
        </a>
        <a href="{{ route('chat.index') }}"
            class="bottom-nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}"
            style="position:relative;">
            <span style="position:relative;display:inline-flex;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ request()->routeIs('chat.*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8">
                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                @if(($unreadCount ?? 0) > 0)
                <span class="bottom-nav-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </span>
            <span>Chat</span>
        </a>
        @if(isset($user))
        <a href="{{ route('profile.show', $user->username) }}"
            class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,var(--ac-3) 0%,#312e81 100%);color:var(--ac-light);font-size:.5rem;font-weight:700;display:flex;align-items:center;justify-content:center;border:{{ request()->routeIs('profile.*') ? '2px solid var(--ac-light)' : '1px solid var(--bd-2)' }};">
                {{ strtoupper(substr($user->username, 5, 2)) }}
            </span>
            <span>Profil</span>
        </a>
        @endif
    </nav>

    {{-- Image lightbox --}}
    <div id="lightbox" class="lightbox" role="dialog" aria-modal="true">
        <button class="lightbox-close" onclick="closeLightbox()" aria-label="Tutup">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <img id="lightbox-img" src="" alt="">
        <span class="lightbox-url" id="lightbox-url"></span>
    </div>

    {{-- Delete confirmation modal --}}
    <div id="delete-modal" class="modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal-box">
            <div class="modal-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <p class="modal-title" id="delete-modal-title">Hapus?</p>
            <p class="modal-desc"  id="delete-modal-desc">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="btn-ghost text-sm" style="padding:.5rem 1rem;">Batal</button>
                <form id="delete-modal-form" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger text-sm" style="padding:.5rem 1rem;">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
    /* ---- Dropdown ---- */
    function openMenu(id, btn) {
        const menu = document.getElementById(id);
        if (!menu) return;
        const isOpen = menu.classList.contains('open');
        closeAllMenus();
        if (!isOpen) {
            menu.classList.add('open');
            if (btn) btn.classList.add('active');
        }
    }
    function closeAllMenus() {
        document.querySelectorAll('.dropdown-menu.open').forEach(m => {
            m.classList.remove('open');
            const btn = m.previousElementSibling;
            if (btn) btn.classList.remove('active');
        });
    }
    document.addEventListener('click', e => { if (!e.target.closest('.dropdown-wrap')) closeAllMenus(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeAllMenus(); closeDeleteModal(); } });

    /* ---- Delete modal ---- */
    function confirmDelete(action, title, desc) {
        document.getElementById('delete-modal-title').textContent = title || 'Hapus?';
        document.getElementById('delete-modal-desc').textContent  = desc  || 'Tindakan ini tidak dapat dibatalkan.';
        document.getElementById('delete-modal-form').action = action;
        document.getElementById('delete-modal').classList.add('open');
        closeAllMenus();
    }
    function closeDeleteModal() { document.getElementById('delete-modal').classList.remove('open'); }
    document.getElementById('delete-modal').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

    /* ---- Edit form ---- */
    function openEditForm(contentId, formId) {
        const content = document.getElementById(contentId);
        const form    = document.getElementById(formId);
        if (!content || !form) return;
        content.style.display = 'none';
        form.classList.add('open');
        form.querySelector('textarea')?.focus();
        closeAllMenus();
    }
    function closeEditForm(contentId, formId) {
        document.getElementById(contentId).style.display = '';
        document.getElementById(formId).classList.remove('open');
    }

    /* ---- Reply form ---- */
    function toggleReplyForm(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const hidden = el.classList.contains('hidden');
        el.classList.toggle('hidden');
        if (hidden) el.querySelector('textarea')?.focus();
    }

    /* ---- Post detail edit ---- */
    function togglePostDetailEdit() {
        document.getElementById('post-detail-content')?.style.display === 'none'
            ? closeEditForm('post-detail-content', 'post-detail-edit')
            : openEditForm('post-detail-content', 'post-detail-edit');
    }

    /* =====================================================
       IMAGE EMBED — Discord-style auto-detect
       ===================================================== */
    (function () {
        function tryEmbed(item) {
            const url         = item.dataset.url;
            const originalUrl = item.dataset.originalUrl || url;
            if (!url) return;

            const img = new Image();

            img.onload = function () {
                img.alt   = '';
                img.title = originalUrl;

                const badge = document.createElement('span');
                badge.className   = 'embed-size';
                badge.textContent = img.naturalWidth + ' × ' + img.naturalHeight;

                item.appendChild(img);
                item.appendChild(badge);
                item.classList.add('loaded');

                hideUrlSpan(originalUrl, item);

                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openLightbox(url, originalUrl, img.naturalWidth, img.naturalHeight);
                });
            };

            img.onerror = function () {
                item.remove();
            };

            img.src = url;
        }

        // Sembunyikan span URL asli di teks setelah gambar berhasil di-embed
        function hideUrlSpan(originalUrl, embedItem) {
            const container = embedItem.closest('.post-card-wrap, .reply-card, article, .widget');
            if (!container) return;
            container.querySelectorAll('.content-url').forEach(function (span) {
                // data-embed-url sudah berisi URL raw (bukan HTML-escaped)
                // karena browser otomatis decode atribut HTML saat dibaca via dataset
                const spanUrl = span.dataset.embedUrl || '';
                if (spanUrl === originalUrl) {
                    span.style.display = 'none';
                }
            });
        }

        function initEmbeds() {
            document.querySelectorAll('.embed-item:not([data-init])').forEach(function (item) {
                item.dataset.init = '1';
                tryEmbed(item);
            });
        }

        // Jalankan saat DOM siap
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initEmbeds);
        } else {
            initEmbeds();
        }

        // Expose untuk re-run setelah konten dinamis dimuat
        window.initEmbeds = initEmbeds;
    })();

    /* ---- Lightbox ---- */
    function openLightbox(url, originalUrl, w, h) {
        const lb  = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const lbl = document.getElementById('lightbox-url');
        const label = originalUrl || url;
        img.src         = url;
        img.alt         = w && h ? w + ' × ' + h + 'px' : '';
        lbl.textContent = label.length > 80 ? label.slice(0, 77) + '…' : label;
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.remove('open');
        document.body.style.overflow = '';
        // Reset src setelah animasi tutup
        setTimeout(() => { document.getElementById('lightbox-img').src = ''; }, 200);
    }

    document.getElementById('lightbox').addEventListener('click', function (e) {
        if (e.target === this) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeAllMenus(); closeDeleteModal(); closeLightbox(); }
    });
    </script>
</body>
</html>
