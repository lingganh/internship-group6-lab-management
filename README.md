## Requirements

- PHP >= 8.3.0

## Usage

1. Clone project
2. Create .env file, copy content from .env.example to .env file and config your database in .env:

``` bash
    APP_DEBUG=false
    APP_URL=domain
	
	DB_CONNECTION=mysql
	DB_HOST=database_server_ip
	DB_PORT=3306
	DB_DATABASE=database_name
	DB_USERNAME=username
	DB_PASSWORD=password

	REVERB_HOST=127.0.0.1

```

3. Run

``` bash
	 composer install
	 php artisan key:generate
	 php artisan migrate
	 php artisan db:seed --class=DatabaseSeeder
	 php artisan storage:link
	 php artisan route:clear
	 php artisan config:clear
	
	
	 npm install

```

4. Local development server

- Run

``` bash
back-end
	# 2 terminal
	 php artisan serve
	 php artisan reverb:start 
	
- Login with default admin account : admin and password: 123456aA@
