@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="login-card">
    <!-- LEFT BANNER -->
    <div class="login-banner">
        <div class="banner-body">
            <!-- LOGO & BRAND -->
            <div class="banner-brand-wrapper">
                <div class="banner-logo-box">
                    <img src="{{ asset('images/logo-lp3i.png') }}" alt="Logo LP3I Purwakarta">
                </div>
                <div class="banner-brand-text">
                    <div class="banner-brand-title">LP3I PURWAKARTA</div>
                </div>
            </div>

            <!-- MAIN TITLE & DESCRIPTION -->
            <h1 class="banner-main-title">Evaluasi Kinerja Dosen</h1>
            <p class="banner-description">
                Portal resmi evaluasi perkuliahan untuk mewujudkan standar mutu akademik unggul dan terpercaya di LP3I Purwakarta.
            </p>
        </div>

        <!-- BANNER FOOTER -->
        <div class="banner-footer">
            <div class="banner-divider"></div>
            <div class="banner-copyright">&copy; {{ date('Y') }} LP3I Purwakarta</div>
        </div>
    </div>

    <!-- RIGHT FORM AREA -->
    <div class="login-form-area">
        <div class="form-content-inner">
            <div class="form-header">
                <h2>Selamat Datang 👋</h2>
                <p>Silakan masukkan kredensial akun Anda untuk masuk ke sistem.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓ {{ session('success') }}</span>
                    <button type="button" class="alert-close" onclick="dismissAlert(this.closest('.alert'))" title="Tutup">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <span>✕ {{ session('error') }}</span>
                    <button type="button" class="alert-close" onclick="dismissAlert(this.closest('.alert'))" title="Tutup">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <span>✕ {{ $errors->first() }}</span>
                    <button type="button" class="alert-close" onclick="dismissAlert(this.closest('.alert'))" title="Tutup">✕</button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf

                {{-- KELAS PERKULIAHAN INPUT --}}
                <div id="fieldKelas" class="form-group">
                    <label class="form-label" for="inputKelas">Kelas Perkuliahan</label>
                    <div class="input-group autocomplete-wrapper">
                        <span class="input-icon">🏫</span>
                        <input type="text" name="kelas" id="inputKelas" class="form-control" 
                               placeholder="Pilih kelas (contoh: ASE24-001)" 
                               value="{{ old('kelas') }}" autocomplete="off">
                        <button type="button" class="clear-btn" id="clearKelasBtn" onclick="clearKelas()" title="Hapus pilihan kelas">✕</button>
                        <button type="button" class="dropdown-trigger-btn" onclick="toggleKelasDropdown()" title="Lihat daftar kelas">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="autocomplete-dropdown" id="dropdownKelas"></div>
                    </div>
                </div>

                {{-- NAMA / NIM / NIDN AUTOCOMPLETE --}}
                <div class="form-group">
                    <label class="form-label" for="inputNama">Nama / NIM / NIDN</label>
                    <div class="input-group autocomplete-wrapper">
                        <span class="input-icon" id="iconNama">👤</span>
                        <input type="text" name="name" id="inputNama" class="form-control" 
                               placeholder="Ketik Nama, NIM, atau NIDN" 
                               value="{{ old('name') }}" required autofocus autocomplete="off">
                        <button type="button" class="clear-btn" id="clearNamaBtn" onclick="clearNama()" title="Hapus nama">✕</button>
                        <div class="autocomplete-dropdown" id="dropdownNama"></div>
                    </div>
                </div>

                {{-- PASSWORD INPUT --}}
                <div class="form-group">
                    <label class="form-label" for="inputPassword">Kata Sandi / Password</label>
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="inputPassword" class="form-control" 
                               placeholder="Masukkan kata sandi akun" required>
                        <button type="button" class="pwd-toggle" onclick="togglePassword()" title="Tampilkan/sembunyikan password">
                            <svg id="eyeIcon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ACTIONS ROW -->
                <div class="form-actions-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat sesi saya</span>
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span>Masuk ke Akun</span>
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .form-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        margin-top: 4px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--muted);
        cursor: pointer;
        user-select: none;
        transition: color 0.15s;
    }

    .remember-me:hover {
        color: var(--ink);
    }

    .remember-me input {
        cursor: pointer;
        width: 16px;
        height: 16px;
        border-radius: 4px;
        accent-color: #0284C7;
    }

    .pwd-toggle {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 8px;
        transition: color 0.15s, background 0.15s;
        z-index: 2;
    }

    .pwd-toggle:hover { 
        color: #0284C7;
        background: rgba(56, 189, 248, 0.12);
    }
    
    .autocomplete-wrapper { 
        position: relative; 
    }

    .dropdown-trigger-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: color 0.15s, background 0.15s;
        z-index: 2;
    }

    .dropdown-trigger-btn svg {
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .dropdown-trigger-btn.active svg {
        transform: rotate(180deg);
    }

    .dropdown-trigger-btn:hover {
        color: #0284C7;
        background: rgba(56, 189, 248, 0.12);
    }

    .clear-btn {
        position: absolute;
        right: 38px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(148, 163, 184, 0.25);
        border: none;
        color: var(--muted);
        cursor: pointer;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        display: none;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: all 0.15s ease;
        z-index: 2;
    }

    .clear-btn:hover {
        background: rgba(239, 68, 68, 0.25);
        color: #DC2626;
    }

    /* Ultra-Smooth Autocomplete Dropdown Menu */
    .autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: var(--dropdown-bg);
        border: 1.5px solid var(--card-border);
        border-radius: 14px;
        box-shadow: 0 18px 40px -6px rgba(0, 0, 0, 0.4), 0 0 20px rgba(2, 132, 199, 0.15);
        max-height: 240px;
        overflow-y: auto;
        z-index: 1000;
        padding: 6px;
        -webkit-overflow-scrolling: touch;
        
        /* Smooth Animation States */
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.98);
        transform-origin: top center;
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), 
                    transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), 
                    visibility 0.2s;
        pointer-events: none;
    }

    [data-theme="light"] .autocomplete-dropdown {
        border: 1.5px solid #BAE6FD;
        box-shadow: 0 16px 36px -4px rgba(2, 132, 199, 0.18), 0 4px 12px rgba(0,0,0,0.06);
    }

    .autocomplete-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Modern Custom Scrollbar */
    .autocomplete-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .autocomplete-dropdown::-webkit-scrollbar-track {
        background: transparent;
    }
    .autocomplete-dropdown::-webkit-scrollbar-thumb {
        background: rgba(56, 189, 248, 0.25);
        border-radius: 10px;
    }
    .autocomplete-dropdown::-webkit-scrollbar-thumb:hover {
        background: rgba(56, 189, 248, 0.5);
    }

    .autocomplete-item {
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        transition: background-color 0.15s ease, transform 0.12s ease, color 0.15s ease;
        font-size: 13px;
        user-select: none;
        color: var(--ink);
    }

    .autocomplete-item:hover, .autocomplete-item.active {
        background: rgba(2, 132, 199, 0.15);
        color: #0284C7;
        transform: translateX(2px);
    }

    [data-theme="light"] .autocomplete-item:hover,
    [data-theme="light"] .autocomplete-item.active {
        background: #F0F9FF;
        color: #0284C7;
    }

    .item-main {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow: hidden;
    }

    .item-icon {
        font-size: 15px;
        flex-shrink: 0;
    }

    .item-title {
        font-weight: 600;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-title mark {
        background: rgba(251, 191, 36, 0.35);
        color: var(--ink);
        padding: 0 3px;
        border-radius: 3px;
        font-weight: 700;
    }

    .item-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 5px;
        background: var(--input-bg);
        color: var(--muted);
        border: 1px solid var(--input-border);
        flex-shrink: 0;
        font-family: monospace;
    }

    .item-role-badge {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 2px 6px;
        border-radius: 5px;
    }

    .badge-mahasiswa {
        background: rgba(2, 132, 199, 0.2);
        color: #0284C7;
        border: 1px solid rgba(56, 189, 248, 0.35);
    }

    .badge-dosen {
        background: rgba(16, 185, 129, 0.18);
        color: #16A34A;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .item-kelas-badge {
        background: rgba(2, 132, 199, 0.18);
        color: #0284C7;
    }

    .autocomplete-empty {
        padding: 14px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }
</style>

<script>
    // ==========================================
    // INITIAL LOCAL DATA & IN-MEMORY CACHE
    // ==========================================
    const _INITIAL_KELAS = @json($kelasList ?? []);
    const _cache = {};

    function cacheKey(endpoint, params) {
        return endpoint + '?' + new URLSearchParams(params).toString();
    }

    async function cachedFetch(endpoint, params) {
        const key = cacheKey(endpoint, params);
        if (_cache[key] !== undefined) return _cache[key];
        const res = await fetch(endpoint + '?' + new URLSearchParams(params).toString());
        const data = await res.json();
        _cache[key] = data;
        // Simpan cache 5 menit agar interaksi super instan
        setTimeout(() => delete _cache[key], 300000);
        return data;
    }

    let activeKelasIndex = -1;
    let activeNamaIndex = -1;
    let currentKelasList = _INITIAL_KELAS || [];
    let currentMahasiswaList = [];
    let kelasDebounceTimer = null;
    let namaDebounceTimer = null;

    function togglePassword() {
        const pwd = document.getElementById('inputPassword');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }

    function closeDropdownKelas() {
        const dropKelas = document.getElementById('dropdownKelas');
        const triggerBtn = document.querySelector('.dropdown-trigger-btn');
        if (dropKelas) dropKelas.classList.remove('show');
        if (triggerBtn) triggerBtn.classList.remove('active');
        activeKelasIndex = -1;
    }

    function closeDropdownNama() {
        const dropNama = document.getElementById('dropdownNama');
        if (dropNama) dropNama.classList.remove('show');
        activeNamaIndex = -1;
    }

    function closeAllDropdowns() {
        closeDropdownKelas();
        closeDropdownNama();
    }

    function clearKelas() {
        const inputKelas = document.getElementById('inputKelas');
        inputKelas.value = '';
        document.getElementById('clearKelasBtn').style.display = 'none';
        closeDropdownNama();
        filterKelasLocal('');
    }

    function clearNama() {
        const inputNama = document.getElementById('inputNama');
        inputNama.value = '';
        document.getElementById('clearNamaBtn').style.display = 'none';
        const iconNama = document.getElementById('iconNama');
        if (iconNama) iconNama.textContent = '👤';
        closeDropdownKelas();
        inputNama.focus();
    }

    // ==========================================
    // AUTOCOMPLETE KELAS (INSTANT 0ms LOCAL FILTER & PRODI INFO)
    // ==========================================
    const PRODI_MAP = {
        'ASE': 'Application Software Engineering',
        'OAA': 'Office Administration Automatization',
        'AIS': 'Accounting Information System'
    };

    function getProdiInfo(kelasStr) {
        if (!kelasStr) return { code: '', name: '' };
        const upper = kelasStr.toUpperCase();
        if (upper.includes('ASE')) return { code: 'ASE', name: 'Application Software Engineering' };
        if (upper.includes('OAA')) return { code: 'OAA', name: 'Office Administration Automatization' };
        if (upper.includes('AIS')) return { code: 'AIS', name: 'Accounting Information System' };
        return { code: '', name: '' };
    }

    function filterKelasLocal(q = '') {
        closeDropdownNama();
        const trimmed = (q || '').trim().toLowerCase();
        let filtered = _INITIAL_KELAS;
        if (trimmed) {
            filtered = _INITIAL_KELAS.filter(k => {
                const info = getProdiInfo(k);
                return k.toLowerCase().includes(trimmed) ||
                       info.name.toLowerCase().includes(trimmed) ||
                       info.code.toLowerCase().includes(trimmed);
            });
        }
        currentKelasList = filtered;
        renderKelasDropdown(filtered, q);
    }

    function renderKelasDropdown(items, query = '') {
        const dropdown = document.getElementById('dropdownKelas');
        const triggerBtn = document.querySelector('.dropdown-trigger-btn');

        closeDropdownNama();

        if (!items || items.length === 0) {
            dropdown.innerHTML = '<div class="autocomplete-empty">Tidak ada kelas yang cocok</div>';
            dropdown.classList.add('show');
            if (triggerBtn) triggerBtn.classList.add('active');
            return;
        }

        let html = '';
        items.forEach((kelas, idx) => {
            let highlighted = escapeHtml(kelas);
            if (query.trim()) {
                const regex = new RegExp(`(${escapeRegExp(query.trim())})`, 'gi');
                highlighted = highlighted.replace(regex, '<mark>$1</mark>');
            }

            const info = getProdiInfo(kelas);

            html += `
                <div class="autocomplete-item" onclick="selectKelas('${escapeHtml(kelas)}')" data-index="${idx}">
                    <div class="item-main">
                        <span class="item-icon">🏫</span>
                        <div>
                            <div class="item-title">${highlighted}</div>
                            ${info.name ? `<div style="font-size: 11px; color: var(--muted); margin-top: 2px; font-weight: 400;">${escapeHtml(info.name)}</div>` : ''}
                        </div>
                    </div>
                    <span class="item-badge item-kelas-badge">${escapeHtml(info.code || 'Kelas')}</span>
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.classList.add('show');
        if (triggerBtn) triggerBtn.classList.add('active');
    }

    function toggleKelasDropdown() {
        const dropdown = document.getElementById('dropdownKelas');
        const triggerBtn = document.querySelector('.dropdown-trigger-btn');

        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            if (triggerBtn) triggerBtn.classList.remove('active');
        } else {
            closeDropdownNama();
            filterKelasLocal(document.getElementById('inputKelas').value);
        }
    }

    function selectKelas(kelas) {
        const inputKelas = document.getElementById('inputKelas');
        inputKelas.value = kelas;
        document.getElementById('clearKelasBtn').style.display = 'flex';
        
        closeDropdownKelas();

        const inputNama = document.getElementById('inputNama');
        inputNama.focus();
        fetchUserData('', kelas);
    }

    // ==========================================
    // AUTOCOMPLETE USERS (MAHASISWA & DOSEN DENGAN GELAR)
    // ==========================================
    async function fetchUserData(q = '', kelas = '') {
        try {
            closeDropdownKelas();
            const data = await cachedFetch(`{{ route('auth.autocomplete.user') }}`, { kelas, q });
            currentMahasiswaList = data;
            renderUserDropdown(data, q, kelas);
        } catch (e) {
            console.error('Error fetching users:', e);
        }
    }

    function renderUserDropdown(items, query = '', selectedKelas = '') {
        const dropdown = document.getElementById('dropdownNama');
        closeDropdownKelas();

        if (!items || items.length === 0) {
            if (query.trim().length > 0 || selectedKelas) {
                const msg = selectedKelas 
                    ? `Tidak ada mahasiswa di kelas ${escapeHtml(selectedKelas)} yang cocok` 
                    : 'Tidak ada akun atau data yang cocok';
                dropdown.innerHTML = `<div class="autocomplete-empty">${msg}</div>`;
                dropdown.classList.add('show');
            } else {
                dropdown.classList.remove('show');
            }
            return;
        }

        let html = '';
        items.forEach((item, idx) => {
            let highlightedName = escapeHtml(item.name);
            let code = item.nim || item.nidn || '';
            let highlightedCode = escapeHtml(code);
            if (query.trim()) {
                const regex = new RegExp(`(${escapeRegExp(query.trim())})`, 'gi');
                highlightedName = highlightedName.replace(regex, '<mark>$1</mark>');
                if (highlightedCode) {
                    highlightedCode = highlightedCode.replace(regex, '<mark>$1</mark>');
                }
            }

            const isMhs = item.type === 'mahasiswa';
            const icon = isMhs ? '🎓' : '🧑‍🏫';
            const roleBadgeClass = isMhs ? 'badge-mahasiswa' : 'badge-dosen';
            const roleLabel = isMhs ? 'Mahasiswa' : 'Dosen';

            // Nama lengkap dosen sudah bergelar (item.name)
            html += `
                <div class="autocomplete-item" onclick="selectUser('${escapeHtml(item.name)}', '${escapeHtml(item.kelas || '')}', '${item.type}', '${icon}')" data-index="${idx}">
                    <div class="item-main">
                        <span class="item-icon">${icon}</span>
                        <div>
                            <div class="item-title">${highlightedName}</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:6px; align-items:center;">
                        <span class="item-role-badge ${roleBadgeClass}">${roleLabel}</span>
                        ${code ? `<span class="item-badge">${highlightedCode}</span>` : ''}
                        ${isMhs && !selectedKelas && item.kelas ? `<span class="item-badge item-kelas-badge">${escapeHtml(item.kelas)}</span>` : ''}
                    </div>
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.classList.add('show');
    }

    function selectUser(fullNameWithGelar, kelas, type, icon) {
        document.getElementById('inputNama').value = fullNameWithGelar;
        document.getElementById('clearNamaBtn').style.display = 'flex';
        
        const iconNama = document.getElementById('iconNama');
        if (iconNama && icon) {
            iconNama.textContent = icon;
        }

        closeDropdownNama();

        const inputKelas = document.getElementById('inputKelas');
        if (type === 'mahasiswa' && !inputKelas.value && kelas) {
            inputKelas.value = kelas;
            document.getElementById('clearKelasBtn').style.display = 'flex';
        }

        document.getElementById('inputPassword').focus();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // ==========================================
    // EVENT LISTENERS & KEYBOARD NAV
    // ==========================================
    document.addEventListener('DOMContentLoaded', () => {
        const inputKelas = document.getElementById('inputKelas');
        const inputNama  = document.getElementById('inputNama');
        const inputPassword = document.getElementById('inputPassword');
        const clearKelasBtn = document.getElementById('clearKelasBtn');
        const clearNamaBtn  = document.getElementById('clearNamaBtn');

        if (inputKelas && inputKelas.value) {
            clearKelasBtn.style.display = 'flex';
        }
        if (inputNama && inputNama.value) {
            clearNamaBtn.style.display = 'flex';
        }

        // Kelas input events (Instant local filtering)
        if (inputKelas) {
            inputKelas.addEventListener('input', () => {
                const val = inputKelas.value;
                clearKelasBtn.style.display = val ? 'flex' : 'none';
                closeDropdownNama();
                filterKelasLocal(val);
            });

            inputKelas.addEventListener('focus', () => {
                closeDropdownNama();
                filterKelasLocal(inputKelas.value);
            });
        }

        // Nama input events (Super fast 30ms debounce)
        if (inputNama) {
            inputNama.addEventListener('input', () => {
                const val = inputNama.value;
                clearNamaBtn.style.display = val ? 'flex' : 'none';
                closeDropdownKelas();

                clearTimeout(namaDebounceTimer);
                namaDebounceTimer = setTimeout(() => {
                    const selectedKelas = inputKelas ? inputKelas.value : '';
                    fetchUserData(val, selectedKelas);
                }, 30);
            });

            inputNama.addEventListener('focus', () => {
                closeDropdownKelas();
                const selectedKelas = inputKelas ? inputKelas.value : '';
                if (selectedKelas || inputNama.value.trim()) {
                    fetchUserData(inputNama.value, selectedKelas);
                }
            });
        }

        // Password input focus event
        if (inputPassword) {
            inputPassword.addEventListener('focus', () => {
                closeAllDropdowns();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.autocomplete-wrapper')) {
                closeAllDropdowns();
            }
        });

        // Keyboard navigation for Kelas
        if (inputKelas) {
            inputKelas.addEventListener('keydown', (e) => {
                const dropdown = document.getElementById('dropdownKelas');
                const items = dropdown.querySelectorAll('.autocomplete-item');
                if (!dropdown.classList.contains('show') || items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeKelasIndex = (activeKelasIndex + 1) % items.length;
                    updateActiveItem(items, activeKelasIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeKelasIndex = (activeKelasIndex - 1 + items.length) % items.length;
                    updateActiveItem(items, activeKelasIndex);
                } else if (e.key === 'Enter') {
                    if (activeKelasIndex >= 0 && activeKelasIndex < items.length) {
                        e.preventDefault();
                        items[activeKelasIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('show');
                    const triggerBtn = document.querySelector('.dropdown-trigger-btn');
                    if (triggerBtn) triggerBtn.classList.remove('active');
                }
            });
        }

        // Keyboard navigation for Nama
        if (inputNama) {
            inputNama.addEventListener('keydown', (e) => {
                const dropdown = document.getElementById('dropdownNama');
                const items = dropdown.querySelectorAll('.autocomplete-item');
                if (!dropdown.classList.contains('show') || items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeNamaIndex = (activeNamaIndex + 1) % items.length;
                    updateActiveItem(items, activeNamaIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeNamaIndex = (activeNamaIndex - 1 + items.length) % items.length;
                    updateActiveItem(items, activeNamaIndex);
                } else if (e.key === 'Enter') {
                    if (activeNamaIndex >= 0 && activeNamaIndex < items.length) {
                        e.preventDefault();
                        items[activeNamaIndex].click();
                    }
                } else if (e.key === 'Escape') {
                    dropdown.classList.remove('show');
                }
            });
        }

        function updateActiveItem(items, index) {
            items.forEach((it, i) => {
                if (i === index) {
                    it.classList.add('active');
                    it.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    it.classList.remove('active');
                }
            });
        }
    });
</script>
@endsection


