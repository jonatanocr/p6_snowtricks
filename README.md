## Snowtrick

Snowtrick is a snowboard tricks open library based on Symfony library.

## Installation

### 1. project's requirements

symfony cli, composer

### 2. clone the project
```
cd your_project_dir
git clone https://github.com/jonatanocr/p6_snowtricks.git
```
### 3. Configuration and dependencies
```
cd p6_snowtricks
# edit .env.exemple to .env file to match your configuration
# to configure the email, you can follow this documentation https://symfony.com/doc/current/mailer.html#installation

# make Composer install the project's dependencies into vendor/
composer install
```

### 4. Set the database
```
# Create the database (if needed): 
php bin/console doctrine:database:create
# Add database schema: 
php bin/console doctrine:migrations:migrate
#(optional) if you have some trouble to load the tables (error: There is no active transaction) you can fix it with updating doctrine bundle
composer update doctrine/migrations && composer require doctrine/doctrine-migrations-bundle:^3.1.1
# You can load the fixtures (2 users are already created to test the website, username1 and username2 with 'password' as password)
php bin/console doctrine:fixtures:load
```

### 5. Yarn
```
yarn install --force
yarn encore production
```

### 6. Enjoy
```
symfony server:start
```
Open your browser and navigate to http://localhost:8000/

## License
[MIT](https://choosealicense.com/licenses/mit/)