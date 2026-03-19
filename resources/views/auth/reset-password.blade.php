<!doctype html>
<html lang="en" class="layout-wide customizer-hide" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Reset Password - Golf Club Management System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
      * { font-family: "Century Gothic", "CenturyGothic", "AppleGothic", "Roboto", sans-serif !important; }

      .otp-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 1.5rem; }
      .otp-digit {
        width: 52px; height: 60px; font-size: 1.6rem; font-weight: 700;
        text-align: center; border: 2px solid #d9dee3; border-radius: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
      }
      .otp-digit:focus { border-color: #940000; box-shadow: 0 0 0 3px rgba(148,0,0,0.15); }
      .otp-digit.filled { border-color: #940000; background: rgba(148,0,0,0.04); }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
  </head>
  <body>
    <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6 mx-4">
          <div class="card p-sm-7 p-2">
            <!-- Logo -->
            <div class="app-brand justify-content-center mt-5">
              <a href="{{ route('login') }}" class="app-brand-link gap-3">
                <span class="app-brand-logo demo">
                  <span class="text-primary">
                    <svg width="30" height="24" viewBox="0 0 250 196" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M12.3002 1.25469L56.655 28.6432C59.0349 30.1128 60.4839 32.711 60.4839 35.5089V160.63C60.4839 163.468 58.9941 166.097 56.5603 167.553L12.2055 194.107C8.3836 196.395 3.43136 195.15 1.14435 191.327C0.395485 190.075 0 188.643 0 187.184V8.12039C0 3.66447 3.61061 0.0522461 8.06452 0.0522461C9.56056 0.0522461 11.0271 0.468577 12.3002 1.25469Z" fill="currentColor"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M237.71 1.22393L193.355 28.5207C190.97 29.9889 189.516 32.5905 189.516 35.3927V160.631C189.516 163.469 191.006 166.098 193.44 167.555L237.794 194.108C241.616 196.396 246.569 195.151 248.856 191.328C249.605 190.076 250 188.644 250 187.185V8.09597C250 3.64006 246.389 0.027832 241.935 0.027832C240.444 0.027832 238.981 0.441882 237.71 1.22393Z" fill="currentColor"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2787 1.18923L125 70.3075V136.87L0 65.2465V8.06814C0 3.61223 3.61061 0 8.06452 0C9.552 0 11.0105 0.411583 12.2787 1.18923Z" fill="currentColor"/>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M237.721 1.18923L125 70.3075V136.87L250 65.2465V8.06814C250 3.61223 246.389 0 241.935 0C240.448 0 238.99 0.411583 237.721 1.18923Z" fill="currentColor"/>
                    </svg>
                  </span>
                </span>
                <span class="app-brand-text demo text-heading fw-semibold">Golf Club Management</span>
              </a>
            </div>

            <div class="card-body mt-1">
              <h4 class="mb-1">Enter Reset OTP ✉️</h4>
              <p class="mb-4 text-muted">
                A 6-digit OTP was sent to 
                @if ($phone_hint ?? false)
                  <strong>{{ $phone_hint }}</strong>
                @else
                  your registered phone number
                @endif.
                It expires in <strong>10 minutes</strong>.
              </p>

              @if ($errors->any())
                <div class="alert alert-danger mb-4">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form method="POST" action="{{ route('password.reset.update') }}" id="resetForm">
                @csrf

                {{-- OTP Digit Boxes --}}
                <div class="mb-4">
                  <label class="form-label fw-bold text-center d-block mb-3">Enter the 6-digit OTP</label>
                  <div class="otp-inputs" id="otpBoxes">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_1" autocomplete="off">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_2" autocomplete="off">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_3" autocomplete="off">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_4" autocomplete="off">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_5" autocomplete="off">
                    <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_6" autocomplete="off">
                  </div>
                  {{-- Hidden field that collects the full OTP --}}
                  <input type="hidden" name="otp" id="otp_combined">
                </div>

                {{-- New Password --}}
                <div class="mb-4">
                  <div class="form-password-toggle form-control-validation">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="password" id="password" name="password" class="form-control"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="6" />
                        <label for="password">New Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="togglePassword">
                        <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                      </span>
                    </div>
                  </div>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-5">
                  <div class="form-password-toggle form-control-validation">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required minlength="6" />
                        <label for="password_confirmation">Confirm New Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer" id="toggleConfirmPassword">
                        <i class="icon-base ri ri-eye-off-line icon-20px"></i>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <button type="submit" class="btn btn-primary d-grid w-100" id="resetBtn">
                    <i class="ri ri-lock-password-line me-2"></i> Reset Password
                  </button>
                </div>
              </form>

              <div class="text-center">
                <a href="{{ route('password.forgot') }}" class="d-flex align-items-center justify-content-center">
                  <i class="ri ri-refresh-line me-1"></i> Resend OTP
                </a>
              </div>

              <div class="text-center mt-4">
                <small class="text-muted">Powered by <strong>EmCa Technologies</strong></small>
              </div>
            </div>
          </div>
          <img src="{{ asset('assets/img/illustrations/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block" />
          <img src="{{ asset('assets/img/illustrations/auth-basic-mask-light.png') }}" class="authentication-image d-none d-lg-block scaleX-n1-rtl" height="172" alt="triangle-bg" />
          <img src="{{ asset('assets/img/illustrations/tree.png') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block" />
        </div>
      </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script>
      // OTP box auto-advance
      const digits = document.querySelectorAll('.otp-digit');
      digits.forEach((input, idx) => {
        input.addEventListener('input', function() {
          this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
          if (this.value) {
            this.classList.add('filled');
            if (idx < digits.length - 1) digits[idx + 1].focus();
          } else {
            this.classList.remove('filled');
          }
          updateOtpCombined();
        });

        input.addEventListener('keydown', function(e) {
          if (e.key === 'Backspace' && !this.value && idx > 0) {
            digits[idx - 1].focus();
            digits[idx - 1].value = '';
            digits[idx - 1].classList.remove('filled');
            updateOtpCombined();
          }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
          e.preventDefault();
          const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
          pasted.split('').slice(0, 6).forEach((char, i) => {
            if (digits[i]) {
              digits[i].value = char;
              digits[i].classList.add('filled');
            }
          });
          updateOtpCombined();
          const nextEmpty = [...digits].findIndex(d => !d.value);
          if (nextEmpty !== -1) digits[nextEmpty].focus();
          else digits[5].focus();
        });
      });

      function updateOtpCombined() {
        document.getElementById('otp_combined').value = [...digits].map(d => d.value).join('');
      }

      // Validate OTP before submit
      document.getElementById('resetForm').addEventListener('submit', function(e) {
        const otp = document.getElementById('otp_combined').value;
        if (otp.length !== 6) {
          e.preventDefault();
          alert('Please enter the complete 6-digit OTP.');
          digits[0].focus();
        }
      });

      // Password toggles
      document.getElementById('togglePassword')?.addEventListener('click', function() {
        const pw = document.getElementById('password');
        const icon = this.querySelector('i');
        pw.type = pw.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('ri-eye-off-line');
        icon.classList.toggle('ri-eye-line');
      });

      document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
        const pw = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');
        pw.type = pw.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('ri-eye-off-line');
        icon.classList.toggle('ri-eye-line');
      });

      // Auto-focus first digit
      document.addEventListener('DOMContentLoaded', () => digits[0]?.focus());
    </script>
  </body>
</html>
