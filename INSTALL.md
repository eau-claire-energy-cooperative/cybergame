## Docker Install

Basic instructions for bundling this app to run as a Docker container. 

### Before you start

Review the Known Issues in the [README](README.md#known-issues) first. Change the example database passwords in `docker-compose.yml` and `private/credentials.php`, and set your LDAP/domain settings in `private/credentials.php`.

### Creating the container
To run the application within Docker you first have to build the Codebreaker app into a Docker image. 

```
docker build -t cybergame .
```

This will build the web app container and add it to your container registry. Any changes made to the source files will require rebuilding the container to take effect. 

### Running the app

Codebreaker should be run using `docker compose` as this will start both the database and web application. The game can be accessed at http://server_ip:7000/. To change the default port edit the `docker-compose.yml` file. 

```
# start the containers
sudo docker compose up -d
```

__Note:__ When the database container is first created it will initialize the database. If you subsequently remove the container and restart it, the database will be recreated. To stop this you can remove the volume mounting from the `docker-compose.yml` file. 

### Stopping the app

To stop the application use: `docker compose stop`. This will stop the containers but persist the database. If you want to remove the containers use `docker compose down`. 
