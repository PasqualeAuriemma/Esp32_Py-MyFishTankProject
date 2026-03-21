<#
.SYNOPSIS
    Compila tutti i moduli PyTank da .py a .mpy usando mpy-cross.

.DESCRIPTION
    Alternativa immediata ai frozen modules nel firmware: non richiede di
    ricompilare il firmware né ESP-IDF.  Funziona con Pymakr.

    Differenza rispetto ai frozen modules veri:
      - Frozen firmware : codice in flash via XIP, 0 RAM heap usata
      - .mpy pre-compilato: bytecode già pronto, elimina il costo del parser
        (~10-15 KB RAM risparmiati), il bytecode viene comunque copiato in RAM

    Dopo l'esecuzione:
      - Carica i file .mpy sull'ESP32 con Pymakr (punta il progetto su build\mpy)
      - NON caricare i .py corrispondenti (MicroPython preferisce .mpy)
      - I file boot.py, main.py, secrets.py vengono copiati invariati

    Prerequisiti:
        pip install mpy-cross

.PARAMETER OptLevel
    Livello di ottimizzazione mpy-cross (0-3).
    Default: 2  — rimuove assert e docstring (max risparmio RAM su ESP32)
    Usa 0 per debug (conserva numeri di riga per i traceback)

.PARAMETER OutputDir
    Directory dove scrivere i file .mpy.
    Default: build\mpy nella radice del progetto

.EXAMPLE
    .\scripts\build_mpy.ps1
    .\scripts\build_mpy.ps1 -OptLevel 0
    .\scripts\build_mpy.ps1 -OutputDir D:\deploy
#>

[CmdletBinding()]
param(
    [int]   $OptLevel  = 2,
    [string]$OutputDir = ""
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Write-Step  { param($msg) Write-Host "==> $msg" -ForegroundColor Cyan }
function Write-Ok    { param($msg) Write-Host " OK  $msg" -ForegroundColor Green }
function Write-Warn  { param($msg) Write-Host "WARN $msg" -ForegroundColor Yellow }
function Write-Fatal { param($msg) Write-Host "ERR  $msg" -ForegroundColor Red; exit 1 }

# ── Percorsi base ─────────────────────────────────────────────────────
$ProjectDir = (Get-Item (Join-Path $PSScriptRoot "..")).FullName
if ($OutputDir -eq "") { $OutputDir = Join-Path $ProjectDir "build\mpy" }

Write-Step "Progetto : $ProjectDir"
Write-Step "Output   : $OutputDir"
Write-Step "OptLevel : -O$OptLevel"

# ────────────────────────────────────────────────────────────────────
# 1. VERIFICA mpy-cross
# ────────────────────────────────────────────────────────────────────
Write-Step "Verifica mpy-cross"

if (-not (Get-Command mpy-cross -ErrorAction SilentlyContinue)) {
    Write-Warn "mpy-cross non trovato — installazione in corso..."
    pip install mpy-cross --quiet
    if ($LASTEXITCODE -ne 0) {
        Write-Fatal "Installazione mpy-cross fallita. Eseguire manualmente: pip install mpy-cross"
    }
}
$mpyVer = mpy-cross --version 2>&1
Write-Ok "mpy-cross: $mpyVer"

# ────────────────────────────────────────────────────────────────────
# 2. CONFIGURAZIONE
# ────────────────────────────────────────────────────────────────────

# Directory contenenti pure librerie da compilare
$ModuleDirs = @("Helper", "Icons", "Manager", "Menu", "Modules", "Resource")

# File che devono restare .py invariati (non compilare mai)
$SkipFiles  = @("boot.py", "main.py", "secrets.py")

# ────────────────────────────────────────────────────────────────────
# 3. COMPILAZIONE .py → .mpy
# ────────────────────────────────────────────────────────────────────
Write-Step "Compilazione moduli"

$ok     = 0
$errors = @()

foreach ($dir in $ModuleDirs) {
    $srcDir = Join-Path $ProjectDir $dir
    if (-not (Test-Path $srcDir)) {
        Write-Warn "Directory non trovata: $dir — saltata"
        continue
    }

    foreach ($pyFile in (Get-ChildItem -Path $srcDir -Filter "*.py" -Recurse)) {
        # Ricostruisci il percorso relativo per mantenere la struttura di directory
        $relative  = $pyFile.FullName.Substring($ProjectDir.Length).TrimStart('\', '/')
        $outPath   = Join-Path $OutputDir ($relative -replace '\.py$', '.mpy')
        $outSubDir = Split-Path $outPath -Parent

        New-Item -ItemType Directory -Force -Path $outSubDir | Out-Null

        # Compila — target Xtensa LX6 (ESP32 classico)
        # Usa xtensalx106 per ESP32 standard, xtensalx7 per ESP32-S2/S3
        mpy-cross "-O$OptLevel" -march=xtensalx106 -o $outPath $pyFile.FullName 2>&1
        if ($LASTEXITCODE -eq 0) {
            $sizeSrc = [math]::Round($pyFile.Length / 1KB, 1)
            $sizeDst = [math]::Round((Get-Item $outPath).Length / 1KB, 1)
            Write-Host ("  {0,-52} {1,5} KB  ->  {2,5} KB" -f $relative, $sizeSrc, $sizeDst)
            $ok++
        } else {
            $errors += $relative
            Write-Warn "ERRORE: $relative"
        }
    }
}

# ────────────────────────────────────────────────────────────────────
# 4. COPIA FILE CHE RESTANO .py
# ────────────────────────────────────────────────────────────────────
foreach ($name in $SkipFiles) {
    $src = Join-Path $ProjectDir $name
    if (Test-Path $src) {
        Copy-Item $src (Join-Path $OutputDir $name) -Force
        Write-Host ("  {0,-52} (copiato come .py)" -f $name) -ForegroundColor DarkGray
    }
}

# ────────────────────────────────────────────────────────────────────
# 5. RIEPILOGO
# ────────────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "=====================================================" -ForegroundColor White
if ($errors.Count -eq 0) {
    Write-Host "  COMPLETATO: $ok moduli compilati in .mpy" -ForegroundColor Green
} else {
    Write-Host "  COMPLETATO CON ERRORI: $ok OK, $($errors.Count) falliti" -ForegroundColor Yellow
    $errors | ForEach-Object { Write-Host "    - $_" -ForegroundColor Red }
}
Write-Host "  Output: $OutputDir" -ForegroundColor White
Write-Host "=====================================================" -ForegroundColor White
Write-Host ""
Write-Host "Prossimi passi:" -ForegroundColor Cyan
Write-Host "  Punta Pymakr su questa cartella: $OutputDir" -ForegroundColor White
Write-Host "  Caricare sull'ESP32 SOLO il contenuto di build\mpy\" -ForegroundColor White
Write-Host ""
Write-Warn "NON caricare .py e .mpy con lo stesso nome: MicroPython usa .mpy e ignora .py"
