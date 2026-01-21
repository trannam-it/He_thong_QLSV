<?php
require_once '../config/config.php';

$sql = "
SELECT audit_id, username, action, table_name, record_id, ip_address, created_at
FROM audit_logs
ORDER BY created_at DESC
";

$result = $conn->query($sql);
?>

<table border="1" width="100%">
<tr>
  <th>Time</th>
  <th>User</th>
  <th>Action</th>
  <th>Table</th>
  <th>Record</th>
  <th>IP</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['created_at'] ?></td>
  <td><?= $row['username'] ?></td>
  <td><?= $row['action'] ?></td>
  <td><?= $row['table_name'] ?></td>
  <td><?= $row['record_id'] ?></td>
  <td><?= $row['ip_address'] ?></td>
</tr>
<?php endwhile; ?>
</table>
