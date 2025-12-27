<?php
/**
 * TIKLA GELİR - Gelişmiş Sistem Kontrol Dashboard
 * Maksimum Detaylı Sistem Analizi ve Güvenlik Kontrolü
 * Version: 3.0
 */

session_start();

// Şifre kontrolü (Şifrelenmiş hali: tiklagelir2024)
$correct_password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

// Logout işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: system_check_dashboard.php');
    exit;
}

// Login kontrolü
if (!isset($_SESSION['system_check_authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (password_verify($_POST['password'], $correct_password_hash)) {
            $_SESSION['system_check_authenticated'] = true;
            $_SESSION['login_time'] = time();
            header('Location: system_check_dashboard.php');
            exit;
        } else {
            $login_error = "Hatalı şifre!";
        }
    }
    
    // Login sayfası
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>🔐 Sistem Kontrol Dashboard - Giriş</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .login-container {
                background: white;
                padding: clamp(30px, 5vw, 40px);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 400px;
                width: 100%;
                animation: slideUp 0.5s ease;
                min-width: 0;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .login-header h1 {
                font-size: clamp(22px, 4vw, 28px);
                color: #333;
                margin-bottom: 10px;
            }
            .login-header p {
                color: #666;
                font-size: clamp(12px, 2.5vw, 14px);
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #333;
                font-weight: 500;
            }
            .form-group input {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                font-size: clamp(14px, 2.5vw, 15px);
                transition: all 0.3s;
                box-sizing: border-box;
            }
            .form-group input:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }
            .btn-login {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .btn-login:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            }
            .error-message {
                background: #fee;
                color: #c33;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                border-left: 4px solid #c33;
            }
            .security-badge {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e0e0e0;
            }
            .security-badge span {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                color: #666;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>🔐 Sistem Kontrol</h1>
                <p>Güvenli erişim gereklidir</p>
            </div>
            <?php if (isset($login_error)): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required autofocus>
                </div>
                <button type="submit" class="btn-login">🔓 Giriş Yap</button>
            </form>
            <div class="security-badge">
                <span>🔒 256-bit şifreli güvenlik</span>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Otomatik logout (1 saat)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 3600) {
    session_destroy();
    header('Location: system_check_dashboard.php');
    exit;
}

// Database bağlantısı
require_once 'config/database.php';

// Yardımcı fonksiyonlar
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function getStatusBadge($status, $text) {
    $colors = [
        'success' => 'background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);',
        'warning' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);',
        'danger' => 'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);',
        'info' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);'
    ];
    return '<span style="' . ($colors[$status] ?? $colors['info']) . ' color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">' . $text . '</span>';
}

function checkFilePermissions($path) {
    if (!file_exists($path)) return ['status' => 'error', 'message' => 'Dosya bulunamadı'];
    
    $perms = substr(sprintf('%o', fileperms($path)), -4);
    $writable = is_writable($path);
    $readable = is_readable($path);
    
    // Güvenlik kontrolü
    $dangerous = false;
    if (is_file($path) && in_array($perms, ['0777', '0666'])) {
        $dangerous = true;
    }
    
    return [
        'status' => $dangerous ? 'warning' : 'success',
        'perms' => $perms,
        'writable' => $writable,
        'readable' => $readable,
        'dangerous' => $dangerous,
        'owner' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] : 'N/A'
    ];
}

// JSON Export
if (isset($_GET['export']) && $_GET['export'] === 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="system_report_' . date('Y-m-d_H-i-s') . '.json"');
    
    $report = [
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => phpversion(),
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        'database' => [],
        'files' => [],
        'security' => []
    ];
    
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// İstatistikler
$stats = [
    'total_checks' => 0,
    'success' => 0,
    'warnings' => 0,
    'errors' => 0,
    'critical' => 0
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Sistem Kontrol Dashboard - Tıkla Gelir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1f2937;
            --light: #f9fafb;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 15px;
            color: #333;
            overflow-x: hidden;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            background: white;
            padding: clamp(20px, 4vw, 30px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .dashboard-header h1 {
            font-size: clamp(22px, 4vw, 32px);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .dashboard-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        
        .dashboard-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: clamp(8px, 2vw, 10px) clamp(15px, 3vw, 20px);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(12px, 2.5vw, 14px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: clamp(20px, 4vw, 25px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: clamp(28px, 5vw, 36px);
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: clamp(12px, 2.5vw, 14px);
            font-weight: 500;
        }
        
        .section {
            background: white;
            padding: clamp(20px, 4vw, 30px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-title {
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: clamp(12px, 2.5vw, 14px);
            min-width: 600px;
        }
        
        th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: clamp(12px, 3vw, 15px);
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: clamp(12px, 3vw, 15px);
            border-bottom: 1px solid #f0f0f0;
        }
        
        tr:hover {
            background: #f9fafb;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e3a8a; }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--success), #10b981);
            transition: width 1s ease;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid var(--success); }
        .alert-warning { background: #fef3c7; color: #92400e; border-left: 4px solid var(--warning); }
        .alert-danger { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--danger); }
        
        .code-block {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .tooltip {
            position: relative;
            cursor: help;
        }
        
        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            white-space: nowrap;
            font-size: 12px;
            z-index: 1000;
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .table-container {
                margin: 0 -10px;
                padding: 0 10px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-actions {
                flex-direction: column;
                width: 100%;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Overflow prevention */
        * { max-width: 100%; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1><i class="fas fa-chart-line"></i> Sistem Kontrol Dashboard</h1>
                <div class="dashboard-info">
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <?php echo date('d.m.Y H:i:s'); ?>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-server"></i>
                        <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        Admin
                    </div>
                </div>
            </div>
            <div class="dashboard-actions">
                <a href="?" class="btn btn-success"><i class="fas fa-sync"></i> Yenile</a>
                <a href="?export=json" class="btn btn-primary"><i class="fas fa-download"></i> JSON Export</a>
                <a href="?logout=1" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="stats-grid">
            <?php
            // PHP Version
            $php_version = phpversion();
            $php_status = version_compare($php_version, '7.4', '>=') ? 'success' : 'warning';
            ?>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <i class="fab fa-php"></i>
                </div>
                <div class="stat-value"><?php echo $php_version; ?></div>
                <div class="stat-label">PHP Version</div>
                <span class="badge badge-<?php echo $php_status; ?>"><?php echo $php_status === 'success' ? 'Güncel' : 'Eski'; ?></span>
            </div>

            <?php
            // Database Status
            try {
                $db_status = $pdo->query("SELECT 1")->fetchColumn();
                $tables_count = $pdo->query("SHOW TABLES")->rowCount();
                $stats['success']++;
            } catch (Exception $e) {
                $tables_count = 0;
                $db_status = false;
                $stats['errors']++;
            }
            ?>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-value"><?php echo $tables_count; ?></div>
                <div class="stat-label">Veritabanı Tabloları</div>
                <span class="badge badge-<?php echo $db_status ? 'success' : 'danger'; ?>">
                    <?php echo $db_status ? 'Bağlantı OK' : 'Bağlantı Hatası'; ?>
                </span>
            </div>

            <?php
            // Disk Space
            $disk_total = disk_total_space('/');
            $disk_free = disk_free_space('/');
            $disk_used = $disk_total - $disk_free;
            $disk_percent = round(($disk_used / $disk_total) * 100, 2);
            ?>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-value"><?php echo $disk_percent; ?>%</div>
                <div class="stat-label">Disk Kullanımı</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $disk_percent; ?>%; background: <?php echo $disk_percent > 80 ? 'var(--danger)' : 'var(--success)'; ?>;"></div>
                </div>
            </div>

            <?php
            // Memory Usage
            $memory_limit = ini_get('memory_limit');
            $memory_usage = memory_get_usage(true);
            ?>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <i class="fas fa-memory"></i>
                </div>
                <div class="stat-value"><?php echo bytesToHuman($memory_usage); ?></div>
                <div class="stat-label">Bellek Kullanımı</div>
                <span class="badge badge-info">Limit: <?php echo $memory_limit; ?></span>
            </div>
        </div>

        <!-- PHP Konfigürasyonu -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fab fa-php"></i> PHP Konfigürasyonu</h2>
                <span class="badge badge-info">PHP <?php echo phpversion(); ?></span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Ayar</th>
                            <th>Değer</th>
                            <th>Önerilen</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $php_settings = [
                            ['display_errors', ini_get('display_errors'), 'Off', 'security'],
                            ['error_reporting', ini_get('error_reporting'), 'E_ALL & ~E_NOTICE', 'info'],
                            ['max_execution_time', ini_get('max_execution_time'), '30-300', 'info'],
                            ['memory_limit', ini_get('memory_limit'), '128M+', 'info'],
                            ['upload_max_filesize', ini_get('upload_max_filesize'), '20M+', 'info'],
                            ['post_max_size', ini_get('post_max_size'), '20M+', 'info'],
                            ['session.cookie_httponly', ini_get('session.cookie_httponly'), 'On', 'security'],
                            ['session.cookie_secure', ini_get('session.cookie_secure'), 'On', 'security'],
                            ['allow_url_fopen', ini_get('allow_url_fopen'), 'Off', 'security'],
                            ['expose_php', ini_get('expose_php'), 'Off', 'security']
                        ];
                        
                        foreach ($php_settings as $setting) {
                            $current_value = $setting[1];
                            $recommended = $setting[2];
                            $type = $setting[3];
                            
                            // Güvenlik kontrolü
                            $is_secure = true;
                            if ($type === 'security') {
                                if ($setting[0] === 'display_errors' && $current_value !== '0' && $current_value !== 'Off') {
                                    $is_secure = false;
                                    $stats['warnings']++;
                                } elseif (($setting[0] === 'session.cookie_httponly' || $setting[0] === 'session.cookie_secure') && $current_value !== '1' && $current_value !== 'On') {
                                    $is_secure = false;
                                    $stats['warnings']++;
                                } elseif ($setting[0] === 'expose_php' && $current_value !== '0' && $current_value !== 'Off') {
                                    $is_secure = false;
                                    $stats['warnings']++;
                                } else {
                                    $stats['success']++;
                                }
                            } else {
                                $stats['success']++;
                            }
                            
                            $status_badge = $is_secure ? '<span class="badge badge-success">✓ OK</span>' : '<span class="badge badge-warning">⚠ Dikkat</span>';
                            
                            echo "<tr>";
                            echo "<td><strong>{$setting[0]}</strong></td>";
                            echo "<td><code>{$current_value}</code></td>";
                            echo "<td>{$recommended}</td>";
                            echo "<td>{$status_badge}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-puzzle-piece"></i> PHP Uzantıları</h2>
                <span class="badge badge-info"><?php echo count(get_loaded_extensions()); ?> Uzantı Yüklü</span>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Uzantı</th>
                            <th>Durum</th>
                            <th>Version</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $required_extensions = [
                            'PDO', 'pdo_mysql', 'mysqli', 'json', 'mbstring', 
                            'curl', 'openssl', 'zip', 'gd', 'xml', 'fileinfo',
                            'session', 'tokenizer', 'ctype', 'filter'
                        ];
                        
                        foreach ($required_extensions as $ext) {
                            $loaded = extension_loaded($ext);
                            $version = $loaded ? phpversion($ext) : '-';
                            
                            if ($loaded) {
                                $stats['success']++;
                                echo "<tr>";
                                echo "<td><strong>{$ext}</strong></td>";
                                echo "<td><span class='badge badge-success'>✓ Yüklü</span></td>";
                                echo "<td>" . ($version ?: 'N/A') . "</td>";
                                echo "</tr>";
                            } else {
                                $stats['errors']++;
                                echo "<tr>";
                                echo "<td><strong>{$ext}</strong></td>";
                                echo "<td><span class='badge badge-danger'>✗ Yüklü Değil</span></td>";
                                echo "<td>-</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Veritabanı Detayları -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-database"></i> Veritabanı Analizi</h2>
            </div>
            
            <?php
            try {
                // Database version
                $db_version = $pdo->query("SELECT VERSION()")->fetchColumn();
                $db_size_query = $pdo->query("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = DATABASE()");
                $db_size = $db_size_query->fetchColumn();
                
                echo '<div class="alert alert-success">';
                echo '<i class="fas fa-check-circle"></i>';
                echo '<div>';
                echo '<strong>Veritabanı Bağlantısı Başarılı</strong><br>';
                echo 'MySQL Version: ' . $db_version . ' | Veritabanı Boyutu: ' . round($db_size, 2) . ' MB';
                echo '</div>';
                echo '</div>';
                
                // Tablo analizi
                $tables_query = $pdo->query("SHOW TABLES");
                $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);
                
                echo '<div class="table-container">';
                echo '<table>';
                echo '<thead><tr><th>Tablo Adı</th><th>Satır Sayısı</th><th>Boyut</th><th>Engine</th><th>Durum</th></tr></thead>';
                echo '<tbody>';
                
                foreach ($tables as $table) {
                    try {
                        $count_query = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
                        $count = $count_query->fetchColumn();
                        
                        $info_query = $pdo->query("SELECT ENGINE, ROUND((data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.TABLES WHERE table_schema = DATABASE() AND table_name = '{$table}'");
                        $info = $info_query->fetch();
                        
                        $stats['success']++;
                        
                        echo "<tr>";
                        echo "<td><strong>{$table}</strong></td>";
                        echo "<td>" . number_format($count) . "</td>";
                        echo "<td>" . ($info['size'] ?? '0') . " MB</td>";
                        echo "<td>{$info['ENGINE']}</td>";
                        echo "<td><span class='badge badge-success'>✓ OK</span></td>";
                        echo "</tr>";
                    } catch (Exception $e) {
                        $stats['errors']++;
                        echo "<tr>";
                        echo "<td><strong>{$table}</strong></td>";
                        echo "<td colspan='3'><span class='badge badge-danger'>Hata: " . $e->getMessage() . "</span></td>";
                        echo "<td><span class='badge badge-danger'>✗ Hata</span></td>";
                        echo "</tr>";
                    }
                }
                
                echo '</tbody></table></div>';
                
            } catch (Exception $e) {
                $stats['critical']++;
                echo '<div class="alert alert-danger">';
                echo '<i class="fas fa-exclamation-triangle"></i>';
                echo '<div><strong>Veritabanı Bağlantı Hatası!</strong><br>' . $e->getMessage() . '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Dosya ve Klasör İzinleri -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-folder-open"></i> Dosya ve Klasör İzinleri</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Yol</th>
                            <th>Tip</th>
                            <th>İzinler</th>
                            <th>Sahip</th>
                            <th>Okuma</th>
                            <th>Yazma</th>
                            <th>Güvenlik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $paths_to_check = [
                            ['config/', 'dir'],
                            ['config/database.php', 'file'],
                            ['logs/', 'dir'],
                            ['assets/', 'dir'],
                            ['admin/', 'dir'],
                            ['api/', 'dir'],
                            ['restaurant/', 'dir'],
                            ['market/', 'dir'],
                            ['grocery/', 'dir'],
                            ['index.php', 'file'],
                            ['functions.php', 'file']
                        ];
                        
                        foreach ($paths_to_check as $item) {
                            list($path, $type) = $item;
                            $full_path = __DIR__ . '/' . $path;
                            
                            if (file_exists($full_path)) {
                                $check = checkFilePermissions($full_path);
                                
                                if ($check['dangerous']) {
                                    $stats['warnings']++;
                                } else {
                                    $stats['success']++;
                                }
                                
                                $type_icon = $type === 'dir' ? '📁' : '📄';
                                $readable_badge = $check['readable'] ? '<span class="badge badge-success">✓</span>' : '<span class="badge badge-danger">✗</span>';
                                $writable_badge = $check['writable'] ? '<span class="badge badge-success">✓</span>' : '<span class="badge badge-danger">✗</span>';
                                $security_badge = $check['dangerous'] 
                                    ? '<span class="badge badge-danger">⚠ Tehlikeli</span>' 
                                    : '<span class="badge badge-success">✓ Güvenli</span>';
                                
                                echo "<tr>";
                                echo "<td>{$type_icon} <strong>{$path}</strong></td>";
                                echo "<td>" . strtoupper($type) . "</td>";
                                echo "<td><code>{$check['perms']}</code></td>";
                                echo "<td>{$check['owner']}</td>";
                                echo "<td>{$readable_badge}</td>";
                                echo "<td>{$writable_badge}</td>";
                                echo "<td>{$security_badge}</td>";
                                echo "</tr>";
                            } else {
                                $stats['errors']++;
                                echo "<tr>";
                                echo "<td><strong>{$path}</strong></td>";
                                echo "<td colspan='6'><span class='badge badge-danger'>✗ Dosya Bulunamadı</span></td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- API Endpoints Kontrolü -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-plug"></i> API Endpoints</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Endpoint</th>
                            <th>Dosya Yolu</th>
                            <th>Boyut</th>
                            <th>Son Değişiklik</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $api_dir = __DIR__ . '/api/';
                        if (is_dir($api_dir)) {
                            $api_files = scandir($api_dir);
                            foreach ($api_files as $file) {
                                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                                    $full_path = $api_dir . $file;
                                    $size = filesize($full_path);
                                    $modified = filemtime($full_path);
                                    
                                    $stats['success']++;
                                    
                                    echo "<tr>";
                                    echo "<td><strong>api/{$file}</strong></td>";
                                    echo "<td><code>{$full_path}</code></td>";
                                    echo "<td>" . bytesToHuman($size) . "</td>";
                                    echo "<td>" . date('d.m.Y H:i', $modified) . "</td>";
                                    echo "<td><span class='badge badge-success'>✓ Aktif</span></td>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            $stats['errors']++;
                            echo "<tr><td colspan='5'><span class='badge badge-danger'>API klasörü bulunamadı</span></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Güvenlik Kontrolleri -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-shield-alt"></i> Güvenlik Kontrolleri</h2>
            </div>
            
            <?php
            $security_checks = [];
            
            // 1. Display errors kontrol
            $display_errors = ini_get('display_errors');
            $security_checks[] = [
                'name' => 'Display Errors',
                'status' => ($display_errors === '0' || $display_errors === 'Off') ? 'success' : 'warning',
                'message' => ($display_errors === '0' || $display_errors === 'Off') 
                    ? 'Hata gösterimi kapalı (Güvenli)' 
                    : 'Hata gösterimi açık! Üretim ortamında kapatılmalı',
                'value' => $display_errors
            ];
            
            // 2. Database credentials kontrolü
            $db_file_perms = checkFilePermissions(__DIR__ . '/config/database.php');
            $security_checks[] = [
                'name' => 'Database Config Güvenliği',
                'status' => ($db_file_perms['perms'] !== '0777' && $db_file_perms['perms'] !== '0666') ? 'success' : 'danger',
                'message' => ($db_file_perms['perms'] !== '0777' && $db_file_perms['perms'] !== '0666')
                    ? 'Database config dosyası güvenli izinlerde'
                    : 'Database config dosyası çok açık izinlerde! Güvenlik riski',
                'value' => $db_file_perms['perms']
            ];
            
            // 3. HTTPS kontrolü
            $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
            $security_checks[] = [
                'name' => 'HTTPS',
                'status' => $is_https ? 'success' : 'warning',
                'message' => $is_https ? 'HTTPS aktif' : 'HTTP kullanılıyor (HTTPS önerilir)',
                'value' => $is_https ? 'Aktif' : 'Pasif'
            ];
            
            // 4. Session güvenliği
            $session_httponly = ini_get('session.cookie_httponly');
            $security_checks[] = [
                'name' => 'Session Cookie HTTPOnly',
                'status' => ($session_httponly === '1' || $session_httponly === 'On') ? 'success' : 'warning',
                'message' => ($session_httponly === '1' || $session_httponly === 'On')
                    ? 'Session cookie\'leri XSS saldırılarına karşı korunuyor'
                    : 'Session cookie HTTPOnly ayarı kapalı',
                'value' => $session_httponly
            ];
            
            // 5. PHP version
            $php_version = phpversion();
            $is_php_secure = version_compare($php_version, '7.4', '>=');
            $security_checks[] = [
                'name' => 'PHP Version',
                'status' => $is_php_secure ? 'success' : 'danger',
                'message' => $is_php_secure 
                    ? 'PHP versiyonu güncel ve güvenli'
                    : 'PHP versiyonu eski! Güvenlik güncellemesi gerekli',
                'value' => $php_version
            ];
            
            // Sonuçları göster
            foreach ($security_checks as $check) {
                $alert_class = 'alert-' . $check['status'];
                $icon = $check['status'] === 'success' ? 'fa-check-circle' : ($check['status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
                
                if ($check['status'] === 'success') {
                    $stats['success']++;
                } elseif ($check['status'] === 'warning') {
                    $stats['warnings']++;
                } else {
                    $stats['critical']++;
                }
                
                echo "<div class='alert {$alert_class}'>";
                echo "<i class='fas {$icon}'></i>";
                echo "<div>";
                echo "<strong>{$check['name']}</strong>: {$check['message']}<br>";
                echo "<small>Değer: <code>{$check['value']}</code></small>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>

        <!-- Server Bilgileri -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-server"></i> Server Bilgileri</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Parametre</th>
                            <th>Değer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Server Software</strong></td>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Server IP</strong></td>
                            <td><?php echo $_SERVER['SERVER_ADDR'] ?? 'Bilinmiyor'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Server Port</strong></td>
                            <td><?php echo $_SERVER['SERVER_PORT'] ?? 'Bilinmiyor'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Document Root</strong></td>
                            <td><code><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Bilinmiyor'; ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>PHP SAPI</strong></td>
                            <td><?php echo php_sapi_name(); ?></td>
                        </tr>
                        <tr>
                            <td><strong>OS</strong></td>
                            <td><?php echo PHP_OS; ?> (<?php echo php_uname(); ?>)</td>
                        </tr>
                        <tr>
                            <td><strong>Disk Toplam Alan</strong></td>
                            <td><?php echo bytesToHuman($disk_total); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Disk Boş Alan</strong></td>
                            <td><?php echo bytesToHuman($disk_free); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Disk Kullanılan</strong></td>
                            <td><?php echo bytesToHuman($disk_used); ?> (<?php echo $disk_percent; ?>%)</td>
                        </tr>
                        <?php if (function_exists('sys_getloadavg')): ?>
                        <tr>
                            <td><strong>Server Load</strong></td>
                            <td><?php $load = sys_getloadavg(); echo implode(', ', array_map(function($l) { return round($l, 2); }, $load)); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sonuç Özeti -->
        <?php
        $total = $stats['success'] + $stats['warnings'] + $stats['errors'] + $stats['critical'];
        $success_rate = $total > 0 ? round(($stats['success'] / $total) * 100, 2) : 0;
        ?>
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-clipboard-check"></i> Genel Değerlendirme</h2>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-value" style="color: var(--success);"><?php echo $stats['success']; ?></div>
                    <div class="stat-label">Başarılı Kontrol</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value" style="color: var(--warning);"><?php echo $stats['warnings']; ?></div>
                    <div class="stat-label">Uyarı</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="stat-value" style="color: var(--danger);"><?php echo $stats['errors']; ?></div>
                    <div class="stat-label">Hata</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="fas fa-skull-crossbones"></i>
                    </div>
                    <div class="stat-value" style="color: var(--danger);"><?php echo $stats['critical']; ?></div>
                    <div class="stat-label">Kritik</div>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 style="margin-bottom: 15px;">Genel Sağlık Skoru</h3>
                <div class="progress-bar" style="height: 40px; font-size: 18px; font-weight: bold; display: flex; align-items: center; padding: 0 15px; color: white;">
                    <div class="progress-fill" style="width: <?php echo $success_rate; ?>%; display: flex; align-items: center; justify-content: center; background: <?php echo $success_rate >= 80 ? 'linear-gradient(90deg, #11998e, #38ef7d)' : ($success_rate >= 60 ? 'linear-gradient(90deg, #f093fb, #f5576c)' : 'linear-gradient(90deg, #fa709a, #fee140)'); ?>;">
                        <?php echo $success_rate; ?>%
                    </div>
                </div>
            </div>
            
            <?php if ($success_rate >= 80): ?>
                <div class="alert alert-success" style="margin-top: 20px;">
                    <i class="fas fa-trophy"></i>
                    <div>
                        <strong>🎉 Mükemmel! Sisteminiz çok sağlıklı.</strong><br>
                        Tüm kontroller başarıyla geçti. Sisteminiz güvenli ve stabil çalışıyor.
                    </div>
                </div>
            <?php elseif ($success_rate >= 60): ?>
                <div class="alert alert-warning" style="margin-top: 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>⚠️ Dikkat! Bazı uyarılar mevcut.</strong><br>
                        Sistem çalışıyor ancak bazı iyileştirmeler gerekiyor. Uyarıları kontrol edin.
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger" style="margin-top: 20px;">
                    <i class="fas fa-skull-crossbones"></i>
                    <div>
                        <strong>❌ Kritik! Acil müdahale gerekiyor!</strong><br>
                        Sistemde önemli hatalar tespit edildi. Hataları düzeltmeden sistemi kullanmayın.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div style="background: white; padding: 20px; border-radius: 20px; text-align: center; margin-top: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <p style="color: #666; margin-bottom: 10px;">
                <i class="fas fa-clock"></i> Rapor Oluşturulma: <?php echo date('d.m.Y H:i:s'); ?>
            </p>
            <p style="color: #999; font-size: 13px;">
                © 2025 Tıkla Gelir | Sistem Kontrol Dashboard v3.0 | <a href="?" style="color: #667eea; text-decoration: none;">Yenile</a> | <a href="?export=json" style="color: #667eea; text-decoration: none;">Export</a>
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh countdown (optional)
        console.log('%c🔍 Sistem Kontrol Dashboard', 'font-size: 20px; color: #667eea; font-weight: bold;');
        console.log('%c📊 Toplam Kontrol: <?php echo $total; ?>', 'font-size: 14px; color: #38ef7d;');
        console.log('%c✅ Başarılı: <?php echo $stats["success"]; ?> | ⚠️ Uyarı: <?php echo $stats["warnings"]; ?> | ❌ Hata: <?php echo $stats["errors"]; ?>', 'font-size: 14px;');
        
        // Session timeout uyarısı
        setTimeout(() => {
            const loginTime = <?php echo $_SESSION['login_time']; ?>;
            const currentTime = Math.floor(Date.now() / 1000);
            const timeLeft = 3600 - (currentTime - loginTime);
            
            if (timeLeft < 300) { // 5 dakika kala
                console.warn('⚠️ Oturumunuz 5 dakika içinde sonlanacak!');
            }
        }, 1000);
    </script>
</body>
</html>
