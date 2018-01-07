VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # box-config
    config.vm.box = "DreiWolt/devops007"

    # network-config
    config.vm.network :private_network, ip: "192.168.3.27"
    config.vm.hostname = "PHPDD"
    config.vm.synced_folder ".", "/vagrant", type: "nfs"
    config.hostsupdater.aliases = ["2018-dev.phpdd.org", "pma.phpdd.org", "readis.phpdd.org"]

    config.vm.provision "file", source: "env/vagrant/id_rsa", destination: "/home/vagrant/.ssh/id_rsa"
    config.vm.provision "file", source: "env/vagrant/ssh_config", destination: "/home/vagrant/.ssh/config"
    config.vm.provision "shell", path: "env/vagrant/bootstrap.sh", args: "first-up"
    config.vm.provision "shell", path: "env/vagrant/bootstrap.sh", args: "regular-up", run: "always"

end
