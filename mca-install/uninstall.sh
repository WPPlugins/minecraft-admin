#!/bin/bash

clear

rm /etc/init.d/msm
rm /etc/cron.d/msm
rm /etc/bash_completion.d/msm
rm /etc/msm.conf
rm /usr/local/bin/msm
touch ./uninstalled

echo -e "\n\033[1;32mMCA UNINSTALL: $*\033[mfinished. Go to the MCA controlpanel and finished removing Minecraft Admin"
