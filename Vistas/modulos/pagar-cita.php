<?php
// Cargar el autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Configurar el token de acceso de MercadoPago
MercadoPago\SDK::setAccessToken("TEST-4737373440007846-120116-f57d6c94b2ad7aa75ef122997ac882d8-1109246217");

// Crear una preferencia
$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = "CT-001";  // ID del producto
$item->title = "Cita";  // Nombre del producto
$item->quantity = 1;    // Cantidad
$item->unit_price = 29.99; // Precio

$preference->items = [$item];
$preference->statement_descriptor = "Clinica Maria Auxiliadora";
$preference->external_reference = "CTD001";

// Definir las URLs de redirección
$preference->back_urls = [
    "success" => "http://localhost/clinica/success.php",
    "failure" => "http://localhost/clinica/failure.php",
    "pending" => "http://localhost/clinica/pending.php"
];

$preference->auto_return = "approved";

// Guardar la preferencia
$preference->save();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pago con MercadoPago</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>
    <!-- Contenedor del botón de pago -->
    <div id="wallet_container"></div>

    <script>
        const mp = new MercadoPago('TEST-70493c00-15e4-4568-aa44-401f0ee7214e', {
            locale: 'es-PE'
        });

        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: '<?php echo $preference->id; ?>'
            },
            customization: {
                texts: {
                    action: 'Comprar',
                    valueProp: 'Seguridad y facilidad'
                },
                styles: {
                    background: '#28a745',
                    borderRadius: '10px',
                    color: '#ffffff',
                    fontSize: '18px',
                }
            }
        });
    </script>
</body>
</html>
