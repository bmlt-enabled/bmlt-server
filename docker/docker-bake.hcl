# docker-bake.hcl
#
# Single source of truth for building the images this repo publishes with
# Docker Buildx Bake. Always invoked from the repo root (relative context paths
# below resolve against the working directory, not this file's location), via
# `-f docker/docker-bake.hcl` (the Makefile wraps this as $(BAKE)):
#
#   docker buildx bake -f docker/docker-bake.hcl app --load     # app image -> local docker
#   docker buildx bake -f docker/docker-bake.hcl base --push    # all PHP base variants (multi-arch)
#   docker buildx bake -f docker/docker-bake.hcl base db --push # weekly base + sample-db publish
#   docker buildx bake -f docker/docker-bake.hcl --print        # resolve + print config, no build
#
# Variables are overridden via environment (the Makefile and GitHub workflows
# supply CI values), e.g. TAG=latest APP_IMAGE=bmltenabled/bmlt-server ... bake app --push

variable "BASE_IMAGE" {
  default = "bmltenabled/bmlt-server-base"
}

variable "DB_IMAGE" {
  default = "bmltenabled/bmlt-server-sample-db"
}

# App image name/dockerfile default to the local debug image that backs
# `make lint` / `make test`; CI overrides these to the published prod image.
variable "APP_IMAGE" {
  default = "bmltserver"
}

variable "APP_DOCKERFILE" {
  default = "docker/Dockerfile-debug"
}

# App image tag (e.g. "local", "latest", "unstable", or a version-commit).
variable "TAG" {
  default = "local"
}

variable "PHP_VERSION" {
  default = "8.3"
}

variable "SHA" {
  default = ""
}

variable "CREATED" {
  default = ""
}

# Base image tags: always ":<php>", plus ":latest" for the primary (8.3) build.
function "base_tags" {
  params = [php]
  result = concat(
    ["${BASE_IMAGE}:${php}"],
    php == "8.3" ? ["${BASE_IMAGE}:latest"] : []
  )
}

group "default" {
  targets = ["app"]
}

target "base" {
  matrix = {
    php = ["8.3", "8.4", "8.5"]
  }
  name       = "base-${replace(php, ".", "-")}"
  context    = "docker"
  dockerfile = "Dockerfile-base"
  platforms  = ["linux/amd64", "linux/arm64/v8"]
  args = {
    PHP_VERSION = php
  }
  tags = base_tags(php)
  labels = {
    "org.opencontainers.image.title"       = "BMLT Server Base (PHP ${php})"
    "org.opencontainers.image.description" = "Base image for BMLT Server with PHP ${php}"
    "org.opencontainers.image.vendor"      = "BMLT"
    "org.opencontainers.image.version"     = php
    "org.opencontainers.image.revision"    = SHA
    "org.opencontainers.image.created"     = CREATED
    "php.version"                          = php
  }
}

target "db" {
  context    = "docker"
  dockerfile = "Dockerfile-db"
  platforms  = ["linux/amd64", "linux/arm64/v8"]
  tags       = ["${DB_IMAGE}:latest"]
  labels = {
    "org.opencontainers.image.title"       = "BMLT Server Sample DB"
    "org.opencontainers.image.description" = "MariaDB seeded with the BMLT sample schema"
    "org.opencontainers.image.vendor"      = "BMLT"
    "org.opencontainers.image.revision"    = SHA
    "org.opencontainers.image.created"     = CREATED
  }
}

target "app" {
  context    = "."
  dockerfile = APP_DOCKERFILE
  args = {
    PHP_VERSION = PHP_VERSION
  }
  tags = ["${APP_IMAGE}:${TAG}"]
  labels = {
    "org.opencontainers.image.revision" = SHA
    "org.opencontainers.image.created"  = CREATED
  }
}
