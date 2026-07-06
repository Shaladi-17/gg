# 📝 أسئلة متوقعة من مشروع المجلة — مع الحل المختصر

> قاعدة البيانات: **magazine**

## 📋 الجداول

```
users:   no_U*, name_u, password, priv(R/U), name, nationality, gender, address, email
article: no_A*, subject, A_body, no_U, category, Date_A, no_E, page, activate(0/1), image
info:    E_no*, U_No, date_I
images:  no_A, File_IM
```

---

## ❓ سؤال 1: تسجيل دخول (Login)

**المطلوب**: صفحة تسجيل دخول تتحقق من اسم المستخدم وكلمة المرور وتوجّه للوحة المناسبة

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");

if(isset($_POST['ok'])){
  $u = $_POST['user']; $p = $_POST['pass'];
  $r = $conn->prepare("SELECT * FROM users WHERE name_u=? AND password=?");
  $r->execute(array($u,$p));
  if($row = $r->fetch(PDO::FETCH_OBJ)){
    $_SESSION['no_U'] = $row->no_U;
    $_SESSION['priv'] = $row->priv;
    if($row->priv=='R') header("Location: mdashbord.php");
    else header("Location: udashbord.php");
    exit;
  } else echo "خطأ في البيانات";
} else { ?>
<form method="post">
  اسم المستخدم: <input type="text" name="user"><br>
  كلمة المرور: <input type="password" name="pass"><br>
  <input type="submit" name="ok" value="دخول">
</form>
<?php } ?>
```

### 🔑 النقاط المهمة:
- `session_start()` أول سطر
- `prepare` مع `?` لأن البيانات من المستخدم
- `$row->priv == 'R'` ← رئيس تحرير → `mdashbord`
- `$row->priv == 'U'` ← كاتب → `udashbord`
- `exit` بعد `header`

---

## ❓ سؤال 2: تسجيل مستخدم جديد (Register)

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");

if(isset($_POST['ok'])){
  $u=$_POST['user']; $p=$_POST['pass']; $n=$_POST['name'];
  $nat=$_POST['nat']; $g=$_POST['gender']; $e=$_POST['email'];

  // فحص البريد المكرر
  $r = $conn->prepare("SELECT * FROM users WHERE email=?");
  $r->execute(array($e));
  if($r->rowCount()>0){ echo "البريد مسجل مسبقاً"; }
  else{
    $sql="INSERT INTO users(name_u,password,priv,name,nationality,gender,email)
          VALUES('$u','$p','U','$n','$nat','$g','$e')";
    $conn->exec($sql);
    echo "تم التسجيل بنجاح";
  }
} else { ?>
<form method="post">
  اسم المستخدم: <input type="text" name="user"><br>
  كلمة المرور: <input type="password" name="pass"><br>
  الاسم: <input type="text" name="name"><br>
  الجنسية: <input type="text" name="nat"><br>
  الجنس: <input type="radio" name="gender" value="male">ذكر
         <input type="radio" name="gender" value="female">أنثى<br>
  البريد: <input type="text" name="email"><br>
  <input type="submit" name="ok" value="تسجيل">
</form>
<?php } ?>
```

---

## ❓ سؤال 3: إضافة مقال مع رفع صور

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");

if(isset($_POST['add'])){
  $sub=$_POST['subject']; $body=$_POST['body']; $cat=$_POST['category'];
  $uid=$_SESSION['no_U']; $date=date('Y-m-d');

  $sql="INSERT INTO article(subject,A_body,no_U,category,Date_A,activate)
        VALUES('$sub','$body',$uid,'$cat','$date',0)";
  $conn->exec($sql);
  $aid=$conn->lastInsertId();

  // رفع الصور
  if(!empty($_FILES['img']['name'][0])){
    foreach($_FILES['img']['name'] as $i=>$name){
      $tmp=$_FILES['img']['tmp_name'][$i];
      $ext=pathinfo($name,PATHINFO_EXTENSION);
      $new="art_".$aid."_".$i.".".$ext;
      move_uploaded_file($tmp,"uploads/".$new);
      $conn->exec("INSERT INTO images VALUES($aid,'$new')");
    }
  }
  echo "تم إضافة المقال";
} else { ?>
<form method="post" enctype="multipart/form-data">
  العنوان: <input type="text" name="subject"><br>
  التصنيف: <input type="text" name="category"><br>
  المحتوى: <textarea name="body"></textarea><br>
  صور: <input type="file" name="img[]" multiple><br>
  <input type="submit" name="add" value="إضافة">
</form>
<?php } ?>
```

### 🔑 النقاط المهمة:
- `$conn->lastInsertId()` ← يرجّع رقم المقال اللي تم إدخاله للتو
- `activate=0` ← المقال ينتظر الموافقة
- `enctype="multipart/form-data"` ← إجباري لرفع الملفات
- `name="img[]"` ← ملفات متعددة

---

## ❓ سؤال 4: موافقة على المقالات (رئيس التحرير)

**المطلوب**: عرض المقالات المعلّقة والموافقة عليها

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");

if(isset($_POST['approve'])){
  foreach($_POST['id'] as $v)
    $conn->exec("UPDATE article SET activate=1 WHERE no_A=$v");
  echo "تم الموافقة";
}

$r=$conn->query("SELECT article.*,users.name FROM article
  JOIN users ON article.no_U=users.no_U WHERE activate=0");
echo "<form method='post'><table border='1'>
<tr><th>✓</th><th>العنوان</th><th>الكاتب</th><th>التصنيف</th><th>التاريخ</th></tr>";
while($row=$r->fetch(PDO::FETCH_OBJ)){
  echo "<tr><td><input type='checkbox' name='id[]' value='$row->no_A'></td>
  <td>$row->subject</td><td>$row->name</td>
  <td>$row->category</td><td>$row->Date_A</td></tr>";
}
echo "</table><input type='submit' name='approve' value='موافقة'></form>";
?>
```

### 🔑 النقاط المهمة:
- `JOIN users ON article.no_U = users.no_U` ← لعرض اسم الكاتب
- `WHERE activate=0` ← المقالات المعلّقة فقط
- `UPDATE SET activate=1` ← الموافقة

---

## ❓ سؤال 5: بحث في المقالات (حسب العنوان أو التصنيف)

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");

if(isset($_POST['s'])){
  $key=$_POST['key'];
  $r=$conn->prepare("SELECT * FROM article
    WHERE activate=1 AND (subject LIKE ? OR category LIKE ?)");
  $r->execute(array("%$key%","%$key%"));
  while($row=$r->fetch(PDO::FETCH_OBJ)){
    echo "$row->subject — $row->category — $row->Date_A<br>";
  }
} else { ?>
<form method="post">
  بحث: <input type="text" name="key">
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
```

---

## ❓ سؤال 6: حذف مقالات الكاتب

```php
<?php session_start();
$conn = new PDO("mysql:host=localhost;dbname=magazine","root","");
$uid = $_SESSION['no_U'];

if(isset($_POST['del'])){
  foreach($_POST['id'] as $v){
    $conn->exec("DELETE FROM images WHERE no_A=$v");
    $conn->exec("DELETE FROM article WHERE no_A=$v");
  }
  echo "تم الحذف";
}

$r=$conn->prepare("SELECT * FROM article WHERE no_U=?");
$r->execute(array($uid));
echo "<form method='post'><table border='1'>
<tr><th>✓</th><th>العنوان</th><th>الحالة</th></tr>";
while($row=$r->fetch(PDO::FETCH_OBJ)){
  $st = $row->activate ? "منشور" : "معلّق";
  echo "<tr><td><input type='checkbox' name='id[]' value='$row->no_A'></td>
  <td>$row->subject</td><td>$st</td></tr>";
}
echo "</table><input type='submit' name='del' value='حذف'></form>";
?>
```

### 🔑 النقطة المهمة:
- **احذف من images أولاً** ثم من article ← لأن images مرتبطة بـ article

---

## 🎯 خلاصة — القالب الموحد لكل الأسئلة:

```php
<?php
$conn = new PDO("mysql:host=localhost;dbname=___","root","");

if(isset($_POST['btn'])){
  // نفّذ العملية (DELETE/UPDATE/INSERT)
}

// ابحث واعرض
$r = $conn->prepare("SELECT ... WHERE ...=?");
$r->execute(array($_POST['...']));
echo "<form method='post'><table border='1'>";
while($row = $r->fetch(PDO::FETCH_OBJ)){
  echo "<tr><td><input type='checkbox' name='id[]' value='$row->...'></td>
        <td>$row->...</td></tr>";
}
echo "</table><input type='submit' name='btn' value='...'></form>";
?>
```

> **غيّر اسم القاعدة + اسم الجدول + الأعمدة + العملية = أي سؤال تحله! ✅**
