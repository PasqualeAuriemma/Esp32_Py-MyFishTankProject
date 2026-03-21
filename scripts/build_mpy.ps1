<#
.SYNOPSIS
    Compila tutti i moduli del progetto da .py a .mpy usando mpy-cross.

.DESCRIPTION
    Compila i moduli Python in bytecode MicroPython pre-compilato.
    Vantaggi rispetto ai .py plain:
      - Elimina il costo del parser all'import (~10-15 KB RAM risparmiati)
      - File piu piccoli da trasferire sul device
      - Con -O2: rimuove assert e docstring (risparmio RAM extra)

    Dopo l'esecuzione:
      - Carica il contenuto di build\mpy\ sull'ESP32
      - NON caricare .py e .mpy con lo stesso nome sullo stesso device
      - boot.py e main.py vengono copiati invariati (MicroPython li cerca sempre come .py)

    Prerequisiti:
        pip install mpy-cross

.PARAMETER OptLevel
    Livello di ottimizzazione (0-3).
    0 = debug (conserva numeri di riga nei traceback)
    2 = produzione (rimuove assert e docstring) — default

.PARAMETER OutputDir
    Directory di output. Default: build\mpy nella radice del progetto.

.PARAMETER Port
    Porta seriale ESP32 per upload automatico (es. COM3, /dev/ttyUSB0).
    Se omessa, non viene fatto l'upload.

.EXAMPLE
    .\build_mpy.ps1
    .\build_mpy.ps1 -OptLevel 0
    .\build_mpy.ps1 -OutputDir D:\deploy
    .\build_mpy.ps1 -Port COM3
#>

[CmdletBinding()]
param(
    [ValidateRange(0, 3)]
    [int]    $OptLevel  = 2,
    [string] $OutputDir = "",
    [string] $Port      = ""
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

# ── Helpers ────────────────────────────────────────────────────────────
function Write-Step  { param($msg) Write-Host "`n==> $msg" -ForegroundColor Cyan }
function Write-Ok    { param($msg) Write-Host "  OK   $msg" -ForegroundColor Green }
function Write-Warn  { param($msg) Write-Host "  WARN $msg" -ForegroundColor Yellow }
function Write-Fail  { param($msg) Write-Host "  ERR  $msg" -ForegroundColor Red }
function Write-Fatal { param($msg) Write-Fail $msg; exit 1 }

# ── Percorsi ───────────────────────────────────────────────────────────
# PSScriptRoot e' la cartella dello script (es. scripts\)
# ProjectDir e' la radice del progetto (un livello su)
$ScriptDir  = $PSScriptRoot
if (-not $ScriptDir) {
    # Fallback se lo script viene eseguito con . .\build_mpy.ps1
    $ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
}
$ProjectDir = Split-Path -Parent $ScriptDir

if ($OutputDir -eq "") {
    $OutputDir = Join-Path $ProjectDir "build\mpy"
}

Write-Host ""
Write-Host "PyTank — build .mpy" -ForegroundColor White
Write-Host "Progetto : $ProjectDir"
Write-Host "Output   : $OutputDir"
Write-Host "OptLevel : -O$OptLevel"

# ── 1. Verifica mpy-cross ──────────────────────────────────────────────
Write-Step "Verifica mpy-cross"

$mpyCross = Get-Command mpy-cross -ErrorAction SilentlyContinue
if (-not $mpyCross) {
    Write-Warn "mpy-cross non trovato — installazione in corso..."
    & pip install mpy-cross --quiet
    if ($LASTEXITCODE -ne 0) {
        Write-Fatal "Installazione fallita. Esegui manualmente: pip install mpy-cross"
    }
    $mpyCross = Get-Command mpy-cross -ErrorAction SilentlyContinue
    if (-not $mpyCross) {
        Write-Fatal "mpy-cross ancora non trovato dopo l'installazione. Controlla il PATH."
    }
}

# Leggi la versione — mpy-cross stampa su stderr, cattura entrambi
$mpyVerRaw = & mpy-cross --version 2>&1
$mpyVer    = ($mpyVerRaw | Out-String).Trim()
Write-Ok $mpyVer

# ── 2. Configurazione ──────────────────────────────────────────────────

# Cartelle dei moduli da compilare (percorsi relativi alla radice progetto)
# Adatta questo elenco alla struttura del tuo progetto
$ModuleDirs = @(
    "helper",
    "icons",
    "manager",
    "menu",
    "modules",
    "resource"
)

# File che devono restare .py — MicroPython li cerca sempre con questa estensione
$KeepAsPy = @("boot.py", "main.py", "secrets.py")

# ── 3. Pulizia output precedente ───────────────────────────────────────
Write-Step "Pulizia output precedente"
if (Test-Path $OutputDir) {
    Remove-Item -Path $OutputDir -Recurse -Force
    Write-Ok "Rimossa: $OutputDir"
}
New-Item -ItemType Directory -Force -Path $OutputDir | Out-Null
Write-Ok "Creata:  $OutputDir"

# ── 4. Compilazione .py → .mpy ────────────────────────────────────────
Write-Step "Compilazione moduli"

$compiled = 0
$skipped  = 0
$errors   = @()
$report   = @()

foreach ($dir in $ModuleDirs) {
    $srcDir = Join-Path $ProjectDir $dir
    if (-not (Test-Path $srcDir)) {
        Write-Warn "Directory non trovata: $dir — saltata"
        $skipped++
        continue
    }

    $pyFiles = Get-ChildItem -Path $srcDir -Filter "*.py" -Recurse -File
    if ($pyFiles.Count -eq 0) {
        Write-Warn "Nessun .py in: $dir — saltata"
        continue
    }

    foreach ($pyFile in $pyFiles) {
        # Percorso relativo rispetto alla radice progetto
        $relative = $pyFile.FullName.Substring($ProjectDir.Length).TrimStart([char]'\', [char]'/')

        # Percorso di output con estensione .mpy
        $outRelative = $relative -replace '\.py$', '.mpy'
        $outPath     = Join-Path $OutputDir $outRelative
        $outSubDir   = Split-Path $outPath -Parent

        # Crea la sottocartella di destinazione se non esiste
        if (-not (Test-Path $outSubDir)) {
            New-Item -ItemType Directory -Force -Path $outSubDir | Out-Null
        }

        # Compila
        # -march=xtensalx106 = ESP32 classico (Xtensa LX6)
        # Usa xtensalx7 per ESP32-S2/S3
        $compileOutput = & mpy-cross "-O$OptLevel" -march=xtensalx106 -o $outPath $pyFile.FullName 2>&1

        if ($LASTEXITCODE -eq 0) {
            $sizeSrc = $pyFile.Length
            $sizeDst = (Get-Item $outPath).Length
            $saving  = [math]::Round((1 - $sizeDst / [math]::Max($sizeSrc, 1)) * 100)
            $report += [PSCustomObject]@{
                File    = $relative
                SrcKB   = [math]::Round($sizeSrc / 1KB, 1)
                DstKB   = [math]::Round($sizeDst / 1KB, 1)
                Saving  = "$saving%"
            }
            $compiled++
        } else {
            $errMsg = ($compileOutput | Out-String).Trim()
            $errors += [PSCustomObject]@{ File = $relative; Error = $errMsg }
            Write-Fail "ERRORE: $relative"
            if ($errMsg) { Write-Host "         $errMsg" -ForegroundColor DarkRed }
        }
    }
}

# ── 5. Copia file che restano .py ──────────────────────────────────────
Write-Step "Copia file .py invariati"

foreach ($name in $KeepAsPy) {
    $src = Join-Path $ProjectDir $name
    if (Test-Path $src) {
        $dst = Join-Path $OutputDir $name
        Copy-Item $src $dst -Force
        Write-Ok "$name → copiato come .py"
    }
}

# ── 6. Tabella riepilogo compilazione ─────────────────────────────────
if ($report.Count -gt 0) {
    Write-Step "Riepilogo compilazione"
    $report | Format-Table -AutoSize -Property File, SrcKB, DstKB, Saving
}

# ── 7. Upload automatico (opzionale) ──────────────────────────────────
if ($Port -ne "") {
    Write-Step "Upload su ESP32 ($Port)"

    $mpremote = Get-Command mpremote -ErrorAction SilentlyContinue
    if (-not $mpremote) {
        Write-Warn "mpremote non trovato — installazione in corso..."
        & pip install mpremote --quiet
    }

    # Carica ricorsivamente tutto il contenuto di build\mpy\
    $allFiles = Get-ChildItem -Path $OutputDir -Recurse -File
    foreach ($f in $allFiles) {
        $rel     = $f.FullName.Substring($OutputDir.Length).TrimStart([char]'\', [char]'/')
        $relUnix = $rel -replace '\\', '/'

        # Crea cartella remota se necessario
        $remoteDir = Split-Path $relUnix -Parent
        if ($remoteDir -and $remoteDir -ne ".") {
            & mpremote connect $Port mkdir ":$remoteDir" 2>$null
        }

        # Upload file
        & mpremote connect $Port cp $f.FullName ":$relUnix"
        if ($LASTEXITCODE -eq 0) {
            Write-Ok $relUnix
        } else {
            Write-Fail "Upload fallito: $relUnix"
        }
    }

    Write-Ok "Upload completato — riavvio ESP32..."
    & mpremote connect $Port reset
}

# ── 8. Riepilogo finale ────────────────────────────────────────────────
Write-Host ""
Write-Host "=====================================================" -ForegroundColor White

if ($errors.Count -eq 0) {
    Write-Host "  COMPLETATO: $compiled moduli compilati" -ForegroundColor Green
} else {
    Write-Host "  COMPLETATO CON ERRORI: $compiled OK, $($errors.Count) falliti" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  File con errori:" -ForegroundColor Red
    foreach ($e in $errors) {
        Write-Host "    - $($e.File)" -ForegroundColor Red
        if ($e.Error) {
            Write-Host "      $($e.Error)" -ForegroundColor DarkRed
        }
    }
}

Write-Host "  Output: $OutputDir" -ForegroundColor White
Write-Host "=====================================================" -ForegroundColor White
Write-Host ""
Write-Host "Prossimi passi:" -ForegroundColor Cyan
Write-Host "  1. Carica il contenuto di build\mpy\ sull'ESP32" -ForegroundColor White
Write-Host "     (con Pymakr, Thonny, o: .\build_mpy.ps1 -Port COM3)" -ForegroundColor White
Write-Host "  2. NON caricare .py e .mpy con lo stesso nome" -ForegroundColor White
Write-Host "  3. Per debug usa -OptLevel 0 (conserva i numeri di riga)" -ForegroundColor White
Write-Host ""