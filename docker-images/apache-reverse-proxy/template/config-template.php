<?php
	$DYNAMIC_APP = getenv('DYNAMIC_APP');
	$STATIC_APP = getenv('STATIC_APP');
?>


<VirtualHost *:80>
        ServerName laboinfra.res.ch

        ProxyPass '/api/futur/' 'http://<?php print "$DYNAMIC_APP"?>/'
        ProxyPassReverse '/api/futur/' 'http://<?php print "$DYNAMIC_APP_APP"?>/'

        ProxyPass '/' 'http://<?php print "$STATIC_APP"?>/'
        ProxyPassReverse '/' '<?php print "$STATIC_APP"?>/'

</VirtualHost>