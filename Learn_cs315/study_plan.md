# 🎯 خطة دراسة مكثفة — يوم واحد للامتحان النهائي (CS 315)

> [!IMPORTANT]
> **المواد المستثناة من الامتحان**: CSS و JavaScript
> **وقت الخطة**: من 7:00 صباحاً حتى 12:00 منتصف الليل (17 ساعة صافية مع استراحات)
> **استراتيجية**: الأهم أولاً → الأكثر تكراراً في الامتحانات → التفاصيل الدقيقة

---

## ⏰ الجدول الزمني العام

| الوقت | الجلسة | الموضوع | الأولوية |
|-------|--------|---------|----------|
| 7:00 - 8:30 | 🟢 الجلسة 1 | PHP + PDO (الاتصال + CRUD) | 🔴 حرجة |
| 8:30 - 8:45 | ☕ استراحة | — | — |
| 8:45 - 10:15 | 🟢 الجلسة 2 | PHP: Sessions, Cookies, File Upload | 🔴 حرجة |
| 10:15 - 10:30 | ☕ استراحة | — | — |
| 10:30 - 12:00 | 🟡 الجلسة 3 | SQL: الأوامر الأربعة (CRUD) | 🔴 حرجة |
| 12:00 - 13:00 | 🍽️ غداء + صلاة | — | — |
| 13:00 - 14:30 | 🟡 الجلسة 4 | PHP: الأساسيات + معالجة النماذج | 🟡 مهمة |
| 14:30 - 14:45 | ☕ استراحة | — | — |
| 14:45 - 16:00 | 🟡 الجلسة 5 | PHP: Validation + Regular Expressions | 🟡 مهمة |
| 16:00 - 16:15 | ☕ استراحة | — | — |
| 16:15 - 17:30 | 🔵 الجلسة 6 | HTML: النماذج (Forms) + الجداول (Tables) | 🟡 مهمة |
| 17:30 - 18:00 | 🍽️ عشاء + صلاة | — | — |
| 18:00 - 19:15 | 🔵 الجلسة 7 | HTML الأساسي + XHTML + الإطارات | 🟢 متوسطة |
| 19:15 - 19:30 | ☕ استراحة | — | — |
| 19:30 - 21:00 | 🔴 الجلسة 8 | مراجعة شاملة + حل تمارين + محاكاة امتحان | 🔴 حرجة |
| 21:00 - 22:00 | 🔴 الجلسة 9 | مراجعة الأكواد الأكثر أهمية + النوم مبكراً | 🔴 حرجة |

---

# 🟢 الجلسة 1 (7:00 - 8:30) — PHP + PDO: الاتصال و CRUD

> [!CAUTION]
> هذا هو **أهم موضوع** في الامتحان. احفظ هذه الأكواد كما تحفظ اسمك!

## 1.1 الاتصال بقاعدة البيانات — احفظه حرفياً

```php
<?php
$servername = 'mysql:host=localhost; dbname=student';
$username   = 'root';
$password   = '';

try {
    $conn = new PDO($servername, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Error :" . $e->getMessage();
}
?>
```

### 🔑 نقاط الحفظ:
- `new PDO(...)` ← يأخذ 3 بارامترات: **اسم السيرفر مع القاعدة**، اسم المستخدم، كلمة المرور
- `setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)` ← يحوّل الأخطاء لاستثناءات
- `catch(PDOException $e)` ← يمسك أخطاء PDO فقط
- `$e->getMessage()` ← يطبع رسالة الخطأ
- `$conn = null;` ← لإغلاق الاتصال

---

## 1.2 الإدخال (INSERT)

```php
function insert($conn) {
    $id     = $_POST['id'];
    $name   = $_POST['name'];
    $gender = $_POST['gender'];
    $n      = $_POST['nation'];

    try {
        $sql = "INSERT INTO info VALUES ($id, '$name', '$gender', '$n')";
        $conn->exec($sql);
        echo "Record insert successfully";
    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}
```

### 🔑 نقاط الحفظ:
- `$conn->exec($sql)` ← لتنفيذ INSERT, UPDATE, DELETE (لا ترجع صفوف)
- القيم النصية تُحاط بعلامات تنصيص مفردة `'$name'`
- القيم الرقمية **بدون** علامات تنصيص `$id`

---

## 1.3 البحث (SELECT) — أهم دالة!

```php
function search($conn) {
    $nat = $_POST['nation'];
    try {
        $sql  = "SELECT * FROM info WHERE nation=?";
        $rows = $conn->prepare($sql);
        $rows->execute(array($nat));
        $n = $rows->rowCount();

        if ($n > 0) {
            while ($row = $rows->fetch(PDO::FETCH_OBJ)) {
                echo $row->id . ":" . $row->name;
            }
        }
    } catch(PDOException $e) {
        echo "Error:" . $e->getMessage();
    }
}
```

### 🔑 الفرق بين طرق الجلب:

| الطريقة | الوصول للبيانات | مثال |
|---------|----------------|------|
| `PDO::FETCH_OBJ` | كأوبجكت بسهم | `$row->name` |
| `PDO::FETCH_ASSOC` | كمصفوفة ترابطية | `$row['name']` |
| `fetchAll()` | كل الصفوف دفعة واحدة | `$row[0]['name']` |

### 🔑 الفرق بين `query` و `prepare`:

| الأمر | الاستخدام |
|-------|-----------|
| `$conn->query($sql)` | استعلام **بدون متغيرات** من المستخدم |
| `$conn->prepare($sql)` + `execute(array(...))` | استعلام **فيه متغيرات** (أكثر أماناً) |
| `$conn->exec($sql)` | INSERT / UPDATE / DELETE (لا يرجع صفوف) |

---

## 1.4 التحديث (UPDATE)

```php
$query = "UPDATE info SET name='$name', gender='$gender', nation='$nation' WHERE id=$id";
$conn->exec($query);
echo "Record updated successfully";
```

## 1.5 الحذف (DELETE) — حذف متعدد بحلقة

```php
$std = $_POST['id'];  // مصفوفة من الـ checkboxes
foreach ($std as $value) {
    $sql = "delete from info where id=$value";
    $conn->exec($sql);
    echo "Record deleted successfully";
}
$conn = null;
```

---

## ✏️ تمرين سريع (5 دقائق)
اكتب على ورقة بدون النظر:
1. كود اتصال PDO كامل
2. كود insert كامل
3. كود select مع prepare و FETCH_OBJ

---

# 🟢 الجلسة 2 (8:45 - 10:15) — Sessions, Cookies, File Upload

## 2.1 الجلسات (Sessions) — مهمة جداً في المشروع

```php
// بداية كل صفحة تستخدم sessions
session_start();

// حفظ بيانات في الجلسة (عند تسجيل الدخول)
$_SESSION['user_id']   = $row['no_U'];
$_SESSION['user_name'] = $row['name_u'];

// قراءة بيانات الجلسة
$user_id = $_SESSION['user_id'];

// التحقق من تسجيل الدخول (في أول كل صفحة محمية)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// تدمير الجلسة (Logout)
session_destroy();
```

### 🔑 قواعد ذهبية:
- `session_start()` **يجب** أن تكون **أول سطر** في الملف (قبل أي HTML)
- `$_SESSION` مصفوفة **عامة** تعيش بين الصفحات
- `isset()` تتحقق هل المتغير موجود أم لا
- `header("Location: ...")` يحوّل المستخدم لصفحة أخرى
- `exit;` **ضروري** بعد `header()` لإيقاف باقي الكود

---

## 2.2 الكوكيز (Cookies)

```php
// إنشاء كوكي (الاسم، القيمة)
setcookie('bg', $colors[$bg_name]);
setcookie('fg', $colors[$fg_name]);

// قراءة كوكي
$bg = $_COOKIE['bg'];
$fg = $_COOKIE['fg'];
```

### 🔑 الفرق بين Session و Cookie:

| الخاصية | Session | Cookie |
|---------|---------|--------|
| مكان التخزين | السيرفر | جهاز المستخدم |
| المدة | تنتهي بإغلاق المتصفح | يمكن تحديد مدة |
| الأمان | أكثر أماناً | أقل أماناً |
| الحجم | غير محدود تقريباً | 4KB تقريباً |
| البدء | `session_start()` | `setcookie()` |
| القراءة | `$_SESSION['key']` | `$_COOKIE['key']` |

---

## 2.3 رفع الملفات (File Upload)

```php
// في الـ HTML — لاحظ enctype و multiple
<form method="post" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple>
    <input type="submit" name="add" value="Upload">
</form>
```

```php
// في PHP — معالجة الرفع
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $uploadDir = 'uploads/';

    foreach ($_FILES['images']['name'] as $index => $origName) {
        if ($_FILES['images']['error'][$index] == UPLOAD_ERR_OK) {
            $tmpName    = $_FILES['images']['tmp_name'][$index];
            $ext        = pathinfo($origName, PATHINFO_EXTENSION);
            $newName    = 'art_' . $articleId . '_' . $index . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                echo "تم الرفع بنجاح";
            }
        }
    }
}
```

### 🔑 نقاط الحفظ:
- `enctype="multipart/form-data"` ← **إجباري** في الفورم لرفع الملفات
- `name="images[]"` ← الأقواس `[]` تعني **مصفوفة** (ملفات متعددة)
- `$_FILES['images']['tmp_name']` ← المسار المؤقت للملف
- `$_FILES['images']['name']` ← الاسم الأصلي للملف
- `$_FILES['images']['error']` ← كود الخطأ (0 = OK)
- `move_uploaded_file($tmp, $target)` ← نقل الملف من المؤقت للمجلد النهائي
- `pathinfo($name, PATHINFO_EXTENSION)` ← استخراج امتداد الملف

### فحص نوع الملف:
```php
if (!preg_match("image", $_FILES['pix']['type'])) {
    echo "File is not a picture";
}
```

---

# 🟡 الجلسة 3 (10:30 - 12:00) — SQL: الأوامر الأربعة

## 3.1 SELECT — الاستعراض

```sql
-- اختيار الكل
SELECT * FROM info;

-- اختيار أعمدة محددة
SELECT name, gender FROM info;

-- مع شرط
SELECT * FROM info WHERE nation='Libya';

-- بدون تكرار
SELECT DISTINCT nation FROM info;

-- ترتيب تصاعدي/تنازلي
SELECT * FROM info ORDER BY name ASC;
SELECT * FROM info ORDER BY name DESC;

-- مع أكثر من شرط
SELECT * FROM info WHERE gender='male' AND nation='Libya';
SELECT * FROM info WHERE nation='Libya' OR nation='Egypt';

-- البحث بنمط (LIKE)
SELECT * FROM article WHERE subject LIKE '%technology%';
SELECT * FROM article WHERE category LIKE '%sport%';

-- الربط بين جداول (JOIN)
SELECT article.*, users.name_u
FROM article
JOIN users ON article.no_U = users.no_U
WHERE activate = 1;

-- أكبر / أصغر قيمة
SELECT MAX(E_no) FROM info;
SELECT MIN(E_no) FROM info;

-- العد
SELECT COUNT(*) FROM article WHERE activate = 0;
```

### 🔑 قواعد LIKE:
| النمط | المعنى |
|-------|--------|
| `'%ali%'` | يحتوي على ali في أي مكان |
| `'ali%'` | يبدأ بـ ali |
| `'%ali'` | ينتهي بـ ali |
| `'_li'` | حرف واحد ثم li |

---

## 3.2 INSERT — الإدخال

```sql
-- إدخال مع تحديد الأعمدة
INSERT INTO info (id, name, gender, nation)
VALUES (1, 'Ahmed', 'male', 'Libya');

-- إدخال بدون تحديد الأعمدة (يجب ذكر كل القيم بالترتيب)
INSERT INTO info VALUES (1, 'Ahmed', 'male', 'Libya');
```

## 3.3 UPDATE — التحديث

```sql
UPDATE info SET name='Ali', gender='male' WHERE id=1;

-- تحديث أكثر من عمود
UPDATE article SET no_E = 5, page = 3 WHERE no_A = 10;

-- ⚠️ بدون WHERE يحدّث كل السجلات!
UPDATE info SET nation='Libya';  -- خطير!
```

## 3.4 DELETE — الحذف

```sql
DELETE FROM info WHERE id=1;

-- حذف بشرط
DELETE FROM article WHERE no_A = 5;

-- ⚠️ بدون WHERE يحذف كل السجلات!
DELETE FROM info;  -- خطير!
```

### 🔑 ملخص سريع:

| الأمر | الصيغة | يستخدم في PDO |
|-------|--------|---------------|
| SELECT | `SELECT cols FROM table WHERE condition` | `query()` أو `prepare()` |
| INSERT | `INSERT INTO table VALUES (...)` | `exec()` |
| UPDATE | `UPDATE table SET col=val WHERE condition` | `exec()` |
| DELETE | `DELETE FROM table WHERE condition` | `exec()` |

---

# 🟡 الجلسة 4 (13:00 - 14:30) — PHP الأساسيات + معالجة النماذج

## 4.1 المتغيرات والأنواع

```php
$name  = "Ahmed";       // نص
$age   = 25;            // رقم صحيح
$grade = 95.5;          // رقم عشري
$pass  = true;          // منطقي
$arr   = array(1,2,3);  // مصفوفة
```

### 🔑 قواعد:
- المتغيرات تبدأ بـ `$`
- لا حاجة لتعريف النوع
- حساسة لحالة الأحرف: `$Name ≠ $name`

## 4.2 الشروط

```php
if ($age >= 18) {
    echo "بالغ";
} elseif ($age >= 13) {
    echo "مراهق";
} else {
    echo "طفل";
}
```

## 4.3 الحلقات

```php
// for
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// while
while ($row = $rows->fetch(PDO::FETCH_OBJ)) {
    echo $row->name;
}

// foreach — مهمة جداً!
foreach ($std as $value) {
    echo $value;
}

// foreach مع مفتاح وقيمة
foreach ($_POST as $key => $value) {
    echo "$key = $value";
}
```

## 4.4 الدوال

```php
function display_form($conn) {
    // كود عرض الفورم
}

function insert($conn) {
    // كود الإدخال
    return $result;
}
```

## 4.5 معالجة النماذج — $_GET و $_POST

```php
// قراءة من POST
$name = $_POST['name'];
$id   = $_POST['id'];

// قراءة من GET
$page = $_GET['page'];
$issue = $_GET['issue'];

// التحقق من الإرسال
if (isset($_POST['ok'])) {
    insert($conn);
}

// التحقق من طريقة الطلب
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    display_form($conn);
}

// تضمين ملفات
include_once("connection_pdo.php");
include_once("fun_search_pdo.php");

// استخراج مصفوفة لمتغيرات
extract($_POST);  // يحوّل $_POST['name'] إلى $name
```

### 🔑 الفرق بين GET و POST:

| الخاصية | GET | POST |
|---------|-----|------|
| البيانات | تظهر في URL | مخفية |
| الحجم | محدود | غير محدود |
| الأمان | أقل | أكثر |
| الاستخدام | بحث، فلترة | إدخال، تسجيل |

### 🔑 الفرق بين include و require:

| الأمر | عند الفشل |
|-------|-----------|
| `include` | تحذير Warning ويكمل |
| `require` | خطأ Fatal ويتوقف |
| `include_once` | يضمّن مرة واحدة فقط |

---

## 4.6 التعامل مع Checkbox كمصفوفة

```html
<!-- في HTML: لاحظ [] في الاسم -->
<input type='checkbox' name='id[]' value='1'>
<input type='checkbox' name='id[]' value='2'>
<input type='checkbox' name='id[]' value='3'>
```

```php
// في PHP: يستقبلها كمصفوفة
$std = $_POST['id'];  // مصفوفة مثل [1, 3]
foreach ($std as $value) {
    echo $value;  // 1 ثم 3
}
```

---

# 🟡 الجلسة 5 (14:45 - 16:00) — Validation + Regular Expressions

## 5.1 التحقق من المدخلات (Server-Side)

```php
// التحقق من الحقول الفارغة
$errors = [];

if ($name_u === "") {
    $errors[] = "Username is required.";
}

if ($password === "") {
    $errors[] = "Password is required.";
} else {
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain letters and numbers.";
    }
}

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email format is invalid.";
}

// طباعة الأخطاء
if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p style='color:red;'>$e</p>";
    }
    return;
}
```

## 5.2 filter_var و filter_var_array

```php
// التحقق من البريد
filter_var($email, FILTER_VALIDATE_EMAIL);

// التحقق من رقم صحيح مع نطاق
$filters = array(
    "name"   => array("filter" => FILTER_CALLBACK, "options" => "check_name"),
    "age"    => array("filter" => FILTER_VALIDATE_INT,
                      "options" => array("min_range" => 20, "max_range" => 55)),
    "email"  => FILTER_VALIDATE_EMAIL,
    "gender" => FILTER_NULL_ON_FAILURE,
);

$all_input = array(
    "name"   => $_POST['name'],
    "age"    => $_POST['age'],
    "email"  => $_POST['email'],
    "gender" => @$_POST['gender']
);

$valid = filter_var_array($all_input, $filters);
```

## 5.3 التعبيرات النمطية (Regular Expressions)

```php
// التحقق من اسم (حروف فقط، 3-15 حرف)
function check_name($name) {
    return preg_match("/\b(\w){3,15}\b/", $name);
}

// هل يحتوي على حروف وأرقام؟
preg_match('/[A-Za-z]/', $password);  // فيه حروف؟
preg_match('/[0-9]/', $password);     // فيه أرقام؟

// هل الملف صورة؟
preg_match("image", $_FILES['pix']['type']);
```

### 🔑 رموز Regex الأساسية:

| الرمز | المعنى | مثال |
|-------|--------|------|
| `\w` | حرف أو رقم أو _ | `a`, `5`, `_` |
| `\d` | رقم | `0-9` |
| `\b` | حدود الكلمة | بداية/نهاية كلمة |
| `{3,15}` | من 3 إلى 15 تكرار | |
| `[A-Za-z]` | أي حرف إنجليزي | |
| `[0-9]` | أي رقم | |
| `+` | واحد أو أكثر | |
| `*` | صفر أو أكثر | |
| `.` | أي حرف | |

---

# 🔵 الجلسة 6 (16:15 - 17:30) — HTML: النماذج والجداول

## 6.1 النماذج (Forms) — مهمة جداً!

```html
<form method="post" action="#">

    <!-- حقل نصي -->
    <input type="text" name="id">

    <!-- حقل كلمة مرور -->
    <input type="password" name="password">

    <!-- أزرار راديو (اختيار واحد فقط) -->
    <input type="radio" name="gender" value="male"> male
    <input type="radio" name="gender" value="female"> female

    <!-- صناديق اختيار (اختيار متعدد) -->
    <input type="checkbox" name="id[]" value="1"> Item 1
    <input type="checkbox" name="id[]" value="2"> Item 2

    <!-- قائمة منسدلة -->
    <select name="nation">
        <option value="Libya">Libya</option>
        <option value="Egypt">Egypt</option>
    </select>

    <!-- قائمة مع إكمال تلقائي -->
    <input list="n" name="nation">
    <datalist id="n">
        <option value="Libya">
        <option value="Egypt">
    </datalist>

    <!-- حقل تاريخ -->
    <input type="date" name="Date_A">

    <!-- رفع ملف -->
    <input type="file" name="images[]" multiple>

    <!-- حقل مخفي -->
    <input type="hidden" name="id" value="5">

    <!-- منطقة نصية -->
    <textarea name="body"></textarea>

    <!-- زر الإرسال -->
    <input type="submit" name="ok" value="إرسال">
</form>
```

### 🔑 خصائص مهمة:

| الخاصية | الوظيفة |
|---------|---------|
| `method="post"` | إرسال البيانات مخفية |
| `action="#"` | يرسل لنفس الصفحة |
| `enctype="multipart/form-data"` | **إجباري** لرفع الملفات |
| `name="id[]"` | يرسل كـ**مصفوفة** |
| `checked="checked"` | محدد مسبقاً (radio/checkbox) |
| `maxlength=8` | أقصى عدد حروف |
| `size=20` | عرض الحقل |
| `value="..."` | قيمة افتراضية |
| `multiple` | السماح بملفات متعددة |

### 🔑 الفرق بين select و datalist:

| العنصر | السلوك |
|--------|--------|
| `<select>` | قائمة **مغلقة** — يختار من الموجود فقط |
| `<datalist>` | قائمة **مفتوحة** — يمكنه الكتابة أو الاختيار |

---

## 6.2 الجداول (Tables)

```html
<table border="1" align="center" WIDTH="600"
       bordercolor="red" bgcolor="green">
    <thead>
        <tr>
            <th>Month</th>
            <th>Savings</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>January</td>
            <td>$100</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td>Sum</td>
            <td>$180</td>
        </tr>
    </tfoot>
</table>
```

### 🔑 خصائص الجداول:

| الخاصية | الوظيفة |
|---------|---------|
| `border="1"` | حدود الجدول |
| `align="center"` | محاذاة الجدول |
| `WIDTH="600"` | عرض الجدول |
| `bgcolor="green"` | لون الخلفية |
| `bordercolor="red"` | لون الحدود |
| `colspan="2"` | دمج أعمدة |
| `rowspan="3"` | دمج صفوف |
| `ALIGN="right"` | محاذاة خلية |

### 🔑 أقسام الجدول:
- `<thead>` — رأس الجدول (العناوين)
- `<tbody>` — جسم الجدول (البيانات)
- `<tfoot>` — ذيل الجدول (المجاميع)
- `<th>` — خلية عنوان (عريضة ومركزة)
- `<td>` — خلية بيانات عادية

---

# 🔵 الجلسة 7 (18:00 - 19:15) — HTML الأساسي + XHTML + الإطارات

## 7.1 هيكل صفحة HTML

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>عنوان الصفحة</title>
</head>
<body>
    <h1>عنوان رئيسي</h1>
    <p>فقرة نصية</p>
</body>
</html>
```

## 7.2 العناوين والفقرات

```html
<h1>أكبر عنوان</h1>    <!-- إلى -->    <h6>أصغر عنوان</h6>
<p>فقرة</p>
<br>    <!-- سطر جديد -->
<hr>    <!-- خط أفقي -->
```

## 7.3 القوائم

```html
<!-- قائمة مرتبة (أرقام) -->
<ol>
    <li>العنصر الأول</li>
    <li>العنصر الثاني</li>
</ol>

<!-- قائمة غير مرتبة (نقاط) -->
<ul>
    <li>العنصر الأول</li>
    <li>العنصر الثاني</li>
</ul>

<!-- قائمة متداخلة -->
<ol>
    <li>كلية العلوم
        <ul>
            <li>قسم الرياضيات</li>
            <li>قسم الإحصاء</li>
        </ul>
    </li>
</ol>
```

## 7.4 XHTML — الفروقات عن HTML

| القاعدة | HTML | XHTML |
|---------|------|-------|
| الوسوم | يمكن أن تكون كبيرة | **يجب أن تكون صغيرة** |
| الإغلاق | `<br>` مقبول | يجب `<br/>` |
| التداخل | قد يسامح | **ممنوع التداخل الخطأ** |
| الخصائص | `checked` مقبول | يجب `checked="checked"` |
| علامات التنصيص | اختيارية | **إجبارية** حول القيم |

```html
<!-- HTML مقبول -->
<BR>
<input type=text>

<!-- XHTML صحيح -->
<br/>
<input type="text"/>
```

## 7.5 الروابط والصور

```html
<!-- رابط -->
<a href="page2.html">اضغط هنا</a>
<a href="page2.html" target="_blank">في نافذة جديدة</a>

<!-- صورة -->
<img src="photo.jpg" alt="وصف الصورة" width="200" height="150">
```

---

# 🔴 الجلسة 8 (19:30 - 21:00) — مراجعة شاملة + أسئلة متوقعة

## ❓ أسئلة متوقعة في الامتحان

### السؤال 1: اكتب كود اتصال PDO كامل
← ارجع للجلسة 1.1

### السؤال 2: اكتب دالة PHP تقوم بإدخال بيانات طالب في جدول
← ارجع للجلسة 1.2

### السؤال 3: اكتب كود يبحث في جدول info حسب الجنسية ويعرض النتائج
← ارجع للجلسة 1.3

### السؤال 4: ما الفرق بين الدوال التالية؟
- `exec()` vs `query()` vs `prepare()`
- `FETCH_OBJ` vs `FETCH_ASSOC`
- `include` vs `require`
- `$_GET` vs `$_POST`
- `Session` vs `Cookie`

### السؤال 5: اكتب نموذج HTML فيه:
- حقول نصية
- radio buttons
- checkboxes
- datalist
- file upload

### السؤال 6: اكتب جملة SQL تقوم بـ:
- إدخال سجل جديد
- تحديث سجل موجود
- حذف سجل بشرط
- استعلام مع JOIN

### السؤال 7: اكتب كود PHP للتحقق من المدخلات
- حقول فارغة
- صحة البريد الإلكتروني
- كلمة مرور تحتوي حروف وأرقام

### السؤال 8: اكتب كود رفع ملف في PHP
- الفورم مع enctype
- كود PHP مع move_uploaded_file

### السؤال 9: اشرح كيف يعمل نظام تسجيل الدخول
- session_start
- التحقق من البيانات
- حفظ في $_SESSION
- التوجيه حسب الصلاحية

---

## 📝 ملخص الأكواد الأكثر أهمية للحفظ (بالترتيب)

### 🥇 الأهم — احفظها حرفياً:
1. **اتصال PDO** ← `new PDO(...)` + `setAttribute` + `try-catch`
2. **INSERT مع exec** ← `$conn->exec($sql)`
3. **SELECT مع prepare** ← `prepare()` → `execute(array())` → `fetch(PDO::FETCH_OBJ)`
4. **Session** ← `session_start()` → `$_SESSION['key']` → `isset()` → `header("Location:")`

### 🥈 مهم جداً:
5. **UPDATE / DELETE** مع `exec()`
6. **File Upload** ← `enctype` + `$_FILES` + `move_uploaded_file()`
7. **Validation** ← `filter_var()` + `preg_match()`
8. **Cookie** ← `setcookie()` + `$_COOKIE`

### 🥉 مهم:
9. **HTML Forms** — كل عناصر الإدخال
10. **SQL** — الأوامر الأربعة + JOIN + LIKE

---

# 🔴 الجلسة 9 (21:00 - 22:00) — المراجعة الأخيرة

## ✅ قائمة تحقق نهائية — هل تستطيع كتابة:

- [ ] اتصال PDO كامل بدون نظر؟
- [ ] دالة insert كاملة؟
- [ ] دالة search مع prepare و fetch؟
- [ ] كود update و delete؟
- [ ] `session_start()` + حماية صفحة + تسجيل خروج؟
- [ ] `setcookie()` + قراءة `$_COOKIE`؟
- [ ] فورم رفع ملف + كود PHP للمعالجة؟
- [ ] validation مع `filter_var` و `preg_match`؟
- [ ] فورم HTML فيه radio, checkbox[], datalist, hidden?
- [ ] جدول HTML مع thead, tbody, tfoot, colspan?
- [ ] جملة SQL مع JOIN و LIKE؟

> [!TIP]
> **نصيحة أخيرة**: نم مبكراً! الدماغ يثبّت المعلومات أثناء النوم. 8 ساعات نوم = أفضل من ساعتين دراسة إضافية.

---

> **بالتوفيق في الامتحان! 🎓✨**
