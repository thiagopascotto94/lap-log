<?php
// Function to compute best lap per pilot
function getBestLaps($data) {
    $pilots = getPilots($data);
    $bestLaps = [];
    foreach ($pilots as $pilot => $laps) {
        // Sort by Tempo Volta ascending (fastest first)
        usort($laps, function($a, $b) {
            return lapTimeToSeconds($a['Tempo Volta']) <=> lapTimeToSeconds($b['Tempo Volta']);
        });
        $bestLaps[$pilot] = $laps[0];
    }
    return $bestLaps;
}
?>
