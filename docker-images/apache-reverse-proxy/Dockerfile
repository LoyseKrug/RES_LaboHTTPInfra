FROM php:7.0-apache

RUN apt-get update && \
apt-get install -y vim

#on copie notre version de apache2-foreground dans le container 
COPY apache2-foreground /usr/local/bin/
COPY template /var/apache2/template
COPY conf/ /etc/apache2/

# démarrer les modules du proxy balancer
RUN a2enmod proxy proxy_http proxy_balancer status lbmethod_byrequests headers
RUN a2ensite 000-* 001-*
