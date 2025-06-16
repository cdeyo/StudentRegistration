<?php
session_start();
require_once 'databasehandler.php';

if (!isset($_SESSION['user'])) {
    die("You must be logged in to register for classes.");
}

$db = new DatabaseHandler();
$student_id = $_SESSION['user']['student_id'];

// Handle registration on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

     // Check if student is already actively enrolled
     $checkEnrollment = $db->executeSelectQuery("
         SELECT 1 FROM enrollment 
         WHERE student_id = :sid AND course_id = :cid AND status = 'enrolled'
     ", [':sid' => $student_id, ':cid' => $course_id]);

     // Check if student is currently on the waitlist
     $checkWaitlist = $db->executeSelectQuery("
         SELECT 1 FROM waitlist 
         WHERE student_id = :sid AND course_id = :cid
     ", [':sid' => $student_id, ':cid' => $course_id]);

     if ($checkEnrollment || $checkWaitlist) {
         $message = "You are already enrolled or on the waitlist for this course.";
     } else {
    
        // Get counts
        $result = $db->executeSelectQuery("
            SELECT 
                c.max_students,
                COUNT(e.enrollment_id) AS enrolled_count
            FROM course c
            LEFT JOIN enrollment e ON c.course_id = e.course_id AND e.status = 'enrolled'
            WHERE c.course_id = :cid
            GROUP BY c.max_students
        ", [':cid' => $course_id]);

        $enrolled_count = $result[0]['enrolled_count'] ?? 0;
        $max_students = $result[0]['max_students'] ?? 0;

        if ($enrolled_count < $max_students) {
            // Enroll the student
            $success = $db->executeQuery("
                INSERT INTO enrollment (student_id, course_id, status) 
                VALUES (:sid, :cid, 'enrolled')
            ", [':sid' => $student_id, ':cid' => $course_id]);

            $message = $success ? "Enrolled successfully!" : "Error enrolling.";
        } else {
            // Add to waitlist
            $success = $db->executeQuery("
                INSERT INTO waitlist (student_id, course_id) 
                VALUES (:sid, :cid)
            ", [':sid' => $student_id, ':cid' => $course_id]);

            $message = $success ? "Course full. You’ve been added to the waitlist." : "Error waitlisting.";
        }
    }
}

// Get all available courses and enrollment counts
$allCourses = $db->executeSelectQuery("
    SELECT 
        c.course_id,
        c.course_name,
        c.semester,
        c.max_students,
        COUNT(e.enrollment_id) AS enrolled_count
    FROM course c
    LEFT JOIN enrollment e ON c.course_id = e.course_id AND e.status = 'enrolled'
    GROUP BY c.course_id, c.course_name, c.semester, c.max_students
    ORDER BY c.course_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll in Courses</title>
</head>
<body>
    <h1>Available Courses</h1>

    <?php if (!empty($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <?php if ($allCourses && count($allCourses) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Semester</th>
                    <th>Max Students</th>
                    <th>Enrolled</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allCourses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_id']) ?></td>
                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                        <td><?= htmlspecialchars($course['semester']) ?></td>
                        <td><?= htmlspecialchars($course['max_students']) ?></td>
                        <td><?= htmlspecialchars($course['enrolled_count']) ?></td>
                        <td>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                <button type="submit">Register</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No courses available.</p>
    <?php endif; ?>

    <br>
     <nav>
       <ul>
         <li><a href="home.php">Home</a></li>
       </ul>
     </nav>
  <footer>
    <p>&copy; 2025 UAGC Online Course Registration System. All rights reserved.</p>
  </footer>
</body>
</html>
