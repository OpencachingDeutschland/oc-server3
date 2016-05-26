# -*- mode: ruby -*-
# vi: set ft=ruby :

VM_IP		= "10.10.0.101"   # IP of the VM, must be unique in your network
VM_MEMORY	= 512
VM_CPUS		= 1
VM_NAME		= "OpenCaching DEV VM"
VM_HOST		= "local.opencaching.de"

if Vagrant::Util::Platform.windows?
  HOSTS_FILE = ENV['SystemRoot'] + '\system32\drivers\etc\hosts'
  SYNCED_FOLDER_TYPE = ''
else
  HOSTS_FILE = '/etc/hosts'
  SYNCED_FOLDER_TYPE = 'nfs'
end


# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
	config.vm.box = "bento/centos-7.1"
	config.vm.hostname = VM_HOST
	config.vm.network "forwarded_port", guest: 80,		host: 80,  	auto_correct: true # http
	config.vm.network "forwarded_port", guest: 443,		host: 443,  auto_correct: true # https
	config.vm.network "forwarded_port", guest: 3306,	host: 3306,	auto_correct: true # mysql

	config.vm.synced_folder "bin/", 	"/var/www/html/bin",	type: SYNCED_FOLDER_TYPE
	config.vm.synced_folder "htdocs/",	"/var/www/html/htdocs",	type: SYNCED_FOLDER_TYPE
	config.vm.synced_folder "doc/", 	"/var/www/html/doc",	type: SYNCED_FOLDER_TYPE
	config.vm.synced_folder "local/", 	"/var/www/html/local",	type: SYNCED_FOLDER_TYPE
	config.vm.synced_folder "sql/", 	"/var/www/html/sql",	type: SYNCED_FOLDER_TYPE
	config.vm.synced_folder "tests/", 	"/var/www/html/tests",	type: SYNCED_FOLDER_TYPE
	if SYNCED_FOLDER_TYPE == "nfs"
		config.nfs.map_uid = Process.uid
		config.nfs.map_gid = Process.gid
	end

	config.vm.provider :virtualbox do |v|
		v.name = VM_NAME
		v.customize([
			"modifyvm", :id,
			"--memory", VM_MEMORY,
			"--cpus", VM_CPUS,
		])
	end

	config.vm.network :private_network, ip: VM_IP

	if Vagrant.has_plugin? 'vagrant-hostmanager'
		config.hostmanager.enabled = true
		config.hostmanager.manage_host = true
		config.hostmanager.ignore_private_ip = false
		config.hostmanager.include_offline = true
		config.hostmanager.aliases = [VM_HOST]
	else
		hosts_entry = VM_IP + " " + VM_HOST
		if not File.open(HOSTS_FILE).each_line.any? { |line| line.chomp == hosts_entry }
			puts "\e[31mvagrant-hostmanager plugin not installed.\nInstall it using 'vagrant plugin install vagrant-hostmanager'"
			puts "Alternatively manually add the following entry to your hosts file in #{HOSTS_FILE}\n"
			puts hosts_entry
			puts "\033[0m"
		end
	end

	# the image is not compatible with latest vbguest additions,
	# if you have vbguest plugin installed and it automatically tries to update
	# it will break the VM
	if Vagrant.has_plugin? 'vagrant-vbguest'
		config.vbguest.auto_update = false
	end

	config.ssh.insert_key = false
	config.vm.provision "shell", path: "bin/provision.sh"
end
