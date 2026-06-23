
$pptApp = New-Object -ComObject PowerPoint.Application
$pptApp.Visible = [Microsoft.Office.Core.MsoTriState]::msoTrue

# --- File 1: INTRODUCTION TO PHP ---
$pres1 = $pptApp.Presentations.Open("C:\Users\acer\Desktop\PHP\INTRODUCTION TO PHP (2).ppt", $false, $false, $false)
$text1 = ""
foreach ($slide in $pres1.Slides) {
    $text1 += "=== Slide " + $slide.SlideIndex + " ===`r`n"
    foreach ($shape in $slide.Shapes) {
        if ($shape.HasTextFrame) {
            $text1 += $shape.TextFrame.TextRange.Text + "`r`n"
        }
    }
    $text1 += "`r`n"
}
$pres1.Close()
$text1 | Out-File -FilePath "C:\Users\acer\Desktop\PHP\intro_php.txt" -Encoding UTF8
Write-Host "Done: INTRODUCTION TO PHP"

# --- File 2: lecture2 ---
$pres2 = $pptApp.Presentations.Open("C:\Users\acer\Desktop\PHP\lecture2 (2).ppt", $false, $false, $false)
$text2 = ""
foreach ($slide in $pres2.Slides) {
    $text2 += "=== Slide " + $slide.SlideIndex + " ===`r`n"
    foreach ($shape in $slide.Shapes) {
        if ($shape.HasTextFrame) {
            $text2 += $shape.TextFrame.TextRange.Text + "`r`n"
        }
    }
    $text2 += "`r`n"
}
$pres2.Close()
$text2 | Out-File -FilePath "C:\Users\acer\Desktop\PHP\lecture2.txt" -Encoding UTF8
Write-Host "Done: lecture2"

$pptApp.Quit()
[System.Runtime.Interopservices.Marshal]::ReleaseComObject($pptApp) | Out-Null
Write-Host "All PPT files extracted successfully!"
