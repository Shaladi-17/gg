<?php
// ========================================
// login.php - تسجيل الدخول
// نفس أسلوب sre/login.php
// ========================================
session_start();
include_once("db.php");

// دالة تسجيل الدخول
function login($u_name, $pass, $conn) {
    $sql  = "SELECT * FROM user_info WHERE u_name = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$u_name, $pass]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $_SESSION['u_no']    = $row['u_no'];
        $_SESSION['u_name']  = $row['u_name'];
        $_SESSION['Name']    = $row['Name'];
        $_SESSION['Priv']    = $row['Priv'];

        if ($row['Priv'] == 1) {
            header("Location: admin/dashboard.php");
            exit;
        } else {
            header("Location: user/dashboard.php");
            exit;
        }
    } else {
        return "<p class='msg-error'>اسم المستخدم أو كلمة المرور غير صحيحة.</p>";
    }
}

$error_msg = "";

if (isset($_POST['login'])) {
    $u_name = trim(filter_input(INPUT_POST, 'u_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $pass   = trim(filter_input(INPUT_POST, 'pass',   FILTER_SANITIZE_SPECIAL_CHARS));

    if ($u_name === "") {
        $error_msg = "<p class='msg-error'>اسم المستخدم مطلوب.</p>";
    } elseif ($pass === "") {
        $error_msg = "<p class='msg-error'>كلمة المرور مطلوبة.</p>";
    } else {
        $error_msg = login($u_name, $pass, $conn);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تسجيل الدخول - نظام حجز قاعة الجامع</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-box">
    <h3>🕌 نظام حجز قاعة الجامع</h3>
    <hr>
    <h3>تسجيل الدخول</h3>

    <form method="post" name="loginForm" onsubmit="return validateLogin();">
        <p>اسم المستخدم</p>
        <input type="text" name="u_name" id="u_name" value="<?php echo isset($_POST['u_name']) ? htmlspecialchars($_POST['u_name']) : ''; ?>">

        <p>كلمة المرور</p>
        <input type="password" name="pass" id="pass">

        <input type="submit" name="login" value="دخول">
    </form>

    <a href="register.php">إنشاء حساب جديد</a>

    <?php if (!empty($error_msg)) echo $error_msg; ?>

    <?php
    // عرض رسالة النجاح القادمة من صفحة التسجيل
    if (isset($_SESSION['success_msg'])) {
        echo "<p class='msg-success'>" . htmlspecialchars($_SESSION['success_msg']) . "</p>";
        unset($_SESSION['success_msg']); // حذفها بعد العرض لمنع تكرارها
    }
    ?>
</div>

<script type="text/javascript">
function validateLogin() {
    var form   = document.loginForm;
    var uname  = form.u_name.value.trim();
    var pass   = form.pass.value.trim();

    if (uname === "") {
        alert("اسم المستخدم مطلوب");
        form.u_name.focus();
        return false;
    }
    if (pass === "") {
        alert("كلمة المرور مطلوبة");
        form.pass.focus();
        return false;
    }
    return true;
}
</script>

</body>
</html>
