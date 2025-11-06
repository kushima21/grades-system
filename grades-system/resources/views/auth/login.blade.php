<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
  @vite(['resources/css/login.css', 'resources/js/app.js'])

</head>
<body>
    <div class="main-content">
        <div class="content-container">

            <div class="sidebar-container">
                <img class="logo" src="{{ asset('system_images/logo.jpg') }}" alt="LOGO">
                <p class="just-shipped">Just Shipped v1.2025.07.01  <span class="s">></span></p>
                <div class="login-header">
                    <h2>Sign in to your Grading Account</h2>
                </div>
                <div class="login-form-container">
                    <form method="POST" action="#">
                        @csrf
                        <div class="form-box">
                            <label for="email">Email</label>
                            <input type="text" name="login" id="email" placeholder="@ckcm.edu.ph" required>
                        </div>
                        <div class="form-box">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Password" required>
                        </div>
                        <div class="remember-row">
                            <div class="remember-left">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <div class="remember-right">
                                <a href="#">Forgot password?</a>
                            </div>
                        </div>
                        <div class="loginBtn">
                            <button type="submit" class="signin">Sign in Now</button>
                            <p class="or">--------or continue with--------</p>
                            <a href="#">
                                <button type="button" class="signup">
                                    <img src="{{ asset('system_images/google.svg') }}" class="google-logo">
                                    Sign with Google
                                </button>
                            </a>
                        </div>
                        <div class="term-conditions">
                        <p>
                        By clicking “Sign in”, you agree to our 
                        <a href="#">Terms of Service</a> and 
                        <a href="#">Privacy Statement</a>. 
                        We’ll occasionally send you account related emails.
                        </p>
                        </div>
                        <div class="footer">
                            <p class="footer-ckcm">
                                <span>CKCM Network</span> is a trademark of MIS. <br>
                                © 2025-2026 CKCM Technologies. All rights reserved.
                            </p>

                        </div>
                    </form>
                </div>
            </div>

            <div class="logo-image">
                <img class="logo" src="{{ asset('system_images/logo-image.svg') }}" alt="LOGO">
            </div>

        </div>
    </div>
</body>
</html>