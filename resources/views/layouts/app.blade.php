<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KOMIQ - Platform Baca & Publikasi Komik')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        :root {
            --bg-primary: #121318;
            --bg-secondary: #181920;
            --bg-card: #1a1b23;
            --bg-card-hover: #22232d;
            --border-color: #27272a;
            --text-primary: #f4f4f5;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --accent-primary: #dc2626;
            --accent-primary-hover: #b91c1c;
            --accent-text: #f87171;
            --status-draft-bg: rgba(113, 113, 122, 0.2);
            --status-draft-text: #d4d4d8;
            --status-pending-bg: rgba(245, 158, 11, 0.15);
            --status-pending-text: #fbbf24;
            --status-approved-bg: rgba(16, 185, 129, 0.15);
            --status-approved-text: #34d399;
            --status-rejected-bg: rgba(239, 68, 68, 0.15);
            --status-rejected-text: #f87171;
        }

        @layer base {
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: var(--bg-primary);
                color: var(--text-primary);
                line-height: 1.6;
            }

            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
                font-weight: 700;
                letter-spacing: -0.02em;
            }

            a {
                color: var(--accent-text);
                transition: color 0.2s;
            }

            ::selection {
                background: var(--accent-primary);
                color: #fff;
            }
        }

        @layer components {
            .font-brand { font-family: 'Outfit', sans-serif; }

            .main-container {
                max-width: 1200px;
                width: 100%;
                margin: 2rem auto;
                padding: 0 1.5rem;
                flex: 1;
            }

            .card {
                background: var(--bg-card);
                border: 1px solid var(--border-color);
                border-radius: 16px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .alert {
                padding: 0.9rem 1.1rem;
                border-radius: 10px;
                margin-bottom: 1.5rem;
                font-size: 0.875rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.35); color: #34d399; }
            .alert-warning { background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.35); color: #fbbf24; }
            .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.35); color: #f87171; }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.2rem 0.7rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .badge-draft { background: var(--status-draft-bg); color: var(--status-draft-text); border: 1px solid rgba(161, 161, 170, 0.3); }
            .badge-pending { background: var(--status-pending-bg); color: var(--status-pending-text); border: 1px solid rgba(245, 158, 11, 0.4); }
            .badge-approved { background: var(--status-approved-bg); color: var(--status-approved-text); border: 1px solid rgba(16, 185, 129, 0.4); }
            .badge-rejected { background: var(--status-rejected-bg); color: var(--status-rejected-text); border: 1px solid rgba(239, 68, 68, 0.4); }
            .badge-premium { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.6rem 1.15rem;
                border-radius: 8px;
                font-size: 0.875rem;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.2s, color 0.2s;
            }
            .btn-primary { background: var(--accent-primary); color: #fff; }
            .btn-primary:hover { background: var(--accent-primary-hover); color: #fff; }
            .btn-success { background: #059669; color: #fff; }
            .btn-success:hover { background: #047857; color: #fff; }
            .btn-danger { background: #dc2626; color: #fff; }
            .btn-danger:hover { background: #b91c1c; color: #fff; }
            .btn-secondary { background: #27272a; color: var(--text-primary); border: 1px solid #3f3f46; }
            .btn-secondary:hover { background: #3f3f46; color: #fff; }
            .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.8rem; border-radius: 6px; }

            .form-group { margin-bottom: 1.25rem; }
            .form-label {
                display: block;
                margin-bottom: 0.4rem;
                font-size: 0.8rem;
                font-weight: 600;
                color: #d4d4d8;
            }
            .form-control {
                width: 100%;
                padding: 0.65rem 0.9rem;
                background-color: var(--bg-primary);
                border: 1px solid #3f3f46;
                border-radius: 8px;
                color: var(--text-primary);
                font-size: 0.875rem;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .form-control::placeholder { color: var(--text-muted); }
            .form-control:focus {
                outline: none;
                border-color: var(--accent-primary);
                box-shadow: 0 0 0 1px var(--accent-primary);
            }
            .form-control.is-invalid { border-color: #ef4444; }
            .form-error { margin-top: 0.3rem; font-size: 0.75rem; color: #f87171; }

            .table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
            .table th {
                padding: 0.9rem 1.25rem;
                background: var(--bg-secondary);
                color: var(--text-muted);
                font-weight: 600;
                text-align: left;
                border-bottom: 1px solid var(--border-color);
            }
            .table td { padding: 1.25rem; border-bottom: 1px solid var(--border-color); }
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col">
    <header class="bg-[var(--bg-secondary)] border-b border-[var(--border-color)] sticky top-0 z-50">
        <div class="max-w-[1200px] mx-auto px-6 py-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-2xl font-black font-brand tracking-wider text-white hover:text-white">KOMIQ<span class="text-red-500">.</span></a>
                <nav class="hidden md:flex items-center gap-6 text-xs font-semibold">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-white' : 'text-zinc-400' }} hover:text-white">Beranda</a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.chapter-reviews.index') }}" class="{{ request()->routeIs('admin.*') ? 'text-white' : 'text-zinc-400' }} hover:text-white">Moderasi</a>
                        @endif
                    @endauth
                </nav>
            </div>

            <div class="hidden sm:flex flex-1 max-w-xs mx-4 relative">
                <input type="text" placeholder="Cari..." disabled
                    class="w-full bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-full px-4 py-1.5 text-xs text-zinc-400 placeholder-zinc-600 cursor-default">
                <x-icon name="search" class="absolute right-3 top-2 text-zinc-600 text-xs" />
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <span class="flex items-center gap-1.5 bg-[var(--bg-primary)] border border-[var(--border-color)] px-3 py-1 rounded-full text-xs font-bold text-amber-300">
                        <x-icon name="coins" /> {{ number_format(auth()->user()->coin_balance) }} Koin
                    </span>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2 text-xs font-bold text-white hover:text-white">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-red-600">
                        <span class="hidden md:inline">{{ auth()->user()->name }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="main-container">
        @if(session('success'))
            <div class="alert alert-success">
                <x-icon name="circle-check" />
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <x-icon name="triangle-alert" />
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <x-icon name="circle-x" />
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @sectionMissing('inline-errors')
            @if ($errors->any())
                <div class="alert alert-error">
                    <x-icon name="triangle-alert" />
                    <div>
                        <strong>Terdapat kesalahan pengisian data:</strong>
                        <ul class="list-disc mt-2 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-[var(--border-color)] bg-[var(--bg-secondary)] py-6 text-center text-xs text-zinc-400">
        <p class="font-bold text-zinc-300">KOMIQ &copy; {{ date('Y') }}</p>
    </footer>
    <div class="h-1.5 bg-red-600"></div>

    @yield('scripts')
</body>
</html>
