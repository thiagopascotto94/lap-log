<?php
// Function to compute average speed per pilot
function getAverageSpeeds($data) {
    $pilots = getPilots($data);
    $averageSpeeds = [];
    foreach ($pilots as $pilot => $laps) {
        $totalSpeed = 0;
        foreach ($laps as $lap) {
            $totalSpeed += floatval(str_replace(',', '.', $lap['Velocidade Média da Volta']));
        }
        $averageSpeeds[$pilot] = $totalSpeed / count($laps);
    }
    return $averageSpeeds;
}
?>
