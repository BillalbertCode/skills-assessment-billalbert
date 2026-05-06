#!/bin/bash
set -e

# Working directories
PACKAGE_DIR="/package"
HOST_DIR="/var/www"

# --- Fresh Laravel Instance Check ---
if [ ! -f "$HOST_DIR/artisan" ]; then
    echo "Installing fresh Laravel instance..."
    
    # Use a temporary directory to avoid "Directory not empty" errors in Docker volumes
    TEMP_INSTALL_DIR="/tmp/laravel-install"
    rm -rf $TEMP_INSTALL_DIR && mkdir -p $TEMP_INSTALL_DIR
    
    composer create-project laravel/laravel $TEMP_INSTALL_DIR --quiet
    
    # Move all content (including hidden files) to the host directory
    cp -a $TEMP_INSTALL_DIR/. $HOST_DIR/
    rm -rf $TEMP_INSTALL_DIR
    
    cd $HOST_DIR
fi

# --- Package Preparation and Compilation (Isolated) ---
echo "Preparing package assets..."
cd $PACKAGE_DIR

# Install dependencies and compile assets INSIDE the package
# This generates files in $PACKAGE_DIR/public
npm install --quiet
npm run build --quiet

# --- Linking and Registration in Host ---
echo "Linking package to host..."
cd $HOST_DIR

# Create destination directory if it does not exist
mkdir -p $HOST_DIR/public/vendor/quotes

# DEVELOPER TRICK: Instead of copying, create a symbolic link (Symlink)
# This makes any change in /package/public visible INSTANTLY on the server
rm -rf $HOST_DIR/public/vendor/quotes/build
ln -s $PACKAGE_DIR/public/build $HOST_DIR/public/vendor/quotes/build

# Require the package
composer require "dustov/quotes:@dev" --no-interaction

# --- Final Host Configuration ---
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
php artisan key:generate --force --no-interaction

# --- 5. Launch ---
echo "All set. Access http://localhost:8080"
php artisan serve --host=0.0.0.0 --port=8080
