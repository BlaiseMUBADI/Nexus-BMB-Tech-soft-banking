@echo off
cd /d "C:\wamp64\www\Nexus-BMB-Tech-soft-banking"
"C:\wamp64\bin\php\php8.3.28\php.exe" artisan schedule:run >> "C:\wamp64\www\Nexus-BMB-Tech-soft-banking\storage\logs\scheduler.log" 2>&1
