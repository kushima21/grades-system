@extends('layouts.default')
@vite(['resources/css/user.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

@section('content')

    <div class="users-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Users</span>
        </div>
        <h2 class="my-header">
            Users
        </h2>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchUser" id="searchUser" placeholder="Quick Search...">
            </form>
        </div>
        <div class="users-modal-main-container">
            <h3 class="user-header-modal">
                Edit User
            </h3>
            <div class="users-edit-form">
                <form method="">
                    <div class="user-info">
                        <label for="studentID">School ID</label>
                        <input type="number" name="studentID" id="studentID">
                    </div>
                    <div class="user-info">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name">
                    </div>
                    <div class="user-info">
                        <label for="studentID">School ID</label>
                        <select name="gender">
                            <option value=""></option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                    <div class="user-info">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="user-info">
                        <label for="department">Department</label>
                        <select name="department">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                     <div class="user-info">
                        <label for="role">Role</label>
                        <select name="role">
                            <option value=""></option>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="dean">Dean</option>
                            <option value="registrar">Registrar</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="user-wrapper-container">
            <table class="user-table-container">
                <thead>
                    <tr>
                        <th>School ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->studentID }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
                                <div style="display:flex; gap:5px;">
                                    
                                    {{-- Student --}}
                                    @if($user->role == 'student')
                                        <span class="role-dot" style="background:green;" data-role="Student"></span>
                                    @endif

                                    {{-- Instructor --}}
                                    @if(str_contains($user->role, 'instructor'))
                                        <span class="role-dot" style="background:orange;" data-role="Instructor"></span>
                                    @endif

                                    {{-- Dean --}}
                                    @if(str_contains($user->role, 'dean'))
                                        <span class="role-dot" style="background:red;" data-role="Dean"></span>
                                    @endif

                                    {{-- Registrar --}}
                                    @if($user->role == 'registrar')
                                        <span class="role-dot" style="background:violet;" data-role="Registrar"></span>
                                    @endif

                                    {{-- Admin --}}
                                    @if($user->role == 'admin')
                                        <span class="role-dot" style="background:black;" data-role="Admin"></span>
                                    @endif

                                </div>
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <i class="bi bi-pencil-square editUserBtn"
                                data-id="{{ $user->studentID }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-gender="{{ $user->gender }}"
                                data-department="{{ $user->department }}"
                                data-role="{{ $user->role }}">
                                </i>

                                <!-- Delete Button -->
                                <form action="{{ route('user.destroy') }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="user_id" value="{{ $user->studentID }}">

                                    <button type="submit" style="border:none; background:none; cursor:pointer;">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection