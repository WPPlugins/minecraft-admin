#!/bin/bash

UPDATE_URL="https://raw.github.com/profenter/minecraft-server-manager/master"
msm_user_system=true
dl_dir="$(mktemp -d -t msm-XXX)"

# Outputs an MCA INSTALL log line
function install_starttext() {
    info "################################################"
    info "#                                              #"
    info "#                                              #"
    info "#                                              #"
    info "#                                              #"
    info "#                                              #"
    info "#               Welcome to the                 #"
    info "#           MINECRAFT ADMIN INSTALLER          #"
    info "#                                              #"
    info "#        Please go back to your website        #"
    info "#                                              #"
    info "#                                              #"
    info "#                                              #"
    info "#         This installer starts in 10sec       #"
    info "#           \033[1;37;41mpress CRTL+C  to cancle$*\033[m            #"
    info "#                                              #"
    info "################################################"


}

# Runs a system software update to make sure we're using all fresh packages
function update_system_packages() {
    # OVERLOAD THIS
    install_error "No function definition for update_system_packages"
}

# Installs additional dependencies (screen, rsync, zip, wget) using system package manager
function install_dependencies() {
    # OVERLOAD THIS
    install_error "No function definition for install_dependencies"
}

# Verifies existence of or adds user for Minecraft server (default "minecraft")
function add_minecraft_user() {
    install_log "Creating default user '${msm_user}'"
    if $msm_user_system; then
         useradd ${msm_user} --home "$msm_dir"
    else
         useradd ${msm_user} --system --home "$msm_dir"
    fi
}

# Verifies existence and permissions of msm server directory (default /opt/msm)
function create_msm_directories() {
    install_log "Creating MSM directories"
    if [ ! -d "$msm_dir" ]; then
         mkdir -p "$msm_dir" || install_error "Couldn't create directory '$msm_dir'"
    fi
     chown -R $msm_user:$msm_user "$msm_dir" || install_error "Couldn't change file ownership for '$msm_dir'"
}

# Fetches latest msm.conf, cron job, and init script
function download_latest_files() {
    if [ ! -d "$dl_dir" ]; then
        install_error "Temporary download directory was not created properly"
    fi

    install_log "Downloading latest MSM configuration file"
    wget ${UPDATE_URL}/msm.conf \
        -O "$dl_dir/msm.conf.orig" || install_error "Couldn't download configuration file"

    install_log "Downloading latest MSM cron file"
    wget ${UPDATE_URL}/cron/msm \
        -O "$dl_dir/msm.cron.orig" || install_error "Couldn't download cron file"

    install_log "Downloading latest MSM version"
    wget ${UPDATE_URL}/init/msm \
        -O "$dl_dir/msm.init.orig" || install_error "Couldn't download init file"
}

# Patches msm.conf and cron job to use specified username and directory
function patch_latest_files() {
    # patch config file
    install_log "Patching MSM configuration file"
    sed 's#USERNAME="minecraft"#USERNAME="'$msm_user'"#g' "$dl_dir/msm.conf.orig" | \
        sed "s#/opt/msm#$msm_dir#g" | \
        sed "s#UPDATE_URL=.*\$#UPDATE_URL=\"$UPDATE_URL\"#" >"$dl_dir/msm.conf"

    # patch cron file
    install_log "Patching MSM cron file"
    awk '{ if ($0 !~ /^#/) sub(/minecraft/, "'$msm_user'"); print }' \
        "$dl_dir/msm.cron.orig" >"$dl_dir/msm.cron"

    # patch init file
    install_log "Patching MSM init file"
    cp "$dl_dir/msm.init.orig" "$dl_dir/msm.init"
}

# Installs msm.conf into /etc
function install_config() {
    install_log "Installing MSM configuration file"
    install -b -m0644 "$dl_dir/msm.conf" /etc/msm.conf
    if [ ! -e /etc/msm.conf ]; then
        install_error "Couldn't install configuration file"
    fi
}

# Installs msm.cron into /etc/cron.d
function install_cron() {
    install_log "Installing MSM cron file"
    install -m0644 "$dl_dir/msm.cron" /etc/cron.d/msm || install_error "Couldn't install cron file"
}

# Installs init script into /etc/init.d
function install_init() {
    install_log "Installing MSM init file"
    install -b "$dl_dir/msm.init" /etc/init.d/msm || install_error "Couldn't install init file"

    install_log "Making MSM accessible as the command 'msm'"
    ln -s /etc/init.d/msm /usr/local/bin/msm
}

# Enables init script in default runlevels
function enable_init() {
    # OVERLOAD THIS
    install_error "No function defined for enable_init"
}

# Updates rest of MSM using init script updater
function update_msm() {
    install_log "Asking MSM to update itself"
    /etc/init.d/msm update --noinput
}

# Updates rest of MSM using init script updater
function setup_jargroup() {
    install_log "Setup default jar groups"
    /etc/init.d/msm jargroup create vanilla https://s3.amazonaws.com/Minecraft.Download/versions/1.8/minecraft_server.1.8.jar
    /etc/init.d/msm jargroup create glowestone_last http://ci.chrisgward.com/job/Glowstone/98/artifact/build/libs/glowstone.jar
}

function install_complete() {
    install_log "Done. Type in console 'msm help' to get started. Go to next step. Have fun!"
}

function install_msm() {
    install_starttext
    sleep 10
    touch $mca_start
    install_log "This installer installs MSN (Minecraft Server Manager), a powerfull backend for Minecraft Servers."
    install_log "Get more information at http://profenter.de/blog/948-backend-von-mca-backend-auf-msn-umgestellt"
    add_minecraft_user
    update_system_packages
    install_dependencies
    create_msm_directories
    download_latest_files
    patch_latest_files
    install_config
    install_cron
    install_init
    enable_init
    update_msm
    setup_jargroup
    install_complete
    touch $mca_inst
}
