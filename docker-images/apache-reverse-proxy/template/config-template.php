<?php
	$dynamic_app1 = getenv('DYNAMIC_APP1');
	$dynamic_app2 = getenv('DYNAMIC_APP2');
	$static_app1 = getenv('STATIC_APP1');
	$static_app2 = getenv('STATIC_APP2');
?>
<VirtualHost *:80>
        ServerName laboinfra.res.ch
	
	<Proxy "balancer://mydynamiccluster">
		BalancerMember 'http://<?php print "$dynamic_app1"?>'
		BalancerMember 'http://<?php print "$dynamic_app2"?>'
		ProxySet lbmethod=byrequests
	</Proxy>

        ProxyPass '/api/futur/' 'balancer://mydynamiccluster/'
        ProxyPassReverse '/api/futur/' 'balancer://mydynamiccluster/'

	Header add Set-Cookie "ROUTEID=.%{BALANCER_WORKER_ROUTE}e; path=/" env=BALANCER_ROUTE_CHANGED
	<Proxy "balancer://mystaticcluster">
		BalancerMember 'http://<?php print "$static_app1"?>' route=1
		BalancerMember 'http://<?php print "$static_app2"?>' route=2
		ProxySet stickysession=ROUTEID
	</Proxy>

        ProxyPass '/' 'balancer://mystaticcluster/'
        ProxyPassReverse '/' 'balancer://mystaticcluster/'
</VirtualHost>
