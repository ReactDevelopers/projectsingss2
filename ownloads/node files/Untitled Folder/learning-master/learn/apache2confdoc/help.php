pata kro redis db ki location change kaise krte hain

// free ssl
letsencrypt free ssl


/etc/apache2
	//enabling mod rewrite
	apache2.conf 

/etc/apache2/sites-available
	// adding additional site to map with domain and port

	//enable custom conf file
	sudo a2ensite 001-gossip.conf 

	//disable custom conf file
	sudo a2dissite 001-gossip.conf

	//reload apace after changes
	sudo systemctl reload apache2 