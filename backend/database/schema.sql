-- Crear base de datos
CREATE DATABASE IF NOT EXISTS facturacion_db;

-- Tabla user
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(30) NOT NULL DEFAULT 'user',
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabla client
CREATE TABLE IF NOT EXISTS client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_number VARCHAR(30) NOT NULL UNIQUE,
    business_name VARCHAR(150) NOT NULL,
    email VARCHAR(120),
    address VARCHAR(180),
    status TINYINT(1) NOT NULL DEFAULT 1,
    deleted_by INT DEFAULT NULL,
    FOREIGN KEY (deleted_by) REFERENCES user(id)
);

-- Tabla invoice
CREATE TABLE IF NOT EXISTS invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(30) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    payment_method VARCHAR(30),
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id)
);

-- Usuarios de prueba (password: 123456, hash generado en https://bcrypt-generator.com/)
INSERT INTO user (email, password, name, last_name, phone, role, status) VALUES
('admin@facturacion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Yosman', 'Martinez', '3001234567', 'admin', 1);

-- Clientes de prueba (Datos generados con asistencia de IA)
INSERT INTO client (document_number, business_name, email, address) VALUES
('900123456', 'Distribuciones ABC SAS', 'contacto@abc.com', 'Calle 10 #25-30 Bogota'),
('800654321', 'Comercial XYZ Ltda', 'info@xyz.com', 'Carrera 15 #40-50 Medellin'),
('900789012', 'Servicios Tecnicos Pro', 'soporte@pro.com', 'Av 68 #12-34 Cali'),
('900111222', 'Importaciones El Sol SA', 'ventas@elsol.com', 'Calle 100 #8-55 Bogota'),
('800333444', 'Logistica Nacional SAS', 'admin@logistica.com', 'Av 30 #60-20 Bogota'),
('900555666', 'Papeleria Creativa Ltda', 'info@papeleriacreativa.com', 'Calle 50 #20-15 Medellin'),
('800777888', 'Construcciones Modernas SA', 'contacto@construmoderna.com', 'Carrera 7 #45-10 Cali'),
('900999000', 'Tecnologia Avanzada SAS', 'soporte@tecavanzada.com', 'Av 19 #100-30 Bogota'),
('800222333', 'Alimentos Del Valle Ltda', 'pedidos@alimentosvalle.com', 'Calle 5 #15-40 Palmira'),
('900444555', 'Textiles Colombianos SAS', 'ventas@textilescol.com', 'Carrera 50 #30-20 Medellin');

-- Facturas de prueba (Datos generados con asistencia de IA)
INSERT INTO invoice (number, client_id, issue_date, due_date, payment_method, subtotal, tax_amount, total, notes) VALUES
('FAC-001', 1, '2026-01-15', '2026-02-15', 'Contado', 100000.00, 19000.00, 119000.00, 'Venta de productos'),
('FAC-002', 2, '2026-02-01', '2026-03-01', 'Credito', 250000.00, 47500.00, 297500.00, 'Servicio de consultoria'),
('FAC-003', 3, '2026-03-10', '2026-04-10', 'Transferencia', 500000.00, 95000.00, 595000.00, 'Mantenimiento equipos'),
('FAC-004', 4, '2026-01-20', '2026-02-20', 'Contado', 75000.00, 14250.00, 89250.00, 'Compra de insumos'),
('FAC-005', 5, '2026-02-15', '2026-03-15', 'Credito', 1500000.00, 285000.00, 1785000.00, 'Servicio de transporte'),
('FAC-006', 6, '2026-03-01', '2026-04-01', 'Contado', 35000.00, 6650.00, 41650.00, 'Material de oficina'),
('FAC-007', 7, '2026-03-20', '2026-04-20', 'Transferencia', 3200000.00, 608000.00, 3808000.00, 'Proyecto de construccion'),
('FAC-008', 8, '2026-04-05', '2026-05-05', 'Credito', 850000.00, 161500.00, 1011500.00, 'Licencias de software'),
('FAC-009', 9, '2026-04-10', '2026-05-10', 'Contado', 120000.00, 22800.00, 142800.00, 'Productos alimenticios'),
('FAC-010', 10, '2026-04-15', '2026-05-15', 'Transferencia', 450000.00, 85500.00, 535500.00, 'Telas y materiales'),
('FAC-011', 1, '2026-04-20', '2026-05-20', 'Contado', 200000.00, 38000.00, 238000.00, 'Pedido adicional productos'),
('FAC-012', 3, '2026-05-01', '2026-06-01', 'Credito', 680000.00, 129200.00, 809200.00, 'Soporte tecnico mensual');
