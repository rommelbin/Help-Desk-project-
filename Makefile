generate-dot-env-file:
	test -f $(DOT_ENV_FILE_PATH) || cp "$(DOT_ENV_EXAMPLE_FILE_PATH)" "$(DOT_ENV_FILE_PATH)"

up/build:
	docker-compose build --progress plain
up:
	docker-compose up
up/migrate/backend:
	docker-compose run --rm backend sh -c "/wait && php artisan migrate --force --seed"
up/migrate/fresh/backend:
	docker-compose run --rm backend sh -c "/wait && php artisan migrate:fresh --force --seed"

DOCKER_COMPOSER := docker run --rm --interactive --tty --workdir /app --user $(shell id -u):$(shell id -g)
COMPOSER_COMMAND := composer --ignore-platform-reqs

composer-install/main-service: generate-dot-env-file
	${DOCKER_COMPOSER} --volume $(shell pwd)/backend:/app ${COMPOSER_COMMAND} install
composer-update/main-service: generate-dot-env-file
	${DOCKER_COMPOSER} --volume ${shell pwd}/backend:/app ${COMPOSER_COMMAND} update

start/server: up/migrate/backend up