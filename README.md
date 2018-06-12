# RES - Labo HTTPInfra

[TOC]

## Part 1 - Static HTTP server with apache httpd

###Creating a Docker container

#### Create a Dockerfile with php image 

The Docker container is created from a php image found on docker hub: https://hub.docker.com/_/php/ 

The image "php:7.0-apache" offer a apache server that can serve php web pages, using 7.0 version of the language.

The DockerFile copies all the files and folders located in the local `./content` folder of the project in the `/var/www/html` folder of the container, where are hosted all out web sites.

#### Launching Docker Container

To create the docker image, we run the command

`docker build res_apache_php .` from the the directory that contains the docker image

To launch the container, we use the command

`docker run -p <the port>:80 res_apache_php` where the chosen port is the one through which we will be able te reach the web page.

#### Configurate the docker container

To configurate the docker container. We run the command

`docker run -it <container id> /bin/bash`. The command launch a terminal inside the container 

To know the ip address of the container, we can use the command

`docker inspect <id de votre container> | gerp -i ipaddress` 

#### Configuration of the virtual host

Inside the container terminal we can find a file `000-default.cong` located in  `/etc/apache2/sites-available/`

Inside of this file, we can configure one or more servers that will answer to differents port.



### Content of the web page

In the `./content` directory, (located in `docker-images/apache-php-image` ) we have included the file `index.html`, in which we will have the content of our web page. 

#### Use of the Bootstrap template

The `index.html` file can be filled with content, but without CSS, its aspect will stay very poor.

In order to easily have an well presented web page, we can use a template foundable on the web.

We based our web page on a template found on the site  https://startbootstrap.com, `creative` theme. 

To apply the template:

We copy the content of the downloaded file in the `./content/` directory. The template itself contains a `index.html` file that can be modified.



## Part 2 - Dynamic HTTP server with express.js

### Creation of the docker container

#### Creation of the Dokerfile with a node image

We created  Dockerfile, giving it a node image, found on https://hub.docker.com/_/node/

We used the `node:8.11.2` image, that allow us to launch a server with an access to node js and the npm commands, which allow us to manage the package we want to use.

The Dockerfile copy all the files from the `src` file (located in `docker-images/express-image`) into the `/opt/app/` container, located in the container. (we put our `index.js` file into the `src` directory )

We use the CMD command to launch the file with the line:

`CMD ["node", "/opt/app/index.js"]



### Creation of the js server with Express.js and Chance.js

The objectif of our application was to create a server that "guesses the futur" by sending a JSON array containing "your future job, the date you will be hired, the country you will live in and you salary per year"

In order to do that, we will use the express js framework.

#### Use of nmp init to keep trace of the dependencies

In order for Docker to know which dependencies it will have to install in ordre to make the server run, we will create a json package that contains all the informations on thoses dependencies. Il order to do that, we use the command

`npm init` that will the ask you a list of questions in order to initiate the json package.

#### Express.js installation 

To install express.js, we use the command

`npm install express --save` ,  where --save save the module next to our project repository

***Optionnal installation***

It is also possible to install `express-generator` with the command

`npm install express-generator -g` , where -g means that the module is installed globally in the machine.

Express-generator is not used in the project, but it allow us generates express commands to create modules that could be added to the project.  

#### Chance.js installation

To install chance.js, we use the command 

`npm install --save chance` 



#### Configuration of the express server

To define the listening port of our server, we use the following function

`app.listen(<port>, function(){ ... });`  

It is possible to create requests depending on the url route. 

For example, we can create a GET request from the `/` route:

```
app.get('/', function(req,res){
   <contenu de votre requête> 
}).
```

Or we can crate an other GET request from another route ( `/test` for example) . 

```
app.get('/test', function(req,res){
   <contenu de votre requête> 
}).
```

In our projet, we use the "root" route `/`to make the request about the "future".



## Part 3 - Reverse proxy with apache (static configuration)

The idea of this part is to create a Docker container that will be a point of  redirection to the two services previously created (part 1 and 2). 

In this part, the redirection will be hard coded (typing the ip of the two Docker containters staticly) which is a dangerous bad practice, but which is important to go through in order to understand the mechanisms of a reverse proxy. 

We will create a reverse proxy server we can access using the url  `laboinfra.res.ch`



### Getting the ip adresses of the containers

In odrer to create our reverse proxy, we need to know the ip address of the two services (the html page from part 1 and the server form part 2). 

If the two services are not already running, we must start them with the commands:

`docker run -d --name apache_static res_apache_php`  

`docker run -d --name express_dynamic res_express_js`

where -d make the server turn in background and --name allow you to name our container

Now that the Containers are running we can get their ip address with the command: 

`docker inspect <name of the container> | grep -i ipaddress `

***This is where that manupulation in dangerous!***

We save the current IP Address of the container to use in in an other container. But if we decided to restart our container, we could not be sure it has the same address as before, and we would have to check the address again and update the reverse proxy container...



### Configure the server proxy by hand

In order to configure our reverse proxy so it can reache the html page created in part 1, we have to run a docker container, directly  on the `php:7.0-apache` image in interactive mode, with the command

`docker run -it -p <redirection port>:80 php:7.0-apache /bin/bash`

We chose to redirect our proxy on the port 8080

The -p will allow us to do port mapping so we can connect on the proxy container from the outside.



As said in part 1, the configuration files of the container can be found in the

`/etc/apache2/` folder of the container. Here we can find important folders:

`sites-available` and `mods-available` that contains respectivly all the sites and modules availables in the container. 

`sites-enable` and  `mode-enable` that contains respectively all the sites and modules enabled.



####Add a new virtual host

In `sites-available` , we can find the file 000-default.conf that gives configuration  of the default redirection

![](figures\000default.PNG)

 

In order to create a link to the new redirection, we copy the conf file into a new file with the command

`cp 000-default.conf 001-reverse-proxy.conf ` 



#### Installation of vim and configuring the new virtual host 

We want to write with the vim command in the `001-reverse-proxy.conf` file. If the command vim is not available, we have to run the commands : 

`apt-get update` followed by `apt-get install vim`

Those two commands will have to be repeated each time the docker container Docker is launched, so they are typical commands that will have to be added to the Dockerfile.

In the file `001-reverse-proxy.conf` we configure the virtual host this way: 

![](figures\apacheconf001.PNG)

where the ip address `172.17.0.3` corresponds to the server express ip address from part 1, and `172.17.0.2` corresponds to the static web page from part 1,

We still have to:

-  enable the site from the folder `/etc/apache2/` with the command:

  `a2ensite 001*` , where the * allow us to write only the beginning of the file name		

- enable the modules proxy and proxy_http with the command

  `a2enmod proxy` and `a2enmod proxy_http`



With this configuration, we should be able to connect to the server proxy with docker, (using the port 8080 in our configuration) and redirect to the html page or to the server, wether we write a GET request from the "/" route  or from the "/api/futur/" route.

But this configuration will have to be rewrited every time we restart the server proxy. To avoid that we use the Dockerfile to pre-configurate everything.

 

###Confugure the server proxy through the Dockerfile

To avoid configuration every time we launch the reverse proxy, we create a Dockerfile and a hierarcy of folders that will automate this configuration. 

#### Chose the right image

The image used for the Dockerfile is the same as the one used in part one `php:7.0-apache` 

#### Add the right folders to the project

This time, we copy a hierarchy of folders that fits the hierarchy of forlders in the initial image so that we can add the file added in the previous section (**Configure the server proxy by hand**)

the hierarchy copied is as followed: 

![](figures\hierarchyOfFiles.PNG)



We create a folder `apache-reverse-proxy` (located in the project in the`/docker-images/` directory). In this folder, we crate an other folder `/conf/` that itself will contain a folder called `sites-available`.

In the folder `sites-available ` we crate two files: `000-default.conf` and `001-reverse-proxy.conf` 

The two files contains :

000-default.conf:

```
<VirtualHost *:80>
</VirtualHost>
```

The file is used to be sure that if the host is not given when a request is made, we are not able to reach the web page or the server. 



001-reverse-proxy.conf

```
<VirtualHost *:80>
        ServerName laboinfra.res.ch

        #ErrorLog ${APACHE_LOG_DIR}/error.log
        #CustomLog ${APACHE_LOG_DIR}/access.log combined

        ProxyPass "/api/futur/" "http://172.17.0.3:80/"
        ProxyPassReverse "/api/futur/" "http://172.17.0.3:80/"

        ProxyPass "/" "http://172.17.0.2:80/"
        ProxyPassReverse "/" "172.17.0.2:80/"

</VirtualHost>
```

The file contains the configurations explained in the previous section  (**Configure the server proxy by hand**)

#### Add the commands to enable sites and modules

finally we add the call to the two commands to enable the sites and enable the modules: 

`a2enmod proxyproxy_http` and `a2ensite 000-* 001-*`  



### Link a URL with the IP address of the reverse proxy on windows 10

The last thing to do is to configure the redirection of ip addresses to be sure that if we type the URL `laboinfra.res.ch` we're redirected on the right port. 

In order to do that, on windows 10 we must: 

- open the `hosts` file in admin mode.  The file is located in `C:\Windows\System32\Drivers\etc`
- link the ip address of the proxy with the URL  (example `192.168.99.100 laboinfra.res.ch`



Now the proxy is reachable in a browser with the URL, followed by the good port (in our case 8080)

`laboinfra.res.ch:8080/` => links to the index.html page (part 1)

`laboinfra.res.cg:8080/api/futur/` => links to the servers guessing the futur (part 2) 



## Part 4 - AJAX requests with JQuery

In this part the objective is to make the html page (created in part one) send http requests to the server express in background in order to update a part of its content, without refreshing the entire page.    

### Complete the Dockerfile of part 1 - 2 - 3  (optionnal)

The Dockerfiles of part 1 and part 3 miss the command to install dependencies we have to use now. Those command are added to the Dockerfiles so can use vim to edit files in the container: 

`apt-get update && \`
`apt-get install -y vim`

By doing this we should be able to use the command vi every time we enter the terminal of the container, to modify its content.



### Call a script from the index.html page form part 1

In the html page, we can add a call to a JavaScript script. 

we added this line to the file:

```
<!-- script to load the destination -->
<script src="js/destinations.js"></script>
```

this line calls a js script located in the `content/js` folder 



### Creation of the destination.js file with JQuery

In the `/content/js` folder, we add the `destinations.js` file. 

The file contains the following code:

```
$(function(){
	console.log("Loading destinations");
	
	function loadDestinations(){
		$.getJSON("/api/futur/destination/", function( destinations){
			console.log(destinations);
			var message = "Next stop : " + destinations[0].destination + " !";
			$(".text-faded").text(message);
		}); 	
	};
	
	loadDestinations();
});
```

The '$' indicates that we use the JQuery framework. 

JQuery allow us to send requests to a server in background, using the method: 

`$.getJSON` that create a get request to the URL `/api/futur/destination` 

and that replace a part of the html page (in our case the class named `text-faded`)  with the line: 

`$(".text-faded").text(message);`. 

Here the destinations.js script will call the function loadDestinations(), hat itself calls the $.getJSON function of JQuery. This function sends a request to the server to get random destinations. 

### Repeat requests at set interval

JavaScript offer a function setInterval that call a function at a regular interval in time.

In our script we add the  line

`setInterval(loadDestinations, 2000);`

that will call the function loadDestination every 2 seconds. 



**Note:**

In order to get the destinations, we had to add a request to our index.js file (from part 2)

## Dynamic ip configuration

To dynamically configure the ip of the containers on the proxy we will use environement variables as they can be set when we start the proxy container.

We use a php template file to dynamically generate the `/etc/apache2/sites-available/001-reverse-proxy.conf` file that describes the reverse proxy configuration.

###config-template.php

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

###apache2-foreground

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

###Dockerfile

We still need to add a few mods to apache2 in the Dockerfile :

`RUN a2enmod proxy proxy_http` includes everything needed for basic http proxy.

`RUN a2ensite 000-* 001-*` will enable our configurations

###Portainer

To offer an UI to manage docker container we choosed to use Portainer.

It's free and available at : https://portainer.io

We run it with default configuration using the two commands :

```bash
#docker volume create portainer_data
docker run -d -p 9000:9000 -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer
```

The first command is only used once for installation that's why it's commented out.

As you can see this starts a magical pre-configured container further accessible at http://localhost:9000 witch provides us a nice UI to  manage containers the easy way.

##Load Balancing

The load balancing of our servers is done through a heavy modification of our `config-template.php` file :

###config-template.php

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

###Dockerfile

Plus we need to include a few mods for apache :

`RUN a2enmod proxy proxy_http proxy_balancer status lbmethod_byrequests`

you can see we use `proxy_balancer` for load balancing, `status` because god wants it and `lbmethod_byrequests` as a methode of round-robin load distribution.

##Sticky session

First we need to include new mods for apache2 server :

###Dockerfile

`RUN a2enmod proxy proxy_http proxy_balancer status lbmethod_byrequests headers`

we've added `headers` witch will allow us to work with cookies to save the BalancerMember used.

###config-template.php

Our template gained a few lines 

```php
<VirtualHost *:80>
        ServerName laboinfra.res.ch
	<Proxy "balancer://mydynamiccluster">
		BalancerMember 'http://<?php print "$dynamic_app1"?>'
		BalancerMember 'http://<?php print "$dynamic_app2"?>'
		ProxySet lbmethod=byrequests		// We still want round-robin for this one
	</Proxy>

        ProxyPass '/api/futur/' 'balancer://mydynamiccluster/'
        ProxyPassReverse '/api/futur/' 'balancer://mydynamiccluster/'

    // we write a cookie who will store the balancer route used
	Header add Set-Cookie "ROUTEID=.%{BALANCER_WORKER_ROUTE}e; path=/" env=BALANCER_ROUTE_CHANGED
	<Proxy "balancer://mystaticcluster">
		BalancerMember 'http://<?php print "$static_app1"?>' route=1
		BalancerMember 'http://<?php print "$static_app2"?>' route=2
		ProxySet stickysession=ROUTEID		// we use that cookie to keep the same route
	</Proxy>

        ProxyPass '/' 'balancer://mystaticcluster/'
        ProxyPassReverse '/' 'balancer://mystaticcluster/'
</VirtualHost>
```

You can see that for the second load balancer (for the static app) we store a cookie of the route used and specify to the proxy we want to always use the same one.



