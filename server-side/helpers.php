<?php
// Helper function to convert time to seconds
function timeToSeconds($time) {
    list($h, $m, $s) = explode(':', $time);
    $s = floatval(str_replace(',', '.', $s));
    return $h * 3600 + $m * 60 + $s;
}

// Helper function to convert lap time to seconds
function lapTimeToSeconds($time) {
    list($m, $s) = explode(':', $time);
    $s = floatval(str_replace(',', '.', $s));
    return $m * 60 + $s;
}
?>
