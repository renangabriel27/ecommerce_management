/* scripts for create database */
DROP DATABASE IF EXISTS ecommerce_management;

CREATE DATABASE ecommerce_management;

USE ecommerce_management;

CREATE TABLE states (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	uf CHAR(2) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cities (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	state_id INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT FOREIGN KEY(state_id) REFERENCES states(id)
);

CREATE TABLE clients (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	address VARCHAR(50),
  address_number INT,
  address_cep VARCHAR(10),
	phone VARCHAR(11),
	email VARCHAR(50),
	type VARCHAR(10),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	city_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(city_id) REFERENCES cities(id)
);

CREATE TABLE clients_pi (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  cpf VARCHAR(11),
	date_of_birth DATE,
	client_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(client_id) REFERENCES clients(id)
);

CREATE TABLE clients_pc (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  cnpj VARCHAR(14),
  company_name VARCHAR(50),
	client_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(client_id) REFERENCES clients(id)
);

CREATE TABLE employees (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(50) UNIQUE NOT NULL,
	password VARCHAR(100) NOT NULL,
  salary DECIMAL(10,2),
	comission DOUBLE,
	registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	city_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(city_id) REFERENCES cities(id)
);

CREATE TABLE categories (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
  amount INT,
	description TEXT,
	price DECIMAL(10,2) NOT NULL,
	category_id INTEGER NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT FOREIGN KEY(category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	total DOUBLE NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	client_id INT NOT NULL,
	employee_id INT NOT NULL,
	status INT,
  status_id INT,
  CONSTRAINT FOREIGN KEY(status_id) REFERENCES status(id),
	CONSTRAINT FOREIGN KEY(client_id) REFERENCES clients(id),
	CONSTRAINT FOREIGN KEY(employee_id) REFERENCES employees(id)
);

CREATE TABLE sell_orders_items (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	price DECIMAL(10,2) NOT NULL,
	amount INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	order_id INT NOT NULL,
	product_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(order_id) REFERENCES orders(id),
	CONSTRAINT FOREIGN KEY(product_id) REFERENCES products(id)
);

/*
*** Dumping data for table `states`
*/

INSERT INTO states (name, uf) VALUES ("Paraná", "PR");
INSERT INTO states (name, uf) VALUES ("São Paulo", "PR");
INSERT INTO states (name, uf) VALUES ("Santa Catarina", "SC");


/* ---------------------------------------------------- */

/*
*** Dumping data for table `states`
*/

INSERT INTO cities (name, state_id) VALUES ("Guarapuava", 1);
INSERT INTO cities (name, state_id) VALUES ("Curitiba", 1);
INSERT INTO cities (name, state_id) VALUES ("São Paulo",  2);
INSERT INTO cities (name, state_id) VALUES ("Florianópolis", 3);


/* ---------------------------------------------------- */

/*
*** Dumping data for table `states`
*/

INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Renan", "renanthompsom@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 1200, 3);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Thompsom", "thompsom@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 1500, 1);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Gabriel", "gabriel@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);
