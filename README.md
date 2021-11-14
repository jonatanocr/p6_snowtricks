## Snowtrick

Snowtrick is a snowboard tricks open library based on Symfony library.

## Installation

#### 1. project's requirements

symfony cli, composer

#### 2. clone the project
```
cd your_project_dir
git clone https://github.com/jonatanocr/p6_snowtricks.git
```
#### 3. Configuration and dependencies
```
cd p6_snowtricks
# make Composer install the project's dependencies into vendor/
composer install
```
edit .env file DATABASE_URL= to match your local configuration

#### 4. Set the database
```
# Create the database (if needed): 
php bin/console doctrine:database:create
# Add database schema: 
php bin/console doctrine:migrations:migrate
```

#### 5. Enjoy
```
symfony server:start
```
Open your browser and navigate to http://localhost:8000/

## License
[MIT](https://choosealicense.com/licenses/mit/)