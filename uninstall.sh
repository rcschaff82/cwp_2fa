#!/bin/bash
setenforce 0
logFile='2fainstall.log'
exec >  >(tee -ia $logFile)
exec 2> >(tee -ia $logFile >&2)


rm -rf /home/google/
chattr -R -i /usr/local/cwpsrv/htdocs/admin/design
rm -f /usr/local/cwpsrv/htdocs/resources/admin/modules/cwp2fa.php
rm -f /usr/local/cwpsrv/htdocs/resources/admin/modules/user2fa.php
rm -f /usr/local/cwpsrv/htdocs/admin/design/googleAuthenticator.php
rm -f /usr/local/cwpsrv/htdocs/admin/design/showQRCode.php
# Find a way to remove code from 3rdparty
sd=$(grep -n "<\!-- cwp_2fa --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php | cut -f1 -d:)
ed=$(grep -n "<\!-- end cwp_2fa --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php | cut -f1 -d:)
cmd="$sd"",""$ed""d"
sed -i.bak -e "$cmd" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
rm -rf /usr/local/cwpsrv/htdocs/admin/design/phpqrcode
chattr -R +i /usr/local/cwpsrv/htdocs/admin/design/
chattr -R -i /usr/local/cwpsrv/htdocs/admin/login/
rm -f /usr/local/cwpsrv/htdocs/admin/login/index_working.php
rm -f /usr/local/cwpsrv/htdocs/admin/login/index.php
mv -f /usr/local/cwpsrv/htdocs/admin/login/*.php index.php


#mv index.php back
chattr -R +i /usr/local/cwpsrv/htdocs/admin/login/
chattr -R -i /usr/local/cwpsrv/var/services/users/login/
rm -f /usr/local/cwpsrv/var/services/users/login/login.php
mv -f /usr/local/cwpsrv/var/services/users/login/abcdefg.php /usr/local/cwpsrv/var/services/users/login/index.php
chattr -R +i /usr/local/cwpsrv/var/services/users/login/


echo "/////Installing User Panel Files/////"

rm -f /usr/local/cwpsrv/var/services/user_files/modules/user2fa.php
rm -f /usr/local/cwpsrv/var/services/users/cwp_lang/en/user2fa.ini
rm -f /usr/local/cwpsrv/var/services/users/cwp_theme/modified//mod_user2fa.html
rm -f /usr/local/cwpsrv/var/services/users/cwp_theme/modified/js/modules/user2fa.js.twig
rm -f /usr/local/cwpsrv/var/services/users/cwp_theme/modified//menu_left.html
rm -f /root/watch.sh
sed -i "s@/root/watch.sh@@g" /etc/cron.daily/cwp
crontab -l | grep -v 'watch.sh'  | crontab -
