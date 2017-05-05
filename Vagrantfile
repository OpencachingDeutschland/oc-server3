# -*- mode: ruby -*-
# vi: set ft=ruby :

VM_IP        = "10.10.0.101"
VM_MEMORY    = 1024
VM_CPUS      = 1
VM_NAME      = "OpenCaching DEV VM"
VM_HOST      = "local.team-opencaching.de"

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
    config.vm.box = "bento/centos-7.1"
    config.vm.hostname = VM_HOST
    config.vm.network "forwarded_port", guest: 80,        host: 80,      auto_correct: true # http
    config.vm.network "forwarded_port", guest: 443,        host: 443,  auto_correct: true # https
    config.vm.network "forwarded_port", guest: 3306,    host: 3306,    auto_correct: true # mysql

    config.vm.synced_folder "./", "/var/www/html", id: "v-root", mount_options: ["rw", "tcp", "nolock", "noacl", "async"], type: "nfs", nfs_udp: false
    #config.vm.synced_folder "./", "/var/www/html", create: true, type: "smb"
    config.vm.provider :virtualbox do |v|
        v.name = VM_NAME
        v.customize([
            "modifyvm", :id,
            "--memory", VM_MEMORY,
            "--cpus", VM_CPUS,
        ])
    end

    config.vm.network :private_network, ip: VM_IP

    # the image is not compatible with latest vbguest additions,
    # if you have vbguest plugin installed and it automatically tries to update
    # it will break the VM
    if Vagrant.has_plugin? 'vagrant-vbguest'
        config.vbguest.auto_update = false
    end

    config.ssh.insert_key = false
    config.vm.provision "shell", path: "bin/provision.sh"
    config.vm.provision :shell, run: "always", :inline => "systemctl status httpd || systemctl start httpd"
end
