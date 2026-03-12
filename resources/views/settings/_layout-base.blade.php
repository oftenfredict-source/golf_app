<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Settings') - Golf Club Management System</title>
    <meta name="description" content="@yield('description', 'Settings - Golf Club Management System')" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
      * {
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <style>
      * {
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      }
      body {
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      }
      
      /* Custom Sidebar Theme: Light Red (#940000 base) */
      .layout-menu, 
      .bg-menu-theme {
        background-color: #fdf2f2 !important;
      }

      .menu-inner > .menu-item.active > .menu-link {
        background-color: rgba(148, 0, 0, 0.08) !important;
        color: #940000 !important;
      }

      .menu-inner > .menu-item.active > .menu-link i,
      .menu-inner > .menu-item.active > .menu-link div {
        color: #940000 !important;
      }

      .menu-header {
        color: rgba(148, 0, 0, 0.6) !important;
      }

      .menu-inner .menu-item .menu-link:hover {
        background-color: rgba(148, 0, 0, 0.04) !important;
      }
      
      /* Sidebar Menu Styling - Main Items Bold */
      .menu-inner > .menu-item > .menu-link {
        font-weight: 700 !important;
        font-size: 0.95rem !important;
      }
      
      .menu-inner > .menu-item > .menu-link > div {
        font-weight: 700 !important;
      }
      
      /* Submenu Items - Light Weight with Indentation */
      .menu-inner .menu-sub .menu-item .menu-link {
        font-weight: 300 !important;
        font-size: 0.875rem !important;
        padding-left: 3rem !important;
      }
      
      .menu-inner .menu-sub .menu-item .menu-link div {
        font-weight: 300 !important;
        color: rgba(109, 103, 119, 0.8) !important;
      }
      
      .menu-inner .menu-sub .menu-item .menu-link > div {
        font-weight: 300 !important;
        color: rgba(109, 103, 119, 0.8) !important;
      }
      
      /* Header/Navbar Styling */
      #layout-navbar {
        background-color: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(148, 0, 0, 0.1);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
      }

      .navbar-detached {
        margin-top: 0 !important;
        border-radius: 0 !important;
      }

      #layout-navbar .ri-search-line,
      #layout-navbar .ri-notification-line {
        color: #940000 !important;
      }

      .search-input-wrapper {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 4px 12px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
      }

      .search-input-wrapper:focus-within {
        background-color: #fff;
        border-color: #940000;
        box-shadow: 0 0 0 0.2rem rgba(148, 0, 0, 0.1);
      }

      .navbar-nav .dropdown-notifications .nav-link {
        color: #940000 !important;
      }

      .navbar-nav .dropdown-user .avatar {
        border: 2px solid rgba(148, 0, 0, 0.1);
        padding: 2px;
        border-radius: 50%;
        transition: all 0.3s ease;
      }

      .navbar-nav .dropdown-user:hover .avatar {
        border-color: #940000;
      }
      
      /* Active Submenu Items */
      .menu-inner .menu-sub .menu-item.active .menu-link div,
      .menu-inner .menu-sub .menu-item.active .menu-link > div {
        font-weight: 400 !important;
        color: #940000 !important;
      }
      
      /* Hover Effect for Submenu */
      .menu-inner .menu-sub .menu-item .menu-link:hover div,
      .menu-inner .menu-sub .menu-item .menu-link:hover > div {
        color: #940000 !important;
      }
      
      /* Ensure all submenu text is light */
      .menu-sub .menu-link,
      .menu-sub .menu-link *,
      .menu-sub .menu-link div {
        font-weight: 300 !important;
      }
      
      .menu-sub .menu-item.active .menu-link,
      .menu-sub .menu-item.active .menu-link *,
      .menu-sub .menu-item.active .menu-link div {
        font-weight: 400 !important;
      }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>
  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        @include('dashboard.partials.menu')
        
        <div class="layout-page">
          @include('dashboard.partials.navbar')

          <div class="content-wrapper">
            <div class="container-fluid flex-grow-1 container-p-y">
              @yield('content')
            </div>
            
            @include('dashboard.partials.footer')
            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    
    @include('dashboard.partials.scripts')
    @stack('scripts')
  </body>
</html>

