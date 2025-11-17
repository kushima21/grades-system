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
                    <tr>
                        <td>2001414</td>
                        <td>John Mark Hondrada</td>
                        <td>johnhondrada@ckcm.edu.ph</td>
                        <td>Bachelor Science in Computer Science</td>
                        <td>student</td>
                        <td>
                            <i class="bi bi-pencil-square"></i>
                            <i class="bi bi-trash"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection