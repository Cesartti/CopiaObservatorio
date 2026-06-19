# Procesa infografia.png del paquete grafico para usarla en los carruseles.
Add-Type -AssemblyName System.Drawing

$src = 'C:\xampp\htdocs\Observatorio2026\PAQUETE GRAFICO\OBSERVATORIO IGUALDAD DE GENERO DEL CARIBE\infografia.png'
$out = 'C:\xampp\htdocs\Observatorio2026\website\uploads\cms\2026\06\infografia-observatorio.jpg'

$img = [System.Drawing.Image]::FromFile($src)
Write-Output ("Original: {0}x{1}" -f $img.Width, $img.Height)
$w = [Math]::Min(1600, $img.Width)
$h = [int]($img.Height * ($w / $img.Width))
$bmp = New-Object System.Drawing.Bitmap($w, $h)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.InterpolationMode = 'HighQualityBicubic'
$g.Clear([System.Drawing.Color]::White)
$g.DrawImage($img, 0, 0, $w, $h)
$enc = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object MimeType -eq 'image/jpeg'
$params = New-Object System.Drawing.Imaging.EncoderParameters(1)
$params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]85)
$bmp.Save($out, $enc, $params)
$g.Dispose(); $bmp.Dispose(); $img.Dispose()
Write-Output ("Guardada: {0} ({1} KB)" -f $out, [math]::Round((Get-Item $out).Length / 1KB))
