@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="settings-grid" style="max-width:44rem;margin:0 auto;display:grid;grid-template-columns:13rem 1fr;gap:1.75rem;align-items:start;">

    {{-- Sidebar navigasi setting --}}
    <aside class="settings-sidebar">
        <nav style="position:sticky;top:5.5rem;">
            <p class="section-label" style="margin-bottom:.75rem;padding:0 .5rem;">Pengaturan</p>
            <div style="display:flex;flex-direction:column;gap:.125rem;">
                <a href="#akun"
                    style="display:flex;align-items:center;gap:.625rem;padding:.5rem .75rem;border-radius:var(--r-sm);font-size:.8125rem;font-weight:500;color:var(--tx-2);text-decoration:none;transition:background 150ms,color 150ms;"
                    onmouseover="this.style.background='var(--bg-3)';this.style.color='var(--tx-1)'"
                    onmouseout="this.style.background='transparent';this.style.color='var(--tx-2)'"
                    onclick="setActiveNav(this)">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.6;flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Akun
                </a>
                <a href="#bahaya"
                    style="display:flex;align-items:center;gap:.625rem;padding:.5rem .75rem;border-radius:var(--r-sm);font-size:.8125rem;font-weight:500;color:var(--tx-2);text-decoration:none;transition:background 150ms,color 150ms;"
                    onmouseover="this.style.background='var(--bg-3)';this.style.color='var(--tx-1)'"
                    onmouseout="this.style.background='transparent';this.style.color='var(--tx-2)'"
                    onclick="setActiveNav(this)">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:.6;flex-shrink:0;color:var(--danger);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Zona Bahaya
                </a>
            </div>
        </nav>
    </aside>

    {{-- Konten setting --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;min-width:0;">

        {{-- ===== SEKSI AKUN ===== --}}
        <section id="akun">
            <h2 style="font-size:.875rem;font-weight:700;color:var(--tx-1);margin-bottom:1rem;padding-bottom:.625rem;border-bottom:1px solid var(--bd-1);">
                Akun
            </h2>

            {{-- Ganti Username --}}
            <div class="widget" style="padding:1.25rem;">
                <div style="margin-bottom:1rem;">
                    <p style="font-size:.875rem;font-weight:600;color:var(--tx-1);">Username</p>
                    <p style="font-size:.75rem;color:var(--tx-4);margin-top:.2rem;">Username kamu yang tampil di semua post dan balasan.</p>
                </div>

                <form action="{{ route('settings.updateUsername') }}" method="POST"
                    style="display:flex;flex-direction:column;gap:.875rem;">
                    @csrf @method('PUT')

                    <div>
                        <label style="display:block;font-size:.75rem;font-weight:500;color:var(--tx-3);margin-bottom:.375rem;">
                            Username saat ini
                        </label>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div class="avatar avatar-sm">{{ strtoupper(substr($user->username, 0, 2)) }}</div>
                            <code style="font-size:.875rem;font-weight:600;color:var(--tx-1);background:var(--bg-3);padding:.25rem .625rem;border-radius:.375rem;border:1px solid var(--bd-1);">{{ $user->username }}</code>
                        </div>
                    </div>

                    <div>
                        <label for="username" style="display:block;font-size:.75rem;font-weight:500;color:var(--tx-3);margin-bottom:.375rem;">
                            Username baru
                        </label>
                        <input type="text" id="username" name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username baru..."
                            style="width:100%;padding:.625rem .875rem;font-size:.875rem;"
                            minlength="3" maxlength="30"
                            pattern="[a-zA-Z0-9_\-]+"
                            autocomplete="off"
                            required>
                        @error('username')
                        <p style="font-size:.75rem;color:var(--danger);margin-top:.375rem;">{{ $message }}</p>
                        @enderror
                        <p style="font-size:.6875rem;color:var(--tx-4);margin-top:.375rem;">
                            Huruf (a–z, A–Z), angka (0–9), tanda hubung (-) dan garis bawah (_). 3–30 karakter.
                        </p>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary text-sm" style="padding:.5rem 1.25rem;">
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- ===== SEKSI ZONA BAHAYA ===== --}}
        <section id="bahaya">
            <h2 style="font-size:.875rem;font-weight:700;color:var(--danger);margin-bottom:1rem;padding-bottom:.625rem;border-bottom:1px solid rgba(239,68,68,.2);">
                Zona Bahaya
            </h2>

            <div class="widget" style="padding:1.25rem;border-color:rgba(239,68,68,.15);">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1.5rem;flex-wrap:wrap;">
                    <div>
                        <p style="font-size:.875rem;font-weight:600;color:var(--tx-1);">Hapus Akun</p>
                        <p style="font-size:.75rem;color:var(--tx-4);margin-top:.25rem;line-height:1.55;max-width:26rem;">
                            Setelah akun dihapus, tidak bisa dipulihkan. Semua post dan balasan tetap ada dengan nama <strong style="color:var(--tx-3);">[deleted]</strong>.
                        </p>
                    </div>
                    <button type="button" onclick="openDeleteAccountModal()"
                        class="btn-danger text-sm shrink-0" style="padding:.5rem 1.125rem;">
                        Hapus Akun
                    </button>
                </div>
            </div>
        </section>

    </div>
</div>

{{-- Modal konfirmasi hapus akun --}}
<div id="delete-account-modal" class="modal-backdrop">
    <div class="modal-box" style="max-width:24rem;">
        <div class="modal-icon">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <p class="modal-title">Hapus akun secara permanen?</p>
        <p class="modal-desc">
            Akun ini tidak bisa dipulihkan. Post dan balasan yang sudah dibuat akan tetap ada dengan nama
            <strong style="color:var(--tx-2);">[deleted]</strong>.
        </p>
        <p style="font-size:.8125rem;color:var(--tx-3);margin-bottom:.5rem;">
            Ketik <strong style="color:var(--tx-1);">hapus</strong> untuk konfirmasi:
        </p>
        <input type="text" id="delete-account-confirm-input" placeholder="hapus"
            style="width:100%;padding:.5rem .75rem;font-size:.875rem;margin-bottom:1rem;"
            oninput="document.getElementById('delete-account-btn').disabled = this.value.toLowerCase() !== 'hapus'">
        <div class="modal-actions">
            <button type="button" onclick="closeDeleteAccountModal()" class="btn-ghost text-sm" style="padding:.5rem 1rem;">
                Batal
            </button>
            <form action="{{ route('settings.destroy') }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" id="delete-account-btn" class="btn-danger text-sm" style="padding:.5rem 1rem;" disabled>
                    Hapus Akun
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteAccountModal() {
    const input = document.getElementById('delete-account-confirm-input');
    input.value = '';
    document.getElementById('delete-account-btn').disabled = true;
    document.getElementById('delete-account-modal').classList.add('open');
    setTimeout(() => input.focus(), 180);
}
function closeDeleteAccountModal() {
    document.getElementById('delete-account-modal').classList.remove('open');
}
document.getElementById('delete-account-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteAccountModal();
});

// Auto-focus username kalau ada error
@if($errors->has('username'))
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('username')?.focus();
    document.getElementById('akun')?.scrollIntoView({ behavior: 'smooth' });
});
@endif

// Navigasi sidebar aktif
function setActiveNav(el) {
    document.querySelectorAll('aside nav a').forEach(a => {
        a.style.background = 'transparent';
        a.style.color = 'var(--tx-2)';
    });
    el.style.background = 'var(--ac-dim)';
    el.style.color = 'var(--ac-light)';
}
// Set aktif berdasarkan hash
window.addEventListener('hashchange', function() {
    document.querySelectorAll('aside nav a').forEach(a => {
        const match = a.getAttribute('href') === window.location.hash;
        a.style.background = match ? 'var(--ac-dim)' : 'transparent';
        a.style.color = match ? 'var(--ac-light)' : 'var(--tx-2)';
    });
});
</script>

@endsection
