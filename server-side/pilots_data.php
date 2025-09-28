<?php
// Function to get unique pilot data
function getPilots($data) {
    $pilots = [];
    foreach ($data as $lap) {
        $pilot = $lap['Piloto'];
        if (!isset($pilots[$pilot])) {
            $pilots[$pilot] = [];
        }
        $pilots[$pilot][] = $lap;
    }
    return $pilots;
}
?>
