#!/bin/bash
source ./install-general.sh
source ../../../minecraft.dir/php-infos.sh
function update_system_packages() {
    install_log "Updating sources"
    apt-get update || install_error "Couldn't update package list"
    apt-get upgrade -y --force-yes || install_error "Couldn't upgrade packages"
}

function install_dependencies() {
    install_log "Installing required packages"
    apt-get install -y --force-yes screen rsync zip unrar rar tar || install_error "Couldn't install dependencies"
}

function enable_init() {
    install_log "Enabling automatic startup and shutdown"
    hash insserv 2>/dev/null
    if [[ $? == 0 ]]; then
        insserv msm
    else
        update-rc.d msm defaults
    fi
}

install_msm

