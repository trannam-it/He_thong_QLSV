<?php
header('Content-Type: application/json; charset=UTF-8');
$file = __DIR__ . '/rbac_debug.log';
$dir = __DIR__;
$canWriteDir = is_writable($dir);
$canWriteFile = file_exists($file) ? is_writable($file) : $canWriteDir;
$msg = ['time'=>date('c'),'test'=>'write_test','remote_user'=>($_SERVER['REMOTE_USER'] ?? $_SERVER['USERNAME'] ?? null)];
$ok = false;
$bytes = 0;
$error = null;
try {
    $bytes = @file_put_contents($file, json_encode($msg, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    $ok = $bytes !== false;
    if (!$ok) $error = error_get_last()['message'] ?? 'unknown';
} catch (Throwable $e) {
    $error = $e->getMessage();
}
echo json_encode([
    'success' => $ok,
    'bytes' => $bytes,
    'file_exists' => file_exists($file),
    'file_path' => $file,
    'dir_writable' => $canWriteDir,
    'file_writable' => $canWriteFile,
    'error' => $error
], JSON_UNESCAPED_UNICODE);
