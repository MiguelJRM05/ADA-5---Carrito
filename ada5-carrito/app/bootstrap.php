<?php
declare(strict_types=1);
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
session_name('TECNOSHOP_V2');
session_start();
header('Cache-Control: no-store');
$_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function product_image(int $category): string { return 'assets/images/' . ([1=>'ram',2=>'ssd',3=>'gpu',4=>'case'][$category] ?? 'ram') . '.png'; }
function money(int $cents): string { return '$' . number_format($cents / 100, 2) . ' MXN'; }
function redirect(string $page): void { header('Location: ' . $page); exit; }
function token(): string { return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function positive($value): ?int {
    if (!is_scalar($value)) return null;
    $result = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range'=>1]]);
    return $result === false ? null : $result;
}
function db(): PDO {
    static $pdo;
    if ($pdo) return $pdo;
    $path = __DIR__ . '/config.php';
    if (!is_file($path)) throw new RuntimeException('Falta configurar la conexión.');
    $c = require $path;
    $pdo = new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",
        $c['user'], $c['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    return $pdo;
}
function products(): array {
    return db()->query('SELECT p.*, c.nombre AS categoria FROM productos p JOIN categorias c ON c.id=p.categoria_id ORDER BY p.id')->fetchAll();
}
function product(int $id) {
    $q = db()->prepare('SELECT * FROM productos WHERE id=?'); $q->execute([$id]); return $q->fetch();
}
function start_page(string $title): void {
    $count = array_sum($_SESSION['cart']);
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.e($title).' | TecnoShop</title><link rel="stylesheet" href="assets/style.css"></head><body><header><a class="brand" href="index.php">T<span>+</span> TecnoShop</a><nav><a href="index.php">Catálogo</a><a href="carrito.php">Carrito ('.e($count).')</a></nav></header><main>';
    if (isset($_SESSION['name'])) echo '<div class="user"><span>Hola, <strong>'.e($_SESSION['name']).'</strong></span><form action="acciones.php" method="post">'.token().'<button class="link" name="action" value="logout">Cerrar sesión</button></form></div>';
    if (isset($_SESSION['flash'])) { echo '<p class="notice" role="status">'.e($_SESSION['flash']).'</p>'; unset($_SESSION['flash']); }
}
function end_page(): void { echo '</main><footer>ADA 5 · Miguel Jesús Ruiz May · Tienda de demostración · Modelos, precios y existencias ficticios. Sin pagos reales.</footer></body></html>'; }
function db_error(): void {
    http_response_code(503);
    echo '<section class="panel"><h2>Configura la base de datos</h2><p>Inicia MySQL, importa sql/database.sql y copia app/config.example.php como app/config.php. Revisa los datos de conexión.</p></section>';
}
