#!/bin/bash

IMAGE_NAME="php-node-laravel:8.1"

# Build image if it doesn't exist
if [ -z "$(docker images -q $IMAGE_NAME 2> /dev/null)" ]; then
  echo "Image not found. Building..."
  docker build -t $IMAGE_NAME .
else
  echo "Image already exists. Skipping build."
fi

# Run composer as root inside container to avoid permission issues
docker run --rm -u root -v "$(pwd):/app" -w "/app" "$IMAGE_NAME" sh -c "composer $@"