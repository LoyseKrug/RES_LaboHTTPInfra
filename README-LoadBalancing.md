# Readme - Load Balancing

The load balancing of our servers is done through a heavy modification of our `config-template.php` file :

## config-template.php

```php
<VirtualHost *:80>
        ServerName laboinfra.res.ch

    // this is for the 'nice' ui thingy
	<Location /balancer-manager>
		SetHandler balancer-manager
		Order Allow,Deny
		Allow from all
	</Location>
	ProxyPass /balancer-manager !
	
// we declare a cluster of Balancers that will be used to serve our dynamic app
	<Proxy "balancer://mydynamiccluster">
		BalancerMember 'http://<?php print "$dynamic_app1"?>'
		BalancerMember 'http://<?php print "$dynamic_app2"?>'
	</Proxy>

// we route the path /api/futur/ on the apropriate cluster
        ProxyPass '/api/futur/' 'balancer://mydynamiccluster/'
        ProxyPassReverse '/api/futur/' 'balancer://mydynamiccluster/'

// our second cluster for the static app this time
	<Proxy "balancer://mystaticcluster">
		BalancerMember 'http://<?php print "$static_app1"?>'
		BalancerMember 'http://<?php print "$static_app2"?>'
	</Proxy>
    
// same thing we route '/' to the appropriate cluster
        ProxyPass '/' 'balancer://mystaticcluster/'
        ProxyPassReverse '/' 'balancer://mystaticcluster/'
	

</VirtualHost>
```



## Dockerfile

Plus we need to include a few mods for apache :

`RUN a2enmod proxy proxy_http proxy_balancer status lbmethod_byrequests`

you can see we use `proxy_balancer` for load balancing, `status` because god wants it and `lbmethod_byrequests` as a methode of round-robin load distribution.



