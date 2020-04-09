<?php

declare(strict_types=1);

namespace AcmeWidgetCo\Engine;

use AcmeWidgetCo\Stock\Catalogue as Catalogue;
use AcmeWidgetCo\Customer\Basket as Basket;
use AcmeWidgetCo\Operations\Delivery as Delivery;
use AcmeWidgetCo\Operations\Offers as Offers;
use AcmeWidgetCo\Engine\FormHandler as FormHandler;
use AcmeWidgetCo\Stock\Widget as Widget;

/**
 * Handle the JSON data and return results of the basket changes.
 *
 * @author Matt
 */
class FormHandler {

    /**
     * Handle the JSON data and return results of the basket changes.
     * @return string
     */
    public function handlePost(): string {

        $basket = new Basket(new Catalogue(), new Delivery(), new Offers());

        # Get JSON as a string
        $json_str = file_get_contents('php://input');

        # Get as an object
        $json_obj = json_decode($json_str);

        if (!is_object($json_obj)) {
            return null;
        }

        foreach ($json_obj as $item => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $basket->add($item);
            }
        }

        $data = [
            'basket' => array_count_values($basket->getItemsAsCodes()),
            'delivery' => $basket->getDelivery(),
            'discounts' => $basket->getDiscount(),
            'total' => $basket->total(true, true)
                ];

        header('Content-type: application/json');
        //echo json_encode( $json ) ;
        die(json_encode($data));
    }

}
