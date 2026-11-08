#!/bin/bash

IMAGE_NAME="php-node-laravel:8.2"

# Build image if it doesn't exist
if [ -z "$(docker images -q $IMAGE_NAME 2> /dev/null)" ]; then
  echo "Image not found. Building..."
  docker build -t $IMAGE_NAME .
else
  echo "Image already exists. Skipping build."
fi

# Run command as root inside container to avoid permission issues
echo "Running command: $@"
docker run --rm -u root -v "$(pwd):/app" -w "/app" "$IMAGE_NAME" sh -c "$@"

echo "Run completed."