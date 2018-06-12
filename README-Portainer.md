# Readme - Portainer

To offer an UI to manage docker container we choosed to use Portainer.

It's free and available at : https://portainer.io

We run it with default configuration using the two commands :

```bash
#docker volume create portainer_data
docker run -d -p 9000:9000 -v /var/run/docker.sock:/var/run/docker.sock -v portainer_data:/data portainer/portainer
```

The first command is only used once for installation that's why it's commented out.

As you can see this starts a magical pre-configured container further accessible at http://localhost:9000 witch provides us a nice UI to  manage containers the easy way.