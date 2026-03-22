# How to Auto-Start SACCO System on Windows Boot

## Method 1: Manual Start (Recommended)
After restarting your PC, simply double-click: `start-system.bat`

## Method 2: Windows Startup (Automatic)
1. Press `Win + R`, type `shell:startup`, press Enter
2. Right-click in the folder → New → Shortcut
3. Location: `"\\wsl$\Ubuntu\home\danielz\projects\system\start-system.bat"`
4. Name: `SACCO System`
5. Click Finish

## Method 3: Task Scheduler (More Reliable)
1. Open Task Scheduler
2. Create Basic Task → Name: "SACCO System Startup"
3. Trigger: "When I log on"
4. Action: "Start a program"
5. Program: `"\\wsl$\Ubuntu\home\danielz\projects\system\start-system.bat"`
6. Finish

## What the Script Does:
- ✅ Stops old containers
- ✅ Starts fresh containers  
- ✅ Fixes file permissions (prevents 500 errors)
- ✅ Waits for full startup
- ✅ Tests if system is working
- ✅ Shows success/failure status

## If You Still Get 500 Errors:
Run these commands manually:
```bash
cd \\wsl$\Ubuntu\home\danielz\projects\system
docker-compose down
docker-compose up -d --force-recreate
docker exec saco_system_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec saco_system_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```
