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
cp -v -R google/ /home/.
chmod -R 755 /home/google
echo "Install CWP 2FA Files"
chattr -R -i /usr/local/cwpsrv/htdocs/admin/design
cp -v admin/cwp2fa.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
cp -v admin/user2fa.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
cp -v admin/googleAuthenticator.php /usr/local/cwpsrv/htdocs/admin/design/
cp -v admin/showQRCode.php /usr/local/cwpsrv/htdocs/admin/design/
if ! grep -q "\-- cwp_2fa --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
then
        cat 3rdparty.txt >> /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
fi
cp -v -R admin/phpqrcode /usr/local/cwpsrv/htdocs/admin/design/
chattr -R +i /usr/local/cwpsrv/htdocs/admin/design/
chattr -R -i /usr/local/cwpsrv/htdocs/admin/login/
cp -v admin/login/index_working.php /usr/local/cwpsrv/htdocs/admin/login/
chattr -R +i /usr/local/cwpsrv/htdocs/admin/login/
chattr -R -i /usr/local/cwpsrv/var/services/users/login/
cp -v admin/login/login.php /usr/local/cwpsrv/var/services/users/login/
chattr -R +i /usr/local/cwpsrv/var/services/users/login/
echo "/////Installing User Panel Files/////"
cp -v -R /usr/local/cwpsrv/var/services/users/cwp_theme/original/ /usr/local/cwpsrv/var/services/users/cwp_theme/modified/
cp -v users/user2fa.php /usr/local/cwpsrv/var/services/user_files/modules/
cp -v users/user2fa.ini /usr/local/cwpsrv/var/services/users/cwp_lang/en/
cp -v users/mod_user2fa.html /usr/local/cwpsrv/var/services/users/cwp_theme/modified/
cp -v users/user2fa.js.twig /usr/local/cwpsrv/var/services/users/cwp_theme/modified/js/modules/
\cp -fv users/menu_left.html /usr/local/cwpsrv/var/services/users/cwp_theme/modified/



echo "/////Installing 2FA Watch Script/////"
cp -v watch.sh /root/watch.sh
chmod 755 /root/watch.sh
echo "/root/watch.sh" >> /etc/cron.daily/cwp
/etc/cron.daily/cwp
crontab -l > mycron
echo "*/5 * * * * /root/watch.sh >/dev/null 2>&1" >> mycron
crontab mycron
rm mycron
clear

echo "Please log into you CWP Root Panel, and generate an API key"
echo "CWP Settings->API Manager"
echo "Make sure to check \"Account->list\" when generating your key"
echo "You MUST input a valid key to continue"
[ -a .apikey.key ] && apikey=`cat .apikey.key`
while ! [[ "$apikey" =~ ^[A-Za-z0-9]{45}$ ]] 
do
read -p "Please Enter your API Key Here: " apikey ;echo $apikey
done
sed -i "s@API_KEY@$apikey@g" /usr/local/cwpsrv/htdocs/resources/admin/modules/user2fa.php
echo $apikey > .apikey.key
clear
setenforce 1
/root/watch.sh
clear
echo "Don't forget to change your users Theme Settings User Acounts->Features,Themes,Languages [Themes]"
echo "Please set your proper timezone using 'timedatectl set-timezone (Time Zone)'"
echo "To see a list of timezones, use 'timedatectl list-timezones'"
read -n 1 -s -r -p "Press any key to continue"; echo

