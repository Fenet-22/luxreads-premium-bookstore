# LuxReads Premium Bookstore 📚✨

This is an online bookstore platform built using **HTML & CSS, JavaScript, PHP & MySQL**.
The platform allows users to browse, access, and download books, with a premium subscription model for exclusive content.

### ✨ Features

* **User Authentication**: Seamless registration and login flow.
* **Diverse Collection**: Browse a curated library of business, mindset, and finance literature.
* **Premium Content Access**: Exclusive books available for subscribed users.
* **Secure Downloads**: Robust handling for premium book downloads.
* **Intuitive Interface**: Clean and user-friendly design.
* **Search & Filter**: Easily find books by various criteria.

### 🔐 Requirements

* **XAMPP**: (Apache + MySQL)
* **PHP**: Version 7+
* **Modern Web Browser**

### 🧪 How to Use

1.  **Clone the project**: Place the `luxreads-premium-bookstore` folder into your XAMPP `htdocs` directory (e.g., `E:\xampp\htdocs\luxreads-premium-bookstore`).
    ```bash
    cd E:/xampp/htdocs/
    git clone [https://github.com/Fenet-22/luxreads-premium-bookstore.git](https://github.com/Fenet-22/luxreads-premium-bookstore.git)
    ```
2.  **Start XAMPP**: Run Apache & MySQL using the XAMPP control panel.
3.  **Database Setup**:
    * Open `http://localhost/phpmyadmin/` in your browser.
    * Create a database named `if0_39050947_luxreads`.
    * Import your database schema (e.g., `db.sql` if you have one, containing `users` and `subscriptions` tables).
    * **Important**: Update your PHP connection files (like `download.php` and `index.php`) to use `localhost`, `root`, and an empty password for local development:
        ```php
        $conn = new mysqli("localhost", "root", "", "if0_39050947_luxreads");
        ```
4.  **Place PDF Files**: Create a `pdf` folder inside your project root and place all your book `.pdf` files there. Ensure filenames match your database/code references exactly (case-sensitive).
5.  **Access in Browser**: Open your web browser and go to:
    ```
    http://localhost/luxreads-premium-bookstore
    ```

### 💡 Developed By

**Fened Ahmed**
Aspiring Web Developer | Data Analyst | Cybersecurity Enthusiast

---
