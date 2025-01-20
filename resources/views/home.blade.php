    <!DOCTYPE html>
    <html lang="en">

    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>File Management</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('assets/css/index.css') }}" rel="stylesheet">
        <link href="{{ asset('dist/css/styles.css') }}" rel="stylesheet" />

    </head>
    @if (session('access_token'))
    <script>
        // Store the token in local storage
        localStorage.setItem('access_token', "{{ session('access_token') }}");
        console.log("Access token saved:", localStorage.getItem('access_token'));
    </script>
@endif

    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light" style="height: 60px;">
            <div class="container px-4 px-lg-5">
                <!-- Logo Section -->
                <div>
                    <img src="{{ asset('icons/it-logo.png') }}" alt="IT Faculty Logo" class="it-logo">
                </div>

               <!-- Dropdown Button -->
        <div class="dropdown d-lg-none">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownNavButton"
                data-bs-toggle="dropdown" aria-expanded="false" style="background: white;">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Dropdown Menu -->
            <ul class="dropdown-menu" aria-labelledby="dropdownNavButton"
                style="right: 0; top: 40px; border: 1px solid #ccc; padding: 10px;">
                <li>
                    <a class="dropdown-item" href="{{ route('home') }}">🏠 {{ __('Home') }}</a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}">🔓 {{ __('Logout') }}</a>
                </li>
            </ul>
        </div>

            </div>





            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left-side Navigation -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link">
                            <img src="{{ asset('icons/home-icon.webp') }}" alt="{{ __('Home') }}" class="home-icon">
                        </a>
                    </li>
                </ul>

                <!-- Right-side Navigation -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-primary logout-button" type="submit">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                </ul>

            </div>
            </div>
        </nav>


        <!-- Header-->
        <header class="head_tap ">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center">
                    <h1>File Management</h1>
                    <h2 class="text-white-50">the best site to manage your files</h2>
                </div>
            </div>
        </header>


        @if ($status == 'groups')
            @include('partials.groups')
        @else
            @include('partials.files', ['users' => $users, 'group' => $group])
        @endif
        <!-- Footer-->
        <footer class="footer_tap">
            <div class="container">
                <h4 class="text-center">Copyright Damascus University &copy; 2024</h4>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('dist/js/scripts.js') }}"></script>
        <script src="{{ asset('assets/js/auth.js') }}"></script>


    </body>

    </html>
