<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM Santri')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Link ke Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Font Cinzel untuk splash screen -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <!-- Link ke file CSS utama -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Tambahkan CSS untuk memastikan smooth transition -->
    <style>
        .main-content {
            transition: opacity 0.3s ease-in;
            opacity: 1; /* Pastikan opacity default 1 saat dimuat */
        }
    </style>
</head>
<body>

    <!-- 1. SPLASH SCREEN -->
    <div id="splash-screen" class="splash-screen">
        <div class="splash-content">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PKPPS Riyadlul Jannah" class="splash-logo">
            <p class="splash-title">PKPPS RIYADLUL JANNAH</p>
            <div class="spinner"></div>
        </div>
    </div>
    <!-- END SPLASH SCREEN -->

    <div id="app-wrapper" class="app-wrapper" style="opacity: 0;">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-header">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon-wrapper">
            <i class="fas fa-graduation-cap sidebar-brand-icon"></i>
        </div>
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">PKPPS</span>
            <span class="sidebar-brand-sub">Riyadlul Jannah</span>
        </div>
        <div class="sidebar-brand-ornament">
            <span class="ornament-line"></span>
            <span class="ornament-dot"></span>
            <span class="ornament-line"></span>
        </div>
    </div>
    <button id="sidebar-toggle-btn-mobile" class="sidebar-toggle-btn-mobile">
        <i class="fas fa-times"></i>
    </button>
</div>
            <ul class="sidebar-menu">
                {{-- Sidebar berdasarkan guard yang aktif --}}
                @if(auth()->check())
                    @if(auth()->user()->isAdmin())
                        @include('layouts.admin-sidebar')
                    @endif
                @elseif(auth('santri')->check())
                    @include('layouts.santri-wali-sidebar')
                @endif
            </ul>
        </aside>
        <!-- END SIDEBAR -->

        <!-- MAIN CONTENT AREA -->
        <div class="main-content-wrapper">
            <!-- HEADER/NAVBAR -->
            <header class="main-header">
                <button id="sidebar-toggle-btn" class="sidebar-toggle-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info">
                    @if(auth()->check())
                        <li class="logout-item">
                            <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" 
                                        style="border: none; background: none; color: #6FBA9D; cursor: pointer; padding: 4px 8px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px; text-decoration: none; font-weight: 500;"
                                        title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    @elseif(auth('santri')->check())
                        <li class="logout-item">
                            <form method="POST" action="{{ route('santri.logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" 
                                        style="border: none; background: none; color: #6FBA9D; cursor: pointer; padding: 4px 8px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px; text-decoration: none; font-weight: 500;"
                                        title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    @else
                        <span>Selamat Datang!</span>
                    @endif
                </div>
            </header>
            <!-- END HEADER/NAVBAR -->

            <!-- CONTENT -->
            <main id="main-content" class="main-content">
                @yield('content')
            </main>
            <!-- END CONTENT -->
        </div>
        <!-- END MAIN CONTENT AREA -->
    </div>

    <!-- SCRIPT JS UNTUK SPLASH & INTERAKSI DASAR -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const splashScreen = document.getElementById('splash-screen');
            const appWrapper = document.getElementById('app-wrapper');
            const sidebar = document.getElementById('sidebar');
            const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
            const sidebarToggleBtnMobile = document.getElementById('sidebar-toggle-btn-mobile');
            const mainContent = document.getElementById('main-content');

            // Fungsi untuk splash screen (hanya muncul sekali per session)
            if (!sessionStorage.getItem('splash_shown')) {
                setTimeout(() => {
                    splashScreen.style.opacity = '0';
                    setTimeout(() => {
                        splashScreen.style.display = 'none';
                        appWrapper.style.opacity = '1';
                    }, 500);
                    sessionStorage.setItem('splash_shown', 'true');
                }, 2000);
            } else {
                splashScreen.style.display = 'none';
                appWrapper.style.opacity = '1';
            }

            // Smooth Scroll
            document.documentElement.style.scrollBehavior = 'smooth';

            // Sidebar Toggle (Desktop/Tablet)
            sidebarToggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                    appWrapper.classList.toggle('sidebar-collapsed');
                } else {
                    // Mobile: Toggle aktifkan sidebar
                    sidebar.classList.toggle('mobile-active');
                }
            });

            // Sidebar Toggle (Mobile) - Tombol Close di dalam Sidebar
            sidebarToggleBtnMobile.addEventListener('click', function() {
                sidebar.classList.remove('mobile-active');
            });
            
            // Close sidebar saat klik di luar (Mobile)
            appWrapper.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-active') && !sidebar.contains(e.target) && !sidebarToggleBtn.contains(e.target)) {
                    sidebar.classList.remove('mobile-active');
                }
            });


            // Smooth Transition Saat Klik Link Sidebar
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                if (link.getAttribute('href') && !link.getAttribute('href').startsWith('javascript:')) {
                    link.addEventListener('click', function(e) {
                        // Cek apakah bukan tombol logout (yang akan menggunakan form POST)
                        if (!this.closest('form')) {
                            e.preventDefault();
                            const targetUrl = this.href;

                            // Fade out konten utama
                            mainContent.style.opacity = '0';

                            // Tunggu sebentar lalu redirect
                            setTimeout(() => {
                                window.location.href = targetUrl;
                            }, 300); // Durasi fade-out
                        }
                    });
                }
            });

            // Submenu Toggle
            document.querySelectorAll('.menu-toggle > .menu-parent').forEach(parent => {
                parent.addEventListener('click', function() {
                    this.closest('.menu-toggle').classList.toggle('active');
                });
            });

            // Inisiasi state sidebar mobile jika page load di mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
                appWrapper.classList.remove('sidebar-collapsed');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
