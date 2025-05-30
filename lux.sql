-- Create the database if it doesn't exist


-- Use the luxreads database
USE if0_39050947_luxreads;

-- Create users table (no changes needed here from your original)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create subscriptions table (ADJUSTED)
-- This table now stores the chosen payment method and a JSON blob
-- for method-specific details, removing individual card/address fields.
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) NOT NULL, -- e.g., 'Credit Card', 'PayPal', 'Bank Transfer'
    payment_details_json TEXT,           -- Stores JSON string of payment details (card, PayPal email, bank info)
    status VARCHAR(50) DEFAULT 'pending', -- e.g., 'active', 'pending', 'cancelled', 'expired'
    approved ENUM('yes', 'no') DEFAULT 'no', -- 'yes' if manually approved or auto-approved
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Add an index to the email column for faster lookups
CREATE INDEX idx_subscriptions_email ON subscriptions (email);