-- Importar en phpMyAdmin. No elimina tablas existentes.
CREATE TABLE IF NOT EXISTS categorias (
 id INT UNSIGNED PRIMARY KEY,
 nombre VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS productos (
 id INT UNSIGNED PRIMARY KEY,
 categoria_id INT UNSIGNED NOT NULL,
 nombre VARCHAR(100) NOT NULL,
 descripcion VARCHAR(255) NOT NULL,
 precio_centavos INT UNSIGNED NOT NULL,
 stock INT UNSIGNED NOT NULL,
 simbolo VARCHAR(10) NOT NULL,
 FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;
INSERT IGNORE INTO categorias VALUES (1,'Memoria RAM'),(2,'Almacenamiento'),(3,'Tarjetas gráficas'),(4,'Gabinetes');
INSERT IGNORE INTO productos VALUES
(1,1,'RAM Pulse 16 GB','DDR4 · 3200 MT/s · 1 módulo de 16 GB. Una opción de ejemplo para equipos de estudio y juego.',69900,20,'RAM'),
(2,1,'RAM Prism RGB 32 GB','DDR5 · 6000 MT/s · Kit de 2 × 16 GB con iluminación RGB. Requiere una placa compatible con DDR5.',189900,12,'RAM'),
(3,2,'SSD Velocity NVMe 1 TB','M.2 2280 · PCIe 4.0 · 1 TB. Almacenamiento de ejemplo para sistema, aplicaciones y juegos.',119900,18,'SSD'),
(4,2,'SSD Velocity NVMe 2 TB','M.2 2280 · PCIe 4.0 · 2 TB. Mayor capacidad para bibliotecas de juegos y proyectos.',219900,10,'SSD'),
(5,3,'Gráfica Nova Dual 8 GB','8 GB GDDR6 · Doble ventilador · PCIe. Modelo ilustrativo; verifica dimensiones y alimentación al elegir una GPU real.',549900,8,'GPU'),
(6,3,'Gráfica Nova Pro 12 GB','12 GB GDDR6 · Doble ventilador · PCIe. Ejemplo de una tarjeta para un equipo de alto rendimiento.',799900,6,'GPU'),
(7,4,'Gabinete Orbit Air Black','Mid tower · ATX / micro-ATX · Panel lateral transparente. Ventilación frontal e iluminación cyan.',129900,14,'CASE'),
(8,4,'Gabinete Orbit Air Plus','Mid tower · ATX / micro-ATX · Panel lateral transparente. Ejemplo de gabinete para un ensamble personalizable.',169900,9,'CASE');
