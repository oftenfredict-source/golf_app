<!doctype html>
<html
  lang="en"
  class="layout-wide customizer-hide"
  data-assets-path="{{ asset('assets/') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

    <title>Login - Golf Club Management System | Powered by EmCa Technologies</title>

    <meta name="description" content="Integrated Golf Club Cashless Payment & Management System" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
      * {
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      }
      body {
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
      /* Splash Screen Styles */
      .splash-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .splash-screen.active {
        display: flex;
        opacity: 1;
      }

      .splash-content {
        text-align: center;
        color: white;
      }

      .splash-logo {
        width: 120px;
        height: 120px;
        margin-bottom: 30px;
        animation: pulse 2s infinite;
      }

      @keyframes pulse {
        0%, 100% {
          transform: scale(1);
        }
        50% {
          transform: scale(1.1);
        }
      }

      .splash-title {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 10px;
      }

      .splash-subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 40px;
      }

      .progress-container {
        width: 300px;
        height: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 20px;
      }

      .progress-bar {
        height: 100%;
        background: white;
        border-radius: 10px;
        width: 0%;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
      }

      .progress-text {
        font-size: 14px;
        opacity: 0.9;
      }

      .emca-brand {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 14px;
        opacity: 0.8;
      }

      .emca-brand strong {
        color: #fff;
        font-weight: 600;
      }
    </style>
  </head>

  <body>
    <!-- Splash Screen -->
    <div class="splash-screen" id="splashScreen">
      <div class="splash-content">
        <div class="splash-logo">
          <svg width="120" height="120" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z"
              fill="white" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z"
              fill="white" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
              fill="white" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
              fill="white" />
          </svg>
        </div>
        <h1 class="splash-title">Golf Club Management</h1>
        <p class="splash-subtitle">Loading your dashboard...</p>
        <div class="progress-container">
          <div class="progress-bar" id="progressBar"></div>
        </div>
        <div class="progress-text" id="progressText">0%</div>
      </div>
      <div class="emca-brand">
        Powered by <strong>EmCa Technologies</strong>
      </div>
    </div>

    <!-- Content -->
    <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
          <!-- Login -->
          <div class="card p-sm-7 p-2">
            <!-- Logo -->
            <div class="app-brand justify-content-center mt-5">
              <a href="{{ route('login') }}" class="app-brand-link gap-3">
                <span class="app-brand-logo demo">
                  <span class="text-primary">
                    <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z"
                        fill="currentColor" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0 65.2656L60.4839 99.9629V133.979L0 65.2656Z"
                        fill="black" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0 65.2656L60.4839 99.0795V119.859L0 65.2656Z"
                        fill="black" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z"
                        fill="currentColor" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M250 65.2656L189.516 99.8897V135.006L250 65.2656Z"
                        fill="black" />
                      <path
                        opacity="0.077704"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M250 65.2656L189.516 99.0497V120.886L250 65.2656Z"
                        fill="black" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                        fill="currentColor" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z"
                        fill="white"
                        fill-opacity="0.15" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                        fill="currentColor" />
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z"
                        fill="white"
                        fill-opacity="0.3" />
                    </svg>
                  </span>
                </span>
                <span class="app-brand-text demo text-heading fw-semibold">Golf Club Management</span>
              </a>
            </div>
            <!-- /Logo -->

            <div class="card-body mt-1">
              
              @if (session('success'))
                <div class="alert alert-success mb-4">
                  <i class="ri ri-check-circle-line me-2"></i>{{ session('success') }}
                </div>
              @endif

              @if ($errors->any())
                <div id="errorAlert" class="alert alert-danger mb-4">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <div id="ajaxErrorAlert" class="alert alert-danger mb-4" style="display: none;"></div>

              <form id="formAuthentication" class="mb-5" action="{{ route('login') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-floating form-floating-outline mb-5 form-control-validation">
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
                    value="{{ old('email') }}"
                    autofocus
                    required />
                  <label for="email">Email or Username</label>
                </div>
                <div class="mb-5">
                  <div class="form-password-toggle form-control-validation">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input
                          type="password"
                          id="password"
                          class="form-control"
                          name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password"
                          required />
                        <label for="password">Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="togglePassword">
                        <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="mb-5 pb-2 d-flex justify-content-between pt-2 align-items-center">
                  <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                  </div>
                  <a href="{{ route('password.forgot') }}" class="float-end mb-1">
                    <span>Forgot Password?</span>
                  </a>
                </div>
                <div class="mb-5">
                  <button class="btn btn-primary d-grid w-100" type="submit" id="loginBtn">Login</button>
                </div>
              </form>

              
              <div class="text-center mt-4">
                <small class="text-muted">Powered by <strong>EmCa Technologies</strong></small>
              </div>
            </div>
          </div>
          <!-- /Login -->
          <img
            src="{{ asset('assets/img/illustrations/tree-3.png') }}"
            alt="auth-tree"
            class="authentication-image-object-left d-none d-lg-block" />
          <img
            src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}"
            class="authentication-image d-none d-lg-block scaleX-n1-rtl"
            height="172"
            alt="triangle-bg" />
          <img
            src="{{ asset('assets/img/illustrations/tree.png') }}"
            alt="auth-tree"
            class="authentication-image-object-right d-none d-lg-block" />
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Login Script -->
    <script>
      // Password toggle
      document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('ri-eye-off-line');
          icon.classList.add('ri-eye-line');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('ri-eye-line');
          icon.classList.add('ri-eye-off-line');
        }
      });

      // Login form submission with splash screen
      const loginForm = document.getElementById('formAuthentication');
      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          // Only prevent default if we can handle it with AJAX
          e.preventDefault();
        
        const form = this;
        const splashScreen = document.getElementById('splashScreen');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const loginBtn = document.getElementById('loginBtn');
        
        // Disable login button
        loginBtn.disabled = true;
        loginBtn.textContent = 'Logging in...';
        
        // Get form data
        const formData = new FormData(form);
        
        // Show splash screen
        splashScreen.classList.add('active');
        
        // Start progress animation
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += 2;
          if (progress > 90) progress = 90; // Stop at 90% until response
          
          progressBar.style.width = progress + '%';
          progressText.textContent = progress + '%';
        }, 30);
        
        // Submit form via fetch
        fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
          credentials: 'same-origin'
        })
        .then(async response => {
          clearInterval(progressInterval);
          
          // Get response content type
          const contentType = response.headers.get('content-type');
          const isJson = contentType && contentType.includes('application/json');
          
          // Handle JSON responses
          if (isJson) {
            const data = await response.json();
            
            if (response.ok && data.success) {
              // Complete progress to 100%
              progressBar.style.width = '100%';
              progressText.textContent = '100%';
              
              // Login successful - redirect after a brief delay
              setTimeout(() => {
                window.location.href = data.redirect || '{{ route("dashboard") }}';
              }, 500);
            } else {
              // Handle validation errors
              let errorMessage = data.message || 'Login failed. Please check your credentials.';
              
              // Check for validation errors
              if (data.errors) {
                const errorMessages = [];
                Object.keys(data.errors).forEach(key => {
                  errorMessages.push(data.errors[key][0]);
                });
                errorMessage = errorMessages.join('\n');
              }
              
              throw new Error(errorMessage);
            }
          } 
          // Handle HTML responses (validation errors or redirects)
          else {
            // If status is 200, might be a redirect (successful login)
            if (response.status === 200 || response.status === 302) {
              progressBar.style.width = '100%';
              progressText.textContent = '100%';
              
              setTimeout(() => {
                window.location.href = '{{ route("dashboard") }}';
              }, 500);
            } 
            // Handle validation errors (422)
            else if (response.status === 422) {
              const text = await response.text();
              throw new Error('Invalid credentials. Please check your email and password.');
            }
            // Other errors
            else {
              throw new Error('Login failed. Status: ' + response.status);
            }
          }
        })
        .catch(error => {
          clearInterval(progressInterval);
          
          // Log detailed error for debugging
          console.error('Login Error Details:', {
            error: error,
            message: error.message,
            stack: error.stack
          });
          
          // Hide splash on error
          setTimeout(() => {
            if (splashScreen) splashScreen.classList.remove('active');
            if (loginBtn) {
              loginBtn.disabled = false;
              loginBtn.textContent = 'Login';
            }
            if (progressBar) {
              progressBar.style.width = '0%';
            }
            if (progressText) {
              progressText.textContent = '0%';
            }
            
            // Show detailed error message
            let errorMessage = error.message || 'An error occurred. Please try again.';
            
            // Check if it's a network error
            if (error.message === 'Failed to fetch' || error.message.includes('NetworkError')) {
              errorMessage = 'Network error. Please check your connection and try again.';
            }
            
            // Try to show error in a div
            let errorDiv = document.getElementById('ajaxErrorAlert') || document.getElementById('errorAlert');
            if (!errorDiv && loginForm) {
              // Create error div if it doesn't exist
              errorDiv = document.createElement('div');
              errorDiv.id = 'ajaxErrorAlert';
              errorDiv.className = 'alert alert-danger mb-4';
              loginForm.insertBefore(errorDiv, loginForm.firstChild);
            }
            
            if (errorDiv) {
              errorDiv.innerHTML = '<ul class="mb-0"><li>' + errorMessage + '</li></ul>';
              errorDiv.style.display = 'block';
              // Scroll to error
              errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
              // Fallback to alert if we can't create the div
              alert(errorMessage);
            }
          }, 300);
        });
      }
    </script>
  </body>
</html>

