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

`docker inspect <id de votre container> | gerp "IPAddress"` 



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

#### Installation d'express js 

Pour l'installation du module express.js, on utilise la commande 

`npm install express --save` , où le --save permet de sauver le module dans le projet 

**Installation optionnelle**

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



## Partie 3

















