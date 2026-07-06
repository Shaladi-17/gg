# 🌙 مراجعة ليلة الامتحان — أهم النقاط فقط

> الوقت محدود. ركّز على هذي النقاط وبس. بالتوفيق غداً! 🎓

---

## 1️⃣ اتصال PDO — **أهم كود في الامتحان**

```php
<?php
try {
    $conn = new PDO("mysql:host=localhost; dbname=student", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error:" . $e->getMessage();
}
?>
```

---

## 2️⃣ العمليات الأربعة على قاعدة البيانات

```php
// INSERT
$conn->exec("INSERT INTO info VALUES ($id, '$name', '$gender', '$nation')");

// UPDATE
$conn->exec("UPDATE info SET name='$name' WHERE id=$id");

// DELETE
$conn->exec("DELETE FROM info WHERE id=$id");

// SELECT مع prepare (الأهم!)
$rows = $conn->prepare("SELECT * FROM info WHERE nation=?");
$rows->execute(array($nat));
while ($row = $rows->fetch(PDO::FETCH_OBJ)) {
    echo $row->id . " : " . $row->name;
}
```

---

## 3️⃣ الفروقات — **تجي في الامتحان دائماً**

| | |
|---|---|
| `exec()` | INSERT, UPDATE, DELETE — **ما يرجّع صفوف** |
| `query()` | SELECT **بدون متغيرات** من المستخدم |
| `prepare() + execute()` | SELECT **فيه متغيرات** — أكثر أماناً |

| | |
|---|---|
| `FETCH_OBJ` | `$row->name` (بسهم) |
| `FETCH_ASSOC` | `$row['name']` (بأقواس مربعة) |

| | |
|---|---|
| `$_GET` | البيانات في URL — للبحث والفلترة |
| `$_POST` | البيانات مخفية — للإدخال والتسجيل |

| | |
|---|---|
| `include` | لو فشل = تحذير ويكمل |
| `require` | لو فشل = خطأ ويتوقف |

---

## 4️⃣ Session — تسجيل الدخول

```php
session_start();  // ⚠️ أول سطر دائماً!

// حفظ
$_SESSION['user_id'] = $row['no_U'];

// قراءة
$id = $_SESSION['user_id'];

// حماية صفحة
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// تسجيل خروج
session_destroy();
```

---

## 5️⃣ Cookies

```php
// كتابة
setcookie('bg', '#ff0000');

// قراءة (في الصفحة الجاية!)
$bg = $_COOKIE['bg'];
```

| Session | Cookie |
|---|---|
| في **السيرفر** | في **جهاز المستخدم** |
| تنتهي بإغلاق المتصفح | تعيش حسب المدة |
| `$_SESSION` | `$_COOKIE` |

---

## 6️⃣ رفع ملفات — 3 أشياء احفظها

```html
<!-- 1. الفورم — لاحظ enctype -->
<form method="post" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple>
</form>
```

```php
// 2. الأجزاء الأربعة لـ $_FILES
$_FILES['images']['name']      // الاسم الأصلي
$_FILES['images']['tmp_name']  // المسار المؤقت
$_FILES['images']['error']     // 0 = OK
$_FILES['images']['type']      // نوع الملف

// 3. النقل
move_uploaded_file($tmpName, "uploads/" . $newName);
```

---

## 7️⃣ Validation

```php
// بريد إلكتروني
filter_var($email, FILTER_VALIDATE_EMAIL);

// كلمة مرور فيها حروف وأرقام
preg_match('/[A-Za-z]/', $password);  // فيه حروف؟
preg_match('/[0-9]/', $password);     // فيه أرقام؟

// اسم (3-15 حرف)
preg_match("/\b(\w){3,15}\b/", $name);
```

---

## 8️⃣ SQL — احفظ الصيغ

```sql
SELECT * FROM info WHERE nation='Libya';
SELECT * FROM info WHERE name LIKE '%ali%';
SELECT * FROM article JOIN users ON article.no_U = users.no_U;

INSERT INTO info VALUES (1, 'Ali', 'male', 'Libya');
UPDATE info SET name='Sara' WHERE id=1;
DELETE FROM info WHERE id=1;
```

| `'%ali%'` | يحتوي على ali |
|---|---|
| `'ali%'` | يبدأ بـ ali |
| `'%ali'` | ينتهي بـ ali |

---

## 9️⃣ HTML Forms — عناصر الإدخال

```html
<input type="text" name="id">                    <!-- نص -->
<input type="password" name="pass">              <!-- كلمة مرور -->
<input type="radio" name="gender" value="male">  <!-- اختيار واحد -->
<input type="checkbox" name="id[]" value="1">    <!-- اختيار متعدد → مصفوفة -->
<input type="hidden" name="id" value="5">        <!-- مخفي -->
<textarea name="body"></textarea>                 <!-- نص طويل -->

<!-- datalist = قائمة مفتوحة (يكتب أو يختار) -->
<input list="n" name="nation">
<datalist id="n">
    <option value="Libya">
</datalist>

<!-- select = قائمة مغلقة (يختار فقط) -->
<select name="nation">
    <option value="Libya">Libya</option>
</select>
```

---

## 🔟 HTML Tables

```html
<table border="1">
  <tr><th colspan="2">العنوان</th></tr>   <!-- دمج عمودين -->
  <tr><td>بيانات</td><td>بيانات</td></tr>
</table>
```
- `colspan` = دمج **أعمدة** أفقياً ↔️
- `rowspan` = دمج **صفوف** عمودياً ↕️

---

## ⚠️ أخطاء شائعة — تجنبها غداً!

| ❌ الخطأ | ✅ الصح |
|---------|--------|
| نسيان `session_start()` | حطها **أول سطر** |
| نسيان `enctype` في فورم الرفع | `enctype="multipart/form-data"` |
| نسيان `exit` بعد `header()` | `header("Location:..."); exit;` |
| نسيان `[]` في checkbox | `name="id[]"` ← مصفوفة |
| نسيان علامات التنصيص حول النصوص في SQL | `'$name'` مش `$name` |
| استخدام `query()` مع متغير مستخدم | استخدم `prepare()` |
| قراءة cookie في **نفس الصفحة** | تنقرأ في **الصفحة الجاية** |

---

> 💤 **نم الآن!** الدماغ يثبّت المعلومات أثناء النوم.
> ⏰ **بالتوفيق غداً الساعة 8:30** — إن شاء الله تجيب أعلى درجة! 🌟
