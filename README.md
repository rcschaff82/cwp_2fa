# cwp_2fa
CentOS Control Panel 2fa module


***This module is depreciated as CWP has included 2FA in their project. ***

I have built a Two Factor Authorization Module for CWP.  I have tested it, but please consider this is BETA.   All Issues should be reported to me on this post.

Pre-Requisites.
You need an API key created with ACCOUNT-> list created
After installation, make sure you change User Account -> Themes to the modified theme, and don't allow them to change

Please not that this system works around the CWP login system, and therefore is not infallible, though I did my best to hide that it's there.

Here is a video demonstrating the Module: https://www.youtube.com/watch?v=Uc9pglbZo6I

If you would like to donate to the project, I accept donations at: https://paypal.me/rcschaff

TO install:
From root user via ssh:

wget -O - https://raw.githubusercontent.com/rcschaff82/cwp_installers/master/cwp_2fa.install | bash

That's it. 

Now log into CWP and you should see a new menu 2Factor Auth directly under Server Settings Menu


Users Menu Appears under CWP Settings


Change Log:
5/4/2020 - Added Uninstall Script 
	 - Removed Need for API key
	 - Removed API Key from install
5/23/2020 - Added update check script

Requests:
Please open a new issue with your feature request
