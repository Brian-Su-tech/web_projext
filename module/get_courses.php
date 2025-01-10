<?php
require_once '../config.php';
header('Content-Type: application/json');

$sql = "SELECT c.*, u.username as teacher_name 
        FROM courses c 
        JOIN users u ON c.teacher_id = u.id";
$result = $conn->query($sql);

$events = array();
while ($row = $result->fetch_assoc()) {
    $events[] = array(
        'id' => $row['id'],
        'title' => $row['name'],
        'start' => $row['start_time'],
        'end' => $row['end_time'],
        'classroom' => $row['classroom'],
        'teacher' => $row['teacher_name']
    );
}

echo json_encode($events);
?>