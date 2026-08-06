<?php
$html = file_get_contents('../MBG.html');

if (preg_match('/const initialStudents = (\[.*?\]);/s', $html, $matches)) {
    $students = json_decode($matches[1], true);
    foreach ($students as &$s) {
        if (!isset($s['statusReturn'])) {
            $s['statusReturn'] = false;
        }
        if (!isset($s['returnTime'])) {
            $s['returnTime'] = null;
        }
    }
    
    $newStudentsJson = json_encode($students, JSON_PRETTY_PRINT);
    $html = str_replace($matches[1], $newStudentsJson, $html);
    
    $html = str_replace('mbg_groups_v3', 'mbg_groups_v4', $html);
    $html = str_replace('mbg_students_v3', 'mbg_students_v4', $html);
    
    file_put_contents('../MBG.html', $html);
    echo "Schema upgraded successfully!";
} else {
    echo "Could not find initialStudents array.";
}
