<?php

declare(strict_types=1);

namespace AcmeWidgetCo\Customer;

use AcmeWidgetCo\Stock\Widget as Widget;
use AcmeWidgetCo\Stock\Catalogue as Catalogue;
use AcmeWidgetCo\Operations\Delivery as Delivery;
use AcmeWidgetCo\Operations\Offers as Offers;

/**
 * Description of Basket
 *
 * @author Matt
 */
class Basket implements \Countable, \Iterator {

    private Array $items = [];
    private int $current = 0;
    private $catalogue;
    private $delivery;
    private $offers;

    /**
     * Construct.
     * @param Catalogue $catalogue
     * @param Delivery $delivery
     * @param Offers $offers
     */
    public function __construct(Catalogue $catalogue, Delivery $delivery, Offers $offers) {
        $this->catalogue = $catalogue;
        $this->delivery = $delivery->addBasket($this);
        $this->offers = $offers->addBasket($this)->addCatalogue($catalogue);
    }

    /**
     * Calculate the total value of the basket, optionally with delivery and offers applied.
     * @param bool $withDelivery Include delivery charge?
     * @param bool $withOffers Apply offer discounts?
     * @return string
     */
    public function total(bool $withDelivery = false, bool $withOffers = false): string {

        // Using bcscale of 2 in the bcmath functions to round up to real money values.

        $sum = '0.00';

        if (count($this) === 0) {
            return $sum;
        }

        foreach ($this as $widget) {
            /** @var $widget Widget */
            $sum = bcadd($sum, $widget->getPrice(), 2);
        }

        if ($withOffers) {
            $sum = bcsub($sum, $this->offers->getDiscount(), 2);
        }

        if ($withDelivery) {
            $sum = bcadd($sum, $this->delivery->getCharge(), 2);
        }


        return $sum;
    }

    /**
     * Add a widget to the basket using string reference code.
     * @param string $code
     * @return \AcmeWidgetCo\Customer\Basket
     */
    public function add( string $code): Basket {
        $this->items[] = $this->catalogue->fetch($code);
        return $this;
    }

    /**
     * Empty the basket.
     * @return \AcmeWidgetCo\Customer\Basket
     */
    public function empty(): Basket {
        $this->items = [];
        return $this;
    }

    /**
     * Get the delivery cost, if any.
     * @return string|null
     */
    public function getDelivery(): ?string {
        return $this->delivery->getCharge();
    }

    /**
     * Get the applicable discount, if any.
     * @return string|null
     */
    public function getDiscount(): ?string {
        return $this->offers->getDiscount();
    }

    /**
     * Fetch the entire array of contents.
     * @return array
     */
    public function getContents(): Array {
        return $this->items;
    }

    /**
     * Get an array of basket contents, but just as item codes.
     * @return array
     */
    public function getItemsAsCodes(): Array {
        $codes = [];
        foreach ($this as $item) {
            /** @var $item Widget */
            $codes[] = $item->getCode();
        }
        return $codes;
    }

    /**
     * Check if the current entry is a valid item in the internal items array.
     * @return bool
     */
    final public function valid(): bool {
        return isset($this->items[$this->current]);
    }

    /**
     * Move the pointer on by one.
     * @return void
     */
    final public function next(): void {
        ++$this->current;
    }

    /**
     * Rewind the pointer to the first item.
     * @return void
     */
    final public function rewind(): void {
        $this->current = 0;
    }

    /**
     * Fetch the current key.
     * @return \scalar
     */
    final public function key(): \scalar {
        return $this->current;
    }

    /**
     * Fetch the current item in the array.
     * @return Widget|null
     */
    final public function current(): ?Widget {
        if ($this->current + 1 > count($this->items)) {
            return null;
        }
        return $this->items[$this->current];
    }

    /**
     * Fetch the count of items.
     * @return int
     */
    final public function count(): int {
        return count($this->items);
    }

}
