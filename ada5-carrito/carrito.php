<?php
require __DIR__.'/app/bootstrap.php';
if (!isset($_SESSION['name'])) { $_SESSION['flash']='Ingresa tu nombre para ver tu carrito.'; redirect('index.php'); }
start_page('Mi carrito'); ?>
<section class="hero small"><p class="eyebrow">TU SELECCIÓN</p><h1>Mi carrito.</h1><p>Modifica cantidades o continúa explorando el catálogo.</p></section>
<?php if (!$_SESSION['cart']): ?>
<section class="panel empty"><h2>Aún hay espacio para tus ideas</h2><p>Tu carrito está vacío. Añade tu primer accesorio desde el catálogo.</p><a class="button" href="index.php">Explorar productos</a></section>
<?php else:
try { $all = array_column(products(), null, 'id'); } catch (Throwable $ex) { db_error(); end_page(); exit; }
$sum=0; ?>
<div class="tablewrap"><table><caption>Productos guardados en tu sesión</caption><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Acción</th></tr></thead><tbody>
<?php foreach ($_SESSION['cart'] as $id=>$qty):
if (!isset($all[$id])) { unset($_SESSION['cart'][$id]); continue; }
$p=$all[$id]; $subtotal=(int)$p['precio_centavos']*$qty; $sum+=$subtotal; ?>
<tr><td><div class="cartproduct"><img src="<?=e(product_image((int)$p['categoria_id']))?>" alt="" width="64" height="64"><div><strong><?=e($p['nombre'])?></strong><br><small><?=e($p['categoria'])?></small></div></div></td><td><?=money((int)$p['precio_centavos'])?></td><td><form class="quantity" method="post" action="acciones.php"><?=token()?><input type="hidden" name="id" value="<?=e($id)?>"><input aria-label="Cantidad de <?=e($p['nombre'])?>" type="number" name="quantity" value="<?=e($qty)?>" min="1" max="<?=e($p['stock'])?>" required><button name="action" value="update">Actualizar</button></form></td><td><?=money($subtotal)?></td><td><form method="post" action="acciones.php"><?=token()?><input type="hidden" name="id" value="<?=e($id)?>"><button class="danger" name="action" value="remove">Eliminar</button></form></td></tr>
<?php endforeach; ?></tbody><tfoot><tr><th colspan="3">Total de productos</th><td colspan="2" class="price"><?=money($sum)?></td></tr></tfoot></table></div>
<div class="actions"><a class="button" href="index.php">Seguir comprando</a><form method="post" action="acciones.php"><?=token()?><button class="secondary" name="action" value="clear">Vaciar carrito</button></form></div><p class="hint">El carrito no reserva inventario. Este ejercicio no procesa pedidos ni pagos.</p>
<?php endif; end_page(); ?>
