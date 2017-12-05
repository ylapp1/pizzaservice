Vagrant.configure("2") do |config|
 
  config.vm.box = "ubuntu/xenial64"

  # Apache Port
  config.vm.network :forwarded_port, guest: 80, host: 4567

  # MySQL Port
  config.vm.network :forwarded_port, guest:3306, host: 1234
  
  config.vm.provision "shell", path: "VagrantProvision.sh"
  
  # Disable creation of ubuntu-xenial-16.04-cloudimg-console.log
  config.vm.provider "virtualbox" do |vb|
    vb.customize [ "modifyvm", :id, "--uartmode1", "disconnected" ]
  end
  
end
