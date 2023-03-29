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
	make ide-helper-gen
	make yarn-inst
	make yarn-run-dev
start:
	make up
	make migrate
	make yarn-inst
	make yarn-run-dev
logs:
	docker compose logs
logs-watch:
	docker compose logs -f

exec-app: 
	docker compose exec app /bin/bash
migrate:
	docker compose exec app php artisan migrate
ide-helper-generate:
	docker compose exec app php artisan ide-helper:generate
	docker compose exec app php artisan ide-helper:model

exec-composer:
	docker compose run composer bash
composer-inst:
	docker compose run composer bash -c "composer install"

exec-db:
	docker compose exec db bash
exec-mysql:
	docker compose exec db bash -c "mysql -h localhost -u\$$MYSQL_USER -D \$$MYSQL_DATABASE -p\$$MYSQL_PASSWORD"
init-db:
	docker compose exec db bash -c "mysql -h localhost -uroot -p\$$MYSQL_ROOT_PASSWORD --execute 'source /tmp/init.sql'"

exec-frontend: 
	docker compose exec frontend bash
yarn-inst:
	docker compose exec frontend bash -c "yarn install --frozen-lockfile"
yarn-run-dev:
	docker compose exec frontend bash -c "yarn run dev"
