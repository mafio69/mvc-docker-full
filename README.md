Tested on ubuntu 20.04
To run on the system, docker and docker-compose must be installed  
1.[install docker](https://docs.docker.com/compose/install)  
2.[install docker-compose](https://docs.docker.com/compose/install)  

##  Commands:
    - "docker-compose build" (first command)  
    - "docker-compose up" (second command to start docker and after building the container  
    - "docker ps" (list of current containers)  
    - "docker exec -it mfMVC bash" (entering the container command line (the name of mfMVC can be changed before running in docker-compose.yml))  
           Note: All commands are executed in the directory where docker-compose.yml is located
## Links:
    phpadmin  http://localhost:8090/  
    app http://localhost:8050/  

# Build with:
php:7.3-apache-stretch  
https://github.com/docker-library/repo-info/blob/master/repos/php/remote/7.3-apache-stretch.md  
mariadb  
https://registry.hub.docker.com/_/mariadb  
adminer  
https://registry.hub.docker.com/_/admine

### He put together
[mafio69](mailto:mf1969@gmail.com?subject=[GitHub]%20Docker%20Repo)
