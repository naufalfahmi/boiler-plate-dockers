# ---------------------------------------------------------
# Makefile untuk Project Structure
# Shortcut perintah yang sering dipakai
# ---------------------------------------------------------

.PHONY: help setup build up down logs sh-php sh-mysql sh-redis sh-nginx issue-cert renew-cert test

help:
	@echo "Available commands:"
	@echo "  setup     - Setup semua project (copy .env files)"
	@echo "  build     - Build semua Docker images"
	@echo "  up        - Start semua services"
	@echo "  down      - Stop semua services"
	@echo "  logs      - Show logs"
	@echo "  sh-php    - Shell ke PHP container"
	@echo "  sh-mysql  - Shell ke MySQL container"
	@echo "  sh-redis  - Shell ke Redis container"
	@echo "  sh-nginx  - Shell ke Nginx container"
	@echo "  issue-cert- Issue Let's Encrypt certificate"
	@echo "  renew-cert- Renew Let's Encrypt certificate"
	@echo "  test      - Test semua connections"

setup:
	@echo "Setting up projects..."
	@cp projects/app-bosowa/env.example projects/app-bosowa/.env
	@cp projects/app-lain/env.example projects/app-lain/.env
	@echo "✅ Setup completed!"

build:
	@echo "Building Docker images..."
	@cd shared && docker compose build
	@cd projects/app-bosowa && docker compose build
	@cd projects/app-lain && docker compose build
	@echo "✅ Build completed!"

up:
	@echo "Starting services..."
	@cd shared && docker compose up -d
	@cd projects/app-bosowa && docker compose up -d
	@cd projects/app-lain && docker compose up -d
	@echo "✅ Services started!"

down:
	@echo "Stopping services..."
	@cd shared && docker compose down
	@cd projects/app-bosowa && docker compose down
	@cd projects/app-lain && docker compose down
	@echo "✅ Services stopped!"

logs:
	@echo "Showing logs (Ctrl+C to exit)..."
	@docker compose -f shared/docker-compose.yml -f projects/app-bosowa/docker-compose.yml -f projects/app-lain/docker-compose.yml logs -f

sh-php:
	@echo "Choose PHP container:"
	@echo "1. app-bosowa-php"
	@echo "2. app-lain-php"
	@read -p "Enter choice (1-2): " choice; \
	if [ "$$choice" = "1" ]; then \
		docker exec -it app-bosowa-php bash; \
	elif [ "$$choice" = "2" ]; then \
		docker exec -it app-lain-php bash; \
	else \
		echo "Invalid choice"; \
	fi

sh-mysql:
	@echo "Choose MySQL container:"
	@echo "1. app-bosowa-mysql"
	@echo "2. app-lain-mysql"
	@read -p "Enter choice (1-2): " choice; \
	if [ "$$choice" = "1" ]; then \
		docker exec -it app-bosowa-mysql bash; \
	elif [ "$$choice" = "2" ]; then \
		docker exec -it app-lain-mysql bash; \
	else \
		echo "Invalid choice"; \
	fi

sh-redis:
	docker exec -it shared-redis sh

sh-nginx:
	docker exec -it shared-nginx sh

issue-cert:
	@echo "Issuing Let's Encrypt certificates..."
	@cd shared && docker compose run --rm certbot certbot certonly --webroot -w /var/www/certbot \
		-d app-bosowa.localhost --email admin@app-bosowa.localhost --agree-tos --no-eff-email
	@cd shared && docker compose run --rm certbot certbot certonly --webroot -w /var/www/certbot \
		-d app-lain.localhost --email admin@app-lain.localhost --agree-tos --no-eff-email
	@cd shared && docker compose exec shared-nginx nginx -s reload
	@echo "✅ Certificates issued!"

renew-cert:
	@echo "Renewing Let's Encrypt certificates..."
	@cd shared && docker compose run --rm certbot certbot renew --webroot -w /var/www/certbot --quiet
	@cd shared && docker compose exec shared-nginx nginx -s reload
	@echo "✅ Certificates renewed!"

test:
	@echo "Testing connections..."
	@echo "Testing Redis..."
	@docker exec shared-redis redis-cli ping
	@echo "Testing App Bosowa MySQL..."
	@docker exec app-bosowa-mysql mysqladmin ping -h 127.0.0.1 -uapp_bosowa -psupersecretuser --silent
	@echo "Testing App Lain MySQL..."
	@docker exec app-lain-mysql mysqladmin ping -h 127.0.0.1 -uapp_lain -psupersecretuser --silent
	@echo "✅ All tests passed!"

clean:
	@echo "Cleaning up..."
	@cd shared && docker compose down -v
	@cd projects/app-bosowa && docker compose down -v
	@cd projects/app-lain && docker compose down -v
	@docker system prune -a -f
	@rm -rf shared/certbot shared/letsencrypt
	@rm -rf projects/*/data
	@echo "✅ Cleanup completed!"
