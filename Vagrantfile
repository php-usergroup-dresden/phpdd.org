VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "DreiWolt/devops007"
  config.vm.network :private_network, ip: "192.168.3.15"
  config.vm.hostname = "PHPDD\Website"
  config.hostsupdater.aliases = ["dev.phpdd.org.de", "pma.phpdd.org.de", "readis.phpdd.org.de"]
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
  config.vm.provision "shell", path: "env/bootstrap.sh", run: "always"

end
