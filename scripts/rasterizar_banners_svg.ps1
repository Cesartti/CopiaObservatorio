# Rasteriza los banners SVG a JPG 1600px usando un HTML envoltorio que escala el SVG.
Add-Type -AssemblyName System.Drawing

$src = 'C:\xampp\htdocs\Observatorio2026\PAQUETE GRAFICO\OBSERVATORIO IGUALDAD DE GENERO DEL CARIBE'
$dst = 'C:\xampp\htdocs\Observatorio2026\website\uploads\cms\2026\06'

$browser = @(
    "$env:ProgramFiles\Google\Chrome\Application\chrome.exe",
    "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe",
    "$env:ProgramFiles\Microsoft\Edge\Application\msedge.exe"
) | Where-Object { Test-Path $_ } | Select-Object -First 1
if (-not $browser) { Write-Output 'ERROR: sin navegador'; exit 1 }

# w/h segun el viewBox de cada SVG, escalado a 1600 de ancho
$jobs = @(
    @{ svg = 'ASUNTOS DE GENERO.svg'; out = 'banner-genero';    w = 1600; h = 527 },
    @{ svg = 'SOCIAL.svg';            out = 'banner-social';    w = 1600; h = 527 },
    @{ svg = 'ECONOMICO.svg';         out = 'banner-economico'; w = 1600; h = 527 }
)

$enc = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object MimeType -eq 'image/jpeg'
$params = New-Object System.Drawing.Imaging.EncoderParameters(1)
$params.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [long]88)

foreach ($j in $jobs) {
    $svgPath = Join-Path $src $j.svg
    $svgUri = 'file:///' + ([uri]::EscapeUriString(($svgPath -replace '\\', '/')))
    $html = '<!doctype html><html><body style="margin:0;background:#fff"><img src="' + $svgUri + '" style="width:' + $j.w + 'px;display:block"></body></html>'
    $tmpHtml = Join-Path $env:TEMP ($j.out + '.html')
    [System.IO.File]::WriteAllText($tmpHtml, $html, [System.Text.Encoding]::UTF8)
    $tmpPng = Join-Path $env:TEMP ($j.out + '.png')
    & $browser --headless --disable-gpu --no-sandbox --hide-scrollbars --screenshot="$tmpPng" --window-size="$($j.w),$($j.h)" --virtual-time-budget=8000 ('file:///' + ($tmpHtml -replace '\\', '/')) 2>$null | Out-Null
    Start-Sleep -Seconds 1
    if (-not (Test-Path $tmpPng)) { Write-Output "FALLO: $($j.svg)"; continue }
    $img = [System.Drawing.Image]::FromFile($tmpPng)
    $out = Join-Path $dst ($j.out + '.jpg')
    $bmp = New-Object System.Drawing.Bitmap($img.Width, $img.Height)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.Clear([System.Drawing.Color]::White)
    $g.DrawImage($img, 0, 0, $img.Width, $img.Height)
    $bmp.Save($out, $enc, $params)
    $g.Dispose(); $bmp.Dispose(); $img.Dispose()
    Remove-Item $tmpPng, $tmpHtml -Force
    Write-Output ("OK: {0} -> {1} ({2} KB)" -f $j.svg, (Split-Path $out -Leaf), [math]::Round((Get-Item $out).Length / 1KB))
}
