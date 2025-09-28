<?php
// Load the JSON data
$data = json_decode(file_get_contents('../db/laps.json'), true);

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

// Step 2: Display Scoreboard (Podium)
// Filter pilots who completed 4 laps
$completedLaps = array_filter($data, function($lap) {
    return $lap['Nº Volta'] == 4;
});

// Sort by Hora ascending (earliest first)
usort($completedLaps, function($a, $b) {
    return timeToSeconds($a['Hora']) <=> timeToSeconds($b['Hora']);
});

$podium = array_slice($completedLaps, 0, 3);

// Step 3: Unique pilot data
$pilots = [];
foreach ($data as $lap) {
    $pilot = $lap['Piloto'];
    if (!isset($pilots[$pilot])) {
        $pilots[$pilot] = [];
    }
    $pilots[$pilot][] = $lap;
}

// Step 4: Best lap per pilot
$bestLaps = [];
foreach ($pilots as $pilot => $laps) {
    // Sort by Tempo Volta ascending (fastest first)
    usort($laps, function($a, $b) {
        return lapTimeToSeconds($a['Tempo Volta']) <=> lapTimeToSeconds($b['Tempo Volta']);
    });
    $bestLaps[$pilot] = $laps[0];
}

// Step 5: Average speed per pilot
$averageSpeeds = [];
foreach ($pilots as $pilot => $laps) {
    $totalSpeed = 0;
    foreach ($laps as $lap) {
        $totalSpeed += floatval(str_replace(',', '.', $lap['Velocidade Média da Volta']));
    }
    $averageSpeeds[$pilot] = $totalSpeed / count($laps);
}

// Step 6: Arrival times relative to winner
$winner = $podium[0];
$winnerTime = timeToSeconds($winner['Hora']);
$arrivalTimes = [];
foreach ($completedLaps as $lap) {
    if ($lap['Piloto'] !== $winner['Piloto']) {
        $timeDiff = timeToSeconds($lap['Hora']) - $winnerTime;
        $minutes = floor($timeDiff / 60);
        $seconds = $timeDiff % 60;
        $arrivalTimes[$lap['Piloto']] = sprintf('%02d:%05.2f', $minutes, $seconds);
    }
}

// Output the results
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Grupo CRIAR - Desafio de Lógica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Resultado da Corrida</h1>

    <h2>Passo 2: Placar (Pódio)</h2>
    <table>
        <tr><th>Posição</th><th>Piloto</th><th>Hora de Chegada</th></tr>
        <?php foreach ($podium as $index => $lap): ?>
        <tr>
            <td><?php echo $index + 1; ?>º</td>
            <td><?php echo $lap['Piloto']; ?></td>
            <td><?php echo $lap['Hora']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Passo 3: Dados Exclusivos de Cada Piloto</h2>
    <?php foreach ($pilots as $pilot => $laps): ?>
    <h3><?php echo $pilot; ?></h3>
    <table>
        <tr><th>Nº Volta</th><th>Hora</th><th>Tempo Volta</th><th>Velocidade Média</th></tr>
        <?php foreach ($laps as $lap): ?>
        <tr>
            <td><?php echo $lap['Nº Volta']; ?></td>
            <td><?php echo $lap['Hora']; ?></td>
            <td><?php echo $lap['Tempo Volta']; ?></td>
            <td><?php echo $lap['Velocidade Média da Volta']; ?> km/h</td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endforeach; ?>

    <h2>Passo 4: Melhor Volta de Cada Piloto</h2>
    <table>
        <tr><th>Piloto</th><th>Nº Volta</th><th>Tempo Volta</th><th>Velocidade Média</th></tr>
        <?php foreach ($bestLaps as $pilot => $lap): ?>
        <tr>
            <td><?php echo $pilot; ?></td>
            <td><?php echo $lap['Nº Volta']; ?></td>
            <td><?php echo $lap['Tempo Volta']; ?></td>
            <td><?php echo $lap['Velocidade Média da Volta']; ?> km/h</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Passo 5: Média de Velocidade na Corrida de Cada Piloto</h2>
    <table>
        <tr><th>Piloto</th><th>Média de Velocidade (km/h)</th></tr>
        <?php foreach ($averageSpeeds as $pilot => $avgSpeed): ?>
        <tr>
            <td><?php echo $pilot; ?></td>
            <td><?php echo number_format($avgSpeed, 3, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Passo 6: Tempo de Chegada do Segundo Colocado em Diante</h2>
    <table>
        <tr><th>Piloto</th><th>Tempo Após o Vencedor</th></tr>
        <?php foreach ($arrivalTimes as $pilot => $time): ?>
        <tr>
            <td><?php echo $pilot; ?></td>
            <td><?php echo $time; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
