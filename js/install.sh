BASEDIR=$(pwd)
apt-get -y update && apt-get -y install apt-utils curl python3 build-essential
curl -sL https://deb.nodesource.com/setup_10.x | /bin/bash > /dev/null 2>&1
apt-get -y update && apt-get -y install nodejs
cd $BASEDIR/files && npm install > /dev/null 2>&1
cd $BASEDIR/files/static/vendor/ && python3 initDB.py
chmod 777 $BASEDIR/files/static/vendor/db.sqlite3