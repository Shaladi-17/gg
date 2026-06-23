<?php
// ========================================
// register.php - تسجيل مستخدم جديد
// نفس أسلوب sre/register.php
// ========================================
session_start();
include_once("db.php");

function registerUser($conn) {
    // استقبال وتنظيف البيانات
    $u_name   = trim(filter_input(INPUT_POST, 'u_name',   FILTER_SANITIZE_SPECIAL_CHARS));
    $pass     = trim(filter_input(INPUT_POST, 'pass',     FILTER_SANITIZE_SPECIAL_CHARS));
    $Name     = trim(filter_input(INPUT_POST, 'Name',     FILTER_SANITIZE_SPECIAL_CHARS));
    $Address  = trim(filter_input(INPUT_POST, 'Address',  FILTER_SANITIZE_SPECIAL_CHARS));
    $Telphone = trim(filter_input(INPUT_POST, 'Telphone', FILTER_SANITIZE_SPECIAL_CHARS));
    $email    = trim(filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL));
    $Priv     = 2; // المستخدم العادي دائماً

    $errors = [];

    // التحقق من اسم المستخدم
    if ($u_name === "") {
        $errors[] = "اسم المستخدم مطلوب.";
    } elseif (strlen($u_name) < 3) {
        $errors[] = "اسم المستخدم يجب أن يكون 3 أحرف على الأقل.";
    }

    // التحقق من كلمة المرور (يجب أن تحتوي على حروف وأرقام)
    if ($pass === "") {
        $errors[] = "كلمة المرور مطلوبة.";
    } elseif (!preg_match('/[A-Za-z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
        $errors[] = "كلمة المرور يجب أن تحتوي على حروف وأرقام.";
    }

    // التحقق من البريد الإلكتروني
    if ($email === "") {
        $errors[] = "البريد الإلكتروني مطلوب.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "صيغة البريد الإلكتروني غير صحيحة.";
    }

    // التحقق من الاسم الكامل
    if ($Name === "") {
        $errors[] = "الاسم الكامل مطلوب.";
    }

    // التحقق من العنوان
    if ($Address === "") {
        $errors[] = "العنوان مطلوب.";
    }

    // التحقق من رقم الهاتف (أرقام فقط)
    if ($Telphone === "") {
        $errors[] = "رقم الهاتف مطلوب.";
    } elseif (!preg_match('/^[0-9]{7,15}$/', $Telphone)) {
        $errors[] = "رقم الهاتف يجب أن يحتوي على أرقام فقط (7-15 رقم).";
    }

    // عرض الأخطاء إن وُجدت
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p class='msg-error'>$e</p>";
        }
        return;
    }

    // التحقق من عدم تكرار اسم المستخدم أو البريد
    $check = $conn->prepare("SELECT u_no FROM user_info WHERE u_name = ? OR email = ?");
    $check->execute([$u_name, $email]);
    if ($check->rowCount() > 0) {
        echo "<p class='msg-error'>اسم المستخدم أو البريد الإلكتروني مسجل مسبقاً.</p>";
        return;
    }

    // إدراج المستخدم الجديد
    $sql  = "INSERT INTO user_info (u_name, Password, Priv, Name, Address, Telphone, email)
             VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$u_name, $pass, $Priv, $Name, $Address, $Telphone, $email]);

    // حفظ رسالة النجاح في الجلسة والتوجيه التلقائي لصفحة الدخول
    $_SESSION['success_msg'] = 'تم تسجيل الحساب بنجاح! يمكنك الآن تسجيل الدخول.';
    header("Location: login.php");
    exit;
}

if (isset($_POST['register'])) {
    registerUser($conn);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تسجيل حساب جديد - نظام حجز قاعة الجامع</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-box">
    <h3>🕌 إنشاء حساب جديد</h3>
    <hr>

    <form method="post" name="regForm" onsubmit="return validateRegister();">

        <p>اسم المستخدم</p>
        <input type="text" name="u_name" id="u_name"
               value="<?php echo isset($_POST['u_name']) ? htmlspecialchars($_POST['u_name']) : ''; ?>">

        <p>كلمة المرور</p>
        <input type="password" name="pass" id="pass">

        <p>الاسم الكامل</p>
        <input type="text" name="Name" id="Name"
               value="<?php echo isset($_POST['Name']) ? htmlspecialchars($_POST['Name']) : ''; ?>">

        <p>البريد الإلكتروني</p>
        <input type="email" name="email" id="email"
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <p>العنوان</p>
        <input type="text" name="Address" id="Address"
               value="<?php echo isset($_POST['Address']) ? htmlspecialchars($_POST['Address']) : ''; ?>">

        <p>رقم الهاتف</p>
        <input type="tel" name="Telphone" id="Telphone"
               value="<?php echo isset($_POST['Telphone']) ? htmlspecialchars($_POST['Telphone']) : ''; ?>">

        <input type="submit" name="register" value="تسجيل">
    </form>

    <a href="login.php">لديك حساب؟ سجل دخولك</a>

    <?php if (isset($_POST['register'])): ?>
        <!-- الرسائل تظهر من الدالة أعلاه -->
    <?php endif; ?>
</div>

<script type="text/javascript">
function validateRegister() {
    var form     = document.regForm;
    var uname    = form.u_name.value.trim();
    var pass     = form.pass.value.trim();
    var name     = form.Name.value.trim();
    var email    = form.email.value.trim();
    var address  = form.Address.value.trim();
    var tel      = form.Telphone.value.trim();

    if (uname === "") {
        alert("اسم المستخدم مطلوب");
        form.u_name.focus();
        return false;
    }
    if (uname.length < 3) {
        alert("اسم المستخدم يجب أن يكون 3 أحرف على الأقل");
        form.u_name.focus();
        return false;
    }
    if (pass === "") {
        alert("كلمة المرور مطلوبة");
        form.pass.focus();
        return false;
    }
    if (!/[A-Za-z]/.test(pass) || !/[0-9]/.test(pass)) {
        alert("كلمة المرور يجب أن تحتوي على حروف وأرقام");
        form.pass.focus();
        return false;
    }
    if (name === "") {
        alert("الاسم الكامل مطلوب");
        form.Name.focus();
        return false;
    }
    if (email === "") {
        alert("البريد الإلكتروني مطلوب");
        form.email.focus();
        return false;
    }
    if (address === "") {
        alert("العنوان مطلوب");
        form.Address.focus();
        return false;
    }
    if (tel === "") {
        alert("رقم الهاتف مطلوب");
        form.Telphone.focus();
        return false;
    }
    if (isNaN(tel) || tel.length < 7) {
        alert("رقم الهاتف يجب أن يحتوي على أرقام فقط (7 أرقام على الأقل)");
        form.Telphone.focus();
        return false;
    }
    return true;
}
</script>

</body>
</html>
