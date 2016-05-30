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
	CONSTRAINT FOREIGN KEY(client_id) REFERENCES clients(id),
	CONSTRAINT FOREIGN KEY(employee_id) REFERENCES employees(id)
);

CREATE TABLE sell_orders_items (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	price DECIMAL(10,2) NOT NULL,
	amount INT NOT NULL DEFAULT 1,
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
*** Dumping data for table `employees`
*/

INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Rafael", "renan@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 1200, 3);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Cintia", "carlos@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 1500, 1);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Alisson", "alisson@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Gabriel", "gabriel@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("João", "joao@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `categories`
*/

INSERT INTO categories (name) VALUES ("Processador");
INSERT INTO categories (name) VALUES ("Notebook");
INSERT INTO categories (name) VALUES ("Netbook");
INSERT INTO categories (name) VALUES ("Placa de vídeo");
INSERT INTO categories (name) VALUES ("Monitor");


/* ---------------------------------------------------- */

/*
*** Dumping data for table `categories`
*/

INSERT INTO products (name, amount, description, price, category_id) VALUES ("AMD FX-4300",100, "Processador potente", "417.49", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("AMD FX-6300",100, "Processador potente", "485.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("AMD FX-8350",100, "Processador potente", "819.02", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("INTEL CORE I5 4460",100, "Processador potente", "919.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("INTEL CORE I7",100, "Processador potente", "1618.49", 1);

INSERT INTO products (name, amount, description, price, category_id) VALUES ("EVGA GEFORCE GTX 750TI", 100, "Placa de VGA", "534.75", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("GIGABYTE GEFORCE GTX 970", 100, "Placa de VGA", "1627.12", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("ZOTAC GEFORCE GTX 9700", 100, "Placa de VGA", "1398.32", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("EVGA GEFORCE GTX 970",100, "Placa de VGA", "1255.02", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("ASUS GEFORCE GTX 750TI",100, "Placa de VGA", "547.90", 4);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `clients and clients_pi`
*/

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Amélio", "Rua 1", 1, 230, "9919-2033",
"amelio@hotmail.com", 1, 1);
INSERT INTO clients_pi(cpf, date_of_birth, client_id) VALUES ("18960196410", "1996-12-02", 1);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Bruno", "Rua 2", 2, 240, "9920-2013",
"bruno@hotmail.com", 1, 2);
INSERT INTO clients_pi(cpf, date_of_birth, client_id) VALUES ("89761184641", "1990-06-01", 2);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Rogério", "Rua 3", 3, 250, "8818-2055",
"rogerio@hotmail.com", 1, 3);
INSERT INTO clients_pi(cpf, date_of_birth, client_id) VALUES ("56865189981", "2000-03-05", 3);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Thomas", "Rua 4", 4, 260, "8815-2023",
"thomas@hotmail.com", 1, 2);
INSERT INTO clients_pi(cpf, date_of_birth, client_id) VALUES ("80078987784", "1995-05-01", 4);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Maicon", "Rua 5", 5, 270, "3619-2543",
"maicon@hotmail.com", 1, 2);
INSERT INTO clients_pi(cpf, date_of_birth, client_id) VALUES ("05636927584", "1998-06-07", 5);


/* ---------------------------------------------------- */

/*
*** Dumping data for table `clients and clients_pc`
*/

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Marcos", "Rua 6", 6, 280, "3623-2010",
"marcos@gmail.com", 2, 1);
INSERT INTO clients_pc(cnpj, company_name, client_id) VALUES ("04641158000100", "Contact computadores", 6);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Leandro", "Rua 7", 8, 290, "9910-2040",
"leandro@gmail.com",  2, 2);
INSERT INTO clients_pc(cnpj, company_name, client_id) VALUES ("43441387000124", "Alta tecnologia", 7);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Paulo", "Rua 8", 9, 300, "8818-1033",
"paulo@gmail.com",  2, 3);
INSERT INTO clients_pc(cnpj, company_name, client_id) VALUES ("82260863000162", "Milenium informática", 8);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Guilherme", "Rua 9", 10, 310, "8815-1033",
"guilherme@gmail.com",  2, 2);
INSERT INTO clients_pc(cnpj, company_name, client_id) VALUES ("34568180000124", "Tech tudo", 9);

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Juca", "Rua 10", 11, 320, "3619-3543",
"juca@gmail.com", 2, 2);
INSERT INTO clients_pc(cnpj, company_name, client_id) VALUES ("36674375000184", "Inf hardware", 10);
