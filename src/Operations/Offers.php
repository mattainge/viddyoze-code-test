<?php

declare(strict_types=1);

namespace AcmeWidgetCo\Operations;

use AcmeWidgetCo\Customer\Basket as Basket;
use AcmeWidgetCo\Stock\Catalogue as Catalogue;
use AcmeWidgetCo\Operations\Offer as Offer;

/**
 * 
 *
 * @author Matt
 */
class Offers {

    private Array $offers = [];
    private Basket $basket;
    private Catalogue $catalogue;

    /**
     * Construct
     */
    public function __construct() {
        $this->generateOffers();
    }

    /**
     * Add a basket - necessary to evaluate what offers are applicable.
     * @param Basket $basket
     * @return \AcmeWidgetCo\Operations\Offers
     */
    public function addBasket(Basket $basket): Offers {
        $this->basket = $basket;
        return $this;
    }

    /**
     * Add the catalogue - necessary to get the widget codes.
     * @param Catalogue $catalogue
     * @return \AcmeWidgetCo\Operations\Offers
     */
    public function addCatalogue(Catalogue $catalogue): Offers {
        $this->catalogue = $catalogue;
        return $this;
    }

    /**
     * Fetch the discount applicable. BCMATH works in strings.
     * @return string
     */
    public function getDiscount(): string {

        bcscale(3);

        // Run each offer's 'apply' method on the basket
        $remainingBasketCodes = array_count_values($this->basket->getItemsAsCodes());

        $sum = '0.00';

        foreach ($this->offers as $offer) {
            /* @var $offer Offer */

            $discount = $offer->apply($remainingBasketCodes, $this->catalogue);

            if (is_numeric($discount)) {
                $sum = bcadd($sum, $discount);
                $remainingBasketCodes = $offer->getRemainingBasketCodes();
            }
        }

        return $sum;
    }

    /**
     * Generate offers.
     * @return \AcmeWidgetCo\Operations\Offers
     */
    private function generateOffers(): Offers {

        // Capacity to fetch from a DB
        // 
        // Allow for 'get _n_ this, get _n_ this other {fraction} price/free'
        // Each offer should be a class of its own. I contemplating just using a stdClass 
        // but my conscious wouldn't let me.

        $this->offers[] = new Offer('R01', 1, 'R01', 1, '0.5');

        // Testing a 'buy two B01, get two free' offer
        // $this->offers[] = new Offer('B01', 2, 'B01', 2, '1');

        return $this;
    }

}
