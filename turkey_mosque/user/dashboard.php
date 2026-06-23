<?php
// ========================================
// user/dashboard.php - لوحة المستخدم
// نفس أسلوب sre/udashbord.php
// ========================================
session_start();
include_once("../db.php");

// التحقق من تسجيل الدخول كمستخدم عادي
if (!isset($_SESSION['u_no']) || $_SESSION['Priv'] != 2) {
    header("Location: ../login.php");
    exit;
}

$user_no   = $_SESSION['u_no'];
$user_name = $_SESSION['Name'];

// ===================== الدوال =====================

// جلب جميع الصالات مع صورها
function getAllMosques($conn) {
    $sql = "SELECT * FROM mosque_info ORDER BY no ASC";
    return $conn->query($sql);
}

// جلب صور صالة
function getMosquePictures($no, $conn) {
    $stmt = $conn->prepare("SELECT * FROM Picture WHERE no = ?");
    $stmt->execute([$no]);
    return $stmt;
}

// إضافة حجز جديد
function addReservation($user_no, $conn) {
    $no    = filter_input(INPUT_POST, 'no',   FILTER_VALIDATE_INT);
    $Paid  = trim(filter_input(INPUT_POST, 'Paid',  FILTER_SANITIZE_SPECIAL_CHARS));
    $r_date = trim(filter_input(INPUT_POST, 'r_date', FILTER_SANITIZE_SPECIAL_CHARS));
    $Type  = 0; // الحجز المبدئي دائماً

    $errors = [];

    if (!$no) {
        $errors[] = "يجب اختيار الصالة.";
    }
    if ($Paid === "") {
        $errors[] = "المبلغ المدفوع مطلوب.";
    } elseif (!filter_var($Paid, FILTER_VALIDATE_FLOAT) || $Paid < 0) {
        $errors[] = "المبلغ يجب أن يكون رقماً موجباً.";
    }
    if ($r_date === "") {
        $errors[] = "تاريخ الحجز مطلوب.";
    } else {
        // التحقق أن التاريخ ليس في الماضي
        $today = date('Y-m-d');
        if ($r_date < $today) {
            $errors[] = "لا يمكن الحجز في تاريخ ماضٍ.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p class='msg-error'>$e</p>";
        }
        return;
    }

    // التحقق من عدم وجود حجز في نفس التاريخ لنفس الصالة
    $check = $conn->prepare("SELECT r_no FROM resv_Info WHERE no = ? AND r_date = ?");
    $check->execute([$no, $r_date]);
    if ($check->rowCount() > 0) {
        echo "<p class='msg-error'>الصالة محجوزة في هذا التاريخ. اختر تاريخاً آخر.</p>";
        return;
    }

    $stmt = $conn->prepare("INSERT INTO resv_Info (u_no, Paid, r_date, Type, no)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_no, $Paid, $r_date, $Type, $no]);

    echo "<p class='msg-success'>تم إرسال طلب الحجز بنجاح. بانتظار تأكيد المدير.</p>";
}

// إلغاء حجز مبدئي فقط (Type=0)
function cancelReservation($r_no, $user_no, $conn) {
    $r_no = filter_var($r_no, FILTER_VALIDATE_INT);
    if (!$r_no) {
        echo "<p class='msg-error'>رقم الحجز غير صحيح.</p>";
        return;
    }

    // التحقق أن الحجز مبدئي وتابع للمستخدم الحالي
    $check = $conn->prepare("SELECT Type FROM resv_Info WHERE r_no = ? AND u_no = ?");
    $check->execute([$r_no, $user_no]);
    $resv = $check->fetch();

    if (!$resv) {
        echo "<p class='msg-error'>الحجز غير موجود.</p>";
        return;
    }
    if ($resv['Type'] == 1) {
        echo "<p class='msg-error'>لا يمكن إلغاء الحجز النهائي. تواصل مع المدير.</p>";
        return;
    }

    $stmt = $conn->prepare("DELETE FROM resv_Info WHERE r_no = ? AND u_no = ?");
    $stmt->execute([$r_no, $user_no]);
    echo "<p class='msg-success'>تم إلغاء الحجز بنجاح.</p>";
}

// جلب حجوزات المستخدم من الشهر الحالي حتى نهاية السنة
function getUserReservations($user_no, $conn, $filter = 'all') {
    $current_month_start = date('Y-m-01');
    $year_end            = date('Y-12-31');

    $base_sql = "SELECT r.*, m.description AS mosque_desc, m.price
                 FROM resv_Info r
                 JOIN mosque_info m ON r.no = m.no
                 WHERE r.u_no = ? AND r.r_date >= ? AND r.r_date <= ?";

    if ($filter == 'pending') {
        $base_sql .= " AND r.Type = 0";
    } elseif ($filter == 'confirmed') {
        $base_sql .= " AND r.Type = 1";
    }

    $base_sql .= " ORDER BY r.r_date ASC";

    $stmt = $conn->prepare($base_sql);
    $stmt->execute([$user_no, $current_month_start, $year_end]);
    return $stmt;
}

// ===================== معالجة الطلبات =====================

if (isset($_POST['add_resv'])) {
    addReservation($user_no, $conn);
}
if (isset($_POST['cancel_resv'])) {
    cancelReservation($_POST['r_no'], $user_no, $conn);
}

// فلتر عرض الحجوزات
$filter = 'all';
if (isset($_POST['show_pending']))   $filter = 'pending';
if (isset($_POST['show_confirmed'])) $filter = 'confirmed';
if (isset($_POST['show_all']))       $filter = 'all';

// وضع نموذج الحجز الجديد
$show_booking_form = isset($_POST['new_booking']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>لوحة الزبون - نظام حجز قاعة الجامع</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <div class="site-header">
        <h1>🕌 نظام حجز قاعة الجامع</h1>
        <p>لوحة الزبون</p>
    </div>

    <div class="top-links">
        <h2>مرحباً، <?php echo htmlspecialchars($user_name); ?></h2>
        <a href="../login.php" class="btn-logout">خروج</a>
    </div>
    <hr>

    <!-- زر حجز جديد -->
    <div style="text-align:center; margin-bottom:12px;">
        <form method="post" style="display:inline;">
            <input type="submit" name="new_booking" value="➕ حجز جديد">
        </form>
    </div>
    <hr>

    <!-- ===== نموذج الحجز الجديد ===== -->
    <?php if ($show_booking_form): ?>
    <h2>➕ نموذج حجز جديد</h2>

    <!-- عرض الصالات المتاحة -->
    <h3 style="text-align:right; color:#1a5c38; margin-bottom:10px;">الصالات المتاحة:</h3>
    <?php
    $mosques = getAllMosques($conn);
    if ($mosques->rowCount() > 0):
        while ($m = $mosques->fetch()):
    ?>
    <div class="mosque-card">
        <strong>رقم الصالة:</strong> <?php echo $m['no']; ?> |
        <strong>السعر:</strong> <?php echo number_format($m['price'], 2); ?> ريال<br>
        <strong>الوصف:</strong> <?php echo htmlspecialchars($m['description']); ?><br>
        <?php
        $pics = getMosquePictures($m['no'], $conn);
        $pc = 0;
        while ($p = $pics->fetch()):
            $pc++;
        ?>
        <img src="../uploads/<?php echo htmlspecialchars($p['Pic_path']); ?>" alt="صورة الصالة">
        <?php endwhile; ?>
        <?php if ($pc == 0): ?><small style="color:#888;">لا توجد صور.</small><?php endif; ?>
    </div>
    <?php endwhile; else: ?>
        <p>لا توجد صالات متاحة.</p>
    <?php endif; ?>

    <hr>
    <!-- نموذج الحجز -->
    <form method="post" name="bookingForm" onsubmit="return validateBooking();">
        <p>اختر الصالة</p>
        <select name="no" id="no">
            <option value="">-- اختر --</option>
            <?php
            $mosques2 = getAllMosques($conn);
            while ($m2 = $mosques2->fetch()):
            ?>
            <option value="<?php echo $m2['no']; ?>">
                صالة #<?php echo $m2['no']; ?> - <?php echo htmlspecialchars($m2['description']); ?>
                (<?php echo number_format($m2['price'],2); ?> ريال)
            </option>
            <?php endwhile; ?>
        </select>

        <p>تاريخ الحجز</p>
        <input type="date" name="r_date" id="r_date" min="<?php echo date('Y-m-d'); ?>">

        <p>المبلغ المدفوع (ريال)</p>
        <input type="number" step="0.01" name="Paid" id="Paid" min="0">

        <br>
        <input type="submit" name="add_resv" value="📅 تأكيد الحجز">
    </form>
    <hr>
    <?php endif; ?>

    <!-- ===== فلاتر الحجوزات ===== -->
    <div class="filter-box">
        <form method="post" style="display:inline;">
            <input type="submit" name="show_all" value="📋 جميع الحجوزات">
        </form>
        <form method="post" style="display:inline;">
            <input type="submit" name="show_pending" value="🕐 المبدئية">
        </form>
        <form method="post" style="display:inline;">
            <input type="submit" name="show_confirmed" value="✅ النهائية">
        </form>
    </div>

    <!-- ===== عرض الحجوزات (من الشهر الحالي حتى نهاية السنة) ===== -->
    <h2>حجوزاتي (<?php echo date('m/Y'); ?> - 12/<?php echo date('Y'); ?>)</h2>
    <?php
    $resvs = getUserReservations($user_no, $conn, $filter);
    if ($resvs->rowCount() > 0):
        while ($r = $resvs->fetch()):
            $card_class = ($r['Type'] == 1) ? 'resv-card-confirmed' : 'resv-card';
    ?>
    <div class="<?php echo $card_class; ?>">
        <strong>رقم الحجز:</strong> <?php echo $r['r_no']; ?> |
        <strong>الصالة:</strong> <?php echo htmlspecialchars($r['mosque_desc']); ?><br>
        <strong>تاريخ الحجز:</strong> <?php echo $r['r_date']; ?> |
        <strong>السعر:</strong> <?php echo number_format($r['price'], 2); ?> ريال |
        <strong>المدفوع:</strong> <?php echo number_format($r['Paid'], 2); ?> ريال<br>
        <strong>الحالة:</strong>
        <?php if ($r['Type'] == 0): ?>
            <span class="badge-pending">مبدئي (بانتظار التأكيد)</span>
        <?php else: ?>
            <span class="badge-confirmed">✅ نهائي (مؤكد)</span>
        <?php endif; ?>

        <!-- إلغاء الحجز المبدئي فقط -->
        <?php if ($r['Type'] == 0): ?>
        <form method="post" style="display:inline; margin-right:10px;"
              onsubmit="return confirm('هل تريد إلغاء هذا الحجز؟');">
            <input type="hidden" name="r_no" value="<?php echo $r['r_no']; ?>">
            <input type="submit" name="cancel_resv" value="✘ إلغاء الحجز"
                   style="background:linear-gradient(135deg,#c0392b,#922b21); padding:5px 12px; font-size:12px;">
        </form>
        <?php endif; ?>
    </div>
    <?php
        endwhile;
    else:
        echo "<p>لا توجد حجوزات في الفترة المحددة.</p>";
    endif;
    ?>

</div><!-- end container -->

<script type="text/javascript">
function validateBooking() {
    var no     = document.getElementById('no');
    var r_date = document.getElementById('r_date');
    var paid   = document.getElementById('Paid');
    var today  = new Date().toISOString().split('T')[0];

    if (no.value === "" || no.value === "0") {
        alert("يجب اختيار الصالة");
        no.focus();
        return false;
    }
    if (r_date.value === "") {
        alert("تاريخ الحجز مطلوب");
        r_date.focus();
        return false;
    }
    if (r_date.value < today) {
        alert("لا يمكن الحجز في تاريخ ماضٍ");
        r_date.focus();
        return false;
    }
    if (paid.value === "") {
        alert("المبلغ المدفوع مطلوب");
        paid.focus();
        return false;
    }
    if (isNaN(paid.value) || parseFloat(paid.value) < 0) {
        alert("المبلغ يجب أن يكون رقماً موجباً");
        paid.focus();
        return false;
    }
    return true;
}
</script>

</body>
</html>
