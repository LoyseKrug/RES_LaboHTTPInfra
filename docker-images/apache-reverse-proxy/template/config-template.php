<?php
	$DYNAMIC_APP1 = getenv('DYNAMIC_APP1');
	$DYNAMIC_APP2 = getenv('DYNAMIC_APP2');
	$STATIC_APP1 = getenv('STATIC_APP1');
	$STATIC_APP2 = getenv('STATIC_APP2');
?>


<VirtualHost *:80>
        ServerName laboinfra.res.ch

	ProxyRequests Off
	
	<Proxy "balancer://mydynamiccluster">
		BalancerMember 'http://<?php print "$DYNAMIC_APP1"?>/'
		BalancerMember 'http://<?php print "$DYNAMIC_APP2"?>/'

                Require all granted

		ProxySet lbmethod=byrequests
	</Proxy>

        ProxyPass '/api/futur/' 'balancer://mydynamiccluster'
        ProxyPassReverse '/api/futur/' 'balancer://mydynamiccluster'


	<Proxy "balancer://mystaticcluster">
		BalancerMember 'http://<?php print "$STATIC_APP1"?>/'
		BalancerMember 'http://<?php print "$STATIC_APP2"?>/'

                Require all granted

		ProxySet lbmethod=byrequests
	</Proxy>

        ProxyPass '/' 'balancer://mystaticcluster'
        ProxyPassReverse '/' 'balancer://mystaticcluster'
	
	<Location "/balancer-manager">
		SetHandler balancer-manager
	</Location>
	ProxyPass /balancer-manager !

</VirtualHost>
