<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$coinId = $_GET['coin'] ?? 'bitcoin';
$days = $_GET['days'] ?? 7;

// Validar días permitidos
$allowedDays = [1, 7, 14, 30, 90, 180, 365];
if (!in_array($days, $allowedDays)) {
    $days = 7;
}

// Cache por tipo de moneda y días
$cacheDir = __DIR__ . '/../cache/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$cacheKey = md5($coinId . $days);
$cacheFile = $cacheDir . 'chart_' . $cacheKey . '.json';

// Intentar leer del cache
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
    $cachedData = file_get_contents($cacheFile);
    if ($cachedData !== false) {
        echo $cachedData;
        exit;
    }
}

// Construir URL correcta para CoinGecko
$url = "https://api.coingecko.com/api/v3/coins/{$coinId}/market_chart?vs_currency=usd&days={$days}";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_SSL_VERIFYPEER => false, // Para Windows
    CURLOPT_FOLLOWLOCATION => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Verificar si la respuesta es válida
if ($httpCode !== 200 || empty($response)) {
    // Devolver estructura vacía pero válida
    $errorResponse = json_encode([
        'error' => true,
        'message' => 'No se pudieron obtener datos para ' . $coinId,
        'prices' => [],
        'market_caps' => [],
        'total_volumes' => []
    ]);
    echo $errorResponse;
    exit;
}

$decoded = json_decode($response, true);
if (!$decoded || !isset($decoded['prices'])) {
    $errorResponse = json_encode([
        'error' => true,
        'message' => 'Formato inválido para ' . $coinId,
        'prices' => [],
        'market_caps' => [],
        'total_volumes' => []
    ]);
    echo $errorResponse;
    exit;
}

// Guardar en cache y devolver
file_put_contents($cacheFile, $response);
echo $response;
?>