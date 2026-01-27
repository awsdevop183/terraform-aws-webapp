<?php
/**
 * Terraform AWS Web Application
 * Demo application showing VPC + EC2 + RDS infrastructure
 */

$config = require_once __DIR__ . '/config.php';

// Database connection
function getDbConnection($config) {
    if (empty($config['db_host'])) return null;
    
    try {
        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $config['db_host'], $config['db_port'], $config['db_name']);
        return new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

// Get EC2 instance metadata
function getInstanceMetadata() {
    $metadata = ['instance_id' => 'N/A', 'availability_zone' => 'N/A', 
                 'instance_type' => 'N/A', 'private_ip' => 'N/A'];
    
    $token = @file_get_contents('http://169.254.169.254/latest/api/token', false, 
        stream_context_create(['http' => ['method' => 'PUT', 
            'header' => 'X-aws-ec2-metadata-token-ttl-seconds: 21600', 'timeout' => 1]]));
    
    if ($token) {
        $ctx = stream_context_create(['http' => [
            'header' => "X-aws-ec2-metadata-token: $token", 'timeout' => 1]]);
        $metadata['instance_id'] = @file_get_contents(
            'http://169.254.169.254/latest/meta-data/instance-id', false, $ctx) ?: 'N/A';
        $metadata['availability_zone'] = @file_get_contents(
            'http://169.254.169.254/latest/meta-data/placement/availability-zone', false, $ctx) ?: 'N/A';
        $metadata['instance_type'] = @file_get_contents(
            'http://169.254.169.254/latest/meta-data/instance-type', false, $ctx) ?: 'N/A';
        $metadata['private_ip'] = @file_get_contents(
            'http://169.254.169.254/latest/meta-data/local-ipv4', false, $ctx) ?: 'N/A';
    }
    return $metadata;
}

// Test database
$dbConnection = getDbConnection($config);
if ($dbConnection === null) {
    $dbStatus = ['status' => 'not_configured', 'message' => 'Database not configured'];
} elseif (is_array($dbConnection) && isset($dbConnection['error'])) {
    $dbStatus = ['status' => 'error', 'message' => $dbConnection['error']];
} else {
    try {
        $result = $dbConnection->query('SELECT VERSION() as version')->fetch(PDO::FETCH_ASSOC);
        $dbStatus = ['status' => 'connected', 'message' => 'MySQL ' . $result['version']];
    } catch (Exception $e) {
        $dbStatus = ['status' => 'error', 'message' => $e->getMessage()];
    }
}

$meta = getInstanceMetadata();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terraform AWS Web App</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { color: #fff; font-size: 2.5rem; margin-bottom: 10px; }
        .header h1 .aws { color: #ff9900; }
        .header p { color: #8892b0; font-size: 1.1rem; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .card h3 { color: #64ffda; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        .card-content p {
            color: #ccd6f6;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .card-content p:last-child { border-bottom: none; margin-bottom: 0; }
        .label { color: #8892b0; }
        .value { color: #fff; font-family: Monaco, Menlo, monospace; font-size: 0.9rem; }
        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 0.85rem;
        }
        .status-connected { background: rgba(100,255,218,0.1); color: #64ffda; }
        .status-error { background: rgba(255,107,107,0.1); color: #ff6b6b; }
        .status-not_configured { background: rgba(255,193,7,0.1); color: #ffc107; }
        .footer { text-align: center; margin-top: 40px; }
        .success-message {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(100,255,218,0.1); color: #64ffda;
            padding: 12px 24px; border-radius: 30px;
        }
        .tech-stack { display: flex; justify-content: center; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
        .tech-badge { background: rgba(255,255,255,0.05); color: #8892b0; padding: 6px 14px; border-radius: 6px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 <span class="aws">AWS</span> Web Application</h1>
            <p>Infrastructure deployed with Terraform</p>
        </div>
        
        <div class="cards">
            <div class="card">
                <h3>🖥️ EC2 Instance</h3>
                <div class="card-content">
                    <p><span class="label">Instance ID</span><span class="value"><?= $meta['instance_id'] ?></span></p>
                    <p><span class="label">Type</span><span class="value"><?= $meta['instance_type'] ?></span></p>
                    <p><span class="label">AZ</span><span class="value"><?= $meta['availability_zone'] ?></span></p>
                    <p><span class="label">Private IP</span><span class="value"><?= $meta['private_ip'] ?></span></p>
                </div>
            </div>
            
            <div class="card">
                <h3>⚙️ Server Info</h3>
                <div class="card-content">
                    <p><span class="label">Hostname</span><span class="value"><?= gethostname() ?></span></p>
                    <p><span class="label">PHP Version</span><span class="value"><?= phpversion() ?></span></p>
                    <p><span class="label">Server Time</span><span class="value"><?= date('Y-m-d H:i:s') ?></span></p>
                </div>
            </div>
            
            <div class="card">
                <h3>🗄️ RDS Database</h3>
                <div class="card-content">
                    <p>
                        <span class="label">Status</span>
                        <span class="status-badge status-<?= $dbStatus['status'] ?>">
                            ● <?= ucfirst(str_replace('_', ' ', $dbStatus['status'])) ?>
                        </span>
                    </p>
                    <p><span class="label">Host</span><span class="value"><?= $config['db_host'] ?: 'Not set' ?></span></p>
                    <p><span class="label">Database</span><span class="value"><?= $config['db_name'] ?: 'Not set' ?></span></p>
                    <p><span class="label">Details</span><span class="value"><?= $dbStatus['message'] ?></span></p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="success-message">✅ Infrastructure is running successfully!</div>
            <div class="tech-stack">
                <span class="tech-badge">Terraform</span>
                <span class="tech-badge">AWS VPC</span>
                <span class="tech-badge">EC2 + ALB</span>
                <span class="tech-badge">RDS MySQL</span>
                <span class="tech-badge">Auto Scaling</span>
            </div>
        </div>
    </div>
</body>
</html>
