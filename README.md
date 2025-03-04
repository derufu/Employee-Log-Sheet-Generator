# Log Sheet Management System

This Log Sheet Management System is built using Laravel and Filament. It allows users to generate, view, and download log sheets for employees based on their position types and selected month. The system also includes role-based access control using Filament Shield.

## Features

- Generate log sheets for employees based on position type and month
- View and download generated log sheets
- Role-based access control with Filament Shield
- Grouped navigation for user and role management

## Installation

1. Clone the repository:

```bash
git clone https://github.com/yourusername/log-sheet-management.git
cd log-sheet-management

2.Install dependencies:
composer install
npm install
npm run dev

3.Set up the environment variables:
cp .env.example .env
php artisan key:generate

4.Configure the database in the .env file and run migrations:
php artisan migrate

5.Seed the database with initial data:
php artisan db:seed

6.Install Filament Shield:
composer require bezhansalleh/filament-shield
php artisan vendor:publish --tag=filament-shield-config
php artisan shield:install
php artisan shield:generate

Usage
1.Start the development server:
php artisan serve
2.Access the application at http://localhost:8000.
3.Log in with an admin or super_admin account to access the Filament admin panel at http://localhost:8000/admin.

Role-Based Access Control
The system uses Filament Shield for role-based access control. Only users with the admin or super_admin roles can access the Filament Shield panel.

Adding Roles and Permissions
1.Use the Filament Shield UI to manage roles and permissions.
2.Assign the admin or super_admin role to users who need access to the Filament Shield panel.
Grouped Navigation
The navigation items for user and role management are grouped under the "User Management" section in the Filament admin panel.

Generating Log Sheets
1.Navigate to the Log Sheets section in the Filament admin panel.
2.Click on the "Generate Log Sheet" button.
3.Select the position type, employees, and month.
4.Click "Submit" to generate the log sheet.
The generated log sheet can be viewed and downloaded from the list.
Models
Employee
The Employee model represents the employees in the system. It includes the following fields:

id
first_name
last_name
position_type
created_at
updated_at
LogSheet
The LogSheet model represents the generated log sheets. It includes the following fields:

id
filename
filepath
year
month
created_at
updated_at
Custom Middleware
The system includes custom middleware to restrict access to the Filament Shield panel based on user roles.

CheckAdminRole Middleware
The CheckAdminRole middleware ensures that only users with the admin or super_admin roles can access the Filament Shield panel.

License
This project is licensed under the MIT License. See the LICENSE file for details.
