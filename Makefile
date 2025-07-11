COMMIT := $(shell git rev-parse --short=8 HEAD)
BASE_IMAGE := bmltenabled/bmlt-server-base
BASE_IMAGE_TAG := 8.2
BASE_IMAGE_BUILD_TAG := $(COMMIT)-$(shell date +%s)
CROUTON_JS := src/public/client_interface/html/croutonjs/crouton.js
SEMANTIC_HTML := src/public/semantic/index.html
LEGACY_STATIC_FILES := src/public/local_server/styles.css
VENDOR_AUTOLOAD := src/vendor/autoload.php
NODE_MODULES := src/node_modules/.package-lock.json
FRONTEND := src/public/build/manifest.json
ZIP_FILE := build/bmlt-server.zip
EXTRA_DOCKER_COMPOSE_ARGS :=
ifeq ($(CI)x, x)
	DOCKERFILE := Dockerfile-debug
	IMAGE := bmltserver
	TAG := local
	COMPOSER_ARGS :=
	NPM_FLAG := install
	COMPOSER_PREFIX := docker run --pull=always -t --rm -v '$(shell pwd)':/code -w /code $(BASE_IMAGE):$(BASE_IMAGE_TAG)
	LINT_PREFIX := docker run -t --rm -v '$(shell pwd)':/code -w /code/src $(IMAGE):$(TAG)
	TEST_PREFIX := docker run -e XDEBUG_MODE=coverage,debug -t --rm -v '$(shell pwd)/src:/var/www/html/main_server' -v '$(shell pwd)/docker/test-auto-config.inc.php:/var/www/html/auto-config.inc.php' -w /var/www/html/main_server --network host $(IMAGE):$(TAG)
	ifneq (,$(wildcard docker/docker-compose.dev.yml))
		EXTRA_DOCKER_COMPOSE_ARGS := -f docker/docker-compose.dev.yml
	endif
else
	DOCKERFILE := Dockerfile
	IMAGE := bmltenabled/bmlt-server
	TAG := 3.0.0-$(COMMIT)
	ifeq ($(strip $(GITHUB_REF_NAME)),main)
		TAG := latest
	endif
	ifeq ($(strip $(GITHUB_REF_NAME)),unstable)
		TAG := unstable
	endif
	COMPOSER_ARGS := --classmap-authoritative
	NPM_FLAG := ci
	ifeq ($(DEV)x, x)
		COMPOSER_ARGS := $(COMPOSER_ARGS) --no-dev
	endif
	COMPOSER_PREFIX :=
	LINT_PREFIX := cd src &&
	TEST_PREFIX := cd src &&
endif

help:  ## Print the help documentation
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

$(VENDOR_AUTOLOAD):
	$(COMPOSER_PREFIX) composer install --working-dir=src $(COMPOSER_ARGS)

$(CROUTON_JS):
	curl -sLO https://github.com/bmlt-enabled/crouton/releases/latest/download/croutonjs.zip
	mkdir -p src/public/client_interface/html/croutonjs
	unzip croutonjs.zip -d src/public/client_interface/html/croutonjs
	rm -f croutonjs.zip
	rm -f src/public/client_interface/html/croutonjs/*.html
	rm -f src/public/client_interface/html/croutonjs/*.json
	rm -rf src/public/client_interface/html/croutonjs/examples

$(SEMANTIC_HTML):
	curl -sLO https://github.com/bmlt-enabled/semantic-workshop/releases/latest/download/semantic-workshop.zip
	mkdir -p src/public/semantic
	unzip semantic-workshop.zip -d src/public/semantic
	rm -f semantic-workshop.zip

$(NODE_MODULES):
	cd src && npm $(NPM_FLAG)

$(FRONTEND): $(NODE_MODULES)
	cd src && npm run build

$(LEGACY_STATIC_FILES):
	rsync -a -m \
	    --include='**/*.js' \
	    --include='**/*.css' \
	    --include='**/*.png' \
	    --include='**/*.svg' \
	    --include='**/*.ttf' \
	    --include='**/*.woff' \
	    --include='**/*.woff2' \
	    --include='**/*.eot'  \
	    --include='**/*.json' \
	    --include='**/*.gif' \
	    --include='*/' \
	    --exclude='*' \
	    src/legacy/ src/public

$(ZIP_FILE): $(VENDOR_AUTOLOAD) $(FRONTEND) $(CROUTON_JS) $(SEMANTIC_HTML) $(LEGACY_STATIC_FILES)
	mkdir -p build
	cp -r src build/main_server
	cd build && zip -r $(shell basename $(ZIP_FILE)) main_server -x main_server/node_modules/\*
	rm -rf build/main_server

.PHONY: composer
composer: $(VENDOR_AUTOLOAD) ## Runs composer install

.PHONY: npm
npm: $(NODE_MODULES) ## Runs npm install

.PHONY: crouton
crouton: $(CROUTON_JS) ## Installs crouton

.PHONY: semantic
semantic: $(SEMANTIC_HTML) ## Installs semantic workshop

.PHONY: frontend
frontend: $(FRONTEND)  ## Builds the frontend

.PHONY: zip
zip: $(ZIP_FILE) ## Builds zip file

.PHONY: docker
docker: zip ## Builds Docker Image
	docker build --pull --build-arg PHP_VERSION=$(BASE_IMAGE_TAG) -f docker/$(DOCKERFILE) . -t $(IMAGE):$(TAG)

.PHONY: docker-push
docker-push: ## Pushes docker image to Dockerhub
	docker push $(IMAGE):$(TAG)

.PHONY: dev
dev: zip ## Docker Compose Up
	docker-compose -f docker/docker-compose.yml $(EXTRA_DOCKER_COMPOSE_ARGS) up --build

.PHONY: test
test:  ## Runs PHP Tests
	$(TEST_PREFIX) php artisan test --parallel --recreate-databases --display-deprecations --coverage-clover coverage.xml
# 	$(TEST_PREFIX) vendor/bin/phpunit tests/Feature/Admin/ServiceBodyPartialUpdateTest.php
# 	$(TEST_PREFIX) vendor/bin/phpunit --filter testUpdateServiceBodyAsServiceBodyAdmin tests/Feature/Admin/ServiceBodyPartialUpdateTest.php

.PHONY: test-js
test-js:  ## Runs JavaScript tests
	cd src && npm run test

.PHONY: coverage
coverage:  ## Generates HTML Coverage Report
	$(TEST_PREFIX) vendor/phpunit/phpunit/phpunit --coverage-html tests/reports/coverage

.PHONY: coverage-serve
coverage-serve:  ## Serves HTML Coverage Report
	python3 -m http.server 8100 --directory src/tests/reports/coverage

.PHONY: generate-api-json
generate-api-json: ## Generates Open API JSON
	$(LINT_PREFIX) php artisan l5-swagger:generate

.PHONY: lint
lint:  ## PHP Lint
	$(LINT_PREFIX) vendor/squizlabs/php_codesniffer/bin/phpcs

.PHONY: lint-fix
lint-fix:  ## PHP Lint Fix
	$(LINT_PREFIX) vendor/squizlabs/php_codesniffer/bin/phpcbf

.PHONY: lint-js
lint-js:  ## JavaScript Lint
	cd src && npm run lint

.PHONY: phpstan
phpstan:  ## PHP Larastan Code Analysis
	$(LINT_PREFIX) vendor/bin/phpstan analyse -c .phpstan.neon --memory-limit=2G

.PHONY: docker-publish-base
docker-publish-base:  ## Builds Base Docker Image
	docker buildx build --platform linux/amd64,linux/arm64/v8 -f docker/Dockerfile-base docker/ -t $(BASE_IMAGE):$(BASE_IMAGE_TAG) --push

.PHONY: mysql
mysql:  ## Runs mysql cli in mysql container
	docker exec -it docker-db-1 mariadb -u root -prootserver rootserver

.PHONY: bash
bash:  ## Runs bash shell in apache container
	docker exec -it -w /var/www/html/main_server docker-bmlt-1 bash

.PHONY: clean
clean:  ## Clean build
	rm -rf src/public/build
	rm -rf src/public/client_interface
	rm -rf src/public/local_server
	rm -rf src/public/semantic
	rm -rf src/node_modules
	rm -rf src/vendor
	rm -rf build
