#!/bin/bash
MCAVERSION=0.8.4.0
source ../../../minecraft.dir/php-infos.sh

function log() {
    echo -e "\n\033[0;34mMCA: $*\033[m\n"
}
function info() {
    echo -e "\033[0;32mMCA: $*\033[m"
}
function install_log() {
    echo -e "\n\033[1;32mMCA INSTALL: $*\033[m"
}

function install_error() {
    echo -e "\n\033[1;37;41mMCA INSTALL ERROR: $*\033[m"
    exit 1
}
function uninstall() {
    echo -e "\n\033[1;37;41mMCA UNINSTALL: $*\033[m"
}


log "Version" $MCAVERSION
case $1 in
    install|-i)
        source ./install-general.sh
        info "Install MCA...."
        if [[ $2 == "ubuntu" ]];
        then
            info "use script for Debian as host system"
            source ./install-debian.sh
        elif [[ $2 == "redhat" ]];
        then
            info "use script for Redhat as host system"
            source ./install-redhat.sh
        else
            info "choose a host system:"
            echo -e "               1: ubuntu"
            echo -e "               2: redhat"
            read OS
            if [[ "$OS" == 1 ]];
            then
                info "use script for Debian as host system"
                source ./install-debian.sh
            elif [[ "$OS" == 2 ]];
            then
                info "use script for Redhat as host system"
                source ./install-redhat.sh
            fi
        fi
    ;;
    upgrade|-u)
        install "Now starting the upgrade...."

        rm /etc/init.d/msm
        rm /etc/cron.d/msm
        rm /etc/bash_completion.d/msm
        rm /etc/msm.conf
        rm /usr/local/bin/msm
        bash $0 -i $2
        touch ../../../minecraft.dir/.upgraded
        echo $MCAVERSION >> ../../../minecraft.dir/.upgraded
    ;;
    remove|-r)
        rm /etc/init.d/msm
        rm /etc/cron.d/msm
        rm /etc/bash_completion.d/msm
        rm /etc/msm.conf
        rm /usr/local/bin/msm


        uninstall "finished. Go to the MCA controlpanel and finished removing Minecraft Admin"
    ;;
    *)
        echo -e "\n\033[1;32mMCA: \033[m choose a option:"
        echo -e "\033[m               - install, -i:"
        echo -e "\033[m               - remove, -r:"
        echo -e "\033[m               - upgrade, -u:"
    ;;
esac