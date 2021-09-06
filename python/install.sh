BASEDIR=$(pwd)
apt-get -y update && apt-get -y install python3 python3-pip
pip3 install Django --upgrade
cd $BASEDIR/files && python3 manage.py makemigrations && python3 manage.py makemigrations app && python3 manage.py migrate

