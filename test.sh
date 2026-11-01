#!/bin/bash

IMAGE_NAME="php-node-laravel:7.4"

if [ -z "$(docker images -q $IMAGE_NAME 2> /dev/null)" ]; then
  echo "Image not found. Building..."
  docker build -t $IMAGE_NAME .
else
  echo "Image already exists. Skipping build."
fi

# Remove -it flag when running non-interactively (e.g. from CI/script)
if [ -t 0 ]; then DOCKER_FLAGS="-it"; else DOCKER_FLAGS=""; fi

docker run --rm $DOCKER_FLAGS -v "$(pwd):/app" -w "/app" "$IMAGE_NAME" sh -c "composer install --no-interaction --no-scripts && vendor/bin/phpunit"
