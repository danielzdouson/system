# SACO System Docker Auto-Start Setup Instructions

## Problem Fixed
Docker containers weren't starting automatically after PC restart, requiring manual `docker-compose up -d` each time.

## Solutions Implemented

### 1. **Docker Compose Update** ✅
- Added `restart: always` to PHP container (database already had it)
- Both containers now automatically restart with Docker daemon

### 2. **Startup Scripts** ✅

#### For Linux/WSL:
- `start-docker.sh` - Bash script for reliable container startup
- Includes Docker daemon check and container status verification

#### For Windows:
- `start-system.bat` - Double-click to start system from Windows
- Automatically calls WSL script

### 3. **Systemd Service** (Optional) ✅
- `saco-system.service` - Linux service for automatic boot startup
- Integrates with system startup process

## Usage Instructions

### Quick Start (Recommended):
1. **Windows**: Double-click `start-system.bat`
2. **Linux/WSL**: Run `./start-docker.sh`

### Permanent Auto-Start Setup:
```bash
# Copy service file and enable
sudo cp saco-system.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable saco-system.service
sudo systemctl start saco-system.service
```

### Manual Commands:
```bash
# Standard start
docker-compose up -d

# Check status
docker-compose ps

# Stop containers
docker-compose down
```

## Access Points
- **Application**: http://localhost:8080
- **Database**: localhost:3306

## Benefits
✅ No more manual container startup after PC restart
✅ Automatic recovery from crashes
✅ Cross-platform compatibility
✅ System integration option available
✅ Easy one-click startup scripts

## Troubleshooting
If containers don't start:
1. Run `start-docker.sh` manually
2. Check Docker daemon: `sudo systemctl status docker`
3. View logs: `docker-compose logs`
4. Ensure ports 8080 and 3306 are available
