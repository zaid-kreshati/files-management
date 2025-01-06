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
           <link href="{{ asset('dist/css/styles.css') }}" rel="stylesheet" /> <!-- Add your CSS files here -->

    </head>

    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light ">
            <div class="container px-4 px-lg-5">
                <div>
                    <img src="{{ asset('icons/it-logo.png') }}" alt="IT Faculty Logo" class="it-logo">
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">

                        <a href="{{ route('home') }}">
                            <img src="{{ asset('icons/home-icon.webp') }}" alt="{{ __('Home') }}" class="home-icon">
                        </a>
                    </ul>

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <form class="logout-button" id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-dark" type="submit" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                            </button>
                        </form>
                    </ul>

                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">File Management</h1>
                    <p class="lead fw-normal text-white-50 mb-0">the best site to manage your files</p>
                </div>
            </div>
        </header>


        @if ($status=="groups")

    <!-- Search Input Field -->
    <div class="search-container">
        <input type="text" id="searchBox" placeholder="{{ __('search_categories') }}" autocomplete="off">
        <img src="{{ asset('icons/search_icon.png') }}" alt="{{ __('search_icon_alt') }}" class="search-icon">
    </div>
    <button id="createGroupBtn" class="create-group-btn" data-bs-toggle="modal" data-bs-target="#createGroupModal">Create New Group</button>


            @include('partials.groups')
        @else
            @include('partials.files', ['users' => $users, 'group' => $group])
        @endif
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container"><p class="m-0 text-center text-white">Copyright Damascus University &copy;  2024</p></div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('dist/js/scripts.js') }}"></script>

    </body>
</html>
