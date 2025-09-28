<?php
// Function to compute the podium
function getPodium($data) {
    // Filter pilots who completed 4 laps
    $completedLaps = array_filter($data, function($lap) {
        return $lap['Nº Volta'] == 4;
    });

    // Sort by Hora ascending (earliest first)
    usort($completedLaps, function($a, $b) {
        return timeToSeconds($a['Hora']) <=> timeToSeconds($b['Hora']);
    });

    return array_slice($completedLaps, 0, 3);
}
?>
