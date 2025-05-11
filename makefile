.PHONY: up down build restart logs composer

up:
	docker compose up --build -d

down:
	docker compose down --remove-orphans

build:
	docker compose build

restart:
	docker compose down --remove-orphans
	docker compose up --build -d

logs:
	docker compose logs -f --tail=100

composer:
	docker compose run --rm app1 composer install