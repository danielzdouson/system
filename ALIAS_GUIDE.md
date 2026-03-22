# SACCO System Quick Aliases

## One-Time Setup

### For WSL/Linux (Recommended)
```bash
cd /home/danielz/projects/system
chmod +x setup-aliases.sh
./setup-aliases.sh
source ~/.bashrc
```

### For Windows PowerShell
```powershell
# Run PowerShell as Administrator
cd "\\wsl$\Ubuntu\home\danielz\projects\system"
.\setup-powershell-aliases.ps1
. $PROFILE
```

## Available Commands

| Command | What it does | When to use |
|---------|--------------|-------------|
| `sacco-start` | Starts system with permission fixes | After PC restart |
| `sacco-stop` | Stops all containers | When you're done working |
| `sacco-restart` | Stop and start system | When having issues |
| `sacco-status` | Shows container status | To check if running |
| `sacco-logs` | Shows recent error logs | When debugging |
| `sacco-test` | Tests if system works | Quick health check |
| `sacco` or `sacco-dir` | Go to project directory | Navigation |

## Daily Usage

### After PC Restart:
```bash
sacco-start
```

### Check if everything is working:
```bash
sacco-status
sacco-test
```

### When you're done:
```bash
sacco-stop
```

### If you get 500 errors:
```bash
sacco-restart
```

## Examples

```bash
# Start working
sacco-start

# Check status
sacco-status

# Something wrong? Check logs
sacco-logs

# Still issues? Restart
sacco-restart

# Done for the day
sacco-stop
```

**That's it! No more long docker commands!** 🎉
