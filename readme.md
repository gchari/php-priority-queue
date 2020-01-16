# Gle Test: Priority Queues: PHP Implementation
# Prerequisite:
Docker # https://docs.docker.com/install/
Connection to internet.

# Instructions to Run the app:
Download the attached files into a folder.

```sh
$ cd tothedir
$ docker-compose up -d --scale app=3 --scale batch=3
$ docker-compose down
```

Wait for few mins till all the services are up.

```sh
$ docker ps
```

Run the postman collection available in the folder to add and get the tasks. Once you add a task the processors will pick it up. 

See their status got changed by calling 'get/task/id' api.

Processor:  “./batch/run.log” file get populated with the commands that you send from the postman collection “add”. Does not execute them.


> Bug:
> The URL is little off because of an apache configuration issue. It was taking longer than I thought. So I focused on PHP instead.

# Goal:
Was to demonstrate strong fundamentals in the following areas:
- Ability to build PHP systems from ground up.
- Ability to create micro services and docker containers.
- Building scalable applications.
- Use of Caching.
- Knowledge of good design principles.

# Code:
(That I added)
- .docker/*
- app/Http/Controller/QueueController.php
- app/Http/Response
- batch/*
- routes/api.php
- database/migrations/*

# Clean up:
```sh
$ docker-compose down
```
