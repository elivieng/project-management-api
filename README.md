Project Setup Guide

Prerequisites
Ensure you have Docker installed (Docker Desktop for Windows) and that WSL 2 is enabled and running correctly.

Setup Steps
1. Clone the Project
Run the following command to clone the project repository:

git clone https://github.com/elivieng/project-management-api.git

2. Navigate to the Project Directory
In your local environment, open a terminal and go to the project directory:

cd projectName

3. Configure Docker Files
We need to set up the Dockerfile and docker-compose.yml files.
In the docker-compose.yml file, modify the following line:

volumes:
      - path_to_your_repo_directory/project_management_api:/var/www/html

4. Create the .env File
For security reasons, .env files are usually not included in version control.
Create a copy of .env.example and rename it to .env:

cp .env.example .env

Now, edit the .env file and update the database configuration:

DB_CONNECTION=mysql
DB_HOST=mysql_db
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=password

Note:

DB_HOST=mysql_db → mysql_db should match the container_name assigned to the database service in docker-compose.yml.
DB_PASSWORD should match the MYSQL_ROOT_PASSWORD value in docker-compose.yml.

5. Build and Start the Containers
Run the following command inside the Laravel project:

docker-compose up -d --build

This will:
    Build the Laravel image with PHP 8.1.
    Start the Laravel and MySQL containers.

To check running containers, use:

docker ps

6. Install Laravel Dependencies Inside the Container
Run the following command to install Laravel dependencies:

docker exec -it laravel_app composer install

7. Generate the Application Encryption Key
Laravel requires an encryption key to function properly.
If the .env file does not contain a value for APP_KEY, generate it by running:

docker exec -it laravel_app php artisan key:generate

8. Create an Empty Database
Manually create a new database named project_management in MySQL.

9. Run Database Migrations
To create the database structure as described in Laravel migrations, run:

docker exec -it laravel_app php artisan migrate
