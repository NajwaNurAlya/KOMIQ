<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KOMIQ - Platform Baca & Publikasi Komik')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: #131b2e;
            --bg-card: #1a233a;
            --bg-card-hover: #222d4a;
            --border-color: #2a3656;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-primary-hover: #4f46e5;
            --accent-secondary: #ec4899;
            --status-draft-bg: rgba(100, 116, 139, 0.2);
            --status-draft-text: #cbd5e1;
            --status-pending-bg: rgba(245, 158, 11, 0.2);
            --status-pending-text: #fbbf24;
            --status-approved-bg: rgba(16, 185, 129, 0.2);
            --status-approved-text: #34d399;
            --status-rejected-bg: rgba(239, 68, 68, 0.2);
            --status-rejected-text: #f87171;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        a {
            color: var(--accent-primary);
            text-decoration: none;
            transition: color 0.2s;
        }

        a:hover {
            color: #818cf8;
        }

        /* Navbar */
        .navbar {
            background-color: rgba(19, 27, 46, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 0.85rem 1.5rem;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #818cf8 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 1.25rem;
            align-items: center;
        }

        .nav-link {
            color: var(--text-secondary);
            font-size: 0.925rem;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            background-color: rgba(99, 102, 241, 0.15);
        }

        /* Container */
        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 2rem auto;
            padding: 0 1.5rem;
            flex: 1;
        }

        /* Flash Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
        }

        .alert-warning {
            background-color: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fbbf24;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #f87171;
        }

        /* Card Component */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-draft { background: var(--status-draft-bg); color: var(--status-draft-text); border: 1px solid rgba(148, 163, 184, 0.3); }
        .badge-pending { background: var(--status-pending-bg); color: var(--status-pending-text); border: 1px solid rgba(245, 158, 11, 0.4); }
        .badge-approved { background: var(--status-approved-bg); color: var(--status-approved-text); border: 1px solid rgba(16, 185, 129, 0.4); }
        .badge-rejected { background: var(--status-rejected-bg); color: var(--status-rejected-text); border: 1px solid rgba(239, 68, 68, 0.4); }
        .badge-premium { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary) 0%, #4f46e5 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
        }

        .btn-secondary {
            background: var(--bg-card-hover);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: #2b395d;
            color: #fff;
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            background-color: var(--bg-secondary);
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="brand-logo">
                <span>⚡</span> KOMIQ
            </a>
            <div class="nav-links">
                <a href="{{ route('admin.chapter-reviews.index') }}" class="nav-link">
                    🛡️ Admin Moderasi
                </a>
                <a href="/" class="nav-link">
                    🏠 Beranda
                </a>
                @auth
                    <!-- Tombol Keluar Sesuai Gambar 3 -->
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                            Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <span>⚠️</span>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>❌</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <span>⚠️</span>
                <div>
                    <strong>Terdapat kesalahan pengisian data:</strong>
                    <ul style="margin-top: 0.5rem; padding-left: 1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} KOMIQ - Platform Webtoon & Publikasi Komik (Tugas Pemrograman Web Lanjut Kelompok 5)</p>
    </footer>

    @yield('scripts')
</body>
</html>