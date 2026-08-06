<?php
$html = file_get_contents('../MBG.html');
$js_data = file_get_contents('output.js');

$pattern = '/const initialGroups = \[.*?\];\s*const initialStudents = \[.*?\];/s';

$html = preg_replace($pattern, $js_data, $html);

file_put_contents('../MBG.html', $html);
echo "Replaced successfully!";
