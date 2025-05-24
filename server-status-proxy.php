<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // CORS için

// Önbellek ayarları
$cache_file = __DIR__ . '/server_status_cache.json';
$cache_duration = 300; // 5 dakika

// Gelen istekleri al
$servers = isset($_GET['servers']) ? json_decode($_GET['servers'], true) : [];
if (empty($servers)) {
    echo json_encode(['error' => 'No servers provided']);
    exit;
}

// Önbelleği kontrol et
$cache_data = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) : [];
$updated_cache = $cache_data ?: [];
$needs_update = false;
$current_time = time();

// Sunucu durumlarını sorgula
$results = [];
foreach ($servers as $server) {
    $ip = $server['ip'];
    $port = $server['port'] ?? '25565';
    $key = md5($ip . ':' . $port);

    // Önbellekte varsa ve süresi dolmadıysa kullan
    if (isset($cache_data[$key]) && ($current_time - $cache_data[$key]['timestamp']) < $cache_duration) {
        $results[$key] = $cache_data[$key]['status'];
    } else {
        // Önbellekte yoksa veya süresi dolduysa sorgula
        $status = check_server_status($ip, $port);
        $results[$key] = $status;
        $updated_cache[$key] = [
            'status' => $status,
            'timestamp' => $current_time
        ];
        $needs_update = true;
    }
}

// Önbelleği güncelle
if ($needs_update) {
    file_put_contents($cache_file, json_encode($updated_cache));
}

echo json_encode($results);
exit;

// Minecraft sunucu durumunu kontrol eden fonksiyon
function check_server_status($ip, $port = '25565') {
    $socket = @fsockopen($ip, $port, $errno, $errstr, 2);
    if (!$socket) {
        return [
            'online' => false,
            'players' => 0,
            'max_players' => 0,
            'latency' => -1,
            'motd' => ''
        ];
    }

    fclose($socket);
    // Daha fazla bilgi için mcsrvstat.us kullanılabilir, ancak burada basit bir ping kontrolü yapıyoruz
    return [
        'online' => true,
        'players' => null, // Daha fazla bilgi için ek sorgu gerekir
        'max_players' => null,
        'latency' => null,
        'motd' => ''
    ];
}
?>