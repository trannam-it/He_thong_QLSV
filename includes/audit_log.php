<?php
function writeAuditLog(
    mysqli $conn,
    int $user_id,
    string $username,
    string $action,
    string $table_name,
    ?int $record_id = null,
    ?string $old_data = null,
    ?string $new_data = null
) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $sql = "
        INSERT INTO audit_logs
        (user_id, username, action, table_name, record_id, old_data, new_data, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    /* 🚨 BẮT LỖI NGAY TẠI ĐÂY */
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
