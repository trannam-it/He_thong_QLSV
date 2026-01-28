<?php
function writeAuditLog(
    mysqli $conn,
    ?int $user_id,
    ?string $username,
    string $action,
    string $table_name,
    ?int $record_id = null,
    $old_data = null,
    $new_data = null
) {
    // audit_logs.user_id trong DB đang NOT NULL, nên luôn fallback về 0 nếu chưa có user
    $user_id = $user_id ?? 0;
    $username = $username ?? 'SYSTEM';

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    // Chuyển array → JSON nếu cần
    if (is_array($old_data)) {
        $old_data = json_encode($old_data, JSON_UNESCAPED_UNICODE);
    }
    if (is_array($new_data)) {
        $new_data = json_encode($new_data, JSON_UNESCAPED_UNICODE);
    }

    $sql = "
        INSERT INTO audit_logs
        (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('AuditLog Prepare Error: ' . $conn->error);
    }

    $stmt->bind_param(
        "isssisss",
        $user_id,
        $username,
        $action,
        $table_name,
        $record_id,
        $old_data,
        $new_data,
        $ip
    );

    $stmt->execute();
    $stmt->close();
}
