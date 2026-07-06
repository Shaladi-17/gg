<?php
$conn = new PDO("mysql:host=localhost;dbname=Airline","root","");

if(isset($_POST['s'])){
  $r = $conn->prepare("SELECT * FROM Flight WHERE F_Date=? AND Res_num_E=0");
  $r->execute(array($_POST['d']));
  echo "<form method='post'><table border='1'>
  <tr><th>✓</th><th>رقم</th><th>طائرة</th><th>شركة</th><th>من</th><th>إلى</th><th>زمن</th></tr>";
  while($row = $r->fetch(PDO::FETCH_OBJ)){
    echo "<tr><td><input type='checkbox' name='id[]' value='$row->F_no'></td>
    <td>$row->F_no</td><td>$row->P_no</td><td>$row->Comp_Name</td>
    <td>$row->from_C_no</td><td>$row->to_C_no</td><td>$row->Ar_time</td></tr>";
  }
  echo "</table><input type='submit' name='del' value='حذف'></form>";
}
elseif(isset($_POST['del'])){
  foreach($_POST['id'] as $v)
    $conn->exec("DELETE FROM Flight WHERE F_no=$v");
  echo "تم الحذف";
}
else{
?>
<form method="post">
  تاريخ: <input type="date" name="d">
  <input type="submit" name="s" value="بحث">
</form>
<?php } ?>
