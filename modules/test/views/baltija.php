<div class="container">

<?php
// List of hydro stations
$stations = [
    'klaipedos-vms' => 'Klaipėdos VMS',
    'palangos-vms' => 'Palangos VMS',
    'juodkrantes-vms' => 'Juodkrantės VMS',
    'klaipedos-juru-uosto-vms' => 'Uosto VMS'
];

// Function to fetch data from API
function getStationData($code) {
    $url = "https://api.meteo.lt/v1/hydro-stations/$code/observations/measured/latest";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Fetch data for all stations
$allData = [];
foreach ($stations as $code => $name) {
    $stationData = getStationData($code);
    if ($stationData && isset($stationData['observations'])) {
        foreach ($stationData['observations'] as $obs) {
            $time = $obs['observationTimeUtc'];
            $temp = $obs['waterTemperature'];
            $allData[$time][$name] = $temp;
        }
    }
}

// Sort times descending (latest first)
krsort($allData);
?>
    <h1>Vandens temperatūros stebėjimai</h1>
    <p>Rodomos naujausios temperatūros reikšmės visose stotyse.</p>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Laikas (UTC)</th>
            <?php foreach ($stations as $code => $name): ?>
                <th><?= htmlspecialchars($name) ?> (°C)</th>
            <?php endforeach; ?>
        </tr>

        <?php foreach ($allData as $time => $temps): ?>
            <tr>
                <td><?= htmlspecialchars($time) ?></td>
                <?php foreach ($stations as $code => $name): ?>
                    <td>
                        <?= isset($temps[$name]) ? htmlspecialchars($temps[$name]) : '-' ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

</div>