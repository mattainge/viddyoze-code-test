<?php

declare(strict_types=1);

namespace AcmeWidgetCo\Operations;

use AcmeWidgetCo\Customer\Basket as Basket;

/**
 * Delivery functions depend on Basket object (with no order, there is no delivery).
 * Delivery is calculated by total cost of basketed widgets combined.
 *
 * @author Matt
 */
class Delivery {

    private Basket $basket;
    private Array $thresholds = [];
    private $freeDeliveries = false;

    /**
     * Construct
     */
    public function __construct() {
        $this->generateRules()->determineFreeDeliveries();
    }

    /**
     * Add a basket object.
     * @param Basket $basket
     * @return \AcmeWidgetCo\Operations\Delivery
     */
    public function addBasket(Basket $basket): Delivery {
        $this->basket = $basket;
        return $this;
    }

    /**
     * Calculate and fetch the delivery charge.
     * @return string|null
     * @throws Exception
     */
    public function getCharge(): ?string {
        bcscale(2);

        // If no basket contents, no (free!) delivery charge
        if (!$this->basket) {
            throw new Exception('No basket provided to Delivery object at ' . __METHOD__);
        }

        // If no basket contents, no (free!) delivery charge
        if ($this->basket->count() === 0) {
            return null;
        }

        foreach ($this->thresholds as $threshold => $charge) {
            if ($this->basketUnder((string) $threshold)) {
                return $this->thresholds[$threshold];
            }
        }

        // If no matches, we have matched or exceeded the top threshold.
        // If free deliveries over this amount are enabled, return 0 (free)

        if ($this->freeDeliveries) {
            return '0.00';
        }

        // Else return the lowest delivery charge
        return end( $this->thresholds );
    }

    /**
     * Internal function to check if the basket total is under the threshold in question.
     * @param string $threshold
     * @return bool
     * @throws Exception
     */
    private function basketUnder(string $threshold): bool {
        // If no basket contents, no (free!) delivery charge
        if (!$this->basket) {
            throw new Exception('No basket provided to Delivery object at ' . __METHOD__);
        }

        return bccomp($this->basket->total(false, true), $threshold) === -1;
    }

    /**
     * Generate the delivery rules.
     * @return \AcmeWidgetCo\Operations\Delivery
     */
    private function generateRules(): Delivery {
        // Fetch from DB all delivery cost thresholds. Smallest must be first.
        // All money values stored as strings to avoid floating point inaccuracies

        $this->thresholds = [
            '50' => '4.95',
            '90' => '2.95'
        ];
        return $this;
    }

    /**
     * Are we allowing free deliveries?
     * @return \AcmeWidgetCo\Operations\Delivery
     */
    private function determineFreeDeliveries(): Delivery {
        // Fetch from DB if we are using free delivery when over the max threshold
        $this->freeDeliveries = true;
        return $this;
    }

    // Other methods nice for customers:
    // how much till next delivery charge saving?
    // how much for free delivery?
}
