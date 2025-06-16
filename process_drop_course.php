<?php
session_start();
require_once 'databasehandler.php';

if (!isset($_SESSION['user'])) {
    die("You must be logged in to drop a course.");
}

$db = new DatabaseHandler();
$student_id = $_SESSION['user']['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Drop the course by setting the status to 'dropped'
    $drop = $db->executeQuery("
        UPDATE enrollment 
        SET status = 'dropped' 
        WHERE student_id = :sid AND course_id = :cid AND status = 'enrolled'
    ", [':sid' => $student_id, ':cid' => $course_id]);

    if ($drop) {
        echo "Course successfully dropped.";

        // Check if there are students on the waitlist
        $waitlist = $db->executeSelectQuery("
            SELECT * FROM waitlist 
            WHERE course_id = :cid 
            ORDER BY waitlist_time ASC 
            LIMIT 1
        ", [':cid' => $course_id]);

        if ($waitlist && count($waitlist) > 0) {
            $nextStudent = $waitlist[0]['student_id'];
            $waitlist_id = $waitlist[0]['waitlist_id'];

            // Enroll the next waitlisted student
            $db->executeQuery("
                INSERT INTO enrollment (student_id, course_id, status) 
                VALUES (:sid, :cid, 'enrolled')
            ", [':sid' => $nextStudent, ':cid' => $course_id]);

            // Remove them from the waitlist
            $db->executeQuery("
                DELETE FROM waitlist WHERE waitlist_id = :wid
            ", [':wid' => $waitlist_id]);

            echo "<br>Waitlisted student automatically enrolled.";
        }
    } else {
        echo "Error dropping the course or you're not enrolled.";
    }
} else {
    echo "Invalid request.";
}
?>
<br>
<a href="my_courses.php">My Courses</a>
