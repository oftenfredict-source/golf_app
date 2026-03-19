<!doctype html>
<html lang="en" class="layout-menu-fixed layout-navbar-fixed layout-footer-fixed layout-compact" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Settings') - Golf Club Management System</title>
    <meta name="description" content="@yield('description', 'Settings - Golf Club Management System')" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
      * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
      }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <style>
      * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
      }
      body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        background-color: #f8f9fa;
      }
      
      /* Custom Sidebar Theme: Reference-Matched (Premium Bold) */
      .layout-menu, 
      .bg-menu-theme {
        background-color: #fff8f8 !important;
        border-right: 1px solid rgba(148, 0, 0, 0.05) !important;
        box-shadow: none !important;
      }
      
      .menu-inner > .menu-header {
        background-color: #940000 !important;
        color: #ffffff !important;
        padding: 0.8rem 1.5rem !important;
        margin: 1.5rem 1rem 0.5rem 1rem !important;
        border-radius: 6px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        font-size: 0.85rem !important;
        display: flex;
        align-items: center;
        justify-content: flex-start;
      }

      .menu-header .menu-header-text {
        font-weight: 800 !important;
        color: #ffffff !important;
      }

      .menu-inner > .menu-item {
        margin: 0.2rem 1rem !important;
      }

      .menu-inner > .menu-item.active > .menu-link {
        background: rgba(148, 0, 0, 0.1) !important;
        color: #940000 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
      }

      .menu-inner > .menu-item.active > .menu-link i,
      .menu-inner > .menu-item.active > .menu-link div {
        color: #940000 !important;
        font-weight: 800 !important;
      }

      .menu-inner .menu-item .menu-link {
        border-radius: 8px;
        transition: all 0.2s ease;
        padding: 0.75rem 1rem !important;
      }

      .menu-inner .menu-item .menu-link:hover {
        background-color: rgba(148, 0, 0, 0.05) !important;
        color: #000000 !important;
      }
      
      /* Main Menu Items Typography */
      .menu-inner > .menu-item > .menu-link {
        font-weight: 700 !important;
        font-size: 1.05rem !important;
        color: #000000 !important;
      }
      
      .menu-inner > .menu-item > .menu-link i {
        font-weight: 800 !important;
        font-size: 1.35rem !important;
        margin-right: 0.75rem !important;
        color: #1a1a1b !important;
      }

      .menu-inner > .menu-item > .menu-link div {
        font-weight: 700 !important;
        color: #000000 !important;
      }

      /* Hover states for icons */
      .menu-inner .menu-item .menu-link:hover i,
      .menu-inner .menu-item .menu-link:hover div {
        color: #940000 !important;
      }
      
      /* Submenu Items */
      .menu-inner .menu-sub .menu-item .menu-link {
        font-weight: 400 !important;
        font-size: 0.85rem !important;
        padding-left: 3.25rem !important;
        color: #6c757d !important;
      }
      
      .menu-inner .menu-sub .menu-item.active .menu-link {
        background-color: transparent !important;
        color: #940000 !important;
        box-shadow: none !important;
      }

      .menu-inner .menu-sub .menu-item.active .menu-link div {
        font-weight: 600 !important;
        color: #940000 !important;
      }
      
      .menu-inner .menu-sub .menu-item .menu-link:hover {
        background-color: transparent !important;
        color: #940000 !important;
      }
      
      /* Premium Navbar/Header Styling */
      #layout-navbar {
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px) saturate(180%);
        -webkit-backdrop-filter: blur(12px) saturate(180%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
        margin: 0 !important;
        padding: 0.5rem 2rem !important;
        z-index: 1000;
      }

      .navbar-detached {
        margin: 0 !important;
        width: 100% !important;
        border-radius: 0 !important;
      }

      .search-input-wrapper {
        background-color: rgba(0, 0, 0, 0.03);
        border-radius: 12px;
        padding: 6px 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        width: 300px;
      }

      .search-input-wrapper:focus-within {
        background-color: #ffffff;
        border-color: #940000;
        box-shadow: 0 4px 12px rgba(148, 0, 0, 0.08);
      }

      #layout-navbar .ri-search-line,
      #layout-navbar .ri-notification-line,
      #layout-navbar .ri-menu-line {
        color: #495057 !important;
        transition: color 0.2s ease;
      }

      #layout-navbar .btn-icon:hover i {
        color: #940000 !important;
      }

      .navbar-nav .dropdown-user .avatar {
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
      }

      .navbar-nav .dropdown-user:hover .avatar {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }

      /* Fixed Layout Adjustments */
      .layout-navbar-fixed .layout-page {
        padding-top: 76px !important;
      }
      .layout-footer-fixed .content-footer {
        position: fixed;
        bottom: 0;
        left: auto;
        right: 0;
        width: calc(100% - 260px); /* 260px is the default sidebar width */
        z-index: 1000;
        background-color: #ffffff !important;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
      }
      /* Mobile adjustment: Footer is full width when sidebar is hidden/toggle */
      @media (max-width: 1199.98px) {
        .layout-footer-fixed .content-footer {
          width: 100%;
        }
      }
      /* Sidebar adjustment for fixed footer */
      .layout-footer-fixed .layout-wrapper:not(.layout-without-menu) .layout-page {
        padding-bottom: 60px !important;
      }

      /* Mobile Optimizations */
      @media (max-width: 767.98px) {
        .container-p-y {
          padding-top: 1rem !important;
          padding-bottom: 1rem !important;
        }
        #layout-navbar {
          padding: 0.5rem 1rem !important;
        }
        .search-input-wrapper {
          width: 150px;
        }
        .card-body {
          padding: 1rem !important;
        }
        .menu-inner > .menu-header {
          margin: 1rem 0.5rem 0.5rem 0.5rem !important;
          padding: 0.6rem 1rem !important;
        }
        .menu-inner > .menu-item {
          margin: 0.1rem 0.5rem !important;
        }
      }

      /* Utility classes for mobile */
      @media (max-width: 575.98px) {
        .m-p-0 { padding: 0 !important; }
        .m-px-0 { padding-left: 0 !important; padding-right: 0 !important; }
        .m-mb-2 { margin-bottom: 0.5rem !important; }
        .m-fs-sm { font-size: 0.875rem !important; }
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
      <div class="layout-overlay layout-menu-toggle" style="display: none;"></div>
    </div>
    
    @include('dashboard.partials.scripts')
    @stack('scripts')
  </body>
</html>

