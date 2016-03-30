# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
# The most common configuration options are documented and commented below.
# For a complete reference, please see the online documentation at
# https://docs.vagrantup.com.
#
config.vm.box = "bento/centos-7.1"
config.vm.hostname = "local.opencaching.de"
config.vm.network "forwarded_port", guest: 22, host: 2223 # ssh
config.vm.network "forwarded_port", guest: 80, host: 80 # http
config.vm.network "forwarded_port", guest: 443, host: 443 # https
config.vm.network "forwarded_port", guest: 3306, host: 3306 # mysql

config.vm.synced_folder "bin/", "/var/www/html/bin", disabled: false
config.vm.synced_folder "htdocs/", "/var/www/html/htdocs", disabled: false
config.vm.synced_folder "doc/", "/var/www/html/doc", disabled: false
config.vm.synced_folder "local/", "/var/www/html/local", disabled: false

config.vm.provider "virtualbox" do |v|
    v.memory = 512
    v.cpus = 1
end

config.vm.network :private_network, type: "dhcp"

config.ssh.insert_key = false
config.vm.provision "shell", path: "provision.sh"
end
