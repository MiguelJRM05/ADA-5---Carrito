# ADA 5 · Guía paso a paso

**Alumno:** Miguel Jesús Ruiz May. **Modalidad:** individual.
**Proyecto:** TecnoShop, carrito académico con PHP, MySQL y sesiones.

## 1. Ejecutar en Windows con XAMPP

1. Extrae el ZIP. Copia solamente la carpeta `ada5-carrito` dentro de `C:\xampp\htdocs`.
2. Abre el panel de XAMPP y pulsa **Start** en Apache y MySQL. Ambos deben estar activos.
3. Abre `http://localhost/phpmyadmin/`. Selecciona **Importar**, elige `ada5-carrito/sql/database.sql` y pulsa **Importar/Continuar**. El script crea la base `ada5_tecnoshop`, sus dos tablas y ocho productos.
4. En `ada5-carrito/app`, copia `config.example.php` y llama a la copia `config.php`. Activa las extensiones visibles en el Explorador para evitar `config.php.txt`.
5. Los valores incluidos son los habituales para XAMPP local: host `127.0.0.1`, puerto `3306`, base `ada5_tecnoshop`, usuario `root` y contraseña vacía. Si configuraste otros, edita la copia.
6. Abre `http://localhost/ada5-carrito/`. Escribe tu nombre y pulsa **Entrar a la tienda**.
7. Añade una RAM Pulse 16 GB y un SSD Velocity NVMe 1 TB. En el carrito cambia la RAM a dos unidades, actualiza, elimina el SSD, vuelve al catálogo y regresa al carrito. Deben conservarse el nombre y las dos unidades.
8. Cierra sesión. El nombre y carrito de esa sesión se eliminan.

Si Apache usa otro puerto, escribe por ejemplo `http://localhost:8080/ada5-carrito/`. Ajusta también la dirección de phpMyAdmin. Si aparece la pantalla de configuración, comprueba MySQL, la importación y `config.php`. Si aparece el código PHP como texto, revisa que estés entrando por Apache, no por Live Server ni por doble clic.

## 2. Cómo se organiza el código

| Archivo | Función |
|---|---|
| `index.php` | Formulario de nombre y catálogo consultado desde MySQL. |
| `carrito.php` | Lista productos de la sesión, muestra formularios y calcula subtotales y total. |
| `acciones.php` | Recibe POST, valida y ejecuta altas, cambios, bajas, vaciado y cierre de sesión. |
| `app/bootstrap.php` | Inicia sesión, configura cookies, conecta mediante PDO y define funciones reutilizables. |
| `app/config.example.php` | Modelo de configuración local. |
| `assets/style.css` | Diseño adaptable en gris oscuro y cyan. |
| `sql/database.sql` | Esquema y registros iniciales. |

`session_start()` recupera la sesión asociada a la cookie del navegador. `$_SESSION['name']` almacena el nombre y `$_SESSION['cart']` relaciona identificadores de productos con cantidades. Por ejemplo, `[1 => 2]` representa dos unidades del producto 1. Los precios se leen en el servidor, no se aceptan desde el formulario. Al ingresar se regenera el identificador de sesión; al salir se destruye la sesión y se caduca la cookie.

El CRUD se aplica a los artículos del carrito: crear es añadir, leer es listar, actualizar es modificar cantidades y eliminar es retirar. No se modifican los productos del catálogo desde la interfaz. El carrito es temporal: no se guarda como pedido en MySQL y no reserva existencias. El nombre identifica al visitante, pero no acredita su identidad ni funciona como contraseña.

## 3. Base de datos para explicar en el video

| Tabla | Campos | Propósito |
|---|---|---|
| `categorias` | `id` (PK), `nombre` | Agrupar artículos en memoria RAM, almacenamiento, tarjetas gráficas y gabinetes. |
| `productos` | `id` (PK), `categoria_id` (FK), `nombre`, `descripcion`, `precio_centavos`, `stock`, `simbolo` | Mantener el catálogo y la disponibilidad máxima para el ejercicio. |

Relación: una categoría tiene muchos productos; cada producto pertenece a una categoría. `productos.categoria_id` referencia a `categorias.id`. El precio se guarda en centavos enteros para evitar errores de redondeo en los cálculos del carrito. La tabla de usuarios no es necesaria porque no se implementan cuentas; el visitante vive en la sesión de PHP.

En phpMyAdmin muestra las pestañas **Estructura** y **Examinar**. Consulta opcional:

```sql
SELECT p.id, p.nombre, c.nombre AS categoria,
       p.precio_centavos / 100 AS precio_mxn, p.stock
FROM productos p
JOIN categorias c ON c.id = p.categoria_id;
```

## 4. Publicar código y sitio

El PDF pide tres enlaces: sitio, GitHub y video. También menciona una versión publicada del FrontEnd; para demostrar todas las funciones, la opción más completa es alojar el proyecto PHP con su base MySQL.

**Código:** crea un repositorio en tu cuenta de GitHub y carga el contenido de `ada5-carrito`, incluyendo `app/config.example.php`, `sql` y `assets`. No cargues `app/config.php` con credenciales reales. Si subes archivos por la web, exclúyelo manualmente: `.gitignore` no impide una carga manual. Conserva la carpeta de materiales fuera del directorio público. Comprueba que el evaluador pueda abrir el repositorio.

**Sitio funcional:** necesitas un alojamiento que admita PHP 8 y MySQL/MariaDB. El despliegue de Sites disponible en este entorno utiliza otro runtime, por lo que no sustituye el servidor PHP requerido. En tu alojamiento:

1. Crea una base y un usuario de base de datos, con acceso únicamente a esa base.
2. Si el proveedor asigna un nombre de base propio, selecciona esa base y omite las líneas `CREATE DATABASE` y `USE` del SQL al importar. Conserva el resto.
3. Carga los archivos de la aplicación en la carpeta web que indique el proveedor.
4. Crea allí `app/config.php` con host, puerto, base, usuario y contraseña del proveedor. No uses las credenciales locales.
5. Usa HTTPS. Protege `app` y `sql` contra descargas directas; se incluyen reglas `.htaccess` para Apache. En servidores distintos solicita la regla equivalente o sitúa configuración y SQL fuera de la carpeta pública y ajusta sus rutas.
6. Abre la URL pública desde una ventana privada y verifica nombre, CRUD y navegación. No entregues una dirección `localhost`, porque solo funciona en tu computadora.

GitHub Pages solo sirve contenido estático y no ejecuta PHP. Subir allí estos archivos no hace funcionar las sesiones ni MySQL. No cambies `.php` a `.html` para intentar arreglarlo.

**Video:** graba la ejecución local y la explicación siguiendo el guion. Comprueba la duración real de 3 a 5 minutos. Carga el video en un servicio permitido y prueba su enlace sin iniciar sesión antes de entregarlo.

## 5. Referencias técnicas

PHP Group. (s. f.). *session_start*. https://www.php.net/manual/es/function.session-start.php

PHP Group. (s. f.). *PDO::prepare*. https://www.php.net/manual/en/pdo.prepare.php

GitHub. (s. f.). *Creating a GitHub Pages site*. https://docs.github.com/en/pages/getting-started-with-github-pages/creating-a-github-pages-site


## Actualización a la tienda de componentes (versión 2)

Esta versión usa la base **ada5_tecnoshop** para no mezclar el catálogo nuevo con la base de la versión anterior. No necesitas borrar tu base anterior.

1. Respalda tu carpeta anterior. Copia la nueva `ada5-carrito` a `C:\xampp\htdocs`.
2. Importa el nuevo `sql/database.sql` en phpMyAdmin local.
3. Si ya tienes `app/config.php`, cambia solamente `database` a `ada5_tecnoshop`. Si no existe, copia `config.example.php` como `config.php`.
4. Abre `http://localhost/ada5-carrito/` y pulsa Ctrl+F5 para recargar los estilos.
5. Ingresa de nuevo tu nombre. La versión 2 utiliza una sesión separada.

En un alojamiento, crea una base nueva e importa **sql/database_hosting.sql**, que ya omite CREATE DATABASE y USE. En el `config.php` remoto usa el nombre completo asignado por el alojamiento.

Se incluyen ocho artículos ficticios en cuatro categorías y cuatro imágenes ilustrativas generadas, una por categoría. Las imágenes están en `assets/images` y no requieren conexión externa. Los filtros y el ordenamiento usan `assets/catalog.js`; sin JavaScript sigue disponible todo el catálogo y el carrito PHP.

Para el video utiliza RAM Pulse 16 GB ($699) y SSD Velocity NVMe 1 TB ($1,199). Una unidad de cada uno suma $1,898; dos RAM y un SSD suman $2,597. Al eliminar el SSD quedan $1,398.
