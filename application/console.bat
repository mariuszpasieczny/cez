REM @echo off

set PATH=%PATH%;D:\WebServ\php
C:\Windows\System32\cmd.exe
d:
cd "D:\WebServ\httpd\cez\centralny\application"
php.exe console.php -l admin@localhost.pl -p @dm!n -u -f -v -o ../data/logs/console.log

pause