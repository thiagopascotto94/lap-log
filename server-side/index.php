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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo CRIAR - Desafio de Lógica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Racing+Sans+One&display=swap" rel="stylesheet">
    <style>
        .f1-bg { background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #333333 100%); }
        .podium-gold { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }
        .podium-silver { background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%); }
        .podium-bronze { background: linear-gradient(135deg, #CD7F32 0%, #A0522D 100%); }
        .animate-fade-in { animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .hover-scale { transition: transform 0.3s ease; }
        .hover-scale:hover { transform: scale(1.05); }
    </style>
</head>
<body class="f1-bg min-h-screen py-8 text-white">
    <div class="container mx-auto px-4 max-w-7xl">
        <header class="text-center mb-16 animate-fade-in">
            <h1 class="text-6xl font-bold text-red-500 mb-4" style="font-family: 'Racing Sans One', cursive;">Resultado da Corrida</h1>
            <p class="text-xl text-gray-300">Desafio de Lógica - Grupo CRIAR</p>
            <div class="mt-4 flex justify-center space-x-4">
                <div class="w-16 h-1 bg-red-500"></div>
                <div class="w-16 h-1 bg-white"></div>
                <div class="w-16 h-1 bg-red-500"></div>
            </div>
        </header>

        <section class="mb-16 animate-fade-in">
            <h2 class="text-3xl font-bold text-center text-white mb-8" style="font-family: 'Racing Sans One', cursive;">🏆 Placar (Pódio)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($podium as $index => $lap): ?>
                <div class="hover-scale bg-gray-800 rounded-xl shadow-2xl overflow-hidden border-4 <?php echo $index === 0 ? 'border-yellow-400 podium-gold' : ($index === 1 ? 'border-gray-400 podium-silver' : 'border-orange-400 podium-bronze'); ?> p-6 text-center">
                    <div class="text-6xl mb-4"><?php echo $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉'); ?></div>
                    <h3 class="text-2xl font-bold text-white mb-2"><?php echo $lap['Piloto']; ?></h3>
                    <p class="text-gray-300">Posição: <?php echo $index + 1; ?>º</p>
                    <p class="text-gray-300">Hora: <?php echo $lap['Hora']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mb-16 animate-fade-in">
            <h2 class="text-3xl font-bold text-center text-white mb-8" style="font-family: 'Racing Sans One', cursive;">📊 Dados Exclusivos de Cada Piloto</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <?php foreach ($pilots as $pilot => $laps): ?>
                <div class="hover-scale bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-red-500">
                    <h3 class="px-6 py-4 bg-red-600 text-xl font-bold text-white"><?php echo $pilot; ?></h3>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase">Volta</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase">Hora</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase">Tempo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase">Vel. Média</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-600">
                                <?php foreach ($laps as $lap): ?>
                                <tr class="hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-4 py-2 text-sm text-white"><?php echo $lap['Nº Volta']; ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-300"><?php echo $lap['Hora']; ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-300"><?php echo $lap['Tempo Volta']; ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-300"><?php echo $lap['Velocidade Média da Volta']; ?> km/h</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mb-16 animate-fade-in">
            <h2 class="text-3xl font-bold text-center text-white mb-8" style="font-family: 'Racing Sans One', cursive;">⚡ Melhor Volta de Cada Piloto</h2>
            <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-red-500">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-red-600">
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Piloto</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Volta</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Tempo</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Vel. Média</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-600">
                            <?php foreach ($bestLaps as $pilot => $lap): ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo $pilot; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-300"><?php echo $lap['Nº Volta']; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-300"><?php echo $lap['Tempo Volta']; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-300"><?php echo $lap['Velocidade Média da Volta']; ?> km/h</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="mb-16 animate-fade-in">
            <h2 class="text-3xl font-bold text-center text-white mb-8" style="font-family: 'Racing Sans One', cursive;">🚀 Média de Velocidade na Corrida</h2>
            <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-red-500">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-red-600">
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Piloto</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Média Vel. (km/h)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-600">
                            <?php foreach ($averageSpeeds as $pilot => $avgSpeed): ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo $pilot; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-300"><?php echo number_format($avgSpeed, 3, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="mb-16 animate-fade-in">
            <h2 class="text-3xl font-bold text-center text-white mb-8" style="font-family: 'Racing Sans One', cursive;">⏱️ Tempo de Chegada Após o Vencedor</h2>
            <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-red-500">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-red-600">
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Piloto</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase">Tempo Após Vencedor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-600">
                            <?php foreach ($arrivalTimes as $pilot => $time): ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-bold text-white"><?php echo $pilot; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-300"><?php echo $time; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
