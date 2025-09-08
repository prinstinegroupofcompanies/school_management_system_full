@echo off
set "PHP_PATH=C:\Users\DELL\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "PATH=%PHP_PATH%;%PATH%"
"C:\ProgramData\ComposerSetup\bin\composer.bat" %*
