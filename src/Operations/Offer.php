<?php

namespace AcmeWidgetCo\Operations;

use AcmeWidgetCo\Customer\Basket as Basket;
use AcmeWidgetCo\Stock\Catalogue as Catalogue;

/**
 * As offers are expanded, this may become an interface for different offer types.
 * For now, we just have the one we are experimenting with.
 *
 * @author Matt
 */
class Offer {

    private bool $applied = false;
    private string $triggerItem;
    private int $triggerCount;
    private string $offerItem;
    private int $offerCount;
    private string $offerPriceScale;
    private Array $remainingBasketCodes = [];
    private string $discount = '0.00';

    /**
     * Construct.
     * @param string $triggerItem  The item triggering the offer.
     * @param int $triggerCount  How many of the trigger item are needed for the offer to apply.
     * @param string $offerItem  What items are benefitted by the offer.
     * @param int $offerCount  How many of the benefitted items must be added for this offer to apply.
     * @param string $offerPriceScale  Fraction of the price removed from the benefitting items (1=100%, 0.5=50%)
     */
    public function __construct(string $triggerItem, int $triggerCount, string $offerItem, int $offerCount, string $offerPriceScale) {

        $this->triggerItem = $triggerItem;
        $this->triggerCount = $triggerCount;
        $this->offerItem = $offerItem;
        $this->offerCount = $offerCount;
        $this->offerPriceScale = $offerPriceScale;
    }


    /**
     * Apply the offer to the basket and get the total resulting discount.
     * The calculation can only be done once, but will apply itself as many times as the count of items allow.
     * e.g. if there is a 'BOGOF' offer, four purchases of the item on the offer will result in two freebies.
     * @param array $itemCounts
     * @param Catalogue $catalogue
     * @return string
     */
    public function apply(Array $itemCounts, Catalogue $catalogue): string {

        // Using bcscale of 3 in the bcmath functions prevents losing half penny amounts 
        // (which would round down effectively).
        bcscale(3);

        if ($this->applied) {
            return $this->discount;
        }

        $this->remainingBasketCodes = $itemCounts;

        // Here's where the offer will apply as many times as makes sense
        while ($this->offerIsApplicable()) {

            // Checks passed - this offer may still be applied.
            $this->remainingBasketCodes[$this->triggerItem] -= $this->triggerCount;
            $this->remainingBasketCodes[$this->offerItem] -= $this->offerCount;

            // Return the discount, multiplying the scale of the offer by the benefitting item price, 
            // then by the number of that item on the offer.
            $discount = bcmul($this->offerPriceScale, $catalogue->fetch($this->offerItem)->getPrice());
            $discount = bcmul($discount, $this->offerCount);

            // Add each applicable instance of the offer to the overall discount this offer produces.
            $this->discount = bcadd($this->discount, $discount);
        }

        // Set the applied flag so this can only be done once.
        $this->applied = true;

        // Return
        return $this->discount;
    }

    /**
     * Is this offer appicable on the items left unaffected in the basket?
     * @return bool
     */
    private function offerIsApplicable(): bool {

        // Sanity checks to see if the offer applies.  Do this in separate statements for readability.
        // If any do not pass, just return the discount as is (default 0.00).
        // Is the trigger item in the basket?
        if (!array_key_exists($this->triggerItem, $this->remainingBasketCodes)) {
            return false;
        }

        // Is the item benefitting from the offer in the basket?
        if (!array_key_exists($this->offerItem, $this->remainingBasketCodes)) {
            return false;
        }

        // Do we have the required count of trigger items?
        if ($this->remainingBasketCodes[$this->triggerItem] < $this->triggerCount) {
            return false;
        }

        // Do we have the required count of items benefitted by this offer?
        // Part one - special case where triggering item code is same as benefitting item code:
        // Only count the number of benefitting items without the needed number of trigger items
        if ($this->offerItem === $this->triggerItem && $this->remainingBasketCodes[$this->offerItem] - $this->triggerCount < $this->offerCount) {
            return false;
        }

        // Part two - where the codes are not the same
        if ($this->offerItem !== $this->triggerItem && $this->remainingBasketCodes[$this->offerItem] < $this->offerCount) {
            return false;
        }

        return true;
    }

    /**
     * We need to keep track of the items that have been used to match on an offer
     * so no offers are applied more than once nor to the same batch of items already
     * enacting an offer benefit.
     * @param array $remainingBasketCodes
     * @return \AcmeWidgetCo\Operations\Offer
     */
    public function setRemainingBasketCodes(Array $remainingBasketCodes): Offer {
        $this->remainingBasketCodes = $remainingBasketCodes;
        return $this;
    }

    /**
     * Each time an offer completes its application, the Offers class needs to have back
     * the unaffected items remaining in the basket before moving onto any further offers.
     * @return array
     */
    public function getRemainingBasketCodes(): Array {
        return $this->remainingBasketCodes;
    }
}
