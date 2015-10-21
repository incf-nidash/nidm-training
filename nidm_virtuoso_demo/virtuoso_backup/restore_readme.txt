sudo su
cd /var/lib/virtuoso-opensource-7/db
cp /vagrant/virtuoso-backup/virtuoso_backup_full* .
isql
shutdown();
rm virtuoso.db
virtuoso-t +restore-backup virtuoso_backup_full
virtuoso-t -f &
