@echo off
setlocal enabledelayedexpansion
chcp 65001 >nul

:: =====================================================================
:: build_mpy.bat — Compila moduli .py in .mpy per ESP32
::
:: Uso:
::   build_mpy.bat              (OptLevel 2, output in build\mpy)
::   build_mpy.bat 0            (OptLevel 0 = debug, conserva traceback)
::   build_mpy.bat 2 COM3       (build + upload automatico su ESP32)
::   build_mpy.bat 2 "" D:\out  (build in cartella custom)
:: =====================================================================

:: ── Parametri ─────────────────────────────────────────────────────────
set OPT_LEVEL=2
set PORT=
set OUTPUT_DIR=

if not "%~1"=="" set OPT_LEVEL=%~1
if not "%~2"=="" set PORT=%~2
if not "%~3"=="" set OUTPUT_DIR=%~3

:: ── Percorsi ──────────────────────────────────────────────────────────
:: %~dp0 = cartella dello script (scripts\)
:: PROJECT_DIR = un livello sopra
set SCRIPT_DIR=%~dp0
for %%i in ("%SCRIPT_DIR%..") do set PROJECT_DIR=%%~fi

if "%OUTPUT_DIR%"=="" set OUTPUT_DIR=%PROJECT_DIR%\build\mpy

:: Cartelle da compilare (relative alla radice progetto)
set MODULE_DIRS=Helper Icons Manager Menu Modules Resource

:: File che restano .py invariati
set KEEP_AS_PY=boot.py main.py secrets.py

:: Contatori
set /a COMPILED=0
set /a ERRORS=0
set /a SKIPPED=0

echo.
echo =====================================================
echo  PyTank — build .mpy
echo =====================================================
echo  Progetto : %PROJECT_DIR%
echo  Output   : %OUTPUT_DIR%
echo  OptLevel : -O%OPT_LEVEL%
if not "%PORT%"=="" echo  Upload   : %PORT%
echo =====================================================

:: ── 1. Verifica mpy-cross ─────────────────────────────────────────────
echo.
echo =^> Verifica mpy-cross...

where mpy-cross >nul 2>&1
if errorlevel 1 (
    echo   WARN mpy-cross non trovato — installazione in corso...
    pip install mpy-cross --quiet
    if errorlevel 1 (
        echo   ERR  Installazione fallita.
        echo        Esegui manualmente: pip install mpy-cross
        pause
        exit /b 1
    )
)

for /f "tokens=*" %%v in ('mpy-cross --version 2^>^&1') do (
    echo   OK   %%v
)

:: ── 2. Pulizia output precedente ──────────────────────────────────────
echo.
echo =^> Pulizia output precedente...

if exist "%OUTPUT_DIR%" (
    rmdir /s /q "%OUTPUT_DIR%"
    echo   OK   Rimossa: %OUTPUT_DIR%
)
mkdir "%OUTPUT_DIR%"
echo   OK   Creata : %OUTPUT_DIR%

:: ── 3. Compilazione .py → .mpy ────────────────────────────────────────
echo.
echo =^> Compilazione moduli...

for %%d in (%MODULE_DIRS%) do (
    set SRC_DIR=%PROJECT_DIR%\%%d
    echo [DEBUG] SRC_DIR = !SRC_DIR!
    if not exist "!SRC_DIR!" (
        echo   WARN Directory non trovata: %%d — saltata
        set /a SKIPPED+=1
    ) else (
        if exist "!SRC_DIR!\*.py" (
            echo [DEBUG] File .py trovati in !SRC_DIR!
        ) else (
            echo [DEBUG] Nessun file .py in !SRC_DIR!
        )
        
        :: Debug: mostra il valore effettivo di SRC_DIR
        echo [DEBUG] Ricerca file .py ricorsiva con forfiles in !SRC_DIR!
        set TMP_PYLIST=%TEMP%\mpy_pylist.txt
        if exist !TMP_PYLIST! del /f /q !TMP_PYLIST!
        forfiles /p "!SRC_DIR!" /s /m *.py /c "cmd /c echo @path" > !TMP_PYLIST!
        set FOUND=0
            for /f "usebackq delims=" %%f in (!TMP_PYLIST!) do (
                set FOUND=1
                setlocal enabledelayedexpansion
                set "FILE=%%~f"
                set "REL=!FILE:%PROJECT_DIR%\=!"
                set "OUT_REL=!REL:.py=.mpy!"
                set "OUT_PATH=%OUTPUT_DIR%\!OUT_REL!"
                echo [DEBUG] Trovato file: !FILE!
                echo !REL!
                for %%o in ("!OUT_PATH!") do (
                    if not exist "%%~dpo" mkdir "%%~dpo"
                )
                mpy-cross -O%OPT_LEVEL% -march=xtensa -o "!OUT_PATH!" "!FILE!" 2>"%TEMP%\mpy_error.log"
                if !errorlevel!==0 (
                    echo   OK   !REL!
                    endlocal
                    set /a COMPILED+=1
                ) else (
                    echo   ERR  !REL!
                    type "%TEMP%\mpy_error.log"
                    endlocal
                    set /a ERRORS+=1
                )
            )
        if !FOUND! == 0 echo [DEBUG] Nessun file .py trovato ricorsivamente in !SRC_DIR!
        if exist !TMP_PYLIST! del /f /q !TMP_PYLIST!
        echo [DEBUG] Fine ciclo in !SRC_DIR!
    )
)

:: ── 4. Copia file che restano .py ─────────────────────────────────────
echo.
echo =^> Copia file .py invariati...

for %%f in (%KEEP_AS_PY%) do (
    if exist "%PROJECT_DIR%\%%f" (
        copy /y "%PROJECT_DIR%\%%f" "%OUTPUT_DIR%\%%f" >nul
        echo   OK   %%f copiato come .py
    )
)

:: ── 5. Upload automatico (opzionale) ──────────────────────────────────
if not "%PORT%"=="" (
    echo.
    echo =^> Upload su ESP32 ^(%PORT%^)...

    where mpremote >nul 2>&1
    if errorlevel 1 (
        echo   WARN mpremote non trovato — installazione in corso...
        pip install mpremote --quiet
    )

    for /r "%OUTPUT_DIR%" %%f in (*) do (
        set FULL=%%f
        set REL_WIN=!FULL:%OUTPUT_DIR%\=!
        set REL_UNIX=!REL_WIN:\=/!

        :: Crea cartella remota se necessario
        for %%o in ("%%f") do (
            set REMOTE_DIR=%%~dpo
            set REMOTE_DIR=!REMOTE_DIR:%OUTPUT_DIR%\=!
            set REMOTE_DIR=!REMOTE_DIR:\=/!
        )

        mpremote connect %PORT% cp "%%f" ":!REL_UNIX!" >nul 2>&1
        if !errorlevel!==0 (
            echo   OK   !REL_UNIX!
        ) else (
            echo   ERR  Upload fallito: !REL_UNIX!
        )
    )

    echo   OK   Riavvio ESP32...
    mpremote connect %PORT% reset >nul 2>&1
)

:: ── 6. Riepilogo finale ───────────────────────────────────────────────
echo.
echo =====================================================
if %ERRORS%==0 (
    echo  COMPLETATO: %COMPILED% moduli compilati in .mpy
) else (
    echo  COMPLETATO CON ERRORI: %COMPILED% OK, %ERRORS% falliti
)
echo  Output: %OUTPUT_DIR%
echo =====================================================
echo.
echo Prossimi passi:
echo   1. Carica il contenuto di build\mpy\ sull'ESP32
echo      con Thonny, Pymakr oppure:
echo      build_mpy.bat 2 COM3
echo   2. NON caricare .py e .mpy con lo stesso nome
echo   3. Per debug usa: build_mpy.bat 0
echo.

if %ERRORS% gtr 0 (
    echo ATTENZIONE: %ERRORS% file non compilati — controlla i messaggi sopra
    echo.
)

pause
endlocal