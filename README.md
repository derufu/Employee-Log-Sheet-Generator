# Log Sheet Management System

## Installation

### 1. Clone the Repository
```sh
git clone https://github.com/yourusername/log-sheet-management.git
cd log-sheet-management
```

### 2. Install Dependencies
```sh
composer install
npm install
npm run dev
```

### 3. Set Up Environment Variables
```sh
cp .env.example .env
php artisan key:generate
```
Configure the database in `.env` and run migrations:
```sh
php artisan migrate
php artisan db:seed
```

### 4. Install Filament Shield
```sh
composer require bezhansalleh/filament-shield
php artisan vendor:publish --tag=filament-shield-config
php artisan shield:install
php artisan shield:generate
```

## Usage

### 1. Start the Development Server
```sh
php artisan serve
```
Access the application at [http://localhost:8000](http://localhost:8000). Log in as an `admin` or `super_admin` to access the Filament panel at [http://localhost:8000/admin](http://localhost:8000/admin).

## Role-Based Access Control
The system uses **Filament Shield** for role-based access control. Only users with `admin` or `super_admin` roles can manage the Filament Shield panel.

### Managing Roles & Permissions
1. Use the Filament Shield UI to assign roles and permissions.
2. Assign `admin` or `super_admin` roles to users who need elevated access.

## Log Sheet Management

### Generating Log Sheets
1. Navigate to **Log Sheets** in the Filament admin panel.
2. Click **Generate Log Sheet**.
3. Select **position type**, **employees**, and **month**.
4. Click **Submit** to generate the log sheet.

Generated log sheets can be viewed and downloaded.

## Models

### Employee
Represents employees in the system.
- `id`
- `first_name`
- `last_name`
- `position_type`
- `created_at`, `updated_at`

### LogSheet
Represents generated log sheets.
- `id`
- `filename`
- `filepath`
- `year`, `month`
- `created_at`, `updated_at`

## Custom Middleware
**CheckAdminRole Middleware** ensures only `admin` or `super_admin` users can access the Filament Shield panel.

## License
This project is licensed under the **MIT License**. See the `LICENSE` file for details.

