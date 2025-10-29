# Docker Setup for Splitify

This directory contains Docker configuration files for running the Splitify application in a containerized environment with SQLite.

## Files

- `Dockerfile` - Main Dockerfile for building the Splitify application image
- `docker-compose.yml` - Docker Compose configuration for easy deployment
- `docker/nginx.conf` - Nginx main configuration
- `docker/default.conf` - Nginx site configuration for the Laravel application
- `docker/supervisord.conf` - Supervisor configuration to manage PHP-FPM, Nginx, and Queue workers
- `docker/entrypoint.sh` - Entrypoint script that initializes the application on container startup

## Quick Start

### Using Docker Compose (Recommended)

1. Build and start the container:
   ```bash
   docker-compose up -d
   ```

2. The application will be available at http://localhost:8000

3. To view logs:
   ```bash
   docker-compose logs -f
   ```

4. To stop the container:
   ```bash
   docker-compose down
   ```

### Using Docker Directly

1. Build the image:
   ```bash
   docker build -t splitify:latest .
   ```

2. Run the container:
   ```bash
   docker run -d \
     -p 8000:80 \
     -v $(pwd)/database/database.sqlite:/var/www/html/database/database.sqlite \
     -v $(pwd)/storage:/var/www/html/storage \
     --name splitify \
     splitify:latest
   ```

3. View logs:
   ```bash
   docker logs -f splitify
   ```

4. Stop and remove the container:
   ```bash
   docker stop splitify
   docker rm splitify
   ```

## What the Dockerfile Does

1. **Base Image**: Uses PHP 8.4 FPM (Debian-based) for better package availability
2. **System Dependencies**: Installs required system packages including:
   - SQLite and development libraries
   - Image processing libraries (libpng, etc.)
   - Node.js and NPM for frontend asset compilation
   - Nginx web server
   - Supervisor for process management

3. **PHP Extensions**: Installs necessary PHP extensions:
   - pdo_sqlite - SQLite database driver
   - mbstring - Multibyte string handling
   - exif - Image metadata handling
   - pcntl - Process control
   - bcmath - Arbitrary precision mathematics
   - gd - Image processing
   - opcache - PHP opcode caching for performance

4. **Application Setup**:
   - Installs Composer dependencies (production only)
   - Installs NPM dependencies and builds frontend assets with Vite
   - Creates and configures SQLite database
   - Sets proper file permissions
   - Configures PHP opcache for production

5. **Services**: Runs three services via Supervisor:
   - PHP-FPM - Processes PHP requests
   - Nginx - Serves HTTP requests
   - Queue Worker - Processes Laravel queue jobs

## Environment Variables

The container supports the following environment variables (configured in docker-compose.yml):

- `APP_NAME` - Application name (default: Splitify)
- `APP_ENV` - Application environment (default: production)
- `APP_DEBUG` - Enable debug mode (default: false)
- `APP_URL` - Application URL (default: http://localhost:8000)
- `DB_CONNECTION` - Database connection type (fixed: sqlite)
- `DB_DATABASE` - Path to SQLite database file
- `SESSION_DRIVER` - Session storage driver (default: database)
- `QUEUE_CONNECTION` - Queue connection (default: database)
- `CACHE_STORE` - Cache storage (default: database)

## Data Persistence

The Docker Compose configuration mounts two volumes:
- `./database/database.sqlite` - SQLite database file
- `./storage` - Application storage directory (logs, uploaded files, etc.)

This ensures your data persists even if you recreate the container.

## Initialization

On first startup, the entrypoint script automatically:
1. Creates `.env` file from `.env.example` if it doesn't exist
2. Generates Laravel application key
3. Creates SQLite database file if it doesn't exist
4. Runs database migrations
5. Caches configuration, routes, and views for performance

## Health Check

The Docker Compose configuration includes a health check that verifies the web server is responding correctly every 30 seconds.

## Troubleshooting

### Container won't start
- Check logs: `docker-compose logs`
- Ensure ports 8000 is not already in use
- Verify file permissions on mounted volumes

### Database errors
- Ensure the database directory is writable: `chmod 775 database`
- Check SQLite file permissions: `chmod 664 database/database.sqlite`

### Frontend assets not loading
- Rebuild the container: `docker-compose up -d --build`
- Check that the build process completed successfully in the logs

## Production Deployment

For production deployment:

1. Update environment variables in `docker-compose.yml`:
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Set appropriate `APP_URL`

2. Use a reverse proxy (like Traefik or nginx) in front of the container for SSL/TLS termination

3. Consider using a named volume or external storage for the database:
   ```yaml
   volumes:
     - splitify-db:/var/www/html/database
   ```

4. Implement regular database backups

5. Monitor container logs and set up log aggregation

## Development

For development purposes, you can mount the entire application directory:

```yaml
volumes:
  - .:/var/www/html
  - /var/www/html/vendor
  - /var/www/html/node_modules
```

However, this is not recommended for production use.
