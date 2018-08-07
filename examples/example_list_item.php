<?php
session_start();
require '../Meli/meli.php';
require '../configApp.php';
$meli = new Meli($appId, $secretKey);
if($_GET['code']) {
	// If the code was in get parameter we authorize
	$user = $meli->authorize($_GET['code'], $redirectURI);
	// Now we create the sessions with the authenticated user
	$_SESSION['access_token'] = $user['body']->access_token;
	$_SESSION['expires_in'] = $user['body']->expires_in;
	$_SESSION['refresh_token'] = $user['body']->refresh_token;
	// We can check if the access token in invalid checking the time
	if($_SESSION['expires_in'] + time() + 1 < time()) {
		try {
			print_r($meli->refreshAccessToken());
		} catch (Exception $e) {
			echo "Exception: ",  $e->getMessage(), "\n";
		}
	}
	// We construct the item to POST
	$item = array(
		"title" => "Item de Prueba. Por favor no Comprar",
        "category_id" => "MLU1648",
        "price" => 1000,
        "currency_id" => "UYU",
        "available_quantity" => 1,
        "buying_mode" => "buy_it_now",
        "listing_type_id" => "bronze",
        "condition" => "new",
        "description" => "Item de Prueba. Por favor no Comprar",
        "video_id" => "Q6dsRpVyyWs",
        "warranty" => "12 month",
        "pictures" => array(
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/IPhone_7_Plus_Jet_Black.svg/440px-IPhone_7_Plus_Jet_Black.svg.png"
            ),
            array(
                "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/IPhone7.jpg/440px-IPhone7.jpg"
            )
        )
    );
	
	// We call the post request to list a item
	echo '<pre>';
	print_r($meli->post('/items', $item, array('access_token' => $_SESSION['access_token'])));
	echo '</pre>';
} else {
	echo '<a href="' . $meli->getAuthUrl($redirectURI, Meli::$AUTH_URL['MLU']) . '">Login using MercadoLibre oAuth 2.0</a>';
}
