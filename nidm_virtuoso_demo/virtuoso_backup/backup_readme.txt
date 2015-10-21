sudo su
cd /var/lib/virtuoso-opensource-7/db
isql
backup_context_clear();
checkpoint;
backup_online ('virtuoso-inc_dump_#',150);
mv virtuoso-inc_dump_# /vagrant/virtuoso_backup
cd /vagrant/virtuoso_backup
