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

# Fix for Vite 5+ manifest location: Copy it from .vite/ to the root if necessary
if [ -f "$PACKAGE_DIR/public/build/.vite/manifest.json" ]; then
    cp "$PACKAGE_DIR/public/build/.vite/manifest.json" "$PACKAGE_DIR/public/build/manifest.json"
fi

# --- 3. Package Linking and Registration ---
echo "Linking package to host..."
cd $HOST_DIR

# 3.1 CRITICAL CONFIG: Tell Composer where the local package is located
# This allows 'composer require' to find the package in the /package volume.
composer config repositories.local-package '{"type": "path", "url": "/package", "options": {"symlink": true}}' --no-interaction

# Require the package using the @dev stability flag for local development
composer require "dustov/quotes:@dev" --no-interaction

# 3.2 Assets: Create the destination folder and symlink the build directory
# This ensures that compiled assets are accessible via the host's public path.
mkdir -p $HOST_DIR/public/vendor/quotes
rm -rf $HOST_DIR/public/vendor/quotes/build
ln -s $PACKAGE_DIR/public/build $HOST_DIR/public/vendor/quotes/build

# 3.3 ROUTE REDIRECTION: Point the root URL '/' to the package UI
# We use 'sed' to dynamically replace the default 'welcome' view with the package's index.
sed -i "s/view('welcome')/view('quotes::index')/g" routes/web.php

# --- 4. Final Host Configuration ---
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
php artisan key:generate --force --no-interaction

# --- 5. Launch ---
echo "All set. Access http://localhost:8080"
php artisan serve --host=0.0.0.0 --port=8080
