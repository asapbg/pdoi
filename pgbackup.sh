#!/bin/bash

#  begin config
APP_DIR=".";
DAYS_TO_KEEP=14;
# end config
source "${APP_DIR}/.env"
DATE=$(date '+%Y-%m-%d_%s');
DIR="${APP_DIR}/sqlbackups";
if [ ! -d $DIR ]
then
 mkdir -p $DIR;
fi
BACKUP_FILE="${DIR}/database_backup_${DATE}.sql";
# end config..
echo "Begin backup.."
export PGPASSWORD=$DB_PASSWORD;
/usr/bin/pg_dump -h$DB_HOST -U $DB_USERNAME $DB_DATABASE -N topology -T spatial_ref_sys > $BACKUP_FILE
echo "End backup.."
echo "Begin clean up.."
/usr/bin/find $DIR -type f -mtime +${DAYS_TO_KEEP} -name '*.sql' -print0 | xargs -r0 rm -
echo "End clean up"
echo "DONE.";