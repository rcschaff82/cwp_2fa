# cwp_2fa
CentOS Control Panel 2fa module

I have built a Two Factor Authorization Module for CWP.  I have tested it, but please consider this is BETA.   All Issues should be reported to me on this post.

Pre-Requisites.
You need an API key created with ACCOUNT-> list created
After installation, make sure you change User Account -> Themes to the modified theme, and don't allow them to change

Please not that this system works around the CWP login system, and therefore is not infallible, though I did my best to hide that it's there.

TO install:
From root user via ssh:

wget -O - https://github.com/rcschaff82/cwp_2fa/tarball/master | tar xz

cd rcschaff82-cwp_2fa-*

./install.sh

When prompted, put in your API key
That's it. 

Now log into CWP and you should see a new menu 2Factor Auth directly under Server Settings Menu


Users Menu Appears under CWP Settings
