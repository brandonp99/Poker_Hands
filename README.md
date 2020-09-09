# Poker_Hands

Steps to set up project

1.  clone git repo to local system using https://github.com/brandonp99/Poker_Hands.git
2.  run "docker network create local"
3.  run "docker run --network local -p 3306:3306 --name pokerhandsDB -e MYSQL_ROOT_PASSWORD=root -d mariadb:latest" to spin up a MariaDB docker instance
4.  access database via an SQL workbench using "127.0.0.1" as the host, "root" for the username, and "root" for the password
5.  create a new Database named "pokerhands"
6.  import pokerhands_2020-09-08.sql from the /sql_dump folder
7.  cd into the project file you cloned earlier and run "composer install" to install any needed dependencies
8.  run "symfony server:start" to spin up the server
9.  Once the server is started move to your browser and load your localhost on http://127.0.0.1:8000
10.  login using "root@local.local" for the email and "root" for the password
11.  upload the txt file and view the answers given
