@extends('layouts.default')
@vite(['resources/css/departments.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@section('content')
    <div class="departments-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Departments</span>
        </div>
        <h2 class="my-header">
            Departments
        </h2>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchUser" id="searchUser" placeholder="Quick Search...">
            </form>
        </div>
        <div class="department-modal-container" id="departmentModal">
            <h2 class="department-modal-header">
                Create New Department
            </h2>
            <div class="department-form-container">
               <form method="POST" action="{{ route('departments.store') }}">
                    @csrf
                    <div class="form-info">
                        <label for="department_code">Department Code:</label>
                        <input type="text" id="department_code" name="department_code" placeholder="Enter Department Code..." value="{{ old('department_code') }}">
                        @error('department_code')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-info">
                        <label for="department_name">Department Name:</label>
                        <input type="text" id="department_name" name="department_name" placeholder="Enter Department Name..." value="{{ old('department_name') }}">
                        @error('department_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="department-modalBtn">
                        <button type="submit" name="submit">Create</button>
                        <button type="button" id="closeDeptModal">Close</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="departments-wrapper-container">
            <div class="iconBtbn">
                <span class="fa-stack fa-2x iconBtn">
                    <!-- Circle background -->
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                    <!-- Layers icon -->
                <i class="fa-solid fa-book-open fa-stack-1x layers-icon"></i>
                    <!-- Plus icon -->
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
                </span>
            </div>
            <table class="departments-table-container">
                <thead>
                    <tr>
                        <th>Department Code</th>
                        <th>Department Name</th>
                        <th>Create At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BSCS</td>
                        <td>Bachelor Science in Computer Science</td>
                        <td>2025-15-12</td>
                        <td>
                            <i class="bi bi-pencil-square"></i>
                            <i class="bi bi-trash"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<script>
    const openBtn = document.querySelector('.iconBtn');
    const modal = document.getElementById('departmentModal');

    // OPEN MODAL
    openBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    // OPTIONAL: CLICK OUTSIDE TO CLOSE
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    document.getElementById('closeDeptModal').addEventListener('click', () => {
    modal.style.display = 'none';
});
</script>

@endsection