# Redimensiona y comprime las imagenes del paquete grafico de genero a uploads/cms.
Add-Type -AssemblyName System.Drawing

$src = 'C:\xampp\htdocs\Observatorio2026\PAQUETE GRAFICO\OBSERVATORIO IGUALDAD DE GENERO DEL CARIBE'
$dst = 'C:\xampp\htdocs\Observatorio2026\website\uploads\cms\2026\06'
New-Item -ItemType Directory -Force $dst | Out-Null

$map = @{
    'AUTONOMIA ECONOMICA' = 'autonomia-economica'
    'AUTONOMIA EN LA TOMA DE DESICIONES+' = 'autonomia-toma-decisiones'
    'AUTONOMIA FÍSICA' = 'autonomia-fisica'
    'participación política de las mujeres en ALC' = 'participacion-politica-alc'
}

$enc = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object MimeType -eq 'image/jpeg'
$params = New-Object System.Drawing.Imaging.EncoderParameters(1)
$params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]82)

$total = 0
foreach ($k in $map.Keys) {
    Get-ChildItem (Join-Path $src $k) -Filter *.png | Sort-Object Name | ForEach-Object {
        $img = [System.Drawing.Image]::FromFile($_.FullName)
        $w = [Math]::Min(1600, $img.Width)
        $h = [int]($img.Height * ($w / $img.Width))
        $bmp = New-Object System.Drawing.Bitmap($w, $h)
        $g = [System.Drawing.Graphics]::FromImage($bmp)
        $g.InterpolationMode = 'HighQualityBicubic'
        $g.Clear([System.Drawing.Color]::White)
        $g.DrawImage($img, 0, 0, $w, $h)
        $out = Join-Path $dst ("genero-caribe-{0}-{1}.jpg" -f $map[$k], $_.BaseName)
        $bmp.Save($out, $enc, $params)
        $g.Dispose(); $bmp.Dispose(); $img.Dispose()
        $total++
    }
}
Write-Output "Procesadas: $total"
Get-ChildItem $dst -Filter 'genero-caribe-*' | ForEach-Object { "{0} ({1} KB)" -f $_.Name, [math]::Round($_.Length/1KB) }
