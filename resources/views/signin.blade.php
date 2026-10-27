<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Fablead Inventory-Billing Software">
    <meta name="keywords"
        content="inventory management, billing system, invoice generator, purchase orders, inventory control, POS system, admin dashboard">
    <meta name="author" content="Fablead Developers Technolab">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="https://fableadtechnolabs.com/favicon-192x192.webp">

    <link rel="stylesheet" href="{{ env('ImagePath') . 'admin/assets/css/bootstrap.min.css' }}">

    <link rel="stylesheet" href="{{ env('ImagePath') . 'admin/assets/plugins/fontawesome/css/fontawesome.min.css' }}">
    <link rel="stylesheet" href="{{ env('ImagePath') . 'admin/assets/plugins/fontawesome/css/all.min.css' }}">

    <link rel="stylesheet" href="{{ env('ImagePath') . 'admin/assets/css/style.css' }}">
    <!-- <style>
        /* .logo-img {
            margin-left: 9rem;
        } */

        .company-title {
            font-weight: 800;
            font-size: 24px;
            /* color: #2c3e50; */
            /* dark professional color */
            text-align: center;
            /* letter-spacing: 1px; */
        }

        .btn-login {
            background: #2c3e50;
            /* match title color */
            border: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 6px;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: #1a242f;
        }


        @media screen and (max-width: 768px) {
            .login-wrapper .login-content {
                width: 90% !important;
            }

            .logo-img {
                margin-left: 0 !important;
            }
        }
    </style> -->
    <style>
        :root {
            --auth-primary: #F7933A;
            --auth-primary-dark: #ea7417;
            --auth-secondary: #1F2937;
            --auth-muted: #667085;
            --auth-border: #d9dee8;
            --auth-bg: #f7f8fb;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body.account-page {
            height: 100%;
        }

        body.account-page {
            position: relative;
            display: flex;
            flex-direction: column;
            margin: 0;
            height: 100vh;
            height: 100dvh;
            padding: clamp(10px, 2.4vh, 24px) 20px clamp(8px, 1.7vh, 16px);
            overflow: hidden;
            color: var(--auth-secondary);
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
            background:
                radial-gradient(circle at 0% 68%, rgba(247, 147, 58, .10) 0 0, transparent 168px),
                radial-gradient(circle at 100% 78%, rgba(247, 147, 58, .12) 0 0, transparent 198px),
                linear-gradient(135deg, #ffffff 0%, #f6f8fb 46%, #ffffff 100%);
        }

        body.account-page::before,
        body.account-page::after {
            content: "";
            position: fixed;
            pointer-events: none;
            z-index: 0;
            opacity: .24;
        }

        body.account-page::before {
            top: -88px;
            left: -82px;
            width: 360px;
            height: 255px;
            border-radius: 0 0 100% 0;
            background: repeating-radial-gradient(ellipse at 0% 0%, transparent 0 13px, rgba(247, 147, 58, .55) 14px 15px, transparent 16px 24px);
        }

        body.account-page::after {
            right: -92px;
            bottom: -78px;
            width: 380px;
            height: 260px;
            border-radius: 100% 0 0 0;
            background: repeating-radial-gradient(ellipse at 100% 100%, transparent 0 13px, rgba(247, 147, 58, .52) 14px 15px, transparent 16px 24px);
        }

        .main-wrapper {
            position: relative;
            z-index: 1;
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .account-content,
        .login-wrapper {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .login-wrapper .login-content {
            width: min(100%, 550px);
        }

        .login-userset {
            position: relative;
            width: min(100%, 550px);
            max-height: calc(100vh - 58px);
            max-height: calc(100dvh - 58px);
            padding: clamp(18px, 3vh, 25px) clamp(28px, 7vw, 50px);
            border: 0;
            border-radius: 22px;
            background: rgba(255, 255, 255, .92);
            /* box-shadow: 0 24px 60px rgba(31, 41, 55, .13); */
            backdrop-filter: blur(16px);
        }

        .login-userset::before {
            content: "";
            position: absolute;
            top: -1px;
            left: 50%;
            width: 92px;
            height: 4px;
            border-radius: 0 0 999px 999px;
            transform: translateX(-50%);
            background: var(--auth-primary);
        }

        .login-logo {
            display: flex;
            justify-content: center;
            margin: 0 0 clamp(10px, 1.9vh, 18px);
            text-align: center;
        }

        .login-logo .logo-img {
            display: block;
            width: clamp(104px, 16vw, 132px);
            max-width: 70%;
            max-height: clamp(62px, 11vh, 82px);
            object-fit: contain;
            margin: 0 auto !important;
        }

        .login-userheading {
            margin-bottom: clamp(16px, 2.8vh, 24px);
            text-align: center;
        }

        .login-userheading h3 {
            margin: 0 0 clamp(5px, 1vh, 8px);
            color: var(--auth-secondary);
            font-size: clamp(24px, 3.8vh, 30px);
            line-height: 1.18;
            font-weight: 700;
            letter-spacing: 0;
        }

        .login-userheading h3 span {
            color: var(--auth-primary);
        }

        .login-userheading h4 {
            margin: 0;
            color: var(--auth-muted);
            font-size: clamp(13px, 1.9vh, 15px);
            line-height: 1.55;
            font-weight: 400;
        }

        .form-login {
            margin-bottom: clamp(12px, 2vh, 17px);
        }

        .form-login label {
            display: block;
            margin-bottom: clamp(5px, 1vh, 8px);
            color: #111827;
            font-size: 14px;
            font-weight: 500;
        }

        .form-addons,
        .pass-group {
            position: relative;
            display: flex;
            align-items: center;
            min-height: clamp(42px, 6.3vh, 48px);
            border: 1px solid var(--auth-border);
            border-radius: 9px;
            background: #fff;
            overflow: hidden;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .form-addons:focus-within,
        .pass-group:focus-within {
            border-color: rgba(247, 147, 58, .78);
            box-shadow: 0 0 0 4px rgba(247, 147, 58, .14);
        }

        .form-addons::before,
        .pass-group::before {
            content: "";
            align-self: stretch;
            width: 56px;
            flex: 0 0 56px;
            border-right: 0;
            background: #fff;
        }

        .form-addons::after,
        .pass-group::after {
            position: absolute;
            left: 19px;
            z-index: 2;
            color: var(--auth-primary);
            font-family: "Font Awesome 5 Free";
            font-size: 16px;
            font-weight: 900;
            line-height: 1;
        }

        .form-addons::after {
            content: "\f0e0";
        }

        .pass-group::after {
            content: "\f023";
        }

        .form-addons input,
        .pass-group input {
            width: 100%;
            height: clamp(42px, 6.3vh, 48px);
            min-width: 0;
            padding: 0 18px 0 8px;
            border: 0;
            outline: 0;
            color: var(--auth-secondary);
            font-size: 15px;
            background: transparent;
        }

        .form-addons input::placeholder,
        .pass-group input::placeholder {
            color: #7a8597;
        }

        .form-addons img {
            display: none;
        }

        /* .pass-group .toggle-password {
            position: static;
            flex: 0 0 48px;
            color: #8a94a6;
            cursor: pointer;
            text-align: center;
            transition: color .2s ease;
        } */
        .pass-group .toggle-password {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            min-width: 48px;
            height: 48px;
            color: #8a94a6;
            cursor: pointer;
            line-height: 1;
        }

        .pass-group .toggle-password:hover {
            color: var(--auth-primary);
        }

        .signin-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-top: -1px;
            margin-bottom: clamp(14px, 2.5vh, 21px);
        }

        .remember-login {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--auth-secondary);
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
        }

        .remember-login input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--auth-primary);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--auth-primary-dark);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: color .2s ease;
        }

        .forgot-link:hover,
        .forgot-link:focus {
            color: var(--auth-primary);
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            min-height: clamp(42px, 6.5vh, 48px);
            border: 0;
            border-radius: 30px;
            color: #fff;
            background: linear-gradient(135deg, #ff9b45 0%, var(--auth-primary) 54%, #f47d23 100%);
            box-shadow: 0 13px 26px rgba(247, 147, 58, .30);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            color: #fff;
            transform: translateY(-1px);
            filter: saturate(1.04);
            box-shadow: 0 16px 30px rgba(247, 147, 58, .36);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .signin-arrow {
            margin-left: 12px;
            font-weight: 500;
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 24px;
            margin: clamp(15px, 2.5vh, 22px) 0 clamp(10px, 1.8vh, 14px);
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        .login-divider::before,
        .login-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #e2e7ef;
        }

        #faceLoginBtn {
            width: 100% !important;
            min-height: clamp(42px, 6.3vh, 48px);
            border: 1px solid #e2e7ef !important;
            border-radius: 999px !important;
            background: #fff !important;
            color: var(--auth-secondary) !important;
            box-shadow: 0 8px 22px rgba(31, 41, 55, .07);
            font-size: 14px !important;
            font-weight: 700 !important;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        #faceLoginBtn i {
            color: var(--auth-primary);
            font-size: 15px !important;
        }

        #faceLoginBtn:hover,
        #faceLoginBtn:focus {
            border-color: rgba(247, 147, 58, .55) !important;
            color: var(--auth-primary-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(31, 41, 55, .10);
        }

        #loginMessage,
        #faceLoginMessage {
            text-align: center;
            font-size: 13px;
            font-weight: 500;
        }

        .login-footer {
            position: relative;
            z-index: 1;
            flex: 0 0 auto;
            padding-top: clamp(6px, 1.4vh, 12px);
            text-align: center;
            color: #667085;
        }

        .login-footer h1 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .login-footer a {
            color: inherit;
            text-decoration: none;
        }

        .login-footer h1:not(.login-footer-title) {
            display: none;
        }

        @media screen and (max-width: 768px) {
            body.account-page {
                padding: 12px 14px 10px;
            }

            .main-wrapper {
                min-height: 0;
            }

            .login-wrapper .login-content {
                width: 100% !important;
            }

            .login-userset {
                padding: 18px 22px;
                border-radius: 20px;
            }

            .login-logo .logo-img {
                width: 112px;
                max-width: 72%;
            }

            .login-userheading h3 {
                font-size: 28px;
            }

            .signin-options {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }
        }

        @media screen and (max-width: 420px) {
            .login-userset {
                padding: 16px 16px;
            }

            .login-userheading h3 {
                font-size: 26px;
            }

            .login-userheading h4 {
                font-size: 14px;
            }
        }

        @media screen and (max-height: 680px) {
            body.account-page {
                padding-top: 8px;
                padding-bottom: 6px;
            }

            .login-userset {
                padding-top: 16px;
                padding-bottom: 16px;
                border-radius: 18px;
            }

            .login-logo {
                margin-bottom: 8px;
            }

            .login-logo .logo-img {
                width: 98px;
                max-height: 58px;
            }

            .login-userheading {
                margin-bottom: 12px;
            }

            .login-userheading h3 {
                font-size: 23px;
                margin-bottom: 4px;
            }

            .login-userheading h4 {
                font-size: 12px;
            }

            .form-login {
                margin-bottom: 10px;
            }

            .form-addons,
            .pass-group {
                min-height: 40px;
            }

            .form-addons input,
            .pass-group input {
                height: 40px;
            }

            .signin-options {
                margin-bottom: 12px;
            }

            .btn-login,
            #faceLoginBtn {
                min-height: 40px;
            }

            .login-divider {
                margin: 12px 0 8px;
            }

            .login-footer h1 {
                font-size: 12px;
            }
        }
        .btn-close-custom {
            background: transparent;
            border: none;
            color: #ea5455;
            font-size: 22px;
            cursor: pointer;
            padding: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close-custom:hover {
            color: #c82333;
        }
    </style>
</head>
@php
    use App\Models\Setting;
    $settings = Setting::first();
@endphp

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <!-- <div class="login-content"> -->
                    <div class="login-userset">
                        <div class="login-logo">
                            <img src="{{ isset($settings) && $settings->logo ? env('ImagePath') . '/storage/' . $settings->logo : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}"
                                alt="img" class="logo-img">

                        </div>
                        {{-- <div class="login-logo text-center mb-4">
                            <h2 class="company-title">Champion Auto Care &amp; Car Spa</h2>
                        </div> --}}
                        <div class="login-userheading">
                            <h3>Welcome <span>Back</span></h3>
                            <h4>Please sign in to your account to continue</h4>
                        </div>
                        <form id="loginForm">
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="text" id="email" placeholder="Enter your email address">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/mail.svg' }}"
                                        alt="img">
                                </div>
                                <small id="emailError" class="text-danger"></small>
                            </div>

                            <div class="form-login">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" id="password" class="pass-input"
                                        placeholder="Enter your password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                                <small id="passwordError" class="text-danger"></small>
                            </div>
                            <div class="signin-options">
                                <label class="remember-login">
                                    <input type="checkbox" id="remember">
                                    <span>Remember me</span>
                                </label>
                            </div>

                            <div class="form-login">
                                <div class="alreadyuser">
                                    <!-- <h4><a href="forgetpassword.html" class="hover-a">Forgot Password?</a></h4> -->
                                </div>
                            </div>

                            <div class="form-login">
                                <button type="submit" class="btn btn-login">Sign In <i class="fas fa-sign-in-alt"></i></button>
                            </div>

                            <div id="loginMessage" class="text-danger"></div>
                        </form>

                        {{-- OR divider --}}
                        <div class="d-flex align-items-center my-3">
                            <hr style="flex:1; border-color:#ccc;">
                            <span style="padding:0 12px; color:#aaa; font-size:13px; font-weight:500;">or continue with</span>
                            <hr style="flex:1; border-color:#ccc;">
                        </div>

                        {{-- Login with Face button --}}
                        <button id="faceLoginBtn" type="button" style="
                            width:100%; padding:12px; border:2px solid #ff9f43; background:#fff;
                            color:#ff9f43; font-weight:600; font-size:15px; border-radius:6px;
                            cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
                            transition:background 0.2s, color 0.2s;">
                            <i class="fa fa-camera"></i> Login with Face
                        </button>

                    </div>
                <!-- </div> -->
                <!-- <div class="login-img">
                    <img src="admin/assets/img/login.jpg" alt="img">
                </div> -->
            </div>
        </div>
    </div>
    <footer style="text-align: center; padding: 10px 0; background-color: #f4f4f4; height: 50px;">
        <h1 style="font-size: 14px; font-weight: 600;">© <?= date('Y') ?> Copyright - Fablead Developers Technolab</h1>
    </footer>

    <script src="{{ env('ImagePath') . 'admin/assets/js/jquery-3.6.0.min.js' }}"></script>
    <script src="{{ env('ImagePath') . 'admin/assets/js/feather.min.js' }}"></script>
    <script src="{{ env('ImagePath') . 'admin/assets/js/bootstrap.bundle.min.js' }}"></script>
    <script src="{{ env('ImagePath') . 'admin/assets/js/script.js' }}"></script>

    {{-- ===================== FACE LOGIN MODAL ===================== --}}
    <div class="modal fade" id="faceLoginModal" tabindex="-1" aria-labelledby="faceLoginModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">

                {{-- Header --}}
                <div class="modal-header" style="background:#fff; border-bottom:1px solid #eee; padding:16px 20px;">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="faceLoginModalLabel" style="font-weight:700; font-size:16px; color:#1a2b4a;">
                        <i class="fa fa-camera" style="color:#4a90e2;"></i> Face Recognition Login
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="stopFaceLoginCamera()"><i class="fas fa-times"></i></button>
                </div>

                {{-- Body --}}
                <div class="modal-body" style="padding:28px 24px; text-align:center; background:#fff;">
                    {{-- Circular viewfinder --}}
                    <div style="position:relative; width:260px; height:260px; margin:0 auto 20px;">
                        {{-- Outer circle with shadow --}}
                        <div style="
                            position:absolute; inset:0; border-radius:50%;
                            background:#f0f0f0;
                            box-shadow: inset 0 2px 12px rgba(0,0,0,0.08), 0 4px 20px rgba(0,0,0,0.1);
                            overflow:hidden;">
                            <video id="login-face-video" autoplay playsinline muted
                                style="width:100%; height:100%; object-fit:cover; display:block; border-radius:50%;"></video>
                        </div>
                        {{-- Overlay canvas for face detection drawing --}}
                        <canvas id="login-face-canvas" style="
                            position:absolute; inset:0; border-radius:50%;
                            pointer-events:none;"></canvas>
                    </div>

                    {{-- Status bar --}}
                    <div style="
                        background:#f8f9fa; border-radius:8px; padding:10px 16px;
                        display:flex; align-items:center; justify-content:center; gap:8px;">
                        <span id="login-camera-spinner" style="display:none;">
                            <i class="fa fa-spinner fa-spin" style="color:#4a90e2;"></i>
                        </span>
                        <span id="login-camera-message" style="font-size:13px; color:#555;">
                            Initializing AI models…
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- ============================================================ --}}

    {{-- face-api.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    {{-- Face login handler --}}
    <script src="{{ env('ImagePath') . 'admin/assets/js/face-login.js' }}"></script>

    <script>
        $(document).ready(function() {
            $("#loginForm").submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                // Get input values
                let email = $("#email").val().trim();
                let password = $("#password").val().trim();

                // Reset error messages
                $("#emailError").text("");
                $("#passwordError").text("");
                $("#loginMessage").text("");

                // Basic validation
                if (email === "") {
                    $("#emailError").text("Email is required.");
                    return;
                }
                if (password === "") {
                    $("#passwordError").text("Password is required.");
                    return;
                }

                // Get submit button
                let $submitBtn = $("#loginForm button[type='submit']");
                let originalContent = $submitBtn.html();

                // Show spinner and disable button
                $submitBtn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Logging in...'
                    )
                    .prop("disabled", true);

                // AJAX request
                $.ajax({
                    url: "/", // Laravel API route
                    type: "POST",
                    data: {
                        email: email,
                        password: password,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    // success: function(response) {
                    //     if (response.token) {
                    //         // Store token in local storage or cookies
                    //         localStorage.setItem("authToken", response.token);

                    //         // Redirect to dashboard
                    //         location.reload();
                    //         // window.location.href = "/dashboard";
                    //         window.location.href = response.redirect; // Go to dashboard
                    //     }
                    // },
                    success: function(response) {
                        if (response.token) {
                            localStorage.setItem("authToken", response.token);

                            // Store flag for one-time popup
                            sessionStorage.setItem("showAppointments", "1");

                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Invalid credentials. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        $("#loginMessage").text(errorMessage);
                    },
                    complete: function() {
                        // Reset button
                        $submitBtn.html(originalContent).prop("disabled", false);
                    }
                });
            });
        });
    </script>
</body>

</html>
