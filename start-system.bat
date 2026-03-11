@echo off
echo Starting SACO System containers...
cd /d %~dp0
wsl -d Ubuntu bash -c "cd /home/danielz/projects/system && ./start-docker.sh"
echo Containers started! Application available at http://localhost:8080
pause
