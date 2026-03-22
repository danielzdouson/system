# Backup System - Quick Start Guide

## Installation (One-Time Setup)

### Step 1: Install AWS SDK
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Step 2: Configure AWS S3 in .env
```env
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sacco-system-backups
```

### Step 3: Create Backup Directory
```bash
mkdir -p storage/app/backups
chmod 755 storage/app/backups
```

### Step 4: Test Backup
```bash
php artisan backup:create
```

## Daily Usage

### Access Backup Dashboard
Navigate to: `/admin/backups`

### Create Manual Backup
- Click **"Create Backup Now"** button
- Wait for completion notification
- Backup saved to local + AWS S3

### Download Backup
- Click download icon (📥) next to any backup
- ZIP file downloads to your computer

### Restore from Backup
1. Click restore icon (↩️) next to backup
2. Type `RESTORE` to confirm
3. Wait for completion
4. System automatically backed up before restore

### Delete Backup
- Click delete icon (🗑️) next to backup
- Confirm deletion
- Removed from local + S3

## Automated Backups

- **Schedule**: Every Sunday at 2:00 AM
- **No action required**
- **Ensure cron is running**:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## CLI Commands

```bash
# Create backup
php artisan backup:create

# List scheduled tasks
php artisan schedule:list

# Test schedule manually
php artisan schedule:run
```

## What Gets Backed Up

✅ Complete MySQL database  
✅ All uploaded documents  
✅ All uploaded forms  
✅ Compressed into ZIP file  
✅ Stored locally + AWS S3  

## Backup Locations

- **Local**: `storage/app/backups/`
- **Cloud**: AWS S3 bucket `/backups/` folder

## Emergency Recovery

If system crashes:
1. Fresh install from Git
2. Configure `.env`
3. Go to `/admin/backups`
4. Download latest backup
5. Click **Restore**
6. System recovered!

## Support

- Full documentation: `BACKUP_SYSTEM_SETUP.md`
- Check logs: `storage/logs/laravel.log`
- Test S3 connection: See troubleshooting in full docs

## Security Notes

⚠️ Never commit `.env` to Git  
⚠️ Keep AWS credentials secure  
⚠️ S3 bucket is private (not public)  
⚠️ Test restores regularly  
