# 🚀 Collaborative Task Management System (Laravel)

A high-fidelity task management application built with Laravel, focusing on project collaboration, task ownership, and strict workload constraints.

## 📌 Key Features

### 1. Project Management
* **Capacity Limit**: Each project is limited to 1 owner and a maximum of 3 contributors.
* **Ownership Transfer**: Owners can transfer full project control to any contributor.
* **Membership Control**: Only owners can add/remove members.
* **Data Retention**: When a member is removed or leaves, their active tasks are automatically unassigned and returned to the "Project Pool" instead of being deleted.

### 2. Task Lifecycle ("Rule of 3")
* **Task Pool**: Tasks can be created as "Unassigned," allowing any member to claim them.
* **Workload Constraint**: To prevent burnout and ensure focus, a user can only have **3 active tasks** (PENDING or IN-PROGRESS) per project at any given time.
* **Status Mutators**: All task statuses are automatically normalized to uppercase in the database for consistency.

### 3. Security & UI
* **Modern Auth**: Custom-styled Login and Register pages with Inter-font typography.
* **Robust Validation**: Strict email (RFC/DNS) and password complexity (letters, numbers, symbols, and uncompromised check).
* **Session Security**: Implements session regeneration and CSRF protection.

---

## 🛠️ Technical Stack

* **Framework**: Laravel 11.x
* **Database**: MySQL / PostgreSQL
* **Frontend**: Blade Templates + Custom CSS
* **Authentication**: Laravel Sanctum / Session-based Auth

---

## 🚀 Installation Guide

1.  **Clone the repository**
    ```bash
    git clone [https://github.com/yourusername/your-repo-name.git](https://github.com/yourusername/your-repo-name.git)
    cd your-repo-name
    ```

2.  **Install dependencies**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database settings in the `.env` file.*

4.  **Run Migrations**
    ```bash
    php artisan migrate
    ```

5.  **Start the Server**
    ```bash
    php artisan serve
    ```

---

## 📂 Project Structure Highlights

* `app/Http/Controllers/`: Contains `AuthController`, `ProjectController`, and `TaskController`.
* `app/Models/Task.php`: Contains the status mutator logic.
* `app/Services/`: Encapsulated business logic for Projects and Tasks.
* `resources/views/auth/`: Polished, responsive authentication views.

---

## 📝 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).