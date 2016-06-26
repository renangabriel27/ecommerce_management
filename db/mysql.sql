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
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	state_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(state_id) REFERENCES states(id)
);

CREATE TABLE clients (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	address VARCHAR(50) NOT NULL,
  address_number INT NOT NULL,
  address_cep CHAR(8) NOT NULL,
	phone VARCHAR(11) NOT NULL,
	email VARCHAR(50) NOT NULL,
	type CHAR(1) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	city_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(city_id) REFERENCES cities(id)
);

CREATE TABLE clients_pi (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  cpf CHAR(11) NOT NULL,
	date_of_birth DATE NOT NULL
);

CREATE TABLE clients_pc (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  cnpj CHAR(14) NOT NULL,
  company_name VARCHAR(50) NOT NULL
);

CREATE TABLE employees (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(50) UNIQUE NOT NULL,
	password VARCHAR(100) NOT NULL,
  salary DOUBLE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

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
  amount INT NOT NULL,
	description TEXT NOT NULL,
	price DOUBLE NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	category_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(category_id) REFERENCES categories(id)
);

CREATE TABLE orders (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	total DOUBLE NOT NULL DEFAULT 0,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	closed_at TIMESTAMP,
	status VARCHAR(8) DEFAULT "Aberto",

	client_id INT NOT NULL,
	employee_id INT NOT NULL,
	CONSTRAINT FOREIGN KEY(client_id) REFERENCES clients(id),
	CONSTRAINT FOREIGN KEY(employee_id) REFERENCES employees(id)
);

CREATE TABLE sell_orders_items (
	id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	price DOUBLE NOT NULL,
	amount INT NOT NULL DEFAULT 1,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

	order_id INT NOT NULL,
	product_id INT NOT NULL REFERENCES products(id),
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
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Carlos", "carlos@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 1500, 1);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Alisson", "alisson@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("Gabriel", "gabriel@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);
INSERT INTO employees (name, email, password, salary, city_id) VALUES ("João", "joao@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 2200, 2);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `categories`
*/

INSERT INTO categories (name) VALUES ("Processador");
INSERT INTO categories (name) VALUES ("HD");
INSERT INTO categories (name) VALUES ("Memória");
INSERT INTO categories (name) VALUES ("Placa de vídeo");
INSERT INTO categories (name) VALUES ("Monitor");


/* ---------------------------------------------------- */

/*
*** Dumping data for table `products`
*/

INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-4300",100, "Processador potente", "417.49", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-4400",100, "Processador potente", "517.49", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-6300",100, "Processador potente", "485.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-6400",100, "Processador potente", "785.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-8350",100, "Processador potente", "819.02", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Amd Fx-8360",100, "Processador potente", "919.02", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Intel Core i5 4460",100, "Processador potente", "919.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Intel Core i5 4480",100, "Processador potente", "1029.00", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Intel Core i7",100, "Processador potente", "1618.49", 1);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("CPU Intel Core i8",100, "Processador potente", "3618.49", 1);

INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Evga Geforce GTX 750TI", 50, "Placa de Vídeo", "534.75", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Evga Geforce GTX 850TI", 50, "Placa de Vídeo", "734.75", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Gigabyte Geforce GTX 970", 50, "Placa de Vídeo", "1627.12", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Gigabyte Geforce GTX 1070", 50, "Placa de Vídeo", "2627.12", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Zotac Geforce GTX 9700", 50, "Placa de Vídeo", "1398.32", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Zotac Geforce GTX 9900", 50, "Placa de Vídeo", "2398.32", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Evga Geforce GTX 970", 50, "Placa de Vídeo", "1255.02", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga Evga Geforce GTX 990", 50, "Placa de Vídeo", "1555.02", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga ASUS Geforce GTX 750TI", 50, "Placa de Vídeo", "547.90", 4);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Placa de vga ASUS Geforce GTX 850TI",50, "Placa de Vídeo", "847.90", 4);

INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Aoc Led E970SWNL", 20, "Monitor", "334.75", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Lg Led HD 20M37L", 20, "Monitor", "234.75", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Led Aoc E1670WU", 20, "Monitor", "127.12", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Lg Led 16M81", 20, "Monitor", "627.12", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Led Aoc E227WN",20, "Monitor", "398.32", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Led hp V189BZ", 20, "Monitor", "798.32", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Aoc Led M2450", 20, "Monitor", "155.02", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor ASUS MXSDE", 20, "Monitor", "125.02", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Lg Lcd 20134", 20, "Monitor", "337.90", 5);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Monitor Aoc Lcd 2309", 20, "Monitor", "247.90", 5);

INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Seagate 3TB ST300", 30, "Disco rigído", "1334.75", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Hgst Travelstar 500GB ZT", 30, "Disco rigído", "434.75", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Western Digital 2TB WDER", 30, "Disco rigído", "527.12", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Seagate Barracuda 2TB", 30, "Disco rigído", "600.99", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Western Digital WD40 4TB", 30, "Disco rigído", "1208.30", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Seagate 1TB ST10DL", 30, "Disco rigído", "678.00", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Western Digital WD40 500GB", 30, "Disco rigído", "308.07", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Western Digital 3TB 3DRE", 30, "Disco rigído", "1205.00", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD IBM 1TB 09450", 30, "Disco rigído", "732.00", 2);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("HD Hp 1TB 76JFDF",30, "Disco rigído", "823.95", 2);

INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Kinsgston KVGFR 8GB", 10, "Memória RAM", "134.05", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Kinsgston KVCD 4GB", 10, "Memória RAM", "234.15", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Corsair VCVS 4GB", 10, "Memória RAM", "127.00", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Markvision KMVCSD 8GB", 10, "Memória RAM", "100.99", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Hyper X Kingston Fury 8GB DFDF", 10, "Memória RAM", "208.50", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Kingston HyperX Fury CLE 4GB", 10, "Memória RAM", "78.90", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Kingston KVGFS 8GB", 10, "Memória RAM", "108.09", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória ThinkServer 0FD 4GB", 10, "Memória RAM", "205.56", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória Corsair BWCS 2GB", 10, "Memória RAM", "332.20", 3);
INSERT INTO products (name, amount, description, price, category_id) VALUES ("Memória IBM 34CV 2GB",10, "Memória RAM", "123.98", 3);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `clients`
*/

INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Amélio", "Rua 1", 1, 85025370, "4299192033",
"amelio@hotmail.com", 1, 1);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Bruno", "Rua 2", 2, 85025790, "4299202013",
"bruno@hotmail.com", 1, 2);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Rogério", "Rua 3", 3, 85725390, "4288182055",
"rogerio@hotmail.com", 1, 3);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Thomas", "Rua 4", 4, 85025790, "4288152023",
"thomas@hotmail.com", 1, 2);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Maicon", "Rua 5", 5, 85725390, "4236192543",
"maicon@hotmail.com", 1, 2);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Marcos", "Rua 6", 6, 87025390, "4236232010",
"marcos@gmail.com", 2, 1);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Leandro", "Rua 7", 8, 85025790, "4299102040",
"leandro@gmail.com",  2, 2);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Paulo", "Rua 8", 9, 85325390, "4288181033",
"paulo@gmail.com",  2, 3);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Guilherme", "Rua 9", 10, 85035390, "4288151033",
"guilherme@gmail.com",  2, 2);
INSERT INTO clients(name, address, address_number, address_cep, phone, email, type, city_id) VALUES ("Juca", "Rua 10", 11, 85035390, "4236193543",
"juca@gmail.com", 2, 2);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `clients_pi`
*/

INSERT INTO clients_pi(id, cpf, date_of_birth) VALUES (1, "18960196410", "1996-12-02");
INSERT INTO clients_pi(id, cpf, date_of_birth) VALUES (2, "89761184641", "1990-06-01");
INSERT INTO clients_pi(id, cpf, date_of_birth) VALUES (3, "56865189981", "2000-03-05");
INSERT INTO clients_pi(id, cpf, date_of_birth) VALUES (4, "80078987784", "1995-05-01");
INSERT INTO clients_pi(id, cpf, date_of_birth) VALUES (5, "05636927584", "1998-06-07");

/* ---------------------------------------------------- */

/*
*** Dumping data for table `clients_pc`
*/


INSERT INTO clients_pc(id, cnpj, company_name) VALUES (6, "04641158000100", "Contact computadores");
INSERT INTO clients_pc(id, cnpj, company_name) VALUES (7, "43441387000124", "Alta tecnologia");
INSERT INTO clients_pc(id, cnpj, company_name) VALUES (8, "82260863000162", "Milenium informática");
INSERT INTO clients_pc(id, cnpj, company_name) VALUES (9, "34568180000124", "Tech tudo");
INSERT INTO clients_pc(id, cnpj, company_name) VALUES (10, "36674375000184", "Inf hardware");

/* ---------------------------------------------------- */

/*
*** Dumping data for table `orders'
*/

INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("417.49", '2016-01-01 18:56:30', '2016-01-01 19:56:30', "Fechado", 6, 1);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("1969.96", '2016-01-01 18:56:30', '2016-01-01 19:56:30', "Fechado", 1, 2);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("2209.65", '2016-01-01 19:09:07', '2016-01-01 20:09:07', "Fechado", 5, 3);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("775.1", '2016-01-01 19:17:13', '2016-01-01 20:17:13', "Fechado", 4, 4);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("4186.1", '2016-01-01 19:26:01', '2016-01-01 20:26:01', "Fechado", 2, 5);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("12550.2", '2016-01-01 19:32:28', '2016-01-01 20:32:28', "Fechado", 3, 1);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("155.02", '2016-01-01 20:09:07', '2016-01-01 21:09:07', "Fechado", 7, 1);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("1838.04", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 2);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("785.00", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 5, 3);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("819.02", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 3);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("919.02", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 5);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("919.00", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 5);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("1029.00", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 1);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("1618.49", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 1);
INSERT INTO orders (total, created_at, closed_at, status, client_id, employee_id) VALUES ("3618.49", '2016-06-01 20:17:13', '2016-06-01 21:17:13', "Fechado", 8, 2);

/* ---------------------------------------------------- */

/*
*** Dumping data for table `orders'
*/

INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("417.49", 1, '2016-01-01 18:56:30', 1, 1);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("417.49", 1, '2016-01-01 18:56:30', 2, 1);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("517.49", 3, '2016-01-01 18:56:30', 2, 2);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("434.75", 1, '2016-01-01 19:09:07', 3, 32);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("823.95", 2, '2016-01-01 19:09:07', 3, 40);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("127", 1, '2016-01-01 19:09:07', 3, 43);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("155.05", 5, '2016-01-01 19:17:13', 4, 27);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("78.9", 5, '2016-01-01 19:26:01', 5, 46);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("823.95", 4, '2016-01-01 19:26:01', 5, 40);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("247.9", 2, '2016-01-01 19:26:01', 5, 30);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("1255.02", 10, '2016-01-01 19:32:28', 6, 17);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("155.02", 1, '2016-01-01 20:09:07', 7, 27);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("919.02", 1, '2016-06-01 20:17:13', 8, 6);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("785.00", 1, '2016-06-01 20:17:13', 9, 4);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("819.02", 1, '2016-06-01 20:17:13', 10, 5);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("919.02", 1, '2016-06-01 20:17:13', 11, 6);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("919.00", 1, '2016-06-01 20:17:13', 12, 7);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("1029.00", 1, '2016-06-01 20:17:13', 13, 8);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("1618.49", 1, '2016-06-01 20:17:13', 14, 9);
INSERT INTO sell_orders_items (price, amount, created_at, order_id, product_id) VALUES ("3618.492", 1, '2016-06-01 20:17:13', 15, 10);
