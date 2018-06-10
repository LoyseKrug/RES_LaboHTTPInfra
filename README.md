# RES - Labo HTTPInfra



## Part 1 - Static HTTP server with apache httpd

###Creating a Docker container

#### Creatie a Dockerfile with php image 

The Docker container is created from a php image found on docker hub: https://hub.docker.com/_/php/ 

The image "php:7.0-apache" offer a apache server that can serve php web pages, using 7.0 version of the language.

The DockerFile copies all the files and folders located in the local `./content` folder of the project in the `/var/www/html` folder of the container, where are hosted all out web sites.

#### Demarrage du container Docker

Pour créer l'image docker, lancer la commande: 

`docker build res_apache_php .` depuis le répertoire contenant votre dockerfile

Pour lancer le container il aut utiliser la commande:

`docker run -p <votre port>:80 res_apache_php` où `votre port` est le port de votre choix sur lequel vous pourrez accéder a votre page depuis l'extérieur du container.

Pour nos exemples nous allons utiliser le port `9090`. 

#### Configuration du container Docker

Pour configurer le container docker il faut qu'il soit démarré (voir **Demarrage du container docker** ci-dessus). Une fois démarré, il faut se connecter dessus avec la commande `docker exec -it <id de votre container> /bin/bash` qui va démarrer un terminal accessible via votre console à l'intérieur de celui-ci.

Pour connaitre l'adresse ip du container, il suffit d'executer la commande 

`docker inspect <id de votre container> | gerp -i ipaddress` 



#### Configuration des virtual host

Le fichier de configuration de virtual host, appelé `000-default.cong` se trouve dans le dossier `/etc/apache2/sites-available/`

à l'iintérieur de celui-ci, vous pouvez configurer un ou plusieurs serveurs qui répondront à différents ports. 

### Contenu de la page web

Dans le dossier `./content` en local, on inclu un fichier `index.html`, dans lequel se trouvera le contenu de la page web. 

#### Utilisation d'un template bootstrap

La page index.html peut être remplie, mais sans javascript ou css, son aspect reste très pauvre. 

Pour pouvoir facilement obtenir une bonne présentation de la page, il est possible d'utiliser un template mis à disposition sur le web.

Le template de ce site est basé sur un template du site https://startbootstrap.com/ , thème `Creative`. 

Il est possible de récupérer le template tout forme d'un dossier compresser. 

Pour appliquer le template au site: 

Copier le contenu du fichier téléchargé dans le dossier `./content/` du projet (en local). Le template contient lui-même un fichier index.html qui'il est ensuite possible de modifier pour adapter la page woeb au gré de votre imagination. 



## Partie 2

### Création du container Docker

#### Création du Dockerfile pour une image node

Le container docker est crée à partir d'une image php trouvée sur le site dockeHub https://hub.docker.com/_/node/

L'image node:8.11.2 permet de démarrer un serveur node js et d'avoir accès à la commande npm qui permet de gérer les package que nous allons utiliser 

Le DockerFile copie tous les fichiers du dossier `src` dans le dossier `/opt/app` du container, où se trouve les fichiers js.

On utilise la commande CMD pour lancer le fichier js avec la ligne 

`CMD ["node", "/opt/app/index.js"]`

 Pour le lancement, se référer à  **Demarrage du container docker**. 



### Création d'un serveur js devinant votre avenir avec Express js et Chance js

Le container docker veux lancer le l'application index.js, il s'agit maintenant de créer ce fichier. 

L'objectif est de créer une application qui renvoie l'avenir sous forme d'un tableau JSON 

Pour ce faire, nous utilisons le framework express js

#### Use of nmp init to keep trace of the dependencies

In order for Docker to know which dependencies it will have to install in ordre to make the server run, we will create a json package that contains all the informations on thoses dependencies. Il order to do that, we use the command

`npm init` that will the ask you a list of questions in order to initiate the json package.

#### Installation d'express js 

Pour l'installation du module express.js, on utilise la commande 

`npm install express --save` , où le --save permet de sauver le module dans le projet 

***Installation optionnelle***

Il est aussi possible d'installer express-generator  avec la commande 

`npm install express-generator -g` , où -g signifie qu'express est installé en global  sur la machine. 

Express-generator n'est pas utilisé pour ce projet, mais il permet de générer des commandes avec express pour créer des modules séparés à ajouter au projet.  



#### Installation de chance.js

Pour l'installation du module chance, on utilise la commande

`npm install --save chance` 



#### Configuration du serveur Express

Dans notre configuration, nous avons défini le port d'écoute du serveur express, comme étant le port 80.

Pour définir le port d'écoute du serveur, utiliser la méthode suivante dans le fichier lançant le serveur:

`app.listen(<votre port>, function(){ ... });`  

Il est possible de créer des requêtes en fonction de la route transmise dans l'url. Par exemple, il est possible de créer une requête GET depuis la route '/', avec la fonction: 

```
app.get('/', function(req,res){
   <contenu de votre requête> 
}).
```

Il est également possible de déterminer une autre route (par exemple '/test') de cette manière:

```
app.get('/test', function(req,res){
   <contenu de votre requête> 
}).
```

Pour le projet, nous écoutons la route "root" `'/'`



## Part 3 Reverse proxy with apache (static configuration)

The idea of this part is to create a Docker container that will be a point of  redirection to the two services previously created (part 1 and 2). 

In this part, the redirection will be hard coded (typing the ip of the two Docker containters staticly) which is a dangerous bad practice, but which is important to go through in order to understand the mechanisms of a reverse proxy. 

We will create a reverse proxy server we can access using the url  `laboinfra.res.ch`



### Getting the ip adresses of the containers

In odrer to create our reverse proxy, we need to know the ip address of the two services (the html page from part 1 and the server form part 2). 

If the two services are not already running, we must start them with the commands:

`docker run -d --name apache_static res_apache_php`  

`docker run -d --name express_dynamic res_express_js`

where -d make the server turn in background and --name allow you to name our container

Now that the Containers are running we can get their ip addresss with the command: 

`docker inspect <name of the container> | grep -i ipaddress `

***This is where that manupulation in dangerous!***

We save the current IP Address of the container to use in in an other container. But if we decided to restart our container, we could not be sure it has the same address as before, and we would have to check the address again and update the reverse proxy container...



### Configure the server proxy by hand

In order to configure our reverse proxy so it can reache the html page created in part 1, we have to run a docker contsainer, directly  ont the `php:7.0-apache` image in interactive mode, with the command

`docker run -it -p <redirection port>:80 php:7.0-apache /bin/bash`

We chose to redirect our proxy on the port 8080

The -p will allow us to do port mapping so we can reache the container.



As said in part 1, the configurations files of the container can be found in the

`/etc/apache2/` folder. Here we can find important folders:

`sites-available` and `mods-available` that contains respectivly all the sites and modules availables in the container. 

`sites-enable` and  `mode-enable` that contains respectively all the sites and modules enabled



In `sites-available` , we can find the file 000-default.conf that gives configuration  of the default redirection

![](figures\000default.PNG)

 

In order to create a link to the new redirection, we copy the conf file into a new file with the command

`cp 000-default.conf 001-reverse-proxy.conf ` 

We want to write with the vim command in the `001-reverse-proxy.conf` file. If the command vim is not available, we have to run the commands : 

`apt-get update` followed by `apt-get install vim`

Those two commands will have to be repeated each time the docker container Docker is launched, so they are typical commands that will have to be added to the Dockerfile.

In the file `001-reverse-proxy.conf` we configure the virtual host this way: 

![](figures\apacheconf001.PNG)



We still have to:

-  enable the site from the folder `/etc/apache2/` with the command:

  `a2ensite 001*` , where the * allow us to write only the beginning of the file name		

- enable the modules proxy and proxy_http with the command

  `a2enmod proxy` and `a2enmod proxy_http`



With this configuration, we should be able to connect to the server proxy with docker, using the port 8080 and redirect to the html page or to the server, wether we write a GET request from the "/" route  or from the "/api/futur/" route.

Nut this configuration will have to be rewrited every time we restart the server proxy. To avoid that we use the Dockerfile to pre-configurate everything.

 

###Confugure the server proxy through the Dockerfile

To avoid configuration every time we launch the reverse proxy, we create a Dockerfile and a hierarcy of folders that will automate this configuration. 



The image used for the Dockerfile is the same as the one used in part one `php:7.0-apache` 

This time, we copy a hierarchy of folders that fits the hierarchy of forlders in the initial image so that we can add the file added in the previous section (**Configure the server proxy by hand**)

the hierarchy copied is as followed: 

![](figures\hierarchyOfFiles.PNG)



The folder `sites-available ` contains two files: `000-default.conf` and `001-reverse-proxy.conf` 

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



## Part 4 AJAX requests with JQuery

### Complete the Dockerfile of part 1

The Dockerfiles of part 1 and part 3 miss the command to install dependencies we have to use now. Those command are add to the Dockerfiles so can use vim to edit files in the container: 

`apt-get update && \`
`apt-get install -y vim`

 













