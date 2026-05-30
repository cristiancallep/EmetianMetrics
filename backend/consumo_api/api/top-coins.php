<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Cache por 5 minutos (evita muchas peticiones)
$cacheFile = __DIR__ . '/../cache/top-coins.json';
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
    echo file_get_contents($cacheFile);
    exit;
}

$url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=20&page=1&sparkline=true&price_change_percentage=24h';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'CryptoDashboard/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo obtener datos']);
    exit;
}

// Guardar en cache
if (!is_dir(__DIR__ . '/../cache')) {
    mkdir(__DIR__ . '/../cache', 0777, true);
}
file_put_contents($cacheFile, $response);

echo $response;
?>