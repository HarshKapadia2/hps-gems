# Welcome, contributors! :wave:

## Local Setup

- Install [XAMPP](https://www.apachefriends.org/download.html), [WAMP](https://wampserver.aviatechno.net/) or a LAMP stack.
- Create a database. (Schema given below.)
- Create a local environment file in the root directory of the project (`.htaccess` for XAMPP) (don't forget to add the file to `.gitignore`) and add the following environment variables to it
  - `SetEnv DB_HOST xxxx`
  - `SetEnv DB_NAME xxxx`
  - `SetEnv DB_USERNAME xxxx`
  - `SetEnv DB_PASSWORD xxxx`
- Run the Apache and MySQL services and access the site at `localhost/<project_folder_name>`. PHPMyAdmin can be accessed at `localhost/phpmyadmin`.

## Schema

![](server/schema.png)
