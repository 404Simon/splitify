FROM dunglas/frankenphp

# ENV SERVER_NAME=your-domain-name.example.com <- for now we deactivate ssl termination and suggest the user to use a reverseproxy
ENV SERVER_NAME=:8080

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

# Install Node.js and npm
RUN apt-get update && apt-get install -y curl gnupg && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install npm dependencies and build assets
WORKDIR /app
RUN npm install && npm run build

RUN composer install --no-dev --optimize-autoloader
