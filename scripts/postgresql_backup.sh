#!/bin/bash

# Backup destination directory
BACKUP_DIR="/var/backups/postgresql"

# PostgreSQL database connection options
DB_USER="dhis"
DB_NAME="crims_live"

# Backup filename format
DATE=`date +%Y-%m-%d_%H-%M-%S`
BACKUP_FILE="$BACKUP_DIR/$DB_NAME-$DATE.sql.gz"

# Create backup directory if it doesn't exist
if [ ! -d "$BACKUP_DIR" ]; then
  sudo mkdir -p "$BACKUP_DIR"
  sudo chown postgres:postgres "$BACKUP_DIR"
fi

# Backup database
sudo -u postgres pg_dump -Fc "$DB_NAME" | gzip > "$BACKUP_FILE"

# Set permissions on backup file
sudo chown postgres:postgres "$BACKUP_FILE"

# Delete old backups (keep last 7 days)
find "$BACKUP_DIR" -type f -name "*.gz" -mtime +7 -delete

# Log backup activity
echo "PostgreSQL backup created: $BACKUP_FILE" >> /var/log/postgresql-backup.log
