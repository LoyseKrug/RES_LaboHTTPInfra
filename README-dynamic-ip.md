# Readme - Dynamic ip configuration

To dynamically configure the ip of the containers on the proxy we will use environement variables as they can be set when we start the proxy container.

We use a php template file to dynamically generate the `/etc/apache2/sites-available/001-reverse-proxy.conf` file that describes the reverse proxy configuration.

## config-template.php

this is our template file.

```php
// we use php to get the environement variables
<?php
	$DYNAMIC_APP = getenv('DYNAMIC_APP');
	$STATIC_APP = getenv('STATIC_APP');
?>
<VirtualHost *:80>
        ServerName laboinfra.res.ch

    	// and print them in the path
        ProxyPass '/api/futur/' 'http://<?php print "$DYNAMIC_APP"?>/'
        ProxyPassReverse '/api/futur/' 'http://<?php print "$DYNAMIC_APP"?>/'

        ProxyPass '/' 'http://<?php print "$STATIC_APP"?>/'
        ProxyPassReverse '/' '<?php print "$STATIC_APP"?>/'

</VirtualHost>
```

The big problem is how to execute this script at startup of our container : we exploit a file that starts apache2 service so everything is in place before we start it so we don't need to restart it afterwards :

## apache2-foreground

We simply add these lines :

```bash
#Add setup for RES lab
echo "Setup for the RES lab..."
echo "Static app URL: $STATIC_APP" 
echo "Dynamic app URL: $DYNAMIC_APP"

# call php script to get the env vars and dynamicly create the sites-available virtulhosts config file.
php /var/apache2/template/config-template.php > /etc/apache2/sites-available/001-reverse-proxy.conf
echo "Env vars copied to sites-available"
```

Our docker image guarantees us that this script is called.

## Dockerfile

We still need to add a few mods to apache2 in the Dockerfile :

`RUN a2enmod proxy proxy_http` includes everything needed for basic http proxy.

`RUN a2ensite 000-* 001-*` will enable our configurations