# Define variables
DOCKER_COMPOSE = docker compose
SERVICE_APP = app
SERVICE_WEB = nginx
SERVICE_DB = db
SERVICE_ADMINER = adminer

# Default target: Build and start the containers
.PHONY: all
all: build up

# Build the Docker images
.PHONY: build
build:
	$(DOCKER_COMPOSE) build

# Start the Docker containers
.PHONY: start
start:
	$(DOCKER_COMPOSE) up -d

# Restart the Docker containers
.PHONY: restart
restart: down build up

# Stop the Docker containers
.PHONY: stop
stop:
	$(DOCKER_COMPOSE) down

# Stop and remove containers, networks, and volumes
.PHONY: clean
clean:
	$(DOCKER_COMPOSE) down --volumes --remove-orphans


# Run tests inside the app container
.PHONY: test
test:
	$(DOCKER_COMPOSE) exec $(SERVICE_APP) php bin/phpunit

# Open a shell inside the app container
.PHONY: in
in:
	$(DOCKER_COMPOSE) exec $(SERVICE_APP) /bin/sh

# Run database migrations inside the app container
.PHONY: migrate
migrate:
	$(DOCKER_COMPOSE) exec $(SERVICE_APP) php bin/console doctrine:migrations:migrate

# Example target to restart a single service
.PHONY: restart-service
restart-service:
	$(DOCKER_COMPOSE) restart $(SERVICE_APP)
