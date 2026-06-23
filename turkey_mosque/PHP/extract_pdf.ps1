Add-Type -AssemblyName "System.Text.Encoding"

$enc = [System.Text.Encoding]::GetEncoding("iso-8859-1")

# File 1
$bytes1 = [System.IO.File]::ReadAllBytes("C:\Users\acer\Desktop\PHP\Regular Expressions2016_php.pdf")
$text1 = $enc.GetString($bytes1)
$matches1 = [regex]::Matches($text1, '\(([^\(\)\\]{4,300})\)')
$out1 = New-Object System.Text.StringBuilder
foreach ($m in $matches1) {
    $val = $m.Groups[1].Value.Trim()
    if ($val -match '[a-zA-Z]{3,}') {
        [void]$out1.AppendLine($val)
    }
}
[System.IO.File]::WriteAllText("C:\Users\acer\Desktop\PHP\regex_pdf.txt", $out1.ToString(), [System.Text.Encoding]::UTF8)
Write-Host ("Regex PDF: " + $out1.Length + " chars")

# File 2
$bytes2 = [System.IO.File]::ReadAllBytes("C:\Users\acer\Desktop\PHP\validation.pdf")
$text2 = $enc.GetString($bytes2)
$matches2 = [regex]::Matches($text2, '\(([^\(\)\\]{4,300})\)')
$out2 = New-Object System.Text.StringBuilder
foreach ($m in $matches2) {
    $val = $m.Groups[1].Value.Trim()
    if ($val -match '[a-zA-Z]{3,}') {
        [void]$out2.AppendLine($val)
    }
}
[System.IO.File]::WriteAllText("C:\Users\acer\Desktop\PHP\validation_pdf.txt", $out2.ToString(), [System.Text.Encoding]::UTF8)
Write-Host ("Validation PDF: " + $out2.Length + " chars")
Write-Host "Done!"
