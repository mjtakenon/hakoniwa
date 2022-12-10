build: 
	docker compose build
up: 
	docker compose up -d
down: 
	docker compose down
exec-app: 
	docker compose exec app /bin/bash
setup: 
	make build
	make up