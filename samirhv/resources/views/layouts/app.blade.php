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

    @stack('styles')

    <title>@yield('title', 'Samirhv') — Projetos</title>

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
