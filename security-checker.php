<?php
/*
Template Name: Security Scanner Pro 3.0
Description: Profesyonel web sitesi güvenlik tarama aracı
Version: 3.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

// Session başlatma
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Header assets
function add_security_scanner_assets() {
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
}
add_action('wp_head', 'add_security_scanner_assets');

get_header();
?>

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1e40af; 
    --secondary: #4f46e5;
    --success: #22c55e;
    --warning: #f59e0b;
    --error: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

body {
    background: var(--gray-50);
    font-family: 'Inter', sans-serif;
    color: var(--gray-800);
    line-height: 1.5;
}

.security-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.dashboard-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 1rem;
    padding: 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.header-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.header-content h1 {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.header-content p {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
}

.feature-list {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1.25rem;
    border-radius: 9999px;
    backdrop-filter: blur(4px);
}

.scan-form-container {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: 1px solid var(--gray-200);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 1.25rem;
    align-items: end;
}

.url-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s;
}

.url-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.scan-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: var(--primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    height: 100%;
    white-space: nowrap;
}

.scan-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.results-overview {
    margin-bottom: 2rem;
}

.overview-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 1.5rem;
    align-items: center;
}

.score-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--gray-200);
}

.score-circle {
    width: 140px;
    height: 140px;
    margin: 0 auto 1rem;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.score-circle::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(
        var(--primary) calc(var(--score) * 1%),
        var(--gray-200) calc(var(--score) * 1%)
    );
}

.score-circle::after {
    content: '';
    position: absolute;
    width: calc(100% - 15px);
    height: calc(100% - 15px);
    border-radius: 50%;
    background: white;
}

.score-value {
    position: relative;
    z-index: 1;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.stat-card {
    background: white;
    padding: 1.25rem;
    border-radius: 0.5rem;
    text-align: center;
    border: 1px solid var(--gray-200);
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-card.success .stat-value { color: var(--success); }
.stat-card.warning .stat-value { color: var(--warning); }
.stat-card.error .stat-value { color: var(--error); }

.stat-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.results-table-container {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--gray-200);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 1.25rem;
}

.table-wrapper {
    overflow-x: auto;
}

.results-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.results-table th,
.results-table td {
    padding: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
}

.results-table th {
    background: var(--gray-50);
    font-weight: 600;
    text-align: left;
    color: var(--gray-700);
}

.results-table tr:hover {
    background: var(--gray-50);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-weight: 500;
    font-size: 0.875rem;
}

.status-badge.status-success {
    background: rgba(34, 197, 94, 0.1);
    color: var(--success);
}

.status-badge.status-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.status-badge.status-error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error);
}

.importance-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.importance-badge.importance-high,
.importance-badge.importance-kritik {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error);
}

.importance-badge.importance-medium,
.importance-badge.importance-orta {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.importance-badge.importance-low,
.importance-badge.importance-düşük {
    background: rgba(34, 197, 94, 0.1);
    color: var(--success);
}

.recommendations-section {
    margin-top: 2rem;
}

.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.25rem;
    margin-top: 1.25rem;
}

.recommendation-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.25rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--gray-200);
    transition: all 0.2s;
}

.recommendation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px -2px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.card-header h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gray-800);
}

.recommendation-card .description {
    color: var(--gray-600);
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.solution {
    background: var(--gray-50);
    padding: 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid var(--gray-200);
}

.solution h4 {
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.375rem;
}

.solution p {
    color: var(--gray-600);
    font-size: 0.9rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--gray-200);
    border-top: 3px solid var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 1024px) {
    .security-dashboard {
        padding: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .overview-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 1.5rem;
    }

    .header-content h1 {
        font-size: 1.75rem;
    }

    .feature-list {
        flex-direction: column;
        align-items: center;
    }
    
    .stats-container {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .score-circle {
        width: 120px;
        height: 120px;
    }
    
    .score-value {
        font-size: 2rem;
    }
}

@media (max-width: 640px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header h1 {
        font-size: 1.5rem;
    }
    
    .scan-form-container {
        padding: 1.25rem;
    }
}
</style>

<div class="security-dashboard">
    <div class="dashboard-header">
        <div class="header-content">
            <h1>Profesyonel Güvenlik Tarayıcı 3.0</h1>
            <p>
                Web sitenizin güvenlik durumunu kapsamlı bir şekilde analiz eder. 
                SSL/TLS yapılandırması, XSS koruması, SQL injection ve daha birçok güvenlik 
                kontrolü tek tıkla gerçekleştirilir.
            </p>
            <div class="feature-list">
                <div class="feature-item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Detaylı Güvenlik Analizi</span>
                </div>
                <div class="feature-item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Hızlı Tarama</span>
                </div>
                <div class="feature-item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Detaylı Raporlama</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarama Formu -->
    <div class="scan-form-container">
        <form method="post" class="scan-form" id="securityScanForm">
            <div class="form-grid">
                <div class="input-wrapper">
                    <label for="scan_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Taranacak Web Sitesi URL'i
                    </label>
                    <input type="url" 
                           id="scan_url"
                           name="scan_url" 
                           class="url-input" 
                           placeholder="https://example.com"
                           value="<?php echo isset($_POST['scan_url']) ? esc_attr($_POST['scan_url']) : ''; ?>"
                           required>
                </div>
                <div class="button-wrapper">
                    <button type="submit" class="scan-button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        Güvenlik Taraması Başlat
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scan_url'])) {
        $url = esc_url_raw($_POST['scan_url']);
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }

        try {
            $security_results = perform_security_scan($url);
            ?>
            <!-- Sonuç Kartları -->
            <div class="results-overview">
                <div class="overview-grid">
                    <!-- Genel Skor Kartı -->
                    <div class="score-card">
                        <div class="score-circle" style="--score: <?php echo $security_results['score']; ?>">
                            <span class="score-value"><?php echo $security_results['score']; ?>%</span>
                        </div>
                        <h3>Genel Güvenlik Skoru</h3>
                    </div>
                    
                    <!-- İstatistik Kartları -->
                    <div class="stats-container">
                        <div class="stat-card success">
                            <span class="stat-value"><?php echo $security_results['counts']['success']; ?></span>
                            <span class="stat-label">Başarılı</span>
                        </div>
                        <div class="stat-card warning">
                            <span class="stat-value"><?php echo $security_results['counts']['warning']; ?></span>
                            <span class="stat-label">Uyarı</span>
                        </div>
                        <div class="stat-card error">
                            <span class="stat-value"><?php echo $security_results['counts']['error']; ?></span>
                            <span class="stat-label">Kritik</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detaylı Sonuçlar Tablosu -->
            <div class="results-table-container">
                <h2 class="section-title">Detaylı Güvenlik Analizi</h2>
                <div class="table-wrapper">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Güvenlik Kontrolü</th>
                                <th>Durum</th>
                                <th>Açıklama</th>
                                <th>Önem Derecesi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($security_results['results'] as $result): ?>
                            <tr class="result-row <?php echo $result['type']; ?>">
                                <td class="test-name"><?php echo esc_html($result['title']); ?></td>
                                <td class="status">
                                    <span class="status-badge status-<?php echo $result['type']; ?>">
                                        <?php 
                                        $status_icon = $result['type'] === 'success' ? '✓' : 
                                                    ($result['type'] === 'warning' ? '⚠️' : '✕');
                                        $status_text = $result['type'] === 'success' ? 'Başarılı' : 
                                                    ($result['type'] === 'warning' ? 'Uyarı' : 'Hata');
                                        echo $status_icon . ' ' . $status_text;
                                        ?>
                                    </span>
                                </td>
                                <td class="description"><?php echo esc_html($result['message']); ?></td>
                                <td class="importance">
                                    <span class="importance-badge importance-<?php echo strtolower($result['importance']); ?>">
                                        <?php echo esc_html($result['importance']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Öneriler Bölümü -->
            <?php if (!empty($security_results['recommendations'])): ?>
            <div class="recommendations-section">
                <h2 class="section-title">Güvenlik Önerileri</h2>
                <div class="recommendations-grid">
                    <?php foreach ($security_results['recommendations'] as $rec): ?>
                    <div class="recommendation-card">
                        <div class="card-header">
                            <h3><?php echo esc_html($rec['title']); ?></h3>
                            <span class="importance-badge importance-<?php echo strtolower($rec['importance']); ?>">
                                <?php echo esc_html($rec['importance']); ?>
                            </span>
                        </div>
                        <p class="description"><?php echo esc_html($rec['description']); ?></p>
                        <?php if (isset($rec['solution'])): ?>
                        <div class="solution">
                            <h4>Çözüm Önerisi:</h4>
                            <p><?php echo esc_html($rec['solution']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php
        } catch (Exception $e) {
            ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: '<?php echo esc_js($e->getMessage()); ?>',
                    confirmButtonColor: '#2563eb'
                });
            </script>
            <?php
        }
    }
    ?>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
document.getElementById('securityScanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    Swal.fire({
        title: 'Tarama Başlatılıyor',
        text: 'Site güvenlik analizi yapılıyor, lütfen bekleyin...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
            loadingOverlay.style.display = 'flex';
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
});
</script>

<?php
get_footer();

/* --------------------------------
 * ANA FONKSİYONLAR
 * -------------------------------- */

// Ana tarama fonksiyonu
function perform_security_scan($url) {
    $results = array();
    $recommendations = array();
    $score = 0;
    $total_checks = 0;
    $counts = array(
        'success' => 0,
        'warning' => 0,
        'error' => 0
    );

    // SSL/HTTPS Kontrolü
    $ssl_result = check_ssl($url);
    $results[] = $ssl_result['result'];
    $score += $ssl_result['score'];
    $total_checks += $ssl_result['max_score'];
    $counts[$ssl_result['result']['type']]++;
    if (isset($ssl_result['recommendation'])) {
        $recommendations[] = $ssl_result['recommendation'];
    }

    // Header Güvenlik Kontrolü
    $headers_check = check_security_headers($url);
    $results = array_merge($results, $headers_check['results']);
    $score += $headers_check['score'];
    $total_checks += $headers_check['max_score'];
    foreach ($headers_check['results'] as $result) {
        $counts[$result['type']]++;
    }
    $recommendations = array_merge($recommendations, $headers_check['recommendations']);

    // WordPress Güvenlik Kontrolü
    $wp_check = check_wordpress_security($url);
    $results = array_merge($results, $wp_check['results']);
    $score += $wp_check['score'];
    $total_checks += $wp_check['max_score'];
    foreach ($wp_check['results'] as $result) {
        $counts[$result['type']]++;
    }
    $recommendations = array_merge($recommendations, $wp_check['recommendations']);
    
    // SSL/TLS Yapılandırma Kontrolü
    $ssl_tls_check = check_ssl_tls_configuration($url);
    $results = array_merge($results, $ssl_tls_check['results']);
    $score += $ssl_tls_check['score'];
    $total_checks += $ssl_tls_check['max_score'];
    foreach ($ssl_tls_check['results'] as $result) {
        $counts[$result['type']]++;
    }
    $recommendations = array_merge($recommendations, $ssl_tls_check['recommendations']);

    // Final skor hesaplama
    $final_score = $total_checks > 0 ? round(($score / $total_checks) * 100) : 0;

    return array(
        'score' => $final_score,
        'results' => $results,
        'recommendations' => $recommendations,
        'counts' => $counts
    );
}

// SSL kontrolü fonksiyonu
function check_ssl($url) {
    $result = array(
        'result' => array(
            'title' => 'SSL/HTTPS Güvenliği',
            'type' => 'error',
            'message' => '',
            'importance' => 'Kritik'
        ),
        'score' => 0,
        'max_score' => 10,
        'recommendation' => null
    );

    if (strpos($url, 'https://') === 0) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response !== false) {
            $result['result']['type'] = 'success';
            $result['result']['message'] = 'SSL sertifikası aktif ve doğru yapılandırılmış';
            $result['score'] = 10;
        } else {
            $result['result']['message'] = 'SSL sertifikası geçersiz veya yapılandırma hatalı';
            $result['recommendation'] = array(
                'title' => 'SSL Sertifikası Sorunu',
                'description' => 'SSL sertifikanız geçersiz veya yanlış yapılandırılmış.',
                'solution' => 'Geçerli bir SSL sertifikası edinin ve sunucu yapılandırmanızı kontrol edin.',
                'importance' => 'Kritik'
            );
        }
    } else {
        $result['result']['message'] = 'HTTPS kullanılmıyor';
        $result['recommendation'] = array(
            'title' => 'HTTPS Gerekli',
            'description' => 'Siteniz güvenli HTTPS protokolünü kullanmıyor.',
            'solution' => 'SSL sertifikası edinin ve sitenizi HTTPS\'e yönlendirin.',
            'importance' => 'Kritik'
        );
    }

    return $result;
}

// Güvenlik Header'larının Kontrolü
function check_security_headers($url) {
    $results = array();
    $recommendations = array();
    $score = 0;
    $max_score = 25;

    $response = wp_remote_get($url, array('timeout' => 10));
    
    if (!is_wp_error($response)) {
        $headers = wp_remote_retrieve_headers($response);
        
        // Kontrol edilecek güvenlik headerları
        $security_headers = array(
            'X-Frame-Options' => array(
                'importance' => 'Yüksek',
                'score' => 5,
                'recommended' => 'SAMEORIGIN',
                'description' => 'Clickjacking koruması'
            ),
            'X-XSS-Protection' => array(
                'importance' => 'Yüksek',
                'score' => 5,
                'recommended' => '1; mode=block',
                'description' => 'XSS koruması'
            ),
            'X-Content-Type-Options' => array(
                'importance' => 'Orta',
                'score' => 5,
                'recommended' => 'nosniff',
                'description' => 'MIME-type sniffing koruması'
            ),
            'Strict-Transport-Security' => array(
                'importance' => 'Yüksek',
                'score' => 5,
                'recommended' => 'max-age=31536000',
                'description' => 'HTTPS zorunluluğu'
            ),
            'Content-Security-Policy' => array(
                'importance' => 'Yüksek',
                'score' => 5,
                'recommended' => "default-src 'self'",
                'description' => 'İçerik güvenlik politikası'
            )
        );

        foreach ($security_headers as $header => $info) {
            if (isset($headers[$header])) {
                $results[] = array(
                    'title' => $header,
                    'type' => 'success',
                    'message' => $info['description'] . ' - Aktif',
                    'importance' => $info['importance']
                );
                $score += $info['score'];
            } else {
                $results[] = array(
                    'title' => $header,
                    'type' => 'warning',
                    'message' => $info['description'] . ' - Eksik',
                    'importance' => $info['importance']
                );
                
                $recommendations[] = array(
                    'title' => $header . ' Header Eksik',
                    'description' => $info['description'] . ' için güvenlik başlığı eksik.',
                    'solution' => 'Header ekleyin: ' . $header . ': ' . $info['recommended'],
                    'importance' => $info['importance']
                );
            }
        }
    }

    return array(
        'results' => $results,
        'recommendations' => $recommendations,
        'score' => $score,
        'max_score' => $max_score
    );
}

// WordPress Güvenlik Kontrolü
function check_wordpress_security($url) {
    $results = array();
    $recommendations = array();
    $score = 0;
    $max_score = 25;

    $response = wp_remote_get($url, array('timeout' => 10));
    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);

        // WordPress versiyon kontrolü
        $wp_version_exposed = false;
        if (preg_match('/<meta name="generator" content="WordPress ([0-9.]+)"/i', $body, $matches)) {
            $wp_version_exposed = true;
            $wp_version = $matches[1];
        }

        // readme.html kontrolü
        $readme_exists = false;
        $readme_response = wp_remote_get($url . '/readme.html', array('timeout' => 5));
        if (!is_wp_error($readme_response) && wp_remote_retrieve_response_code($readme_response) === 200) {
            $readme_exists = true;
        }

        // wp-config.php erişim kontrolü
        $config_protected = true;
        $config_response = wp_remote_get($url . '/wp-config.php', array('timeout' => 5));
        if (!is_wp_error($config_response) && wp_remote_retrieve_response_code($config_response) !== 403) {
            $config_protected = false;
        }

        // XML-RPC kontrolü
        $xmlrpc_enabled = false;
        $xmlrpc_response = wp_remote_get($url . '/xmlrpc.php', array('timeout' => 5));
        if (!is_wp_error($xmlrpc_response) && wp_remote_retrieve_response_code($xmlrpc_response) !== 403) {
            $xmlrpc_enabled = true;
        }

        // WordPress versiyon gizliliği
        if (!$wp_version_exposed) {
            $results[] = array(
                'title' => 'WordPress Versiyon Gizliliği',
                'type' => 'success',
                'message' => 'WordPress versiyon bilgisi gizli',
                'importance' => 'Orta'
            );
            $score += 5;
        } else {
            $results[] = array(
                'title' => 'WordPress Versiyon Gizliliği',
                'type' => 'warning',
                'message' => 'WordPress versiyon bilgisi açıkta: ' . $wp_version,
                'importance' => 'Orta'
            );
            
            $recommendations[] = array(
                'title' => 'WordPress Versiyon Bilgisi Gizlenmeli',
                'description' => 'WordPress versiyon bilginiz public olarak görünüyor.',
                'solution' => 'functions.php dosyanıza şu kodu ekleyin: remove_action(\'wp_head\', \'wp_generator\');',
                'importance' => 'Orta'
            );
        }

        // readme.html kontrolü
        if (!$readme_exists) {
            $results[] = array(
                'title' => 'Readme.html Dosyası',
                'type' => 'success',
                'message' => 'readme.html dosyası erişilemez durumda',
                'importance' => 'Düşük'
            );
            $score += 5;
        } else {
            $results[] = array(
                'title' => 'Readme.html Dosyası',
                'type' => 'warning',
                'message' => 'readme.html dosyası erişilebilir durumda',
                'importance' => 'Düşük'
            );
            
            $recommendations[] = array(
                'title' => 'Readme.html Dosyası Erişimi',
                'description' => 'readme.html dosyası sitenizin WordPress versiyonunu açık ediyor.',
                'solution' => 'readme.html dosyasını silin veya erişimi engelleyin.',
                'importance' => 'Düşük'
            );
        }

        // wp-config.php kontrolü
        if ($config_protected) {
            $results[] = array(
                'title' => 'wp-config.php Koruması',
                'type' => 'success',
                'message' => 'wp-config.php dosyası korunuyor',
                'importance' => 'Kritik'
            );
            $score += 10;
        } else {
            $results[] = array(
                'title' => 'wp-config.php Koruması',
                'type' => 'error',
                'message' => 'wp-config.php dosyası yeterince korunmuyor',
                'importance' => 'Kritik'
            );
            
            $recommendations[] = array(
                'title' => 'wp-config.php Güvenlik Açığı',
                'description' => 'wp-config.php dosyasına dışarıdan erişilebiliyor.',
                'solution' => '.htaccess ile wp-config.php dosyasına erişimi engelleyin.',
                'importance' => 'Kritik'
            );
        }

        // XML-RPC kontrolü
        if (!$xmlrpc_enabled) {
            $results[] = array(
                'title' => 'XML-RPC Devre Dışı',
                'type' => 'success',
                'message' => 'XML-RPC devre dışı bırakılmış',
                'importance' => 'Yüksek'
            );
            $score += 5;
        } else {
            $results[] = array(
                'title' => 'XML-RPC Aktif',
                'type' => 'warning',
                'message' => 'XML-RPC aktif durumda',
                'importance' => 'Yüksek'
            );
            
            $recommendations[] = array(
                'title' => 'XML-RPC Güvenlik Riski',
                'description' => 'XML-RPC aktif olması brute force saldırılarına açık olmanıza neden olabilir.',
                'solution' => 'XML-RPC\'yi devre dışı bırakın veya erişimi kısıtlayın.',
                'importance' => 'Yüksek'
            );
        }
    }

    return array(
        'results' => $results,
        'recommendations' => $recommendations,
        'score' => $score,
        'max_score' => $max_score
    );
}

// SSL/TLS Yapılandırma Kontrolü
function check_ssl_tls_configuration($url) {
    $results = array();
    $recommendations = array();
    $score = 0;
    $max_score = 15;

    $domain = parse_url($url, PHP_URL_HOST);
    $port = 443;

    // SSL bağlantı testi
    $context = stream_context_create(array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'capture_peer_cert' => true
        )
    ));

    $socket = @stream_socket_client(
        "ssl://{$domain}:{$port}",
        $errno,
        $errstr,
        5,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if ($socket) {
        $params = stream_context_get_params($socket);
        $cert_info = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
        
        // Sertifika geçerlilik kontrolü
        $cert_valid = time() < $cert_info['validTo_time_t'];
        
        // Ortak Ad (CN) kontrolü
        $common_name = isset($cert_info['subject']['CN']) ? $cert_info['subject']['CN'] : '';
        $domain_match = false;
        
        if ($common_name === $domain || (strpos($common_name, '*.') === 0 && strpos($domain, substr($common_name, 2)) !== false)) {
            $domain_match = true;
        }
        
        if ($cert_valid) {
            $results[] = array(
                'title' => 'SSL Sertifika Geçerliliği',
                'type' => 'success',
                'message' => 'SSL sertifikası geçerli',
                'importance' => 'Kritik'
            );
            $score += 10;
        } else {
            $results[] = array(
                'title' => 'SSL Sertifika Geçerliliği',
                'type' => 'error',
                'message' => 'SSL sertifikası süresi dolmuş veya geçersiz',
                'importance' => 'Kritik'
            );
            
            $recommendations[] = array(
                'title' => 'SSL Sertifika Yenileme',
                'description' => 'SSL sertifikanız geçersiz veya süresi dolmuş.',
                'solution' => 'SSL sertifikanızı yenileyin veya yeni bir sertifika alın.',
                'importance' => 'Kritik'
            );
        }
        
        if ($domain_match) {
            $results[] = array(
                'title' => 'Domain Eşleşmesi',
                'type' => 'success',
                'message' => 'Sertifika domain adı ile eşleşiyor',
                'importance' => 'Yüksek'
            );
            $score += 5;
        } else {
            $results[] = array(
                'title' => 'Domain Eşleşmesi',
                'type' => 'warning',
                'message' => 'Sertifika domain adı ile eşleşmiyor',
                'importance' => 'Yüksek'
            );
            
            $recommendations[] = array(
                'title' => 'Domain Eşleşme Sorunu',
                'description' => 'SSL sertifikası domain adınızla eşleşmiyor.',
                'solution' => 'Doğru domain adı için SSL sertifikası edinin.',
                'importance' => 'Yüksek'
            );
        }
        
        fclose($socket);
    } else {
        $results[] = array(
            'title' => 'SSL/TLS Bağlantısı',
            'type' => 'error',
            'message' => 'SSL/TLS bağlantısı kurulamadı',
            'importance' => 'Kritik'
        );
        
        $recommendations[] = array(
            'title' => 'SSL/TLS Yapılandırma Hatası',
            'description' => 'SSL/TLS bağlantısı kurulamıyor.',
            'solution' => 'SSL/TLS yapılandırmanızı kontrol edin ve sunucu ayarlarınızı gözden geçirin.',
            'importance' => 'Kritik'
        );
    }

    return array(
        'results' => $results,
        'recommendations' => $recommendations,
        'score' => $score,
        'max_score' => $max_score
    );
}

/* --------------------------------
 * YARDIMCI FONKSİYONLAR
 * -------------------------------- */

// URL Doğrulama
function validate_url($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Geçersiz URL formatı.');
    }
    return true;
}

// Sonuç Formatlandırma
function format_scan_results($results) {
    $formatted = array(
        'score' => 0,
        'tests' => array(),
        'recommendations' => array(),
        'summary' => array(
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0
        )
    );
    
    if (isset($results['score'])) {
        $formatted['score'] = $results['score'];
    }
    
    if (isset($results['results'])) {
        foreach ($results['results'] as $result) {
            $formatted['tests'][] = array(
                'name' => $result['title'],
                'status' => $result['type'],
                'message' => $result['message'],
                'importance' => $result['importance']
            );

            // Önem derecesine göre sayaç artırma
            $importance = strtolower($result['importance']);
            if ($importance === 'kritik') {
                $formatted['summary']['critical']++;
            } elseif ($importance === 'yüksek') {
                $formatted['summary']['high']++;
            } elseif ($importance === 'orta') {
                $formatted['summary']['medium']++;
            } elseif ($importance === 'düşük') {
                $formatted['summary']['low']++;
            }
        }
    }
    
    if (isset($results['recommendations'])) {
        $formatted['recommendations'] = $results['recommendations'];
    }
    
    return $formatted;
}

// Önerileri Önceliklendirme
function prioritize_recommendations($recommendations) {
    $priority_order = array(
        'kritik' => 1,
        'yüksek' => 2,
        'orta' => 3,
        'düşük' => 4
    );

    usort($recommendations, function($a, $b) use ($priority_order) {
        $a_priority = isset($priority_order[strtolower($a['importance'])]) ? 
                     $priority_order[strtolower($a['importance'])] : 5;
        $b_priority = isset($priority_order[strtolower($b['importance'])]) ? 
                     $priority_order[strtolower($b['importance'])] : 5;
        return $a_priority - $b_priority;
    });

    return $recommendations;
}
?>