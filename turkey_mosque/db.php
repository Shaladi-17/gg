<?php
// ========================================
// ربط قاعدة البيانات - بدون try/catch
// ========================================
header('Content-Type: text/html; charset=utf-8');

$db_host   = "localhost";
$db_name   = "turkey_mosque";
$db_user   = "root";
$db_pass   = "";
$db_dsn    = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

$conn = new PDO($db_dsn, $db_user, $db_pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// إجبار MySQL على استخدام UTF-8 لحل مشكلة cp850
$conn->exec("SET NAMES 'utf8mb4'");
$conn->exec("SET CHARACTER SET utf8mb4");
$conn->exec("SET collation_connection = 'utf8mb4_unicode_ci'");
?>
