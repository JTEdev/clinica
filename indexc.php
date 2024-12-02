<?php
// Cargar el autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Configurar el token de acceso de MercadoPago
MercadoPago\SDK::setAccessToken("TEST-4737373440007846-120116-f57d6c94b2ad7aa75ef122997ac882d8-1109246217");

// Crear una preferencia
$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = "CT-001";  // ID del producto (puede ser cualquier identificador único)
$item->title = "Cita";  // Nombre del producto (en este caso una cita médica)
$item->quantity = 1;    // Cantidad de productos
$item->unit_price = 29.99; // Precio del producto

$preference->items = [$item];
$preference->statement_descriptor = "Clinica Maria Auxiliadora";  // Descripción que aparecerá en el estado de cuenta del comprador
$preference->external_reference = "CTD001";  // Referencia externa, útil para rastrear el pago

// Definir las URLs de redirección
$preference->back_urls = [
    "success" => "https://www.tu-sitio.com/success.php",  // Página de éxito después de un pago exitoso
    "failure" => "https://www.tu-sitio.com/failure.php",  // Página de fallo si el pago no fue exitoso
    "pending" => "https://www.tu-sitio.com/pending.php"   // Página de pendiente si el pago queda en espera
];

$preference->auto_return = "approved"; // Redirige automáticamente al éxito si el pago es aprobado

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
    <!-- Contenedor del botón de pago de MercadoPago -->
    <div id="wallet_container"></div>

    <script>
        // Inicializar el SDK de MercadoPago con el public key de tu cuenta
        const mp = new MercadoPago('TEST-70493c00-15e4-4568-aa44-401f0ee7214e', {
            locale: 'es-PE'  // Establecer el idioma y la región
        });

        // Crear el botón de pago usando MercadoPago Bricks
        mp.bricks().create("wallet", "wallet_container", {
            initialization: {
                preferenceId: '<?php echo $preference->id; ?>', // Se pasa el ID de la preferencia desde PHP
                redirectMode: 'modal'  // El pago se realiza en un modal
            },
            customization: {
                texts: {
                    action: 'Comprar',  // Texto en el botón
                    valueProp: 'Seguridad y facilidad'  // Descripción adicional en el botón
                },
                styles: {
                    background: '#28a745',  // Fondo del botón en color verde
                    borderRadius: '10px',   // Bordes redondeados
                    color: '#ffffff',       // Color del texto en blanco
                    fontSize: '18px',       // Tamaño de la fuente del texto
                }
            }
        });
    </script>
</body>
</html>
