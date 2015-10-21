# NIDM - Virtuoso Demo

This is a demo of a PHP web application to access a variety of neuroimaging related
data using NIDM representations and the Virtuoso database.  Currently the demo is in
an alpha state, much cleanup of extra files needs to be completed along with creating a
proper vagrant box for general use.  Below are some installation instructions which 
will probably work if you want to get it running right now. 

# Demo Installation
- Install VirtualBox(https://www.virtualbox.org/wiki/Downloads) 
- Install Vagrant (https://www.vagrantup.com/)
- Download this demo from GitHub
- Change directories to the location where you downloaded the demo
- Type "sudo vagrant up"
  - This should download the Ubuntu/Virtuoso base box, PHP, and Apache2 and install them
  - into the vagrant VM.
- Once the vagrant box has been built and is up, you need to:

  - Restore virtuoso database:
    - Type "sudo vagrant ssh"
    - Type "sudo su"
    - Type "cd /var/lib/virtuoso-opensource-7/db"
    - Type "cp /vagrant/virtuoso-backup/virtuoso_backup_full* ."
    - Type "isql"
    - Type "shutdown();"
    - Quit isql
    - Type "rm virtuoso.db"
    - Type "virtuoso-t +restore-backup virtuoso_backup_full"

  - Start the virtuoso database: 
    - Type "sudo vagrant ssh"
    - Type "sudo su"
    - Type "cd /var/lib/virtuoso-opensource-7/db"
    - Type "virtuoso-t -f &"

# To start demo 
Note, demo should be running after following installation instructions above

- Type "sudo vagrant up"
- Type "sudo vagrant ssh"
- Type "sudo su"
- Type "cd /var/lib/virtuoso-opensource-7/db"
- Type "virtuoso-t -f &"

- From host machine point browser to:

  -Web application: http://127.0.0.1:4567
  
  -Virtuoso Conductor: http://127.0.0.1:8890/conductor/
    - user: dba
    - pw: dba

# Adding / Changing Application

See "Readme.txt" in demo folder to understand how to change and/or add queries the application
