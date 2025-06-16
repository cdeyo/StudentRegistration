<?php
session_start();
require_once 'databasehandler.php';

if (!isset($_SESSION['user'])) {
    die("You must be logged in to perform this action.");
}

$db = new DatabaseHandler();
$student_id = $_SESSION['user']['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];

    // Check if already enrolled or waitlisted
    $check = $db->executeSelectQuery("
        SELECT * FROM enrollment WHERE student_id = :sid AND course_id = :cid
        UNION
        SELECT * FROM waitlist WHERE student_id = :sid AND course_id = :cid
    ", [':sid' => $student_id, ':cid' => $course_id]);

    if ($check && count($check) > 0) {
        echo "You are already enrolled or waitlisted for this course.";
        exit;
    }

    // Check current enrollment count
    $count = $db->executeSelectQuery("
        SELECT COUNT(*) as total FROM enrollment 
        WHERE course_id = :cid AND status = 'enrolled'
    ", [':cid' => $course_id])[0]['total'];

    // Get max_students for the course
    $max = $db->executeSelectQuery("
        SELECT max_students FROM course WHERE course_id = :cid
    ", [':cid' => $course_id])[0]['max_students'];

    if ($count < $max) {
        // Enroll directly
        $enroll = $db->executeQuery("
            INSERT INTO enrollment (student_id, course_id, status) 
            VALUES (:sid, :cid, 'enrolled')
        ", [':sid' => $student_id, ':cid' => $course_id]);

        echo $enroll ? "Successfully enrolled!" : "Error enrolling.";
    } else {
        // Add to waitlist
        $wait = $db->executeQuery("
            INSERT INTO waitlist (student_id, course_id) 
            VALUES (:sid, :cid)
        ", [':sid' => $student_id, ':cid' => $course_id]);

        echo $wait ? "That course is full. You have been added to the waitlist." : "Error adding to waitlist.";
    }
} else {
    echo "Invalid request.";
}
?>
<br>
<a href="enroll.php">Enroll</a>
