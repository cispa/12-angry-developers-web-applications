BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )
apt-get -y update && apt-get -y install python python3 nginx php-fpm php-sqlite3 gnupg php-pear php-dev composer
cd $BASEDIR/files && php composer.json install > /dev/null
cd $BASEDIR/files/static/vendor/ && python3 initDB.py
mv /var/www/html /var/www/html.old
cp -r $BASEDIR/files/* /var/www/html/
mv /etc/nginx/sites-available/default /etc/nginx/sites-available/default.old
cp $BASEDIR/nginx-config /etc/nginx/sites-available/default
chmod -R 777 /var/www/html/
service nginx restart
