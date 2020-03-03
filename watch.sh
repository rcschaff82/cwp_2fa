#!/bin/sh
pid=` ps aux | grep -v grep | pgrep -f cron.php`
check_user()
{
echo "//////////User Checks//////////"
cd /usr/local/cwpsrv/var/services/users/login
if [ "$(tail -1 index.php)" == "?>" ] ; then
                return
        fi
chattr -i .
chattr -i *
echo "Moving Files"
cp -f index.php abcdefg.php
cp -f login.php index.php
chattr +i *
chattr +i .
}
check_configs()
{
echo "//////////Config Checks//////////"
FILES=/usr/local/cwpsrv/conf.d/users/*
for f in $FILES
do
if ! grep -q "/home/google" $f; then
    echo updateing $f
     sed -i -re 's@open_basedir(.*)(";)@open_basedir\1:/home/google\2@' $f
fi
done
if grep -q "open_basedir = /tmp" /usr/local/cwpsrv/conf.d/users.conf; then
updating users.conf
sed -i "s@fastcgi_param   PHP_ADMIN_VALUE \"open_basedir = /tmp@fastcgi_param   PHP_ADMIN_VALUE \"open_basedir = /home/:/tmp@g" /usr/local/cwpsrv/conf.d/users.conf

fi
/usr/local/cwpsrv/bin/cwpsrv -s reload
find /home/*/.conf/cwp.ini -exec sed -i "s@original@modified@g" {} +

}
check_admin() 
{
echo "//////////Admin Checks//////////"
	cd /usr/local/cwpsrv/htdocs/admin/login/
	if [ "$(tail -1 index.php)" == "?>" ] ; then
		return
	fi
	echo "Moving Admin"
	chattr -i .
	chattr -i *
	ls | grep -P "[a-z0-9]{16}" | xargs -d"\n" rm
	RAND_CHARS=$(openssl rand -hex 16)
	mv index.php $RAND_CHARS.php
	cp index_working.php index.php
	sed -i "s@define(\"DO_LOGIN\",\"\");@define(\"DO_LOGIN\",\"$RAND_CHARS.php\");@g" index.php
	chattr +i *
	chattr +i .
}
if [ "$pid" != "" ]; then
while [ -e /proc/$pid ]
do
    sleep .6
done
fi
echo "Start Checks"
check_user
check_admin
check_configs
