Set WshShell = CreateObject("WScript.Shell")
WshShell.Run "powershell -ExecutionPolicy Bypass -WindowStyle Hidden -File ""c:\Users\HP\Documents\GitHub\laravel-emprecord\auto-deploy.ps1""", 0, False
