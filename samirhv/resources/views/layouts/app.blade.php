<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')">
    <meta name="author" content="Samir Hanna Verza">
    <meta name="theme-color" content="#0b0b11">

    <meta property="og:title" content="@yield('title', 'Samirhv') | Projetos">
    <meta property="og:description" content="@yield('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="{{ asset('vendor/canvas/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/blog-theme.css') }}">

    @stack('styles')

    <title>@yield('title', 'Samirhv') — Projetos</title>

    {{-- Matomo Analytics (self-hosted) — só renderiza com MATOMO_* configurado. --}}
    @include('partials.matomo')
</head>

<body class="stretched dark">

    <div id="wrapper">

        <!-- Header -->
        <header id="header" class="transparent-header dark">
            <div id="header-wrap">
                <div class="container">
                    <div class="header-row justify-content-lg-between">

                        <div id="logo" class="col-lg-2 order-lg-2 mx-lg-auto justify-content-lg-center">
                            <a href="{{ route('home') }}" aria-label="Samirhv — início">
                                <span class="s-logo">samirhv<b>.</b></span>
                            </a>
                        </div>

                        <div class="primary-menu-trigger" data-target=".primary-menu">
                            <button class="cnvs-hamburger" type="button" title="Abrir menu" aria-label="Abrir menu">
                                <span class="cnvs-hamburger-box"><span class="cnvs-hamburger-inner"></span></span>
                            </button>
                        </div>

                        <div class="col-lg-5 d-lg-flex justify-content-end order-lg-last">
                            <div class="header-misc">
                                <a href="{{ route('admin.dashboard') }}" class="s-btn s-btn--ghost s-btn--sm m-0">
                                    <span>Admin</span> <i class="bi-arrow-right d-none d-lg-inline"></i>
                                </a>
                            </div>
                        </div>

                        <nav class="primary-menu col-lg-5 order-lg-1 on-click" aria-label="Navegação principal">
                            <ul class="menu-container">
                                <li class="menu-item"><a class="menu-link" href="{{ route('home') }}"><div>Início</div></a></li>
                                @php $navProjects = $navProjects ?? collect(); @endphp
                                @if($navProjects->isNotEmpty())
                                <li class="menu-item s-dd-parent">
                                    <a class="menu-link" href="#" onclick="return false;"><div>Projetos <i class="bi-chevron-down s-caret"></i></div></a>
                                    <ul class="s-dd">
                                        @foreach($navProjects as $navp)
                                        <li>
                                            @if($navp->redirectsToSite())
                                                <a href="{{ $navp->external_url }}" target="_blank" rel="noopener">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-up-right-from-square' }}"></i>
                                                    <span class="s-dd-text"><strong>{{ $navp->title }}</strong><small>{{ preg_replace('#^www\.#', '', parse_url($navp->external_url, PHP_URL_HOST)) }}&nbsp;↗</small></span>
                                                </a>
                                            @else
                                                <a href="{{ route('project.show', $navp) }}">
                                                    <i class="{{ $navp->icon ?: 'fa-solid fa-box-open' }}"></i>
                                                    <span class="s-dd-text"><strong>{{ $navp->title }}</strong><small>{{ $navp->category ?: 'download' }}</small></span>
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
                <div class="footer-widgets-wrap py-5">
                    <div class="row g-4">
                        <div class="col-12 col-lg-5">
                            <p class="s-logo" style="font-size:1.5rem; margin-bottom:1rem;">samirhv<b>.</b></p>
                            <p class="s-body s-muted" style="max-width:34ch; font-size:0.94rem;">Projetos e ferramentas de Samir Hanna Verza, disponibilizados para download. Tecnologia, desenvolvimento e Linux.</p>
                            <div class="d-flex gap-2 mt-3">
                                <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-icon" style="width:40px;height:40px;font-size:1rem;" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
                                <a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="s-icon" style="width:40px;height:40px;font-size:1rem;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                <a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="s-icon" style="width:40px;height:40px;font-size:1rem;" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 offset-lg-1">
                            <h4>Navegação</h4>
                            <ul class="list-unstyled mb-0" style="line-height:2.1;">
                                <li><a href="{{ route('home') }}" class="s-flink">Início</a></li>
                                <li><a href="{{ route('downloads') }}" class="s-flink">Downloads</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-lg-3">
                            <h4>Contato</h4>
                            <ul class="list-unstyled mb-0" style="line-height:2.1;">
                                <li><a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-flink">GitHub</a></li>
                                <li><a href="https://instagram.com/samirhvbr" target="_blank" rel="noopener" class="s-flink">Instagram</a></li>
                                <li><a href="https://www.linkedin.com/in/samirhv/" target="_blank" rel="noopener" class="s-flink">LinkedIn</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div id="copyrights" class="dark">
                <div class="container">
                    <div class="row align-items-center justify-content-between py-3">
                        <div class="col-md-6 s-meta">
                            &copy; {{ date('Y') }} Samirhv. Todos os direitos reservados.
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end mt-2 mt-md-0 s-meta">
                            <a href="{{ route('home') }}" class="s-flink">Início</a>
                            <span style="margin:0 10px; color:var(--s-faint);">·</span>
                            <a href="{{ route('downloads') }}" class="s-flink">Downloads</a>
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
