<?php

declare(strict_types=1);

namespace AcmeWidgetCo\Stock;

use AcmeWidgetCo\Engine\Debug As Debug;

/**
 * Widget class for all widgets in the catalogue.
 * @author Matt <matt@marinatech.ltd>
 */
class Widget {

    protected $title;
    protected $code;
    protected $price;
    protected $description;

    /**
     * 
     * @param string $title Provide the title of the widget.
     * @param string $code The reference code e.g. C01.
     * @param string $price Price of the widget. This must be provided as a string (FP values cannot guarantee accuracy).
     * @param string $description Optional. Any further details about this widget.
     */
    public function __construct(string $title, string $code, string $price, string $description = null) {
        try {
            $this->setTitle($title)
                    ->setCode($code)
                    ->setPrice($price);

            if ($description) {
                $this->setDescription($description);
            }

            // Log success
        } catch (\Exception $e) {
            // Debug log error
            $title = $title ?? 'INVALID TITLE';
            Debug::show('Error creating catalogue item "' . $title . '"', $e);
        }
    }

    /**
     * Fetch the widget code.
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * Fetch product price.
     * @return string
     */
    public function getPrice(): string {
        return $this->price;
    }

    /**
     * Fetch the title.
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Fetch the description.
     * @return string
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * Set the title of the widget. 
     * @param string $t Widget title up to 30 characters.
     * @return \AcmeWidgetCo\Stock\Widget
     * @throws \Exception
     */
    private function setTitle(string $t): Widget {

        if (mb_strlen($t) > 30) {
            throw new \Exception('Title cannot be longer than 30 characters.');
        }
        $this->title = trim($t);
        return $this;
    }

    /**
     * Set the code of the widget.
     * @param string $c Widget code up to 10 characters.
     * @return \AcmeWidgetCo\Stock\Widget
     * @throws \Exception
     */
    private function setCode(string $c): Widget {

        if (mb_strlen($c) > 10) {
            throw new \Exception('Code cannot be longer than 10 characters.');
        }

        $this->code = $c;
        return $this;
    }

    /**
     * Set the price of the widget. Cannot rely on PHP floating points due to inaccuracies.
     * @param string $p Widget price.
     * @return \AcmeWidgetCo\Stock\Widget
     * @throws \Exception
     */
    private function setPrice(string $p): Widget {

        if (!is_numeric($p)) {
            throw new \Exception('Price must be a numeric value');
        }

        // Worth including a check on the amount of precision of the value to avoid errors like 50.455 
        // (which would be rounded to 50.46)

        $this->price = $p;
        return $this;
    }

    /**
     * 
     * @param string $d Widget description up to 100 characters.
     * @return $this
     * @throws \Exception
     */
    private function setDescription(string $d) {

        if (mb_strlen($d) > 100) {
            throw new \Exception('Please keep the description to 100 characters or less.');
        }

        $this->description = $d;
        return $this;
    }

}
