build: 
	docker compose build
up: 
	docker compose up -d
down: 
	docker compose down
setup: 
	make build
	make up
	make create-log-file
	make composer-install
	make init-db
	make init-db-testing
	make migrate
	make seeding
	make ide-helper-generate
	make yarn-install
	make yarn-run-dev
start:
	make up
	make migrate
	make seeding
	make yarn-install
	make yarn-run-dev
logs:
	docker compose logs
logs-watch:
	docker compose logs -f --tail 100

exec-app: 
	docker compose exec --user www-data app /bin/bash
migrate:
	docker compose exec --user www-data app php artisan migrate
migrate-rollback:
	docker compose exec --user www-data app php artisan migrate:rollback
migrate-testing:
	docker compose exec --user www-data app php artisan migrate --env=testing
seeding:
	docker compose exec --user www-data app php artisan db:seed
seeding-testing:
	docker compose exec --user www-data app php artisan db:seed --env=testing
ide-helper-generate:
	docker compose exec --user debian app sudo php artisan ide-helper:generate
	docker compose exec --user debian app sudo php artisan ide-helper:model --write-mixin
er-diagram-generate:
	docker compose exec --user www-data app php artisan generate:erd er_diagram.png
create-log-file:
	docker compose exec --user debian app sudo chown www-data:www-data /app/storage/ -R
	docker compose exec --user debian app sudo chmod 777 /app/storage/ -R
	docker compose exec --user debian app sudo chmod 777 /app/bootstrap/cache/ -R
	docker compose exec --user debian app sudo touch /app/.phpunit.result.cache
	docker compose exec --user debian app sudo chmod 777 .phpunit.result.cache
next-turn:
	@for i in $$(seq 1 $(TIMES)); do \
		docker compose exec --user www-data app php artisan execute:turn; \
	done

exec-composer:
	docker compose run --user debian composer bash
composer-install:
	docker compose run --user debian composer bash -c "composer install"

exec-db:
	docker compose exec db bash
exec-mysql:
	docker compose exec db bash -c "mysql -h localhost -u\$$MYSQL_USER -D \$$MYSQL_DATABASE -p\$$MYSQL_PASSWORD"
init-db:
	docker compose exec db bash -c "mysql -h localhost -uroot -p\$$MYSQL_ROOT_PASSWORD --execute 'source /tmp/init.sql'"
	docker compose exec app bash -c "echo "" > /app/database/sqlite/database.sqlite && chmod 755 /app/database/sqlite/ -R && chown www-data:www-data /app/database/sqlite/ -R"

exec-db-testing:
	docker compose exec db-testing bash
exec-mysql-testing:
	docker compose exec db-testing bash -c "mysql -h localhost -u\$$MYSQL_USER -D \$$MYSQL_DATABASE -p\$$MYSQL_PASSWORD"
init-db-testing:
	docker compose exec db-testing bash -c "mysql -h localhost -uroot -p\$$MYSQL_ROOT_PASSWORD --execute 'source /tmp/init.sql'"

exec-frontend: 
	docker compose exec frontend bash
yarn-install:
	docker compose exec frontend bash -c "yarn install --frozen-lockfile"
	docker compose exec frontend bash -c "chown node:node /app/node_modules -R"
	docker compose cp app:/app/node_modules ./app
yarn-run-dev:
	docker compose exec frontend bash -c "yarn run dev"

testing:
	docker compose exec --user www-data app php artisan test tests/App