<?php
session_start();
include 'databasehandler.php';

if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view your courses.";
    exit;
}

$dbHandler = new DatabaseHandler();
$student_id = $_SESSION['user']['student_id'];

$sql = "SELECT c.course_id, c.course_name, c.semester, c.max_students, e.status, e.enrolled_at
        FROM enrollment e
        JOIN course c ON e.course_id = c.course_id
        WHERE e.student_id = :student_id AND e.status = 'enrolled'";
$params = [':student_id' => $student_id];

$courses = $dbHandler->executeSelectQuery($sql, $params);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Courses</title>
</head>
<body>
    <h1>My Courses</h1>

    <?php if ($courses && count($courses) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Name</th>
                    <th>Semester</th>
                    <th>Enrolled At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_id']) ?></td>
                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                        <td><?= htmlspecialchars($course['semester']) ?></td>
                        <td><?= htmlspecialchars($course['enrolled_at']) ?></td>
                        <td>
                            <form method="POST" action="process_drop_course.php" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                <button type="submit">Drop</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not enrolled in any courses yet.</p>
    <?php endif; ?>

     <nav>
       <ul>
         <li><a href="home.php">Home</a></li>
         <li><a href="enroll.php">Enroll</a></li>
       </ul>
     </nav>
  <footer>
    <p>&copy; 2025 UAGC Online Course Registration System. All rights reserved.</p>
  </footer>
</body>
</html>
