#!/usr/bin/env bash

# Executed on the first start or provisioning of the box
if [ "$1" = "first-up" ]
then

	# set correct permissions for private key
	chmod 0700 /home/vagrant/.ssh
	chmod 0600 /home/vagrant/.ssh/id_rsa
	chmod 0600 /home/vagrant/.ssh/config

	# creating logs directory
	mkdir -p /vagrant/build/logs

exit
fi

# Executed on every start of the box
if [ "$1" = "regular-up" ]
then

	rm -rf /etc/nginx/sites-enabled/*
	for name in 2018 pma readis; do
		# link
		ln -sf /vagrant/env/nginx/$name.conf /etc/nginx/sites-enabled/020-$name
		# check link
		test -L /etc/nginx/sites-enabled/020-$name && echo -e "\e[0mLinking nginx $name config: \e[1;32mOK\e[0m" || echo -e "Linking nginx $name config: \e[1;31mFAILED\e[0m";
	done

	# update composer
	echo -e "\e[0mUpdating composer...\e[1;32m"
	composer self-update

	service php7.2-fpm restart
	service nginx restart

exit
fi
