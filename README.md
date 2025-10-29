# Splitify

Split your bills with friends.

## Install

This is a Laravel application with the Laravel Breeze starter kit.

### Option 1: Docker (Recommended for Quick Start)

The easiest way to get started is using Docker:

1.  **Clone the Repository**

2.  **Start with Docker Compose:**

    ```bash
    docker-compose up -d
    ```

3.  **Access the Application:**

    The application will be available at `http://localhost:8000`

For detailed Docker instructions, troubleshooting, and production deployment, see [DOCKER.md](DOCKER.md).

### Option 2: Manual Installation

Follow these steps to get the application up and running locally:

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
    php artisan serve
    ```

    *   The application should now be accessible at `http://localhost:8000`.

