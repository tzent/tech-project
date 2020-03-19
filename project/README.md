# Technical test

Requirements:
- `docker` /If you don't have installed docker on your machine, use google to find how to do it :D/;

Following steps:
- clone or download the source code;
- open the terminal and go to folder `tech-project/environment`;
- execute `docker-compose up -d --build` /this will create needed container in your docker/;
- when the script is finished execute `docker exec tech-project composer install` /this will install needed libraries in the container/;
- to run the app, execute from your terminal `docker exec tech-project composer run exec-app`;
- to run the tests, execute from your terminal `docker exec tech-project composer run phpunit`;
- to stop the container, execute `docker-compose down`;

That's it. Enjoy! :D


