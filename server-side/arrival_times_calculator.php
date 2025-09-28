<?php
// Function to compute arrival times relative to winner
function getArrivalTimes($data) {
    $podium = getPodium($data);
    $winner = $podium[0];
    $winnerTime = timeToSeconds($winner['Hora']);
    $completedLaps = array_filter($data, function($lap) {
        return $lap['Nº Volta'] == 4;
    });
    $arrivalTimes = [];
    foreach ($completedLaps as $lap) {
        if ($lap['Piloto'] !== $winner['Piloto']) {
            $timeDiff = timeToSeconds($lap['Hora']) - $winnerTime;
            $minutes = floor($timeDiff / 60);
            $remainingSeconds = $timeDiff - ($minutes * 60);
            $arrivalTimes[$lap['Piloto']] = sprintf('%02d:%05.2f', $minutes, $remainingSeconds);
        }
    }
    return $arrivalTimes;
}
?>
