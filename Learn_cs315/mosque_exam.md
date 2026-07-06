# 🕌 مشروع المسجد التركي — أسئلة متوقعة وحلول مختصرة

## 📋 قاعدة البيانات: `turkey_mosque`

### الجداول:

```
user_info:    u_no*, u_name, Password, Priv(1/2), Name, Address, Telphone, email
resv_Info:    r_no*, u_no, Paid, r_date, Type(0/1), no
mosque_info:  no*, price, description
Picture:      pic_no*, Pic_path, no
```

### شرح الحقول:
| الجدول | الحقل | المعنى |
|--------|-------|--------|
| user_info | Priv | 1=admin, 2=user(زبون) |
| resv_Info | Type | 0=حجز مبدئي, 1=حجز نهائي |
| resv_Info | Paid | المبلغ المدفوع |
| resv_Info | r_date | تاريخ الحجز |
| resv_Info | no | رقم القاعة |

### المطلوب في المشروع:
1. تسجيل دخول (admin/user)
2. CRUD لبيانات القاعات
3. تأكيد الحجز المبدئي للزبائن اللي دفعوا
4. إلغاء الحجز حسب نوع الحجز وشهر معين
5. عرض الحجوزات كتقويم مع تلوين (نهائي=لون، مبدئي=لون آخر)
6. حجز مبدئي مع التأكد إن اليوم غير محجوز

---

## ❓ سؤال 1: تسجيل الدخول

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['ok'])){
  $u=$_POST['user']; $p=$_POST['pass'];
  $r=$conn->prepare("SELECT * FROM user_info WHERE u_name=? AND Password=?");
  $r->execute(array($u,$p));
  if($row=$r->fetch(PDO::FETCH_OBJ)){
    $_SESSION['u_no']=$row->u_no;
    $_SESSION['priv']=$row->Priv;
    if($row->Priv==1) header("Location: admin.php");
    else header("Location: user.php");
    exit;
  } else echo "خطأ";
} else { ?>
<form method="post">
  المستخدم: <input type="text" name="user"><br>
  كلمة المرور: <input type="password" name="pass"><br>
  <input type="submit" name="ok" value="دخول">
</form>
<?php } ?>
```

---

## ❓ سؤال 2: إدخال بيانات قاعة جديدة (INSERT)

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['add'])){
  $no=$_POST['no']; $price=$_POST['price']; $desc=$_POST['desc'];
  $conn->exec("INSERT INTO mosque_info VALUES($no,$price,'$desc')");

  // رفع صورة
  if(!empty($_FILES['pic']['name'])){
    $tmp=$_FILES['pic']['tmp_name'];
    $ext=pathinfo($_FILES['pic']['name'],PATHINFO_EXTENSION);
    $name="mosque_".$no.".".$ext;
    move_uploaded_file($tmp,"uploads/".$name);
    $conn->exec("INSERT INTO Picture(Pic_path,no) VALUES('$name',$no)");
  }
  echo "تم الإضافة";
} else { ?>
<form method="post" enctype="multipart/form-data">
  رقم القاعة: <input type="text" name="no"><br>
  السعر: <input type="text" name="price"><br>
  الوصف: <textarea name="desc"></textarea><br>
  صورة: <input type="file" name="pic"><br>
  <input type="submit" name="add" value="إضافة">
</form>
<?php } ?>
```

---

## ❓ سؤال 3: تأكيد الحجز (تحويل من مبدئي 0 إلى نهائي 1)

**المطلوب**: عرض الحجوزات المبدئية للزبائن اللي دفعوا وتأكيدها

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['confirm'])){
  foreach($_POST['id'] as $v)
    $conn->exec("UPDATE resv_Info SET Type=1 WHERE r_no=$v");
  echo "تم التأكيد";
}

$r=$conn->query("SELECT resv_Info.*,user_info.Name FROM resv_Info
  JOIN user_info ON resv_Info.u_no=user_info.u_no
  WHERE Type=0 AND Paid>0");
echo "<form method='post'><table border='1'>
<tr><th>✓</th><th>رقم الحجز</th><th>الزبون</th><th>المبلغ</th><th>التاريخ</th><th>القاعة</th></tr>";
while($row=$r->fetch(PDO::FETCH_OBJ)){
  echo "<tr><td><input type='checkbox' name='id[]' value='$row->r_no'></td>
  <td>$row->r_no</td><td>$row->Name</td><td>$row->Paid</td>
  <td>$row->r_date</td><td>$row->no</td></tr>";
}
echo "</table><input type='submit' name='confirm' value='تأكيد الحجز'></form>";
?>
```

---

## ❓ سؤال 4: إلغاء حجز حسب النوع والشهر

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['del'])){
  foreach($_POST['id'] as $v)
    $conn->exec("DELETE FROM resv_Info WHERE r_no=$v");
  echo "تم الإلغاء";
}

if(isset($_POST['s'])){
  $type=$_POST['type']; $month=$_POST['month'];
  $r=$conn->prepare("SELECT resv_Info.*,user_info.Name FROM resv_Info
    JOIN user_info ON resv_Info.u_no=user_info.u_no
    WHERE Type=? AND MONTH(r_date)=?");
  $r->execute(array($type,$month));
  echo "<form method='post'><table border='1'>
  <tr><th>✓</th><th>الحجز</th><th>الزبون</th><th>التاريخ</th><th>النوع</th></tr>";
  while($row=$r->fetch(PDO::FETCH_OBJ)){
    $t=$row->Type==1?"نهائي":"مبدئي";
    echo "<tr><td><input type='checkbox' name='id[]' value='$row->r_no'></td>
    <td>$row->r_no</td><td>$row->Name</td><td>$row->r_date</td><td>$t</td></tr>";
  }
  echo "</table><input type='submit' name='del' value='إلغاء'></form>";
} else { ?>
<form method="post">
  نوع الحجز: <select name="type">
    <option value="0">مبدئي</option>
    <option value="1">نهائي</option>
  </select><br>
  الشهر: <input type="number" name="month" min="1" max="12"><br>
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
```

---

## ❓ سؤال 5: حجز مبدئي (مع التأكد إن اليوم غير محجوز)

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['book'])){
  $date=$_POST['date']; $no=$_POST['no']; $uid=$_SESSION['u_no'];

  // تحقق إن اليوم غير محجوز
  $r=$conn->prepare("SELECT * FROM resv_Info WHERE r_date=? AND no=?");
  $r->execute(array($date,$no));
  if($r->rowCount()>0){
    echo "هذا اليوم محجوز مسبقاً!";
  } else {
    $conn->exec("INSERT INTO resv_Info(u_no,Paid,r_date,Type,no)
      VALUES($uid,0,'$date',0,$no)");
    echo "تم الحجز المبدئي";
  }
} else { ?>
<form method="post">
  رقم القاعة: <select name="no">
  <?php
    $r=$conn->query("SELECT * FROM mosque_info");
    while($row=$r->fetch(PDO::FETCH_OBJ))
      echo "<option value='$row->no'>قاعة $row->no - $row->price دينار</option>";
  ?>
  </select><br>
  التاريخ: <input type="date" name="date"><br>
  <input type="submit" name="book" value="حجز مبدئي">
</form>
<?php } ?>
```

---

## ❓ سؤال 6: تعديل بيانات قاعة (UPDATE)

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

// الخطوة 1: بحث
if(isset($_POST['s'])){
  $no=$_POST['no'];
  $r=$conn->prepare("SELECT * FROM mosque_info WHERE no=?");
  $r->execute(array($no));
  if($row=$r->fetch(PDO::FETCH_OBJ)){ ?>
    <form method="post">
      <input type="hidden" name="no" value="<?=$row->no?>">
      السعر: <input type="text" name="price" value="<?=$row->price?>"><br>
      الوصف: <textarea name="desc"><?=$row->description?></textarea><br>
      <input type="submit" name="upd" value="تعديل">
    </form>
<?php }
}
// الخطوة 2: تنفيذ التعديل
elseif(isset($_POST['upd'])){
  $no=$_POST['no']; $price=$_POST['price']; $desc=$_POST['desc'];
  $conn->exec("UPDATE mosque_info SET price=$price, description='$desc' WHERE no=$no");
  echo "تم التعديل";
}
else { ?>
<form method="post">
  رقم القاعة: <input type="text" name="no">
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
```

---

## 🎯 ملخص — القالب لأي سؤال من المشروع:

```
1. اتصال: dbname=turkey_mosque
2. الجداول: user_info, resv_Info, mosque_info, Picture
3. Priv: 1=admin, 2=user
4. Type: 0=مبدئي, 1=نهائي
5. نفس القالب دائماً: اتصال → فورم → بحث → عرض → تنفيذ
```
