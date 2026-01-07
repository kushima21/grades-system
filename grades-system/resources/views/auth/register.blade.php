@extends('layouts.auth_default')

<title>@yield('title', 'Login | Grading System, CKCM, Inc.')</title>

@section('content')


<head>
    <title>Register</title>
 
   
</head>


<div class="container">
    <div class="login-box">
        <h2 style="color: var(--ckcm-color4); margin-bottom: 20px;">Register</h2>

        <form action="{{ route('register.post') }}" method="POST" id="registerForm">

            @csrf

               <!-- Email Input -->
            <div class="input-group">
                <label for="name">Student ID</label>
                <input type="number" id="email" name="studentID" required>
            </div>

            <!-- Email Input -->
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="email" name="name" required>
            </div>


            <div class="input-group">
                <label for="email">Gender</label>
                <input type="text" id="email" name="gender" required>
            </div>

            <!-- Email Input -->
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>


            <div class="input-group">
                <label for="department">Program</label>
                <select id="department" name="department">
                    <option value="" disabled selected>Select a Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                    @endforeach
                </select>
            </div>


            <!-- Password Input -->
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="password">Confirm Password</label>
                <input type="password" id="password" name="confirm_password" required>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <label for="role">Select Role</label>
                <select name="role" id="role" required>
                    <option value="student">Student</option>
                    <option value="registrar">Registrar</option>
                    <option value="dean">Dean</option>
                    <option value="instructor">Instructor</option>
                </select>
            </div>

          
            <!-- Submit Button -->
            <button type="submit" class="btn">Submit</button>

            <!-- Register & Forgot Password Links -->
            <div class="links">
                <a href="{{ route('login') }}">Already have an account?</a> |

            </div>
        </form>

        @if (session()->has("success"))
            <div class="alert alert-success">
                {{ session()->get("success") }}
            </div>
        @endif

        @if (session()->has("error"))
            <div class="alert alert-danger">
                {{ session()->get("error") }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</div>


@if (!session('register_access'))
<!-- Modal -->
<div class="modal fade show" id="securityModal" tabindex="-1" aria-modal="true" style="display:block; background:rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="securityForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Security Code Required</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="security_code" class="form-label">Enter Security Code</label>
                        <input type="password" class="form-control" id="security_code" name="security_code" required>
                        <div id="securityError" class="text-danger mt-2" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Prevent interaction with the page -->
<style>
    body { overflow: hidden; }
    #registerForm * { pointer-events: none; opacity: 0.5; }
</style>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
@if (!session('register_access'))
    document.getElementById('securityForm').onsubmit = function(e) {
        e.preventDefault();
        var code = document.getElementById('security_code').value;
        var csrf = document.querySelector('input[name="_token"]').value;

        fetch("{{ route('register.security') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({ security_code: code })
        }).then(res => {
            if (res.ok) {
                location.reload();
            } else {
                res.json().then(data => {
                    document.getElementById('securityError').innerText = data.message;
                    document.getElementById('securityError').style.display = 'block';
                });
            }
        });
    }
@endif
</script>

@endsection