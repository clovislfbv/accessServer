# accessServer

a web server to easily send files to your remote server from anywhere via ssh

## Features implemented
- logging via password (recommended)
- logging via ssh key (not recommended)
- change directory when clicking on its name
- download file when clicking on its name
- view a file when clicking on the button with an eye next to it
- delete a file or folder when clicking on the bin next to it
- share a file or a directory via the share icon next to it
- page that let the user see the temporarly files downloaded on the website's server
- let the user whether he wants to add more or less time for the temporarily files downloaded on the website's server
- git pull a git repo
- git push a git repo

## Future wanted features
- delete several files at the same time
- why not adapt the website into a mobile application ?

## How to use it locally ?
First you need to create a .env file at the root of the project with this content :
```
# WEBSITE SETTINGS
port=YOUR_HTTP_PORT
port2=YOUR_HTTPS_PORT

# PHPMYADMIN SETTINGS
port_phpmyadmin=YOUR_PHPMYADMIN_PORT
root_pswd=YOUR_ROOT_PASSWORD
username=YOUR_USERNAME
pswd=YOUR_USER_PASSWORD

# DATABASE SETTINGS
port_db=YOUR_DATABASE_PORT_LOCALLY
port_db2=YOUR_DATABASE_PORT_ON_DOCKER
db_name=YOUR_DATABASE_NAME
```

When this file is created, you just need to use this command :
```
sudo docker-compose up
```

It will build and run everything necessary for the website.

When everything is done, you can access the website with this link on your browser :
[http://localhost:YOUR_HTTP_PORT_CHOSEN](http://localhost:YOUR_PORT_CHOSEN)