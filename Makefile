build: 
	docker compose build
up: 
	docker compose up -d
down: 
	docker compose down
setup: 
	make build
	make up
	make composer-inst
	make init-db
	make migrate
logs:
	docker compose logs

exec-app: 
	docker compose exec app /bin/bash

exec-composer:
	docker-compose run composer bash
composer-inst:
	docker-compose run composer bash -c "composer install"

exec-db:
	docker compose exec db bash
exec-mysql:
	docker compose exec db bash -c "mysql -h localhost -uroot -p\$$MYSQL_ROOT_PASSWORD hakoniwa"
init-db:
	docker compose exec db bash -c "mysql -h localhost -uroot -p\$$MYSQL_ROOT_PASSWORD --execute 'source /tmp/init.sql'"
migrate:
	docker compose exec app php artisan migrate

	

