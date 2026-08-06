<?php
$html = file_get_contents('../MBG.html');

if (preg_match('/const initialGroups = (\[.*?\]);/s', $html, $matches)) {
    $groups = json_decode($matches[1], true);
    foreach ($groups as &$g) {
        if (strpos($g['name'], 'TKJ 1') !== false) {
            $g['color'] = '#3b82f6'; // Tailwind Blue-500
        } elseif (strpos($g['name'], 'TKJ 2') !== false) {
            $g['color'] = '#a855f7'; // Tailwind Purple-500
        }
    }
    
    $newGroupsJson = json_encode($groups, JSON_PRETTY_PRINT);
    
    $html = str_replace($matches[1], $newGroupsJson, $html);
    
    $html = str_replace('mbg_groups_v2', 'mbg_groups_v3', $html);
    $html = str_replace('mbg_students_v2', 'mbg_students_v3', $html);
    
    file_put_contents('../MBG.html', $html);
    echo "Recolored successfully!";
} else {
    echo "Could not find initialGroups array.";
}
