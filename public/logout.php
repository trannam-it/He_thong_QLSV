<?php
session_start();
session_unset();
session_destroy();

// Dùng query string để hiển thị thông báo sau logout (vì session đã destroy)
header("Location: index.php?logout=1"); 
exit;
?>
