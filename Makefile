help:
	@awk 'BEGIN {FS = ":.*#"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?#/ { printf "  \033[36m%-27s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

test: ## run tests in local environment
	php tests/bootstrap.php

composer: ## install composer dependencies in local environment
	composer install

shell: ## connect to docker shell
	docker compose -f .docker/docker-compose.yml run --rm -it app bash

docker-test: ## run tests in docker environment
	docker compose -f .docker/docker-compose.yml run --rm app make test

docker-composer: ## run tests in docker environment
	docker compose -f .docker/docker-compose.yml run --rm app make composer
