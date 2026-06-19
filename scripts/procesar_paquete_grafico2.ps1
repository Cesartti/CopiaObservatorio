# Procesa las carpetas con acentos resolviendo nombres por comodin.
Add-Type -AssemblyName System.Drawing

$src = 'C:\xampp\htdocs\Observatorio2026\PAQUETE GRAFICO\OBSERVATORIO IGUALDAD DE GENERO DEL CARIBE'
$dst = 'C:\xampp\htdocs\Observatorio2026\website\uploads\cms\2026\06'

$dirs = Get-ChildItem $src -Directory
$fisica = $dirs | Where-Object { $_.Name -like 'AUTONOMIA F*' } | Select-Object -First 1
$particip = $dirs | Where-Object { $_.Name -like 'participaci*' } | Select-Object -First 1

$enc = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object MimeType -eq 'image/jpeg'
$params = New-Object System.Drawing.Imaging.EncoderParameters(1)
$params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]82)

$jobs = @(
    @{ dir = $fisica; slug = 'autonomia-fisica' },
    @{ dir = $particip; slug = 'participacion-politica-alc' }
)
$total = 0
foreach ($j in $jobs) {
    if (-not $j.dir) { Write-Output "Carpeta no encontrada para $($j.slug)"; continue }
    Get-ChildItem $j.dir.FullName -Filter *.png | Sort-Object Name | ForEach-Object {
        $img = [System.Drawing.Image]::FromFile($_.FullName)
        $w = [Math]::Min(1600, $img.Width)
        $h = [int]($img.Height * ($w / $img.Width))
        $bmp = New-Object System.Drawing.Bitmap($w, $h)
        $g = [System.Drawing.Graphics]::FromImage($bmp)
        $g.InterpolationMode = 'HighQualityBicubic'
        $g.Clear([System.Drawing.Color]::White)
        $g.DrawImage($img, 0, 0, $w, $h)
        $out = Join-Path $dst ("genero-caribe-{0}-{1}.jpg" -f $j.slug, $_.BaseName)
        $bmp.Save($out, $enc, $params)
        $g.Dispose(); $bmp.Dispose(); $img.Dispose()
        $total++
    }
}
Write-Output "Procesadas: $total"
