# 📚 CS336 - Concepts of Programming Languages | ملخص شامل للامتحان

> **الكتاب**: Concepts of Programming Languages, Robert W. Sebesta, 12th Ed
> **الامتحان**: غداً 8:30 صباحاً

---

## 📌 Ch1: Preliminaries (المقدمات)

### معايير تقييم اللغات (Language Evaluation Criteria) ⭐مهم جداً - يأتي دائماً
| المعيار | التعريف | مثال عالي | مثال ضعيف |
|---------|---------|-----------|-----------|
| **Readability** | سهولة قراءة وفهم البرنامج | Python | Perl |
| **Writability** | سهولة كتابة البرنامج | Ruby | Assembly |
| **Reliability** | مطابقة المواصفات والموثوقية | Haskell | C |
| **Cost** | التكلفة الإجمالية | JavaScript | C++ |

### عوامل Readability:
- **Overall simplicity**: عدد محدود من الميزات
- **Orthogonality**: عدد صغير من البنى الأولية يُركَّب بطرق قليلة
- **Data types**: أنواع بيانات مُعرَّفة مسبقاً كافية
- **Syntax considerations**: أشكال المعرفات، الكلمات الخاصة

### عوامل Reliability:
- **Type checking** - فحص أخطاء النوع
- **Exception handling** - اعتراض أخطاء التشغيل
- **Aliasing** - أسماء متعددة لنفس الموقع في الذاكرة (ضار للقراءة)

### مجالات البرمجة (Programming Domains):
- **Scientific**: Fortran (أعداد عشرية كبيرة)
- **Business**: COBOL (تقارير، أرقام عشرية)
- **AI**: LISP (رموز، قوائم مترابطة)
- **Systems**: C (كفاءة)
- **Web**: HTML, PHP, Java

### فئات اللغات (Language Categories):
- **Imperative**: C, Java, C++ (متغيرات + إسناد + تكرار)
- **Functional**: LISP, Scheme, ML, F# (تطبيق دوال)
- **Logic**: Prolog (قواعد)
- **Markup/hybrid**: JSTL, XSLT

### طرق التنفيذ (Implementation Methods):
| الطريقة | الوصف | السرعة |
|---------|-------|--------|
| **Compilation** | ترجمة إلى لغة الآلة | ترجمة بطيئة، تنفيذ سريع |
| **Pure Interpretation** | تفسير مباشر | أبطأ 10-100 مرة |
| **Hybrid** | ترجمة إلى لغة وسيطة ثم تفسير | وسط |
| **JIT** | ترجمة الأجزاء عند الاستدعاء | سريع |

### مراحل Compilation:
1. **Lexical analysis**: تحويل الأحرف إلى وحدات معجمية
2. **Syntax analysis**: تحويل إلى أشجار تحليل
3. **Semantic analysis**: توليد كود وسيط
4. **Code generation**: توليد كود الآلة

### Von Neumann Architecture:
- البيانات والبرامج في الذاكرة
- الذاكرة منفصلة عن المعالج
- **Von Neumann bottleneck**: سرعة الاتصال بين الذاكرة والمعالج

---

## 📌 Ch5: Names, Bindings, and Scopes (الأسماء والربط والنطاقات)

### متغيرات (Variables) - 6 خصائص (sextuple) ⭐مهم جداً:
1. **Name** - الاسم
2. **Address** - العنوان (l-value)
3. **Value** - القيمة (r-value)
4. **Type** - النوع
5. **Lifetime** - فترة الحياة
6. **Scope** - النطاق

### الأسماء:
- **Case sensitivity**: لغات C ← حساسة للحالة
- **Reserved word**: لا يمكن استخدامها كاسم مُعرَّف (COBOL: 300 كلمة محجوزة!)
- **Keyword**: خاصة فقط في سياقات معينة
- **Aliases**: اسمان يشيران لنفس الموقع (ضار للقراءة)

### الربط (Binding) ⭐:
> **Binding** = ارتباط بين كيان وخاصية

| وقت الربط | مثال |
|-----------|------|
| **Language design time** | ربط رموز العمليات |
| **Language implementation time** | ربط float بتمثيل |
| **Compile time** | ربط متغير بنوع في C |
| **Load time** | ربط متغير static بخلية ذاكرة |
| **Runtime** | ربط متغير محلي غير static بخلية |

- **Static binding**: يحدث قبل التشغيل ولا يتغير
- **Dynamic binding**: يحدث أثناء التشغيل ويمكن أن يتغير

### Type Binding:
- **Explicit declaration**: بيان تصريح صريح (`int x;`)
- **Implicit declaration**: آلية افتراضية (Perl, PHP, JavaScript)
- **Type inferencing**: C# `var`, ML, Haskell
- **Dynamic type binding**: JavaScript, Python, Ruby, PHP

### فئات المتغيرات حسب Lifetime ⭐مهم جداً:
| الفئة | الوصف | المزايا | العيوب |
|-------|-------|---------|--------|
| **Static** | مربوط قبل التنفيذ ويبقى | كفاءة، دعم history-sensitive | لا recursion |
| **Stack-dynamic** | يُنشأ عند elaboration | يدعم recursion، يوفر مساحة | overhead, لا history |
| **Explicit heap-dynamic** | بأوامر صريحة (new/delete) | إدارة ذاكرة ديناميكية | غير كفء وغير موثوق |
| **Implicit heap-dynamic** | بجمل الإسناد | مرونة | غير كفء، فقدان كشف الأخطاء |

### النطاق (Scope) ⭐مهم جداً - يأتي كسؤال دائماً:

#### Static Scope (النطاق الثابت):
- يعتمد على **نص البرنامج**
- البحث: محلياً → النطاق المحيط الأقرب → ... → العام
- **Static parent**: النطاق المحيط الأقرب
- **Static ancestors**: جميع النطاقات المحيطة

#### Dynamic Scope (النطاق الديناميكي):
- يعتمد على **تسلسل الاستدعاءات**
- البحث عبر سلسلة استدعاءات البرامج الفرعية

#### مثال Scope الكلاسيكي ⭐⭐⭐:
```
function big() {
    function sub1() {
        var x = 7;
        sub2();
    }
    function sub2() {
        var y = x;  // ← أي x؟
    }
    var x = 3;
    sub1();
}
```
- **Static scoping**: `x` في `sub2` = `big`'s `x` = **3**
- **Dynamic scoping**: `x` في `sub2` = `sub1`'s `x` = **7**

### Named Constants:
- **Manifest constants**: ربط ثابت (compile time)
- C# `const` (ثابت) vs `readonly` (ديناميكي)

### Referencing Environment:
- **Static-scoped**: المتغيرات المحلية + جميع المتغيرات المرئية في النطاقات المحيطة
- **Dynamic-scoped**: المتغيرات المحلية + جميع المتغيرات المرئية في البرامج الفرعية النشطة

---

## 📌 Ch6: Data Types (أنواع البيانات)

### Primitive Types:
- **Integer**: byte, short, int, long (Java)
- **Float**: IEEE 754, float & double
- **Complex**: C99, Python `(7+3j)`
- **Decimal**: COBOL, C# (BCD, دقة عالية، نطاق محدود)
- **Boolean**: true/false
- **Character**: ASCII, Unicode (UCS-2: 16-bit, UCS-4: 32-bit)

### Character String:
- أطوال: **Static** (COBOL, Java String), **Limited Dynamic** (C), **Dynamic** (Perl, JavaScript)

### Enumeration Types:
- `enum days {mon, tue, wed, ...};`
- مزايا: readability + reliability

### Array Types ⭐:

#### فئات المصفوفات:
| الفئة | الوصف | مثال |
|-------|-------|------|
| **Static** | حجم ثابت وقت الترجمة | `static int arr[10];` in C |
| **Fixed stack-dynamic** | حجم ثابت عند الإعلان | `int arr[n];` in C99 |
| **Fixed heap-dynamic** | حجم ثابت في heap | `new int[size]` in C++ |
| **Heap-dynamic** | حجم متغير | `ArrayList` in Java |

#### معادلة حساب عنوان عنصر ⭐:
- **1D**: `address(list[k]) = address(list[lb]) + ((k-lb) * element_size)`
- **2D**: `Location(a[i,j]) = address(a[row_lb,col_lb]) + (((i-row_lb)*n) + (j-col_lb)) * element_size`
- **Row major**: معظم اللغات
- **Column major**: Fortran

### Associative Arrays:
- مفهرسة بمفاتيح لا أرقام
- Perl: `%hash`, Python: `dict`, Ruby: `Hash`

### Record Types:
- مجموعة غير متجانسة من العناصر المُسمَّاة
- COBOL: level numbers, Others: dot notation

### Tuple Types:
- مثل Record لكن العناصر غير مُسمَّاة
- Python: `myTuple = (3, 5.8, 'apple')` - غير قابل للتعديل (immutable)

### List Types:
- Scheme: `CAR` (أول عنصر), `CDR` (الباقي), `CONS` (إضافة للبداية), `LIST` (إنشاء قائمة)
- Python: lists قابلة للتعديل (mutable), list comprehensions

### Union Types:
- **Free union**: C/C++ (بدون type checking)
- **Discriminated union**: ML, Haskell, F# (مع type checking)

### Pointer Types ⭐:
- عمليتان أساسيتان: **assignment** و **dereferencing**
- C++: `j = *ptr` (explicit dereferencing)
- مشاكل:
  - **Dangling pointer**: مؤشر يشير لذاكرة محررة (خطير!)
  - **Memory leakage**: ذاكرة محجوزة لا يمكن الوصول إليها (garbage)
- حلول: **Tombstones**, **Locks-and-keys**

### إدارة Heap:
- **Reference counter** (eager): عداد لكل خلية (مشكلة: دوائر)
- **Mark-sweep** (lazy): وسم الخلايا القابلة للوصول ثم تحرير الباقي

### Type Checking ⭐:
- **Type error**: تطبيق عملية على نوع غير مناسب
- **Coercion**: تحويل نوع ضمني
- **Strongly typed**: يكشف جميع أخطاء النوع
  - ML, F# ← strongly typed
  - Java, C# ← almost (بسبب casting)
  - C, C++ ← ليست (بسبب unions وتمرير المعاملات)

### Type Equivalence:
- **Name equivalence**: نفس الإعلان أو نفس اسم النوع (مقيد)
- **Structure equivalence**: نفس البنية (مرن لكن أصعب)

---

## 📌 Ch7: Expressions and Assignment (التعبيرات والإسناد)

### أسبقية العمليات (Operator Precedence):
`() → unary → ** → *, / → +, -`

### Associativity:
- عادة من اليسار لليمين
- `**` من اليمين لليسار
- APL: جميع العمليات متساوية، من اليمين لليسار

### Side Effects ⭐:
- **Functional side effect**: الدالة تغير معامل ثنائي الاتجاه أو متغير غير محلي
- مثال: `a = 10; b = a + fun(&a);` ← النتيجة تعتمد على ترتيب التقييم

### Referential Transparency ⭐:
- تعبيران بنفس القيمة يمكن استبدال أحدهما بالآخر
- اللغات الوظيفية النقية مرجعياً شفافة

### Type Conversions:
- **Narrowing**: float → int (فقدان بيانات)
- **Widening**: int → float (آمن نسبياً)
- **Coercion**: تحويل ضمني (يضعف type checking)
- **Casting**: تحويل صريح `(int)angle`

### Short-Circuit Evaluation ⭐:
- `&&`, `||` في C, C++, Java ← short-circuit
- `&`, `|` ← NOT short-circuit (bitwise)
- مشكلة: `(a > b) || (b++ / 3)` ← قد لا يتم تنفيذ `b++`

### Assignment:
- **Compound**: `a += b`
- **Unary**: `++count` (prefix), `count++` (postfix)
- **Multiple** (Perl/Ruby): `($a, $b) = ($b, $a)` ← تبديل!

### مثال الامتحان المهم ⭐⭐⭐:
```c
int fun(int* k) {
    *k += 4;
    return 3 * (*k) - 1;
}
void main() {
    int i = 10, j = 10, sum1, sum2;
    sum1 = (i / 2) + fun(&i);
    sum2 = fun(&j) + (j / 2);
}
```
| | Left-to-Right | Right-to-Left |
|---|---|---|
| **sum1** | 5 + 41 = **46** | 7 + 41 = **48** |
| **sum2** | 41 + 7 = **48** | 41 + 5 = **46** |

---

## 📌 Ch8: Statement-Level Control Structures

### Selection (الاختيار):
- **Two-way**: `if-then-else`
  - مشكلة dangling else: `else` يتبع أقرب `if` (Java)
- **Multiple-way**: `switch/case`
  - C: لا implicit branch (يحتاج `break`)
  - C#: يجب `break` أو `goto`
  - C#: يدعم strings كتعبير تحكم
- **Scheme COND**: `(COND (pred1 expr1) ... (ELSE exprn))`

### Iteration (التكرار):
- **Counter-controlled**: `for` loops
  - Python: `for x in range(5):`
  - F#: يحاكي بالـ recursion
- **Logically-controlled**: `while`, `do-while`
  - **Pretest**: `while` (C, Java)
  - **Posttest**: `do-while` (C)
- **Data structure-based**: `foreach`
  - Java: `for (String s : list)`
  - Ruby: `list.each {|v| puts v}`

### Loop Control:
- `break`: خروج من الحلقة (C, Python, Ruby)
- `continue`: تخطي بقية التكرار الحالي
- Java: labeled `break` و `continue`

### Guarded Commands (Dijkstra):
- Selection: `if B1 → S1 [] B2 → S2 fi`
- Loop: `do B1 → S1 [] B2 → S2 od`

---

## 📌 Ch9: Subprograms (البرامج الفرعية) ⭐⭐⭐

### المفاهيم الأساسية:
- **Subprogram definition**: يصف الواجهة والأفعال
- **Subprogram call**: طلب صريح للتنفيذ
- **Parameter profile (signature)**: عدد وترتيب وأنواع المعاملات
- **Protocol**: signature + نوع الإرجاع
- **Formal parameter**: معامل وهمي في التعريف
- **Actual parameter**: قيمة/عنوان في الاستدعاء

### أنماط تمرير المعاملات ⭐⭐⭐:

| الطريقة | الاتجاه | الوصف | العيوب |
|---------|---------|-------|--------|
| **Pass-by-value** | In | نسخ القيمة | تكلفة نسخ للحجم الكبير |
| **Pass-by-result** | Out | لا قيمة تُرسل، النتيجة تُنسخ عند الإرجاع | مشكلة `sub(p1,p1)` |
| **Pass-by-value-result** | Inout | نسخ ذهاباً وإياباً | عيوب الاثنين |
| **Pass-by-reference** | Inout | تمرير مسار الوصول (عنوان) | بطء، side effects, aliasing |
| **Pass-by-name** | Inout | استبدال نصي | معقد التنفيذ |

### تمرير المعاملات في اللغات الرئيسية ⭐:
- **C**: pass-by-value (المؤشرات لمحاكاة reference)
- **C++**: reference type لـ pass-by-reference
- **Java**: value للأنواع البدائية، reference للكائنات
- **C#**: default value، `ref` لـ reference
- **Python/Ruby**: pass-by-assignment

### Overloaded Subprograms:
- نفس الاسم، بروتوكول مختلف
- C++, Java, C#, Ada

### Generic Subprograms (Parametric Polymorphism):
- C++: `template <class Type> Type max(Type a, Type b)`
- Java 5.0: `public static <T> T doIt(T[] list)`
- أنواع polymorphism: **ad hoc** (overloading), **subtype** (OOP), **parametric** (generics)

### Closures ⭐:
> **Closure** = برنامج فرعي + بيئة التعريف (referencing environment)

```javascript
function makeAdder(x) {
    return function(y) { return x + y; }
}
var add10 = makeAdder(10);
add10(20); // → 30
```

### Coroutines:
- برنامج فرعي بنقاط دخول متعددة
- `resume` بدل `call`
- تنفيذ شبه متزامن (quasi-concurrent)

---

## 📌 Ch10: Implementing Subprograms

### Activation Record ⭐⭐⭐:
يحتوي: **Local variables** | **Parameters** | **Dynamic link** | **Return address** | **Static link** (إن وُجد)

### Dynamic Link:
- يشير إلى activation record المتصل (caller)
- مجموعة الروابط الديناميكية = **dynamic chain** (call chain)

### Static Link:
- يشير إلى activation record الأب الثابت (static parent)
- مجموعة الروابط الثابتة = **static chain**
- مرجع متغير: **(chain_offset, local_offset)**

### مثال Stack مع Recursion:
```
factorial(3) → factorial(2) → factorial(1)
```
كل استدعاء يُنشئ activation record instance جديد على الـ stack

### تنفيذ Dynamic Scoping:
- **Deep access**: بحث في dynamic chain (بطيء)
- **Shallow access**: جدول مركزي أو stack لكل اسم متغير

---

## 📌 Ch11: Abstract Data Types (ADTs)

### شرطا ADT ⭐:
1. **إخفاء التمثيل** عن وحدات البرنامج التي تستخدمه
2. **الإعلان والعمليات في وحدة نحوية واحدة**

### مزايا:
- **الشرط 1**: reliability, تقليل المتغيرات المرئية
- **الشرط 2**: تنظيم, قابلية التعديل, ترجمة منفصلة

### في C++:
- `class` = وحدة التغليف
- `private`, `public`, `protected`
- **Constructor**: تهيئة البيانات (نفس اسم الفئة)
- **Destructor**: تنظيف (`~ClassName`)
- **Friend**: وصول خاص للأعضاء الخاصة

### في Java:
- جميع الأنواع المعرفة فئات
- جميع الكائنات heap-dynamic
- garbage collection تلقائي
- **package scope** بدل friends

### في C#:
- **properties** (get/set) بدل accessor methods صريحة
- `internal`: مرئي لجميع الفئات في assembly

### Parameterized ADTs (Generic Classes):
- C++: `template <class Type> class Stack { ... }`
- Java: `Stack2<String> myStack = new Stack2<>();`

### Naming Encapsulations:
- C++: **namespaces**
- Java: **packages** (import)
- Ruby: **modules** (require)

---

## 📌 Ch12: Object-Oriented Programming ⭐⭐⭐

### ثلاث ميزات أساسية:
1. **Abstract Data Types** (ADTs)
2. **Inheritance** (الوراثة)
3. **Polymorphism** (تعدد الأشكال) / **Dynamic Binding**

### مفاهيم OOP:
- **Class**: النوع | **Object**: المثيل
- **Subclass/Derived**: الفرعي | **Superclass/Parent**: الأب
- **Method**: عملية | **Message**: استدعاء
- **Instance variables**: متغيرات لكل كائن
- **Class variables**: متغيرات لكل فئة

### 3 طرق يختلف فيها الفرع عن الأب:
1. إضافة متغيرات/دوال جديدة
2. تعديل سلوك الدوال الموروثة (override)
3. إخفاء أعضاء خاصة (private)

### Dynamic Binding ⭐:
- **Polymorphic variable**: يمكن أن يشير لكائنات الفئة أو أي فرع
- **Abstract method** (pure virtual): بدون تعريف
- **Abstract class**: لا يمكن إنشاء كائنات منها

### Inheritance في C++ ⭐:
- `private`, `public`, `protected` أعضاء
- **Public derivation**: public و protected تبقى كما هي
- **Private derivation**: جميعها تصبح private
- **Multiple inheritance** مدعومة (مشكلة: name collisions)
- `virtual` methods = dynamically bound
- `= 0` (pure virtual) → abstract class

### C++ vs Java:
```cpp
// C++: يجب اختيار virtual أو لا
class Shape {
    virtual void draw() = 0;  // pure virtual
};

// Java: كل الدوال dynamically bound إلا final/static/private
```

### Inheritance في Java:
- **Single inheritance** فقط
- **interface**: بديل جزئي لـ multiple inheritance
- `final` methods: لا يمكن override

### Inheritance في C#:
- `virtual` + `override` لـ dynamic binding
- `abstract` methods
- `new` لإعادة تعريف (shadowing)

### Implementation (التنفيذ):
- **CIR** (Class Instance Record): يخزن instance variables
- **vtable** (Virtual Method Table): جدول مؤشرات للدوال الافتراضية ⭐

### Reflection:
- Java: `getClass()`, `getMethod()`, `invoke()`
- C#: `GetType()`, `System.Reflection`
- عيوب: بطء، كشف الأعضاء الخاصة، إلغاء type checking المبكر

---

## 📌 Ch13: Concurrency (التزامن) ⭐⭐

### أنواع التزامن:
- **Physical**: معالجات متعددة فعلياً
- **Logical**: تقاسم الوقت على معالج واحد

### Task/Process/Thread:
- **Heavyweight**: مساحة عنوان خاصة
- **Lightweight**: نفس مساحة العنوان (أكفأ)

### نوعان من التزامن:
- **Cooperation**: المهمة A تنتظر B لتكمل نشاطاً (producer-consumer)
- **Competition**: مهمتان تتنافسان على مورد مشترك

### حالات المهمة:
`New → Ready → Running → Dead`
`Running → Blocked → Ready`

### Semaphores (Dijkstra 1965) ⭐:
- بنية بيانات: **counter** + **queue**
- عمليتان: `wait` (P) و `release` (V)

```
wait(s):   if s.counter > 0 then decrement
           else put caller in queue
release(s): if s.queue empty then increment counter
            else move task from queue to ready
```

- **Binary semaphore**: counter = 0 أو 1 (mutual exclusion)
- Producer-Consumer: `emptyspots`, `fullspots`, `access`

### Monitors:
- ADT للبيانات المشتركة
- ضمان mutual exclusion تلقائياً
- Java: `synchronized` methods/blocks

### Message Passing (Ada):
- **Rendezvous**: الاتصال عندما يكون المرسل والمستقبل جاهزين
- `entry` في specification, `accept` في body
- **Server task**: لديه accept clauses
- **Actor task**: بدون accept clauses
- **Guarded accept**: `when condition => accept ...`

### Java Threads ⭐:
```java
class MyThread extends Thread {
    public void run() { ... }
}
Thread myTh = new MyThread();
myTh.start();
```
- `yield()`, `sleep()`, `join()`
- **Competition sync**: `synchronized` keyword
- **Cooperation sync**: `wait()`, `notify()`, `notifyAll()`

### C# Threads:
- `Thread`, `Start()`, `Join()`, `Sleep()`, `Abort()`
- `Interlocked`, `lock`, `Monitor` classes

### Deadlock:
> جميع المهام تفقد liveness ← لا مهمة تستطيع الاستمرار

---

## 📌 Ch14: Exception Handling and Event Handling ⭐⭐

### المفاهيم:
- **Exception**: حدث غير عادي (خطأ أو لا)
- **Exception handler**: كود المعالجة
- **Raised/Thrown**: عند حدوث الاستثناء

### C++ Exception Handling:
```cpp
try {
    throw expression;
}
catch (Type1 param) { ... }
catch (Type2 param) { ... }
catch (...) { ... }  // catch-all
```
- لا استثناءات مُعرَّفة مسبقاً
- بعد المعالج ← التنفيذ يستمر بعد آخر catch

### Java Exception Handling ⭐:
```java
try { ... }
catch (ExceptionType e) { ... }
finally { ... }  // يُنفَّذ دائماً
```
- **Throwable** ← **Error** (لا تُعالج) + **Exception**
- **Checked exceptions**: يجب معالجتها أو إعلانها في `throws`
- **Unchecked**: `RuntimeException`, `Error` وفروعهما
- `finally` ← يُنفَّذ سواء حدث استثناء أم لا

### Python Exception Handling:
```python
try:
    ...
except ExceptionType as e:
    ...
else:      # لم يحدث استثناء
    ...
finally:   # دائماً
    ...
```

### Ruby Exception Handling:
```ruby
begin
    ...
rescue ExceptionType
    ...
    retry  # إعادة تنفيذ الكود! (ميزة فريدة)
ensure
    ...
end
```

### Event Handling:
- **Event**: إشعار بحدث (مثل نقر زر)
- **Java**: Event listeners, interfaces (`ItemListener`, `ActionListener`)
- **C#**: Delegates + events, `EventHandler`

---

## 📌 Ch15: Functional Programming Languages ⭐⭐

### المبادئ:
- **Lambda expression**: `λ(x) x * x * x`
- **Higher-order function**: تأخذ أو تُرجع دوال
- **Function composition**: `h = f ∘ g` → `h(x) = f(g(x))`
- **Apply-to-all (map)**: تطبق دالة على كل عنصر في قائمة
- **Referential transparency**: نفس المعاملات ← نفس النتيجة دائماً

### Scheme (لهجة من Lisp) ⭐:
```scheme
; تعريف
(DEFINE pi 3.14159)
(DEFINE (square x) (* x x))

; شرط
(IF (< x 0) (- x) x)

; COND
(COND
  ((NULL? lst) 0)
  (ELSE (+ (CAR lst) (sum (CDR lst)))))

; LET
(LET ((x 5) (y 3)) (+ x y))
```

### دوال القوائم في Scheme ⭐⭐⭐:
| الدالة | الوظيفة | مثال |
|--------|---------|------|
| `CAR` | أول عنصر | `(CAR '(A B C))` → `A` |
| `CDR` | الباقي بعد الأول | `(CDR '(A B C))` → `(B C)` |
| `CONS` | إضافة للبداية | `(CONS 'A '(B C))` → `(A B C)` |
| `LIST` | إنشاء قائمة | `(LIST 'A 'B 'C)` → `(A B C)` |
| `NULL?` | هل فارغة؟ | `(NULL? '())` → `#T` |
| `EQ?` | تساوي مؤشرات | `(EQ? 'A 'A)` → `#T` |
| `EQV?` | تساوي قيم | `(EQV? 3 3)` → `#T` |

### Tail Recursion ⭐:
```scheme
; ليست tail recursive
(DEFINE (factorial n)
  (IF (<= n 0) 1 (* n (factorial (- n 1)))))

; tail recursive (مع helper)
(DEFINE (facthelper n partial)
  (IF (<= n 0) partial
    (facthelper (- n 1) (* n partial))))
(DEFINE (factorial n) (facthelper n 1))
```
- Scheme يحول tail recursion إلى iteration تلقائياً

### مقارنة Imperative vs Functional:
| Imperative | Functional |
|------------|------------|
| تنفيذ كفء | دلالات بسيطة |
| دلالات معقدة | تركيب بسيط |
| تركيب معقد | تنفيذ أقل كفاءة |
| تزامن يدوي | تزامن تلقائي |

---

## 🎯 نصائح للامتحان

### أسئلة تتكرر دائماً:
1. ✅ **معايير تقييم اللغات** مع أمثلة
2. ✅ **Static vs Dynamic Scope** مع تتبع كود
3. ✅ **طرق تمرير المعاملات** ومقارنتها
4. ✅ **Side effects** وترتيب تقييم العمليات (sum1, sum2)
5. ✅ **Activation records** والـ stack
6. ✅ **OOP**: inheritance, polymorphism, dynamic binding
7. ✅ **vtable** وتنفيذ dynamic binding
8. ✅ **Concurrency**: semaphores (wait/release), monitors, Java synchronized
9. ✅ **Exception handling**: try/catch/finally
10. ✅ **Scheme**: CAR, CDR, CONS, DEFINE, recursive functions

### أسئلة الاختيار المتعدد المتكررة:
- Pass-by-value = **نسخ القيمة** (c)
- ADT purpose = **فصل الواجهة عن التنفيذ** (b)
- Static scoping needs = **Static Link** (b)
- Closure = **برنامج فرعي + بيئة التعريف** (c)
- `this`/`self` = **وصول لمتغيرات المثيل والدوال** (b)
- Abstract class = **لا يمكن إنشاء كائنات منها** (c)
- Multiple inheritance problem = **name conflicts** (c)
- Dynamic link = **يشير لـ activation record المتصل** (c)
- Dynamic binding implementation = **vtable** (c)
- Interface = **تعريف نماذج دوال + شكل من الوراثة المتعددة** (b)

---

> ⏰ **بالتوفيق في الامتحان! راجع الأمثلة العملية خاصة Scope وParameter Passing وScheme**
