#!/bin/bash
source ./install-general.sh
source ../../../minecraft.dir/php-infos.sh
function update_system_packages() {
    install_log "Updating sources"
    sudo yum update --skip-broken || install_error "Couldn't update packages"
}

function install_dependencies() {
    install_log "Installing required packages"
    sudo yum install screen rsync zip java || install_error "Couldn't install dependencies"
}

function enable_init() {
    install_log "Enabling automatic startup and shutdown"
    sudo chkconfig --add msm
}

install_msm
