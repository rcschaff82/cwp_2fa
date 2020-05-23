#!/bin/bash
setenforce 0
logFile='2fainstall.log'
exec >  >(tee -ia $logFile)
exec 2> >(tee -ia $logFile >&2)
echo "/////Setting up 2FA/////"
pkg="ntp"
if rpm -q $pkg
then
    echo "$pkg installed"
else
    echo "$pkg NOT installed"
    yum -y install ntp
    systemctl start ntpd
fi
setfacl -m u:login:rX /home
setfacl -dm u:login:rX /home
echo "Moving googleAuth Files"
\cp -fv -R google/ /home/.
chmod -R 755 /home/google
echo "Install CWP 2FA Files"
chattr -R -i /usr/local/cwpsrv/htdocs/admin/design
\cp -fv admin/cwp2fa.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
\cp -fv admin/user2fa.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
\cp -fv update_class.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
\cp -fv admin/googleAuthenticator.php /usr/local/cwpsrv/htdocs/admin/design/
\cp -fv admin/showQRCode.php /usr/local/cwpsrv/htdocs/admin/design/
if ! grep -q "\-- cwp_2fa --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
then
        cat 3rdparty.txt >> /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
fi
\cp -fv -R admin/phpqrcode /usr/local/cwpsrv/htdocs/admin/design/
chattr -R +i /usr/local/cwpsrv/htdocs/admin/design/
chattr -R -i /usr/local/cwpsrv/htdocs/admin/login/
\cp -fv admin/login/index_working.php /usr/local/cwpsrv/htdocs/admin/login/
chattr -R +i /usr/local/cwpsrv/htdocs/admin/login/
chattr -R -i /usr/local/cwpsrv/var/services/users/login/
\cp -fv admin/login/login.php /usr/local/cwpsrv/var/services/users/login/
chattr -R +i /usr/local/cwpsrv/var/services/users/login/
echo "/////Installing User Panel Files/////"
\cp -fv -R /usr/local/cwpsrv/var/services/users/cwp_theme/original/ /usr/local/cwpsrv/var/services/users/cwp_theme/modified/
\cp -fv users/user2fa.php /usr/local/cwpsrv/var/services/user_files/modules/
\cp -fv users/user2fa.ini /usr/local/cwpsrv/var/services/users/cwp_lang/en/
\cp -fv users/mod_user2fa.html /usr/local/cwpsrv/var/services/users/cwp_theme/modified/
\cp -fv users/user2fa.js.twig /usr/local/cwpsrv/var/services/users/cwp_theme/modified/js/modules/
\cp -fv users/menu_left.html /usr/local/cwpsrv/var/services/users/cwp_theme/modified/



echo "/////Installing 2FA Watch Script/////"
\cp -fv watch.sh /root/watch.sh
chmod 755 /root/watch.sh
echo "/root/watch.sh" >> /etc/cron.daily/cwp
/etc/cron.daily/cwp
crontab -l > mycron
echo "*/5 * * * * /root/watch.sh >/dev/null 2>&1" >> mycron
crontab mycron
rm mycron
clear

setenforce 1
/root/watch.sh
clear
echo "Don't forget to change your users Theme Settings User Acounts->Features,Themes,Languages [Themes]"
echo "Please set your proper timezone using 'timedatectl set-timezone (Time Zone)'"
echo "To see a list of timezones, use 'timedatectl list-timezones'"
read -n 1 -s -r -p "Press any key to continue"; echo

