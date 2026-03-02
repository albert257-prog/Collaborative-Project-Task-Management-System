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

## ⚙️ Technical Implementation Details

### 1. Advanced Task Validation (The "Rule of 3")
* **Constraint**: Users are strictly restricted to **3 active tasks** per project to ensure focused delivery and prevent burnout.
* **Logic**: The `TaskController` performs a real-time count of tasks with `PENDING` or `IN-PROGRESS` status before allowing a new task to be claimed or assigned.
* **Dry Architecture**: A private `isUserFullInProject` helper method encapsulates this logic, ensuring consistency across the `store` and `claim` actions.

### 2. Data Integrity & Membership
* **Task Re-pooling**: To prevent data loss, when a member leaves or is removed, their active tasks are not deleted. Instead, the `user_id` is set to `null`, returning the tasks to the project pool for other contributors.
* **Ownership Security**: The `transferOwnership` method ensures a project always maintains exactly one owner. Upon transfer, the previous owner is automatically transitioned to a 'Contributor' role to maintain project continuity.

### 3. Security & Performance Optimization
* **Authentication Security**: Implements Laravel's built-in authentication with added session regeneration to mitigate session fixation risks.
* **Robust Validation**: Utilizes strict RFC/DNS email validation and the `uncompromised()` password rule to protect against known data breaches.
* **Performance**: Optimized dashboard loading by using **Eager Loading** (`with(['tasks', 'users'])`) in the `ProjectController`, effectively eliminating the N+1 query problem.

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

# 🛡️ Project Defense & Compliance Documentation

This document outlines how this submission satisfies the **Automatic Disqualification Conditions** and follows Laravel best practices for security and architecture.

---

### 🟢 1. Authorization Logic & Data Security
**Condition:** *Submissions will be rejected if any user can access or modify any data.*

* **Implementation:** * **Controller Level:** The `TaskController` uses `contains(auth()->id())` to verify project membership before allowing a task to be claimed.
    * **UI Level:** Administrative actions (Delete, Transfer, Add Member) are wrapped in `@if($isOwner)` blocks, ensuring only the project creator sees these options.
    * **Policies:** Deletion logic is handled via Laravel **Policies** (`@can('delete', $task)`), ensuring that permissions are verified on the server side, not just hidden in the UI.

### 🟢 2. Robust Input Validation
**Condition:** *Submissions will be rejected if there is no input validation of any kind.*

* **Implementation:** * **Backend Validation:** All store and update methods utilize `$request->validate()`. 
    * **Rule Specificity:** Due dates are strictly validated using `after_or_equal:today` to prevent historical data entry.
    * **Data Integrity:** Fields like `title` are required and length-constrained to prevent database overflows or empty records.

### 🟢 3. Dynamic User Handling (No Hardcoding)
**Condition:** *Submissions will be rejected if hardcoded user IDs or credentials exist.*

* **Implementation:** * **Auth Integration:** The application exclusively uses `auth()->id()` and `auth()->user()` to identify the actor.
    * **Relational Logic:** Records are filtered based on the authenticated session (e.g., `auth()->user()->tasks()`), ensuring the app works dynamically for any registered user.

### 🟢 4. Separation of Concerns (MVC Architecture)
**Condition:** *Submissions will be rejected if business logic is placed directly inside views.*

* **Implementation:** * **Model Attributes:** Complex state checks (such as determining if a user has reached their 3-task capacity) have been moved to the `Project` model as **Accessors** (e.g., `$project->is_user_full`).
    * **Clean Templates:** Blade templates are restricted to display logic and simple boolean checks, adhering to the "Skinny Controller, Fat Model, Clean View" principle.

### 🟢 5. Server-Side Rule Enforcement
**Condition:** *Submissions will be rejected if rules are enforced only at the UI level.*

* **Implementation:** * **The "Bouncer" Principle:** While the "Claim" button is disabled in the UI when a user reaches the 3-task limit, the **Controller** re-verifies this count upon request. 
    * **Bypass Prevention:** This prevents users from bypass restrictions using browser developer tools (Inspect Element) or external API tools like Postman.

### 🟢 6. Professional Naming Conventions
**Condition:** *Submissions will be rejected for poor or inconsistent naming.*

* **Implementation:** * **Standards:** Follows **PSR-12** coding standards and Laravel naming conventions (CamelCase for methods, snake_case for database columns/variables).
    * **Semantic Routes:** Route names (e.g., `projects.transfer`, `tasks.update-status`) are descriptive and follow RESTful patterns.

---

## 📝 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
