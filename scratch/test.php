<?php
$group = ['id' => 'G1', 'name' => 'Group 1'];
$students = [
    ['id' => '1', 'groupId' => 'G1', 'status' => false, 'statusReturn' => false],
    ['id' => '2', 'groupId' => 'G1', 'status' => false, 'statusReturn' => false]
];

$groupStudents = array_filter($students, function($s) use ($group) { return $s['groupId'] === $group['id']; });
$unpicked = array_filter($groupStudents, function($s) { return !$s['status']; });
$unreturned = array_filter($groupStudents, function($s) { return !$s['statusReturn']; });

$currentScanMode = 'pickup';

if ($currentScanMode === 'pickup') {
    if (count($unpicked) > 0) {
        foreach ($students as &$s) {
            if ($s['groupId'] === $group['id']) {
                $s['status'] = true;
                $s['scanTime'] = '12:00';
            }
        }
        
        $s_unpicked = count(array_filter($students, function($s) use ($group) { return $s['groupId'] === $group['id'] && !$s['status']; }));
        $s_unreturned = count(array_filter($students, function($s) use ($group) { return $s['groupId'] === $group['id'] && !$s['statusReturn']; }));
        $len = count($groupStudents);

        $pickupBadge = 'Belum Ambil';
        if ($s_unpicked === 0 && $len > 0) $pickupBadge = 'Sudah Ambil';
        else if ($s_unpicked < $len) $pickupBadge = 'Sebagian Ambil';

        $returnBadge = 'Belum Kembali';
        if ($s_unreturned === 0 && $len > 0) $returnBadge = 'Sudah Kembali';
        else if ($s_unreturned < $len) $returnBadge = 'Sebagian Kembali';

        echo "pickupBadge: $pickupBadge\n";
        echo "returnBadge: $returnBadge\n";
    }
}
