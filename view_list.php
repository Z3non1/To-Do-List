<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$list_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tasks WHERE list_id = ?");
$stmt->bind_param("i", $list_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Tasks in List</h3>
<ul>
    <?php while ($task = $result->fetch_assoc()): ?>
        <li>
            <?php echo htmlspecialchars($task['task_name']); ?> - 
            <?php echo $task['completed'] ? 'Completed' : 'Incomplete'; ?>
        </li>
    <?php endwhile; ?>
</ul>

<a href="create_task.php?list_id=<?php echo $list_id; ?>">Add Task</a>

<?php $stmt->close(); ?>
