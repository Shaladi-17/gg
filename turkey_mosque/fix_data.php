<?php
// ========================================
// fix_data.php - إصلاح بيانات قاعدة البيانات
// شغّل هذا الملف مرة واحدة فقط ثم احذفه
// ========================================
include_once("db.php");

// حذف البيانات القديمة المتضررة
$conn->exec("DELETE FROM resv_Info");
$conn->exec("DELETE FROM Picture");
$conn->exec("DELETE FROM mosque_info");
$conn->exec("DELETE FROM user_info WHERE u_name = 'admin'");

// إضافة المدير بالعربية الصحيحة
$stmt = $conn->prepare("INSERT INTO user_info (u_no, u_name, Password, Priv, Name, Address, Telphone, email)
                        VALUES (2, 'admin', 'admin123', 1, ?, 'الجامعة', '0501234567', 'admin@mosque.com')
                        ON DUPLICATE KEY UPDATE Name = VALUES(Name), Address = VALUES(Address)");
$stmt->execute(['مدير النظام']);

// إضافة الصالات بالعربية الصحيحة
$stmt2 = $conn->prepare("INSERT INTO mosque_info (no, price, description) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE price = VALUES(price), description = VALUES(description)");
$stmt2->execute([1, 500.00, 'قاعة الجامع الكبيرة - تتسع لـ 200 شخص - مجهزة بالتكييف والصوتيات']);
$stmt2->execute([2, 300.00, 'قاعة الجامع الصغيرة - تتسع لـ 100 شخص - مجهزة بالتكييف']);

echo "<p style='color:green; font-size:18px;'>✅ تم إصلاح البيانات بنجاح!</p>";

// التحقق من النتيجة
echo "<h3>المستخدمون:</h3><ul>";
$users = $conn->query("SELECT u_no, u_name, Priv, Name FROM user_info");
foreach ($users as $u) {
    echo "<li>#{$u['u_no']} - {$u['u_name']} (Priv={$u['Priv']}) - {$u['Name']}</li>";
}
echo "</ul>";

echo "<h3>الصالات:</h3><ul>";
$mosques = $conn->query("SELECT no, price, description FROM mosque_info");
foreach ($mosques as $m) {
    echo "<li>#{$m['no']} - {$m['price']} ريال - {$m['description']}</li>";
}
echo "</ul>";

echo "<hr><p style='color:orange;'>⚠️ احذف هذا الملف بعد التأكد من نجاح العملية.</p>";
echo "<p><a href='login.php'>انتقل إلى صفحة الدخول</a></p>";
?>
