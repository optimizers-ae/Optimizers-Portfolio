<?php
declare(strict_types=1);

function redirectWithStatus(string $status, string $source = 'consultation'): never
{
    $target = $source === 'contact' ? 'contact-us.php' : 'index.php';
    $anchor = $source === 'contact' ? '#contact-form' : '#consultation';
    header('Location: ' . $target . '?form=' . rawurlencode($status) . $anchor);
    exit;
}

$formSource = (string) ($_POST['form_source'] ?? '') === 'contact' ? 'contact' : 'consultation';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirectWithStatus('invalid', $formSource);
if (trim((string) ($_POST['website_company'] ?? '')) !== '') redirectWithStatus('success', $formSource);

$name = trim((string) ($_POST['name'] ?? ''));
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$phone = trim((string) ($_POST['phone'] ?? ''));
$budget = trim((string) ($_POST['budget'] ?? ''));
$projectSubject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$allowedBudgets = ['5k-10k', '10k-25k', '25k-50k', '50k-plus'];

$commonInvalid = $name === '' || strlen($name) > 100 || !$email || $message === '' || strlen($message) > 5000;
$contactInvalid = $formSource === 'contact' && ($projectSubject === '' || strlen($projectSubject) > 180);
$consultationInvalid = $formSource !== 'contact' && ($phone === '' || strlen($phone) > 40 || !in_array($budget, $allowedBudgets, true));

if ($commonInvalid || $contactInvalid || $consultationInvalid) {
    redirectWithStatus('invalid', $formSource);
}

// Save lead record into Database
try {
    require_once __DIR__ . '/includes/db.php';
    $db = OptimizersDB::getConnection();
    $stmt = $db->prepare("INSERT INTO leads (name, email, phone, budget, service, message, source, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'New', ?)");
    $stmt->execute([
        $name,
        $email,
        $phone !== '' ? $phone : null,
        $budget !== '' ? $budget : null,
        $projectSubject !== '' ? $projectSubject : 'Free Consultation Request',
        $message,
        $formSource,
        date('Y-m-d H:i:s')
    ]);
} catch (Throwable $e) {
    error_log('Optimizers DB lead insert error: ' . $e->getMessage());
}

$configPath = __DIR__ . '/config/mail.php';
if (!is_file($configPath)) redirectWithStatus('config', $formSource);
$config = require $configPath;
foreach (['host', 'port', 'encryption', 'username', 'password', 'from_email', 'from_name', 'recipient'] as $key) {
    if (!isset($config[$key]) || trim((string) $config[$key]) === '' || (string) $config[$key] === 'PASTE_HOSTINGER_EMAIL_PASSWORD_HERE') redirectWithStatus('config', $formSource);
}

function smtpRead($socket, array $expected): string
{
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }
    $code = (int) substr($response, 0, 3);
    if (!in_array($code, $expected, true)) throw new RuntimeException('SMTP ' . $code . ': ' . trim($response));
    return $response;
}

function smtpCommand($socket, string $command, array $expected): string
{
    fwrite($socket, $command . "\r\n");
    return smtpRead($socket, $expected);
}

function cleanHeader(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

try {
    $host = (string) $config['host'];
    $port = (int) $config['port'];
    $encryption = strtolower((string) $config['encryption']);
    $transport = $encryption === 'ssl' ? 'ssl://' : '';
    $context = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $socket = stream_socket_client($transport . $host . ':' . $port, $errorNumber, $errorMessage, 20, STREAM_CLIENT_CONNECT, $context);
    if (!$socket) throw new RuntimeException('Connection failed: ' . $errorMessage);
    stream_set_timeout($socket, 20);
    smtpRead($socket, [220]);
    smtpCommand($socket, 'EHLO optimizers.ae', [250]);
    if ($encryption === 'tls') {
        smtpCommand($socket, 'STARTTLS', [220]);
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new RuntimeException('TLS failed.');
        smtpCommand($socket, 'EHLO optimizers.ae', [250]);
    }
    smtpCommand($socket, 'AUTH LOGIN', [334]);
    smtpCommand($socket, base64_encode((string) $config['username']), [334]);
    smtpCommand($socket, base64_encode((string) $config['password']), [235]);

    $fromEmail = cleanHeader((string) $config['from_email']);
    $fromName = cleanHeader((string) $config['from_name']);
    $recipient = cleanHeader((string) $config['recipient']);
    smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
    smtpCommand($socket, 'RCPT TO:<' . $recipient . '>', [250, 251]);
    smtpCommand($socket, 'DATA', [354]);

    $safeName = cleanHeader($name);
    $safeReplyTo = cleanHeader((string) $email);
    if ($formSource === 'contact') {
        $safeProjectSubject = cleanHeader($projectSubject);
        $subject = '=?UTF-8?B?' . base64_encode('Website contact: ' . $safeProjectSubject) . '?=';
        $body = "New contact page message\n\nName: {$safeName}\nEmail: {$safeReplyTo}\nSubject: {$safeProjectSubject}\n\nMessage:\n{$message}\n\nSubmitted: " . date('Y-m-d H:i:s T');
    } else {
        $subject = '=?UTF-8?B?' . base64_encode('Website consultation request from ' . $safeName) . '?=';
        $body = "New website consultation request\n\nName: {$safeName}\nEmail: {$safeReplyTo}\nPhone: {$phone}\nBudget: {$budget}\n\nMessage:\n{$message}\n\nSubmitted: " . date('Y-m-d H:i:s T');
    }
    $body = preg_replace('/^\./m', '..', $body) ?? $body;
    $headers = ['Date: ' . date(DATE_RFC2822), 'From: ' . $fromName . ' <' . $fromEmail . '>', 'To: <' . $recipient . '>', 'Reply-To: ' . $safeName . ' <' . $safeReplyTo . '>', 'Subject: ' . $subject, 'MIME-Version: 1.0', 'Content-Type: text/plain; charset=UTF-8', 'Content-Transfer-Encoding: 8bit'];
    fwrite($socket, implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n");
    smtpRead($socket, [250]);
    smtpCommand($socket, 'QUIT', [221]);
    fclose($socket);
    redirectWithStatus('success', $formSource);
} catch (Throwable $exception) {
    error_log('Optimizers SMTP form error: ' . $exception->getMessage());
    redirectWithStatus('error', $formSource);
}
