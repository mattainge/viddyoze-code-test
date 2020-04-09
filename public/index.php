<?php
/*
 * Matthew Ainge 2020-04-08
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use AcmeWidgetCo\Stock\Catalogue as Catalogue;
use AcmeWidgetCo\Engine\FormHandler as FormHandler;
use AcmeWidgetCo\Stock\Widget as Widget;

$catalogue = new Catalogue();

// Handle any submitted JSON data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new FormHandler();
    $handler->handlePost();
}
?>


<!DOCTYPE html>

<html lang="en">
    <head>
        <title>Acme Widget Co.</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="/lib/acme.css">
        <script src="/lib/jquery-3.4.1.min.js"></script>
    </head>
    <body>

        <input type='hidden' name='basketContents' id='basketContents' value='{}' />

        <header style="font-size: 1.1em;"><h1>Acme Widget Co.</h1></header>
        <div id='shop'>
            <div id='basket'>
                <h2>Basket</h2>
                <button id="empty">Start over</button>
                <p id="buystuff">Nothing yet - buy some stuff!</p>
                <ul></ul>
                <p id="delivery">Plus delivery: <span></span></p>
                <p id="discounts">Minus discounts: <span></span></p>
                <p id="activeOffers"></p>
                <p id="total">Total: <span></span></p>
                <button id="checkout">Checkout</button>
            </div>
            <section id='catalogue'>
                <header>All our widgets</header>
                <ul>
                    <?php
                    foreach ($catalogue->list() as $widget) {
                        /* @var $widget Widget */
                        ?>
                        <li class='widget'>
                            <p class='title'><?php echo $widget->getTitle(); ?></p>
                            <p class='code'><?php echo $widget->getCode(); ?></p>
                            <p class='price'><?php echo $widget->getPrice(); ?></p>
                            <p class='description'><?php echo $widget->getDescription(); ?></p>
                            <button class='buy' data-code="<?php echo $widget->getCode(); ?>" id='<?php echo $widget->getCode(); ?>_add'>Add to basket</button>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </section>
        </div>

        <script>

            const basket = $('#basketContents');
            const basketDisplay = $('#basket ul');

            $('#catalogue button.buy').click(function () {
                let code = $(this).data('code');
                let theJSON = JSON.parse(basket.val());
                let count = 0;

                if (theJSON[ code ]) {
                    count = theJSON[ code ];
                }

                theJSON[ code ] = ++count;

                update(JSON.stringify(theJSON));
            });

            $('#checkout').click(function () {
                alert('Thanks for the interest and giving the demo a go. Stay healthy, everyone!');
            });

            $('#empty').click(function () {
                update('{}');
                $('#buystuff').show() ;
                $('p#delivery').hide() ;
            });

            function update(theJSON) {
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: theJSON
                })
                        .then(rawResponse => rawResponse.json())
                        //   .then(json => (console.log(json)) ? json : json)
                        .then(json => updateBasket(json))
                        .then(json => updateInfo('delivery', json))
                        .then(json => updateInfo('discounts', json))
                        .then(json => updateInfo('total', json))
                        ;

            }

            function updateBasket(json) {
                return new Promise((resolve) => {

                    if (!json.hasOwnProperty('basket')) {
                        resolve(json);
                    }
                    
                    if( json.basket.length === 0 ){
                        $(basket).val( '{}' ) ;
                    } else {
                        $(basket).val(JSON.stringify(json.basket));    
                        $('#buystuff').slideUp() ;
                    }

                    $(basketDisplay).html('<ul></ul>');

                    Object.keys(json.basket)
                            .forEach(key =>
                                $(basketDisplay).append('<li>' + key + ' (' + json.basket[key] + ')</li>')
                            );


                    resolve(json);
                });
            }

            function updateInfo(id, json) {
                return new Promise((resolve) => {

                    if (json[id]) {
                        let value = parseFloat(json[id]);
                        $('#' + id).slideDown().find('span').first().html(value.toFixed(2));
                    }

                    resolve(json);
                });
            }

        </script>

    </body>
</html>
