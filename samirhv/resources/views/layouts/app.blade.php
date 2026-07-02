<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')">
    <meta name="author" content="Samir Hanna Verza">

    <meta property="og:title" content="@yield('title', 'Samirhv') | Projetos">
    <meta property="og:description" content="@yield('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('vendor/canvas/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/blog-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/custom.css') }}">

    <style>
        /* ── Menu "Projetos" — dropdown que abre ao passar o mouse ──────── */
        .cp-dropdown-parent { position: relative; }
        .cp-dropdown { list-style: none; margin: 0; padding: 8px; }
        .cp-dropdown > li { margin: 0; }
        .cp-dropdown a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 9px; text-decoration: none;
            color: #cbd5e1; transition: background-color .15s ease, color .15s ease;
        }
        .cp-dropdown a:hover { background-color: rgba(99,102,241,0.12); color: #f1f5f9; }
        .cp-dropdown a > i { font-size: 1.05rem; color: #6366f1; width: 22px; text-align: center; flex-shrink: 0; }
        .cp-dd-text { display: flex; flex-direction: column; line-height: 1.25; }
        .cp-dd-text strong { font-family: 'Inter', sans-serif; font-weight: 600; font-size: 0.92rem; color: inherit; }
        .cp-dd-text small { font-family: 'JetBrains Mono', monospace; font-size: 0.66rem; color: #64748b; }
        .cp-caret { font-size: 0.62rem; margin-left: 6px; opacity: .65; transition: transform .2s ease; }

        /* Desktop (>=992px): flutuante, escondido, aparece no hover de "Projetos" */
        @media (min-width: 992px) {
            .cp-dropdown {
                position: absolute; top: 100%; left: 50%; margin-top: 10px;
                transform: translateX(-50%) scale(.97); transform-origin: top center;
                min-width: 252px;
                background-color: #12121c;
                border: 1px solid rgba(99,102,241,0.18);
                border-radius: 12px;
                box-shadow: 0 18px 44px rgba(0,0,0,0.5);
                opacity: 0; visibility: hidden; pointer-events: none;
                transition: opacity .18s ease, transform .18s ease, visibility .18s;
                z-index: 999;
            }
            /* ponte invisível pra não perder o hover ao descer o mouse */
            .cp-dropdown::before { content: ''; position: absolute; left: 0; right: 0; bottom: 100%; height: 12px; }
            .cp-dropdown-parent:hover > .cp-dropdown {
                opacity: 1; visibility: visible; pointer-events: auto;
                transform: translateX(-50%) scale(1);
            }
            .cp-dropdown-parent:hover .cp-caret { transform: rotate(180deg); }
        }

        /* Mobile (hamburger aberto): lista estática indentada sob "Projetos" */
        @media (max-width: 991.98px) {
            .cp-dropdown { padding-left: 16px; }
            .cp-caret { display: none; }
        }
    </style>

    @stack('styles')

    <title>@yield('title', 'Samirhv') — Projetos</title>

    {{-- Matomo Analytics (self-hosted) — só renderiza com MATOMO_* configurado. --}}
    @include('partials.matomo')
</head>

<body class="stretched dark">

    <div id="wrapper">

        <!-- Header -->
        <header id="header" class="transparent-header dark no-sticky">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row justify-content-lg-between">

                        <div id="logo" class="col-lg-2 order-lg-2 mx-lg-auto justify-content-lg-center">
                            <a href="{{ route('home') }}">
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em;">samirhv<span style="color: #6366f1;">.</span></span>
                            </a>
                        </div>

                        <div class="primary-menu-trigger" data-target=".primary-menu">
                            <button class="cnvs-hamburger" type="button" title="Abrir menu">
                                <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                            </button>
                        </div>

                        <div class="col-lg-5 d-lg-flex justify-content-end order-lg-last">
                            <div class="header-misc">
                                <a href="{{ route('admin.dashboard') }}" class="button cp-header-cta rounded m-0" data-class="down-lg:button-small">
                                    <span>Admin</span> <i class="bi-arrow-right ms-2 me-0 d-none d-lg-inline"></i>
                                </a>
                            </div>
                        </div>

                        <nav class="primary-menu col-lg-5 order-lg-1 on-click" aria-label="Navegação principal">
                            <ul class="menu-container">
                                <li class="menu-item"><a class="menu-link" href="{{ route('home') }}"><div>Início</div></a></li>
                                @php $navProjects = $navProjects ?? collect(); @endphp
                                @if($navProjects->isNotEmpty())
                                <li class="menu-item cp-dropdown-parent">
                                    <a class="menu-link" href="#" onclick="return false;"><div>Projetos <i class="bi-chevron-down cp-caret"></i></div></a>
                                    <ul class="cp-dropdown">
                                        @foreach($navProjects as $navp)
                                        <li>
                                            @if($navp->redirectsToSite())
                                                <a href="{{ $navp->external_url }}" target="_blank" rel="noopener">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-up-right-from-square' }}"></i>
                                                    <span class="cp-dd-text"><strong>{{ $navp->title }}</strong><small>{{ preg_replace('#^www\.#', '', parse_url($navp->external_url, PHP_URL_HOST)) }}&nbsp;↗</small></span>
                                                </a>
                                            @else
                                                <a href="{{ route('project.show', $navp) }}">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-box-open' }}"></i>
                                                    <span class="cp-dd-text"><strong>{{ $navp->title }}</strong><small>{{ $navp->category ?: 'download' }}</small></span>
                                                </a>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </li>
                                @else
                                <li class="menu-item"><a class="menu-link" href="{{ route('downloads') }}"><div>Projetos</div></a></li>
                                @endif
                                <li class="menu-item"><a class="menu-link" href="{{ route('downloads') }}"><div>Downloads</div></a></li>
                            </ul>
                        </nav>

                    </div>
                </div>
            </div>
            <div class="header-wrap-clone"></div>
        </header>

        @yield('content')

        <!-- Footer -->
        <footer id="footer" class="dark">
            <div class="container">
                <div class="footer-widgets-wrap">
                    <div class="row">
                        <div class="col-6 col-lg-4">
                            <p style="font-family: 'JetBrains Mono', monospace; font-size: 1.5rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; margin-bottom: 1rem;">samirhv<span style="color: #6366f1;">.</span></p>
                            <p class="text-white-50">Projetos e ferramentas de Samir Hanna Verza disponibilizados para download. Tecnologia, desenvolvimento e Linux.</p>
                            <div class="d-flex">
                                <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="social-icon bg-white bg-opacity-25 border-transparent rounded-circle si-small h-bg-github" aria-label="GitHub">
                                    <i class="fa-brands fa-github"></i>
                                    <i class="fa-brands fa-github"></i>
                                </a>
                                <a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="social-icon bg-white bg-opacity-25 border-transparent rounded-circle si-small h-bg-instagram" aria-label="Instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                                <a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="social-icon bg-white bg-opacity-25 border-transparent rounded-circle si-small h-bg-linkedin" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin"></i>
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <h4>Navegação</h4>
                            <ul class="list-unstyled mb-0 text-small">
                                <li class="mb-2"><a href="{{ route('home') }}" class="text-light">Início</a></li>
                                <li><a href="{{ route('downloads') }}" class="text-light">Downloads</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-3 mt-5 mt-lg-0">
                            <h4>Contato</h4>
                            <ul class="list-unstyled mb-0 text-small">
                                <li class="mb-2"><a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="text-light">GitHub</a></li>
                                <li class="mb-2"><a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="text-light">Instagram</a></li>
                                <li><a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="text-light">LinkedIn</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div id="copyrights" class="dark">
                <div class="container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-md-6 text-white-50">
                            &copy; {{ date('Y') }} Samirhv. Todos os direitos reservados.
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end mt-4 mt-md-0">
                            <div class="copyrights-menu copyright-links mb-0">
                                <a href="{{ route('home') }}" class="text-white-50">Início</a>/<a href="{{ route('downloads') }}" class="text-white-50">Downloads</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </footer>

    </div><!-- #wrapper end -->

    <script src="{{ asset('vendor/canvas/js/plugins.min.js') }}"></script>
    <script src="{{ asset('vendor/canvas/js/functions.bundle.js') }}"></script>

    @stack('scripts')

</body>
</html>
