# Splitify

Split your bills with friends.

## Developer Setup

This is a Laravel application with the Laravel Breeze starter kit.
Follow these steps to get the application up and running:

1.  **Clone the Repository**

2.  **Install Composer Dependencies:**

    ```bash
    composer install
    ```

3.  **Copy the .env.example file:**

    ```bash
    cp .env.example .env
    ```

4.  **Generate an Application Key:**

    ```bash
    php artisan key:generate
    ```

5.  **Run Database Migrations:**

    ```bash
    php artisan migrate
    ```

    * Choose yes to create a sqlite DB.

6.  **Install NPM Dependencies:**

    ```bash
    npm install
    ```

7.  **Run Vite Server:**

    ```bash
    npm run dev
    ```

8.  **Start the Development Server:**

    ```bash
    composer dev
    ```

    *   The application should now be accessible at `http://localhost:8000`.

## Production Deployment

To setup Splitify in Production we provide a `docker-compose.yml`. We choose to not provide SSL Termination inside the splitify container to give you more flexibility. Note that you need to bring your own Reverse Proxy to properly deploy this with SSL-Certificate.

1. **Download `docker-compose.yml` and example `.env`:**

    ```bash
    wget -O docker-compose.yml https://raw.githubusercontent.com/404Simon/splitify/refs/heads/main/docker-compose.yml
    wget -O .env https://raw.githubusercontent.com/404Simon/splitify/refs/heads/main/.env.production
    ```

2. **Generate a secret key:**

    ```bash
    docker run --rm -v ./.env:/app/.env -w /app splitify php artisan key:generate
    ```

3. **Create and migrate the DB:**

    ```bash
    touch database.sqlite
    docker run --rm -v ./database.sqlite:/app/database/database.sqlite splitify php artisan migrate --force
    ```

4. **Start application:**

   ```bash
   docker compose up -d
   ```

If you didn't change the `SERVER_NAME` and port-mapping in the `docker-compose.yml`, the app should now be accessible on port 8080. If your reverse proxy of choice supports accessing your apps via docker networking you should use that and remove the port-mapping entirely.

### Files created:

- `.env` - Environment configuration
- `database.sqlite` - SQLite database file

Both files are mounted into the container and persist data.

