## Task Scheduler

A web-based Task Scheduler built with PHP, MySQL, and JavaScript. It allows users to register, log in, manage tasks (create, edit, delete), view statistics with interactive charts, and simulate CPU scheduling algorithms (FCFS, Priority, Round Robin) using their task data. A light/dark theme toggle and a live-task timer round out the feature set.

---

### 📋 Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [Prerequisites](#prerequisites)
4. [Installation](#installation)
5. [Database Setup](#database-setup)
6. [Configuration](#configuration)
7. [Usage](#usage)
8. [File Structure](#file-structure)
9. [Contributing](#contributing)
10. [License](#license)

---

### 🚀 Features

* **User Authentication**

  * Register with full name, email, password (hashed).
  * Secure login with “Remember Me” cookie.
  * Forgot-password placeholder alert.

* **Task Management (CRUD)**

  * Add, edit, delete tasks.
  * Fields: title, description, due date, priority, status.
  * Live task timer with start/pause/reset.

* **Dashboards & Charts**

  * “Today,” “Completed,” “Pending” counts.
  * Task-status doughnut chart & priority bar chart via Chart.js.
  * Motivational quotes carousel.

* **CPU Scheduling Simulator**

  * Uses your tasks as processes (burst = description length or fallback 5).
  * Algorithms: FCFS, Priority Scheduling, Round Robin (with time quantum).
  * Displays waiting & turnaround times in a table.

* **Theme Toggle**

  * Light/dark modes persisted in `localStorage`.

---

### 🛠️ Tech Stack

* **Frontend**: HTML5, CSS3, JavaScript (ES6), Chart.js
* **Backend**: PHP 7+, MySQL 5.7+
* **Server**: Apache / Nginx / XAMPP / LAMP stack

---

### ⚙️ Prerequisites

* PHP 7.2 or higher
* MySQL 5.7 or higher
* Web server (Apache, Nginx, or local XAMPP/MAMP)
* Composer (optional, if extending)

---

### 📥 Installation

1. **Clone the repo** into your web server’s document root:

   ```bash
   git clone https://github.com/priya-anshu/task-scheduler.git
   ```

2. **Copy** the contents to e.g. `htdocs/task-scheduler` (XAMPP) or `/var/www/html/task-scheduler`.

3. **Import the database** (see [Database Setup](#database-setup)).

4. **Configure** your DB credentials in `backend/config.php`.

5. **Ensure** `assets/`, `backend/`, and PHP files are readable by your server.

6. **Access**

   * Frontend: `http://localhost/task-scheduler/index.html`
   * CPU Simulator: `http://localhost/task-scheduler/cpu-schedule.php`

---

### 🗄️ Database Setup

Run these SQL queries in your MySQL client or phpMyAdmin:

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS task_scheduler
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE task_scheduler;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tasks table
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  due_date DATE NOT NULL,
  priority ENUM('high','medium','low') NOT NULL DEFAULT 'medium',
  status ENUM('pending','in-progress','completed') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

### ⚙️ Configuration

Edit **`backend/config.php`** to match your environment:
    
```php
<?php
$db_host = 'localhost';
$db_user = 'YOUR_DB_USER';
$db_pass = 'YOUR_DB_PASS';
$db_name = 'task_scheduler';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
  die('DB connect error: '.$conn->connect_error);
}
$conn->set_charset('utf8mb4');
```

---

### ▶️ Usage

1. **Register** a new user via `register.html`.
2. **Log in** using your credentials.
3. **Dashboard**

   * Add or edit tasks in the modal.
   * Use filters and search.
   * View live-task timer & charts.
4. **CPU Simulator**

   * Navigate to `cpu-schedule.php`.
   * Choose algorithm and optional quantum.
   * Click **Run Simulation** to see waiting & turnaround times.

---

### 📂 File Structure

```
task-scheduler/
├─ assets/
│  ├─ style.css
│  ├─ auth.js
│  ├─ script.js
│  └─ cpu-schedule.js
├─ backend/
│  ├─ config.php
│  ├─ login.php
│  ├─ register.php
│  ├─ get_tasks.php
│  ├─ add_task.php
│  └─ delete_task.php
├─ index.html
├─ login.html
├─ register.html
├─ forgot-password.html
├─ cpu-schedule.php
└─ README.md
```

---

### 🤝 Contributing

Contributions are welcome!

1. Fork the repo
2. Create a feature branch (`git checkout -b feature/foo`)
3. Commit your changes (`git commit -am 'Add foo'`)
4. Push to the branch (`git push origin feature/foo`)
5. Open a Pull Request

---

### 📄 License

MIT License © PRIYANSHU DHYANI
Feel free to adapt and redistribute per the MIT terms.
