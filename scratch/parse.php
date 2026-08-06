<?php
$lines = file('data.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$groups = [];
$students = [];

$current_class = "";
$current_group_name = "";
$current_group_id = "";

$group_counter = 1;
$student_counter = 1;

$colors = ['#e11d48', '#2563eb', '#16a34a', '#d97706', '#9333ea', '#0ea5e9', '#ec4899'];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    if (strpos($line, 'Kelas') === 0) {
        if (preg_match('/Kelas\s+(.*?)\s*\(/', $line, $matches)) {
            $current_class = trim($matches[1]);
        }
    } elseif (strpos($line, 'Kelompok') === 0) {
        if (preg_match('/Kelompok\s+(\d+)/', $line, $matches)) {
            $group_num = $matches[1];
            $current_group_name = "Kelompok $group_num ($current_class)";
            $current_group_id = "G$group_counter";
            $color = $colors[($group_counter - 1) % count($colors)];
            $groups[] = [
                'id' => $current_group_id,
                'name' => $current_group_name,
                'color' => $color
            ];
            $group_counter++;
        }
    } elseif (preg_match('/^(\d+\.|•)\s+(.*)/', $line, $matches)) {
        $student_name = trim($matches[2]);
        $student_name = preg_replace('/\s*\(Ketua\)/', '', $student_name);
        
        $student_id = 'MBG' . str_pad($student_counter, 3, '0', STR_PAD_LEFT);
        $students[] = [
            'id' => $student_id,
            'name' => $student_name,
            'nis' => '-',
            'class' => $current_class,
            'groupId' => $current_group_id,
            'status' => false,
            'scanTime' => null
        ];
        $student_counter++;
    }
}

$output = "const initialGroups = " . json_encode($groups, JSON_PRETTY_PRINT) . ";\n\n";
$output .= "const initialStudents = " . json_encode($students, JSON_PRETTY_PRINT) . ";";

file_put_contents('output.js', $output);
echo "Done";
