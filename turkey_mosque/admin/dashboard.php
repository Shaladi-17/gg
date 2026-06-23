<?php
// ========================================
// admin/dashboard.php - لوحة تحكم المدير
// نفس أسلوب sre/mdashbord.php
// ========================================
session_start();
include_once("../db.php");

// التحقق من تسجيل الدخول وصلاحية المدير
if (!isset($_SESSION['u_no']) || $_SESSION['Priv'] != 1) {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['Name'];
$admin_no   = $_SESSION['u_no'];

// ===================== الدوال =====================

// جلب جميع الصالات
function getAllMosques($conn) {
    $sql = "SELECT * FROM mosque_info ORDER BY no ASC";
    return $conn->query($sql);
}

// جلب صور صالة معينة
function getMosquePictures($no, $conn) {
    $stmt = $conn->prepare("SELECT * FROM Picture WHERE no = ?");
    $stmt->execute([$no]);
    return $stmt;
}

// إضافة صالة جديدة
function insertMosque($conn) {
    $price       = trim(filter_input(INPUT_POST, 'price',       FILTER_SANITIZE_SPECIAL_CHARS));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $errors      = [];

    if ($price === "") {
        $errors[] = "السعر مطلوب.";
    } elseif (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price < 0) {
        $errors[] = "السعر يجب أن يكون رقماً موجباً.";
    }
    if ($description === "") {
        $errors[] = "وصف الصالة مطلوب.";
    }

    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p class='msg-error'>$e</p>";
        }
        return;
    }

    $stmt = $conn->prepare("INSERT INTO mosque_info (price, description) VALUES (?, ?)");
    $stmt->execute([$price, $description]);
    $new_no = $conn->lastInsertId();

    // رفع الصورة إن وُجدت
    uploadMosquePicture($new_no);

    echo "<p class='msg-success'>تمت إضافة الصالة بنجاح.</p>";
}

// تعديل بيانات صالة
function updateMosque($conn) {
    $no          = filter_input(INPUT_POST, 'no',          FILTER_VALIDATE_INT);
    $price       = trim(filter_input(INPUT_POST, 'price',       FILTER_SANITIZE_SPECIAL_CHARS));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS));
    $errors      = [];

    if (!$no) {
        $errors[] = "رقم الصالة غير صحيح.";
    }
    if ($price === "") {
        $errors[] = "السعر مطلوب.";
    } elseif (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price < 0) {
        $errors[] = "السعر يجب أن يكون رقماً موجباً.";
    }
    if ($description === "") {
        $errors[] = "وصف الصالة مطلوب.";
    }

    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p class='msg-error'>$e</p>";
        }
        return;
    }

    $stmt = $conn->prepare("UPDATE mosque_info SET price = ?, description = ? WHERE no = ?");
    $stmt->execute([$price, $description, $no]);

    // رفع صورة جديدة إن وُجدت
    uploadMosquePicture($no);

    echo "<p class='msg-success'>تم تعديل بيانات الصالة بنجاح.</p>";
}

// حذف صالة
function deleteMosque($no, $conn) {
    $no = filter_var($no, FILTER_VALIDATE_INT);
    if (!$no) {
        echo "<p class='msg-error'>رقم الصالة غير صحيح.</p>";
        return;
    }
    // حذف الصور من المجلد أولاً
    $pics = $conn->prepare("SELECT Pic_path FROM Picture WHERE no = ?");
    $pics->execute([$no]);
    while ($pic = $pics->fetch()) {
        $f = "../uploads/" . $pic['Pic_path'];
        if (is_file($f)) {
            @unlink($f);
        }
    }
    $stmt = $conn->prepare("DELETE FROM mosque_info WHERE no = ?");
    $stmt->execute([$no]);
    echo "<p class='msg-success'>تم حذف الصالة بنجاح.</p>";
}

// رفع صورة لصالة
function uploadMosquePicture($no) {
    global $conn;
    if (isset($_FILES['pic']) && $_FILES['pic']['error'] == UPLOAD_ERR_OK) {
        $orig_name  = basename($_FILES['pic']['name']);
        $ext        = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        $allowed    = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            echo "<p class='msg-error'>نوع الصورة غير مسموح. المسموح: jpg, png, gif.</p>";
            return;
        }
        $new_name   = "mosque_" . $no . "_" . time() . "." . $ext;
        $upload_dir = "../uploads/";
        if (move_uploaded_file($_FILES['pic']['tmp_name'], $upload_dir . $new_name)) {
            $stmt = $conn->prepare("INSERT INTO Picture (Pic_path, no) VALUES (?, ?)");
            $stmt->execute([$new_name, $no]);
        } else {
            echo "<p class='msg-error'>تعذّر رفع الصورة.</p>";
        }
    }
}

// جلب جميع الحجوزات
function getAllReservations($conn) {
    $sql = "SELECT r.*, u.Name AS user_name, u.Telphone, u.email,
                   m.description AS mosque_desc, m.price
            FROM resv_Info r
            JOIN user_info   u ON r.u_no = u.u_no
            JOIN mosque_info m ON r.no   = m.no
            ORDER BY r.r_date DESC";
    return $conn->query($sql);
}

// تأكيد الحجز (تغيير Type من 0 إلى 1)
function confirmReservation($r_no, $conn) {
    $r_no = filter_var($r_no, FILTER_VALIDATE_INT);
    if (!$r_no) {
        echo "<p class='msg-error'>رقم الحجز غير صحيح.</p>";
        return;
    }
    $stmt = $conn->prepare("UPDATE resv_Info SET Type = 1 WHERE r_no = ?");
    $stmt->execute([$r_no]);
    echo "<p class='msg-success'>تم تأكيد الحجز بنجاح.</p>";
}

// إلغاء حجز من المدير
function cancelReservationAdmin($r_no, $conn) {
    $r_no = filter_var($r_no, FILTER_VALIDATE_INT);
    if (!$r_no) {
        echo "<p class='msg-error'>رقم الحجز غير صحيح.</p>";
        return;
    }
    $stmt = $conn->prepare("DELETE FROM resv_Info WHERE r_no = ?");
    $stmt->execute([$r_no]);
    echo "<p class='msg-success'>تم إلغاء الحجز.</p>";
}

// ===================== معالجة الطلبات =====================

if (isset($_POST['insert_mosque'])) {
    insertMosque($conn);
}
if (isset($_POST['update_mosque'])) {
    updateMosque($conn);
}
if (isset($_POST['delete_mosque'])) {
    deleteMosque($_POST['no'], $conn);
}
if (isset($_POST['confirm_resv'])) {
    confirmReservation($_POST['r_no'], $conn);
}
if (isset($_POST['cancel_resv_admin'])) {
    cancelReservationAdmin($_POST['r_no'], $conn);
}

// تحديد القسم المعروض
function getSection() {
    if (isset($_POST['show_mosques']))     return 'mosques';
    if (isset($_POST['show_reservations'])) return 'reservations';
    if (isset($_POST['show_add_mosque']))  return 'add_mosque';
    return 'mosques';
}
$section = getSection();

// وضع تعديل الصالة
$edit_mosque = null;
if (isset($_POST['edit_mosque'])) {
    $edit_no   = filter_var($_POST['no'], FILTER_VALIDATE_INT);
    $stmt_edit = $conn->prepare("SELECT * FROM mosque_info WHERE no = ?");
    $stmt_edit->execute([$edit_no]);
    $edit_mosque = $stmt_edit->fetch();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>لوحة تحكم المدير - نظام حجز قاعة الجامع</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <div class="site-header">
        <h1>🕌 نظام حجز قاعة الجامع</h1>
        <p>لوحة تحكم المدير</p>
    </div>

    <div class="top-links">
        <h2>مرحباً، <?php echo htmlspecialchars($admin_name); ?></h2>
        <a href="../login.php" class="btn-logout">خروج</a>
    </div>
    <hr>

    <!-- أزرار التنقل -->
    <div class="filter-box">
        <form method="post" style="display:inline;">
            <input type="submit" name="show_mosques" value="🏛️ الصالات">
        </form>
        <form method="post" style="display:inline;">
            <input type="submit" name="show_reservations" value="📋 الحجوزات">
        </form>
        <form method="post" style="display:inline;">
            <input type="submit" name="show_add_mosque" value="➕ إضافة صالة">
        </form>
    </div>
    <hr>

<?php
// ===== قسم عرض الصالات =====
if ($section == 'mosques' && !$edit_mosque):
?>
    <h2>🏛️ الصالات المتاحة</h2>
    <?php
    $mosques = getAllMosques($conn);
    if ($mosques->rowCount() > 0):
        while ($m = $mosques->fetch()):
    ?>
    <div class="mosque-card">
        <strong>رقم الصالة:</strong> <?php echo $m['no']; ?> |
        <strong>السعر:</strong> <?php echo number_format($m['price'], 2); ?> ريال<br>
        <strong>الوصف:</strong> <?php echo htmlspecialchars($m['description']); ?><br>

        <!-- صور الصالة -->
        <?php
        $pics = getMosquePictures($m['no'], $conn);
        $pic_count = 0;
        while ($p = $pics->fetch()):
            $pic_count++;
        ?>
        <img src="../uploads/<?php echo htmlspecialchars($p['Pic_path']); ?>" alt="صورة الصالة">
        <?php endwhile; ?>
        <?php if ($pic_count == 0): ?>
            <br><small style="color:#888;">لا توجد صور.</small>
        <?php endif; ?>

        <br>
        <!-- أزرار التعديل والحذف -->
        <form method="post" style="display:inline; margin-top:8px;">
            <input type="hidden" name="no" value="<?php echo $m['no']; ?>">
            <input type="submit" name="edit_mosque" value="✏️ تعديل">
        </form>
        <form method="post" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف الصالة؟');">
            <input type="hidden" name="no" value="<?php echo $m['no']; ?>">
            <input type="submit" name="delete_mosque" value="🗑️ حذف"
                   style="background: linear-gradient(135deg,#c0392b,#922b21);">
        </form>
    </div>
    <?php
        endwhile;
    else:
        echo "<p>لا توجد صالات مسجّلة.</p>";
    endif;
    ?>

<?php
// ===== قسم تعديل الصالة =====
elseif ($edit_mosque):
?>
    <h2>✏️ تعديل بيانات الصالة رقم <?php echo $edit_mosque['no']; ?></h2>
    <form method="post" enctype="multipart/form-data" name="updateForm" onsubmit="return validateMosqueForm();">
        <input type="hidden" name="no" value="<?php echo $edit_mosque['no']; ?>">

        <p>السعر (ريال)</p>
        <input type="number" step="0.01" name="price" id="price"
               value="<?php echo $edit_mosque['price']; ?>">

        <p>وصف الصالة</p>
        <textarea name="description" id="description"><?php echo htmlspecialchars($edit_mosque['description']); ?></textarea>

        <p>إضافة صورة جديدة (اختياري)</p>
        <input type="file" name="pic" accept="image/*">

        <br>
        <input type="submit" name="update_mosque" value="💾 حفظ التعديلات">
        <form method="post" style="display:inline;">
            <input type="submit" name="show_mosques" value="↩️ رجوع" style="background:linear-gradient(135deg,#555,#333);">
        </form>
    </form>

<?php
// ===== قسم إضافة صالة =====
elseif ($section == 'add_mosque'):
?>
    <h2>➕ إضافة صالة جديدة</h2>
    <form method="post" enctype="multipart/form-data" name="addForm" onsubmit="return validateMosqueForm();">
        <p>السعر (ريال)</p>
        <input type="number" step="0.01" name="price" id="price" min="0">

        <p>وصف الصالة</p>
        <textarea name="description" id="description"></textarea>

        <p>صورة الصالة (اختياري)</p>
        <input type="file" name="pic" accept="image/*">

        <br>
        <input type="submit" name="insert_mosque" value="➕ إضافة">
    </form>

<?php
// ===== قسم عرض الحجوزات =====
elseif ($section == 'reservations'):
?>
    <h2>📋 جميع الحجوزات</h2>
    <?php
    $resvs = getAllReservations($conn);
    if ($resvs->rowCount() > 0):
    ?>
    <table>
        <tr>
            <th>رقم الحجز</th>
            <th>اسم الزبون</th>
            <th>الهاتف</th>
            <th>الصالة</th>
            <th>السعر</th>
            <th>المبلغ المدفوع</th>
            <th>تاريخ الحجز</th>
            <th>النوع</th>
            <th>إجراء</th>
        </tr>
        <?php while ($r = $resvs->fetch()): ?>
        <tr style="<?php echo $r['Type'] == 1 ? 'background:#c8e6c9;' : ''; ?>">
            <td><?php echo $r['r_no']; ?></td>
            <td><?php echo htmlspecialchars($r['user_name']); ?></td>
            <td><?php echo htmlspecialchars($r['Telphone']); ?></td>
            <td><?php echo htmlspecialchars($r['mosque_desc']); ?></td>
            <td><?php echo number_format($r['price'], 2); ?> ريال</td>
            <td><?php echo number_format($r['Paid'], 2); ?> ريال</td>
            <td><?php echo $r['r_date']; ?></td>
            <td>
                <?php if ($r['Type'] == 0): ?>
                    <span class="badge-pending">مبدئي</span>
                <?php else: ?>
                    <span class="badge-confirmed">نهائي</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($r['Type'] == 0): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="r_no" value="<?php echo $r['r_no']; ?>">
                    <input type="submit" name="confirm_resv" value="✔ تأكيد">
                </form>
                <?php endif; ?>
                <form method="post" style="display:inline;"
                      onsubmit="return confirm('هل تريد إلغاء هذا الحجز؟');">
                    <input type="hidden" name="r_no" value="<?php echo $r['r_no']; ?>">
                    <input type="submit" name="cancel_resv_admin" value="✘ إلغاء"
                           style="background:linear-gradient(135deg,#c0392b,#922b21);">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>لا توجد حجوزات.</p>
    <?php endif; ?>

<?php endif; ?>

</div><!-- end container -->

<script type="text/javascript">
function validateMosqueForm() {
    var price = document.getElementById('price');
    var desc  = document.getElementById('description');

    if (price && price.value.trim() === "") {
        alert("السعر مطلوب");
        price.focus();
        return false;
    }
    if (price && (isNaN(price.value) || parseFloat(price.value) < 0)) {
        alert("السعر يجب أن يكون رقماً موجباً");
        price.focus();
        return false;
    }
    if (desc && desc.value.trim() === "") {
        alert("وصف الصالة مطلوب");
        desc.focus();
        return false;
    }
    return true;
}
</script>

</body>
</html>
