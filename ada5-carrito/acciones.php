<?php
require __DIR__.'/app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit('Método no permitido.'); }
if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) { http_response_code(403); exit('Solicitud inválida. Regresa al catálogo y recarga la página.'); }
$action = $_POST['action'] ?? '';
if ($action === 'login') {
    $name = is_string($_POST['name'] ?? null) ? trim($_POST['name']) : '';
    if ($name === '' || preg_match('/[\x00-\x1F\x7F]/u', $name) || !preg_match('/^.{1,60}$/us', $name)) {
        $_SESSION['flash'] = 'Escribe un nombre válido de 1 a 60 caracteres.'; redirect('index.php');
    }
    session_regenerate_id(true); $_SESSION['name'] = $name; $_SESSION['cart'] = [];
    $_SESSION['csrf'] = bin2hex(random_bytes(32)); redirect('index.php');
}
if (!isset($_SESSION['name'])) { $_SESSION['flash']='Primero ingresa tu nombre.'; redirect('index.php'); }
if ($action === 'logout') {
    $_SESSION = []; $p=session_get_cookie_params();
    setcookie(session_name(), '', ['expires'=>time()-42000, 'path'=>$p['path'], 'domain'=>$p['domain'], 'secure'=>$p['secure'], 'httponly'=>true, 'samesite'=>'Lax']);
    session_destroy(); redirect('index.php');
}
if ($action === 'clear') { $_SESSION['cart']=[]; $_SESSION['flash']='Carrito vaciado.'; redirect('carrito.php'); }
$id = positive($_POST['id'] ?? null);
if (!$id || !in_array($action, ['add','update','remove'], true)) { $_SESSION['flash']='Operación o producto inválido.'; redirect('carrito.php'); }
if ($action === 'remove') { unset($_SESSION['cart'][$id]); $_SESSION['flash']='Producto eliminado.'; redirect('carrito.php'); }
$qty = positive($_POST['quantity'] ?? null);
try {
    $p = product($id);
    if (!$p || !$qty) throw new InvalidArgumentException('Selecciona un producto y una cantidad entera mayor que cero.');
    if ($action === 'update' && !isset($_SESSION['cart'][$id])) throw new InvalidArgumentException('El producto ya no está en el carrito.');
    $total = $qty + ($action === 'add' ? ($_SESSION['cart'][$id] ?? 0) : 0);
    if ($total > (int)$p['stock']) throw new InvalidArgumentException('La cantidad supera la disponibilidad del producto.');
    $_SESSION['cart'][$id] = $total;
    $_SESSION['flash'] = $action === 'add' ? 'Producto añadido al carrito.' : 'Cantidad actualizada.';
} catch (InvalidArgumentException $ex) { $_SESSION['flash'] = $ex->getMessage(); }
catch (Throwable $ex) { $_SESSION['flash']='No se pudo consultar la base de datos. Revisa la conexión e intenta otra vez.'; }
redirect('carrito.php');
