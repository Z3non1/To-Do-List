<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's to-do lists from the database
$stmt = $conn->prepare("SELECT * FROM lists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lists = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }


        .navbar {
            margin-bottom: 30px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .profile-container {
            display: flex;
            align-items: center;
        }

        .profile-container img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .profile-container .username {
            font-weight: bold;
            margin-right: 15px;
        }

        .progress-bar {
            width: 100%;
            background-color: #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .progress-bar-inner {
            height: 10px;
            background-color: #28a745;
            border-radius: 5px;
            width: 0;
        }

        .dark-mode .progress-bar {
            background-color: #6c757d;
        }

        .task-list {
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .task.completed span {
            text-decoration: line-through;
            color: #28a745;
        }

        .task.incomplete span {
            color: #dc3545;
        }

        .toggle-complete {
            margin-right: 10px;
            cursor: pointer;
        }

        .add-task-form {
            display: flex;
            align-items: center;
        }

        .add-task-form input[type="text"] {
            flex: 1;
            margin-right: 10px;
        }

        .filter-container {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-container input {
        margin-right: 10px; /* Menambahkan jarak antara search bar dan filter */
    }


        .form-inline .dropdown-toggle::after {
            margin-left: 5px;
        }

        .task-list ul {
            list-style-type: none;
            padding-left: 0;
        }

        .task-list .delete-task-btn, .delete-list-btn {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .dark-mode {
        background-color: #000000;
        color: #ffffff;
    }

    .dark-mode .task-list {
        background-color: #222222;
    }

    .dark-mode .task.completed span {
        color: #ffffff; /* Warna putih untuk teks tugas yang sudah selesai */
    }

    .dark-mode .task.incomplete span {
        color: #cccccc; /* Warna abu-abu untuk teks tugas yang belum selesai */
    }

    .dark-mode .navbar {
        background-color: #333333;
    }

    .dark-mode .btn {
        background-color: #555555;
        color: #ffffff;
        border-color: #555555;
    }

    .dark-mode .btn-outline-info {
        color: #ffffff;
        border-color: #ffffff;
    }

    .dark-mode .btn-outline-dark {
        color: #ffffff;
        border-color: #ffffff;
    }

    .dark-mode .progress-bar-inner {
        background-color: #ffffff;
    }

    .dark-mode .delete-task-btn,
    .dark-mode .delete-list-btn {
        color: #ffffff;
        background-color: #444444;
    }

    .dark-mode .modal-confirm {
        color: #ffffff;
        background-color: #333333;
    }
        .modal-confirm {
            color: #434e65;
            width: 400px;
        }

        .modal-confirm .modal-content {
            padding: 20px;
            border-radius: 5px;
            border: none;
        }

        .modal-confirm .modal-header {
            border-bottom: none;
            position: relative;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .modal-confirm h4 {
            text-align: center;
            font-size: 26px;
            margin: 30px 0 -15px;
        }

        .modal-confirm .close {
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .modal-confirm .modal-body {
            text-align: center;
            padding: 40px 20px;
        }

        .modal-confirm .modal-footer {
            text-align: center;
            border-top: none;
            padding: 20px 20px 30px;
        }

        .modal-confirm .modal-footer a {
            color: #999;
        }

        .modal-confirm .icon-box {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            z-index: 9;
            text-align: center;
            border: 3px solid #f15e5e;
        }

        .modal-confirm .icon-box i {
            color: #f15e5e;
            font-size: 46px;
            display: inline-block;
            margin-top: 13px;
        }

        .modal-confirm .btn {
            color: #fff;
            border-radius: 4px;
            background: #f15e5e;
            text-decoration: none;
            transition: all 0.4s;
            line-height: normal;
            border: none;
        }

        .modal-confirm .btn:hover, .modal-confirm .btn:focus {
            background: #f12b2b;
            outline: none;
        }
    
        .welcome-text {
            color: #343a40; /* Warna yang lebih halus (abu tua) */
            font-size: 1.25rem; /* Sedikit lebih kecil dari sebelumnya */
            margin-left: 10px; /* Mengurangi jarak dari kiri */
            padding: 5px 15px; /* Padding yang lebih kecil */
            background-color: rgba(255, 255, 255, 0.8); /* Latar belakang transparan agar lebih lembut */
            border-radius: 8px; /* Membuat sudut-sudut melengkung */
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">To-Do App</a>

        <div class="ml-auto profile-container align-items-center">
            <h1 class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
            <button class="btn btn-outline-dark ml-2" id="toggleDarkMode">Toggle Dark Mode</button>
            <a href="edit_profile.php" class="btn btn-outline-info ml-2">Edit Profile</a>
            <a href="logout.php" class="btn btn-danger ml-2">Logout</a>
        </div>

    </nav>

    <div class="container">
        <h3>Your To-Do Lists</h3>
        
        <div class="filter-container d-flex align-items-center">
            <input type="text" id="searchBar" class="form-control mr-2" placeholder="Search tasks">
            <select id="taskFilter" class="form-control">
                <option value="all">All Tasks</option>
                <option value="completed">Completed</option>
                <option value="incomplete">Incomplete</option>
            </select>
        </div>


        <!-- Form to add a new to-do list -->
        <div class="add-task-form mb-4">
            <input type="text" id="newToDoList" class="form-control" placeholder="Enter new to-do list name">
            <button class="btn btn-success" id="addList">Add List</button>
        </div>

        <div id="todoLists">
            <?php while ($list = $lists->fetch_assoc()) : ?>
                <div class="todo-list">
                    <h5 class="todo-title"><?= htmlspecialchars($list['list_name']); ?></h5>

                    <!-- Fetch tasks for this list -->
                    <?php
                    $list_id = $list['id'];
                    $stmt_tasks = $conn->prepare("SELECT * FROM tasks WHERE list_id = ?");
                    $stmt_tasks->bind_param("i", $list_id);
                    $stmt_tasks->execute();
                    $tasks = $stmt_tasks->get_result();

                    // Progress bar calculation
                    $total_tasks = $tasks->num_rows;
                    $completed_tasks = 0;
                    while ($task = $tasks->fetch_assoc()) {
                        if ($task['completed']) {
                            $completed_tasks++;
                        }
                    }
                    $progress = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;
                    $tasks->data_seek(0); // Reset result pointer
                    ?>

                    <div class="progress-bar">
                        <div class="progress-bar-inner" style="width: <?= $progress; ?>%;"></div>
                    </div>

                    <div class="task-list">
                        <ul class="tasks">
                            <?php while ($task = $tasks->fetch_assoc()) : ?>
                                <?php
                                $checked = $task['completed'] ? 'checked' : '';
                                $class = $task['completed'] ? 'completed' : 'incomplete';
                                ?>
                                <li class="task <?= $class; ?>">
                                    <input type="checkbox" class="toggle-complete" data-task-id="<?= $task['id']; ?>" <?= $checked; ?>>
                                    <span><?= htmlspecialchars($task['task_name']); ?></span>
                                    <button class="btn btn-danger btn-sm delete-task-btn" data-task-id="<?= $task['id']; ?>">Delete</button>
                                </li>
                            <?php endwhile; ?>
                        </ul>

                        <!-- Add new task -->
                        <div class="add-task-form">
                            <input type="text" class="form-control newTaskInput" placeholder="Add a new task">
                            <button class="btn btn-primary addTaskBtn" data-list-id="<?= $list['id']; ?>">Add Task</button>
                        </div>
                        
                        <!-- Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <p id="modal-message">Are you sure you want to delete this?</p>
        <button id="confirmBtn" class="btn-confirm">Yes</button>
        <button id="cancelBtn" class="btn-cancel">Cancel</button>
    </div>
</div>

<style>

    .add-task-form {
    margin-bottom: 10px; /* Menambah jarak bawah */
}

    </style>
                        <!-- Delete list button -->
                        <button class="btn btn-outline-danger delete-list-btn" data-list-id="<?= $list['id']; ?>">Delete List</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script>
        
            // Toggle dark mode
            function toggleDarkMode() {
                $("body").toggleClass("dark-mode");
                let isDarkMode = $("body").hasClass("dark-mode");
                localStorage.setItem("darkMode", isDarkMode ? "enabled" : "disabled");
            }

            // Check localStorage for dark mode preference on page load
            if (localStorage.getItem("darkMode") === "enabled") {
                $("body").addClass("dark-mode");
            }

            $("#toggleDarkMode").click(toggleDarkMode);

        // Handle task completion
        document.querySelectorAll('.toggle-complete').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var taskId = this.getAttribute('data-task-id');
                var isChecked = this.checked ? 1 : 0;

                $.ajax({
                    url: 'update_task.php',
                    method: 'POST',
                    data: { task_id: taskId, completed: isChecked },
                    success: function (response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating task completion status.');
                    }
                });
            });
        });

        // Handle task deletion
        document.querySelectorAll('.delete-task-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var taskId = this.getAttribute('data-task-id');

                $.ajax({
                    url: 'delete_task.php',
                    method: 'POST',
                    data: { task_id: taskId },
                    success: function (response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting task.');
                    }
                });
            });
        });

        // Handle adding a new task
        document.querySelectorAll('.addTaskBtn').forEach(function (button) {
            button.addEventListener('click', function () {
                var listId = this.getAttribute('data-list-id');
                var taskInput = this.previousElementSibling;
                var taskName = taskInput.value.trim();

                if (taskName) {
                    $.ajax({
                        url: 'add_task.php',
                        method: 'POST',
                        data: { list_id: listId, task_name: taskName },
                        success: function (response) {
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('Error adding new task.');
                        }
                    });
                }
            });
        });

        // Handle adding a new to-do list
        document.getElementById('addList').addEventListener('click', function () {
            var listName = document.getElementById('newToDoList').value.trim();

            if (listName) {
                $.ajax({
                    url: 'add_list.php',
                    method: 'POST',
                    data: { list_name: listName },
                    success: function (response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error adding new list.');
                    }
                });
            }
        });

        // Handle list deletion
        document.querySelectorAll('.delete-list-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var listId = this.getAttribute('data-list-id');

                $.ajax({
                    url: 'delete_list.php',
                    method: 'POST',
                    data: { list_id: listId },
                    success: function (response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting list.');
                    }
                });
            });
        });

        // Search task
        document.getElementById('searchBar').addEventListener('input', function () {
            var searchValue = this.value.toLowerCase();
            document.querySelectorAll('.tasks .task').forEach(function (task) {
                var taskName = task.querySelector('span').textContent.toLowerCase();
                if (taskName.includes(searchValue)) {
                    task.style.display = '';
                } else {
                    task.style.display = 'none';
                }
            });
        });

        // Filter task
        document.getElementById('taskFilter').addEventListener('change', function () {
            var filterValue = this.value;
            document.querySelectorAll('.tasks .task').forEach(function (task) {
                if (filterValue === 'all') {
                    task.style.display = '';
                } else if (filterValue === 'completed' && task.classList.contains('completed')) {
                    task.style.display = '';
                } else if (filterValue === 'incomplete' && task.classList.contains('incomplete')) {
                    task.style.display = '';
                } else {
                    task.style.display = 'none';
                }
            });
        });


    </script>
    
</body>
</html>
