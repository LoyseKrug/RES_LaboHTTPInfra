# RES - Labo HTTPInfra



## Partie 1

###Création  du Container Docker

Le container docker est crée à partir d'une image php trouvée sur le site dockeHub https://hub.docker.com/_/php/ 

L'image "php:7.0-apache" offre un serveur apache permettant permettant de servir des pages php en version 7.0 du language. 

Le DockerFile copie tous les fichiers du dossier `./content` dans le dossier `/var/www/html` du container, où se trouve les sites webs que nous voulons héberger.

Pour créer l'image docker utiliser la commande 

### Demarrage du container Docker

Pour créer l'image docker, lancer la commande: 

`docker build res_apache_php .` depuis le répertoire contenant votre dockerfile

Pour lancer le container il aut utiliser la commande:

`docker run -p <votre port>:80 res_apache_php` où `votre port` est le port de votre choix sur lequel vous pourrez accéder a votre page depuis l'extérieur du container.

Pour nos exemples nous allons utiliser le port `9090`. 

### Configuration du container Docker

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





