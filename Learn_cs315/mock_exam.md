# 📝 امتحان تجريبي — CS 315 (ساعتان)

> **قاعدة البيانات**: `turkey_mosque`

```
user_info:    u_no*, u_name, Password, Priv(1=admin/2=user), Name, Address, Telphone, email
resv_Info:    r_no*, u_no, Paid, r_date, Type(0=مبدئي/1=نهائي), no
mosque_info:  no*, price, description
Picture:      pic_no*, Pic_path, no
```

---

## السؤال 1 (15 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** إلغاء الحجوزات **المبدئية** التي **لم يُدفع فيها أي مبلغ** حيث يتم البحث حسب **شهر محدد** من قبل المستخدم.

ويتم عرض بيانات الحجز مثل: رقم الحجز - اسم الزبون - تاريخ الحجز - رقم القاعة - المبلغ المدفوع.

---

## السؤال 2 (15 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** صفحة تسجيل دخول تتحقق من اسم المستخدم وكلمة المرور، وتوجّه المستخدم إلى `admin.php` إذا كان admin أو `user.php` إذا كان user، مع استخدام الجلسات (Sessions).

---

## السؤال 3 (20 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** إضافة قاعة صلاة جديدة مع **رفع صورة** لها، حيث يتم إدخال: رقم القاعة - سعر الحجز - مواصفات القاعة - صورة القاعة.

---

## السؤال 4 (20 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** تعديل سعر قاعة صلاة، حيث يقوم المستخدم بالبحث عن القاعة حسب **رقمها**، ثم تُعرض بياناتها الحالية (السعر والمواصفات) في حقول قابلة للتعديل، ثم يُحفظ التعديل.

---

## السؤال 5 (15 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** تأكيد الحجوزات المبدئية (تحويلها من `Type=0` إلى `Type=1`) للزبائن الذين **دفعوا مبلغاً أكبر من أو يساوي سعر القاعة**. يتم عرض: رقم الحجز - اسم الزبون - المبلغ المدفوع - سعر القاعة - التاريخ.

---

## السؤال 6 (15 درجة)

استخدم قاعدة البيانات `turkey_mosque` لتصميم الصفحة التالية:

**أ.** تمكين الزبون من عمل حجز مبدئي لقاعة معينة في تاريخ معين، مع التأكد أن القاعة **غير محجوزة** في ذلك التاريخ. يتم عرض قائمة القاعات المتاحة من قاعدة البيانات.

---
---
---

# ✅ الحلول

---

## حل السؤال 1

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['del'])){
  foreach($_POST['id'] as $v)
    $conn->exec("DELETE FROM resv_Info WHERE r_no=$v");
  echo "تم الإلغاء";
}
elseif(isset($_POST['s'])){
  $m=$_POST['month'];
  $r=$conn->prepare("SELECT resv_Info.*,user_info.Name FROM resv_Info
    JOIN user_info ON resv_Info.u_no=user_info.u_no
    WHERE Type=0 AND Paid=0 AND MONTH(r_date)=?");
  $r->execute(array($m));
  echo "<form method='post'><table border='1'>
  <tr><th>✓</th><th>رقم الحجز</th><th>الزبون</th><th>التاريخ</th><th>القاعة</th><th>المبلغ</th></tr>";
  while($row=$r->fetch(PDO::FETCH_OBJ)){
    echo "<tr><td><input type='checkbox' name='id[]' value='$row->r_no'></td>
    <td>$row->r_no</td><td>$row->Name</td><td>$row->r_date</td>
    <td>$row->no</td><td>$row->Paid</td></tr>";
  }
  echo "</table><input type='submit' name='del' value='إلغاء'></form>";
}
else{ ?>
<form method="post">
  الشهر: <input type="number" name="month" min="1" max="12">
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
```

### 🔑 النقاط:
- `Type=0` ← مبدئي
- `Paid=0` ← لم يدفع
- `MONTH(r_date)=?` ← البحث حسب الشهر
- `JOIN user_info` ← لعرض اسم الزبون

---

## حل السؤال 2

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
  } else echo "بيانات خاطئة";
} else { ?>
<form method="post">
  المستخدم: <input type="text" name="user"><br>
  كلمة المرور: <input type="password" name="pass"><br>
  <input type="submit" name="ok" value="دخول">
</form>
<?php } ?>
```

### 🔑 النقاط:
- `session_start()` أول سطر
- `Priv==1` → admin, `Priv==2` → user
- `exit` بعد `header`

---

## حل السؤال 3

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['add'])){
  $no=$_POST['no']; $price=$_POST['price']; $desc=$_POST['desc'];
  $conn->exec("INSERT INTO mosque_info VALUES($no,$price,'$desc')");

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
  المواصفات: <textarea name="desc"></textarea><br>
  صورة: <input type="file" name="pic"><br>
  <input type="submit" name="add" value="إضافة">
</form>
<?php } ?>
```

### 🔑 النقاط:
- `enctype="multipart/form-data"` ← إجباري لرفع الملفات
- `move_uploaded_file()` ← نقل الملف
- INSERT في جدولين: `mosque_info` + `Picture`

---

## حل السؤال 4

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['s'])){
  $no=$_POST['no'];
  $r=$conn->prepare("SELECT * FROM mosque_info WHERE no=?");
  $r->execute(array($no));
  if($row=$r->fetch(PDO::FETCH_OBJ)){ ?>
    <form method="post">
      <input type="hidden" name="no" value="<?=$row->no?>">
      السعر: <input type="text" name="price" value="<?=$row->price?>"><br>
      المواصفات: <textarea name="desc"><?=$row->description?></textarea><br>
      <input type="submit" name="upd" value="حفظ">
    </form>
<?php }
}
elseif(isset($_POST['upd'])){
  $no=$_POST['no']; $price=$_POST['price']; $desc=$_POST['desc'];
  $conn->exec("UPDATE mosque_info SET price=$price, description='$desc' WHERE no=$no");
  echo "تم التعديل";
}
else{ ?>
<form method="post">
  رقم القاعة: <input type="text" name="no">
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
```

### 🔑 النقاط:
- **خطوتين**: بحث أولاً → ثم تعديل
- `input type="hidden"` ← يحمل رقم القاعة بدون عرضه
- `value="<?=$row->price?>"` ← يعرض القيمة الحالية في الحقل

---

## حل السؤال 5

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['confirm'])){
  foreach($_POST['id'] as $v)
    $conn->exec("UPDATE resv_Info SET Type=1 WHERE r_no=$v");
  echo "تم التأكيد";
}

$r=$conn->query("SELECT resv_Info.*,user_info.Name,mosque_info.price
  FROM resv_Info
  JOIN user_info ON resv_Info.u_no=user_info.u_no
  JOIN mosque_info ON resv_Info.no=mosque_info.no
  WHERE Type=0 AND Paid>=mosque_info.price");
echo "<form method='post'><table border='1'>
<tr><th>✓</th><th>الحجز</th><th>الزبون</th><th>المدفوع</th><th>سعر القاعة</th><th>التاريخ</th></tr>";
while($row=$r->fetch(PDO::FETCH_OBJ)){
  echo "<tr><td><input type='checkbox' name='id[]' value='$row->r_no'></td>
  <td>$row->r_no</td><td>$row->Name</td><td>$row->Paid</td>
  <td>$row->price</td><td>$row->r_date</td></tr>";
}
echo "</table><input type='submit' name='confirm' value='تأكيد'></form>";
?>
```

### 🔑 النقاط:
- **JOIN مزدوج**: `user_info` + `mosque_info`
- `Paid >= mosque_info.price` ← دفع أكثر من أو يساوي السعر
- `UPDATE SET Type=1` ← تحويل من مبدئي لنهائي

---

## حل السؤال 6

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=turkey_mosque","root","");

if(isset($_POST['book'])){
  $date=$_POST['date']; $no=$_POST['no']; $uid=$_SESSION['u_no'];
  $r=$conn->prepare("SELECT * FROM resv_Info WHERE r_date=? AND no=?");
  $r->execute(array($date,$no));
  if($r->rowCount()>0){
    echo "القاعة محجوزة في هذا التاريخ!";
  } else {
    $conn->exec("INSERT INTO resv_Info(u_no,Paid,r_date,Type,no)
      VALUES($uid,0,'$date',0,$no)");
    echo "تم الحجز المبدئي بنجاح";
  }
} else { ?>
<form method="post">
  القاعة: <select name="no">
  <?php
    $r=$conn->query("SELECT * FROM mosque_info");
    while($row=$r->fetch(PDO::FETCH_OBJ))
      echo "<option value='$row->no'>قاعة $row->no - $row->price دينار</option>";
  ?>
  </select><br>
  التاريخ: <input type="date" name="date"><br>
  <input type="submit" name="book" value="حجز">
</form>
<?php } ?>
```

### 🔑 النقاط:
- **فحص التكرار**: `rowCount()>0` ← محجوز!
- `Type=0, Paid=0` ← حجز مبدئي بدون دفع
- `<select>` ← القاعات من قاعدة البيانات ديناميكياً
