<?php
session_start();
require_once 'config.php'; // Using your shared config file connection

if (isset($_POST['submit_marks'])) {
    // 1. Check if the form actually submitted student data
    if (!isset($_POST['cat']) || empty($_POST['cat'])) {
        header("Location: admin.php?tab=marks&status=error&msg=no_students");
        exit();
    }

    $unit_id = (int)$_POST['unit_id'];
    $cat_marks = $_POST['cat'];
    $exam_marks = $_POST['exam'] ?? [];

    // 2. Prepare statements ONCE outside the loop (Massive speed boost)
    $check_stmt = $conn->prepare("SELECT id FROM marks WHERE student_id = ? AND unit_id = ?");
    $update_stmt = $conn->prepare("UPDATE marks SET cat = ?, exam = ?, total = ?, grade = ? WHERE student_id = ? AND unit_id = ?");
    $insert_stmt = $conn->prepare("INSERT INTO marks (student_id, unit_id, cat, exam, total, grade) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($cat_marks as $student_id => $cat_val) {
        $student_id = (int)$student_id;
        $cat = (int)$cat_val;
        $exam = isset($exam_marks[$student_id]) ? (int)$exam_marks[$student_id] : 0;
        $total = $cat + $exam;

        // 3. Grading Logic
        if($total >= 70) $grade = 'A';
        elseif($total >= 60) $grade = 'B';
        elseif($total >= 50) $grade = 'C';
        elseif($total >= 40) $grade = 'D';
        else $grade = 'E';

        // 4. Check if record exists
        $check_stmt->bind_param("ii", $student_id, $unit_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Update
            $update_stmt->bind_param("iiisii", $cat, $exam, $total, $grade, $student_id, $unit_id);
            $exec = $update_stmt->execute();
            if (!$exec) { die("Update Failed: " . $update_stmt->error); }
        } else {
            // Insert
            $insert_stmt->bind_param("iiiiis", $student_id, $unit_id, $cat, $exam, $total, $grade);
            $exec = $insert_stmt->execute();
            if (!$exec) { die("Insert Failed: " . $insert_stmt->error); }
        }
    }

    // 5. Close connections after the loop finishes!
    $check_stmt->close();
    $update_stmt->close();
    $insert_stmt->close();
    $conn->close();

    header("Location: admin.php?tab=marks&status=success");
    exit();
} else {
    // If someone tries to access this file directly without posting data
    header("Location: admin.php?tab=marks");
    exit();
}
?>