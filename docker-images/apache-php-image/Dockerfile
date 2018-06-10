#Creates a layer from the php:7.0-apache Docker image
FROM php:7.0-apache

RUN apt-get update && \
apt-get install -y vim

#this line copy the content of the "content" folder in the target folder "/var/www/html"
COPY content/ /var/www/html/