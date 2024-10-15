CREATE TABLE products
(
    id              CHAR(1) PRIMARY KEY,
    name            VARCHAR(30) NOT NULL,
    price           DECIMAL(3, 2) NOT NULL,
    available_stock INT NOT NULL,
    CONSTRAINT check_available_stock CHECK (available_stock >= 0)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO products (id, name, price, available_stock) VALUES ('1', 'Water', 0.65, 50);
INSERT INTO products (id, name, price, available_stock) VALUES ('2', 'Juice', 1.00, 50);
INSERT INTO products (id, name, price, available_stock) VALUES ('3', 'Soda', 1.50, 50);

CREATE TABLE sales
(
    id         CHAR(36) PRIMARY KEY,
    coins      JSON NOT NULL,
    credit     DECIMAL(4, 2) NOT NULL DEFAULT 0.0,
    state      ENUM ('IN_PROGRESS', 'CANCELLED', 'COMPLETED') NOT NULL DEFAULT 'IN_PROGRESS',
    product_id CHAR(1) DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_state (state)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE change_stock
(
    id  CHAR(1) PRIMARY KEY,
    value    DECIMAL(3, 2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    CONSTRAINT check_quantity CHECK (quantity >= 0),
    CONSTRAINT unique_value UNIQUE (value)
) DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO change_stock (id, value, quantity) VALUES ('1', 1.00, 10);
INSERT INTO change_stock (id, value, quantity) VALUES ('2', 0.25, 40);
INSERT INTO change_stock (id, value, quantity) VALUES ('3', 0.10, 100);
INSERT INTO change_stock (id, value, quantity) VALUES ('4', 0.05, 200);
