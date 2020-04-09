<?php

declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcmeWidgetCo\Stock;

use \AcmeWidgetCo\Engine\Debug as Debug;
use AcmeWidgetCo\Stock\Widget as Widget;

/**
 * Description of catalogue
 *
 * @author Matt
 */
class Catalogue {

    private Array $widgets = [];

    public function __construct() {

        $this->build();
    }

    /**
     * Get raw contents of catalogue, an array of widgets.
     * @return Array
     */
    public function list(): Array {
        return $this->widgets;
    }

    /**
     * Build the widgets available.
     * @return \AcmeWidgetCo\Stock\Catalogue
     */
    private function build(): Catalogue {

        // Real world: this would likely loop over a recordset from a DB query  

        $this
                ->add(new Widget('Red Widget', 'R01', '32.95', 'Buy one, get one 50% off'))
                ->add(new Widget('Green Widget', 'G01', '24.95'))
                ->add(new Widget('Blue Widget', 'B01', '7.95'))
        ;

        return $this;
    }

    /**
     * Get a specific widget using the widget code ref.
     * @param string $code
     * @return Widget
     */
    public function fetch(string $code): Widget {
        return $this->widgets[$code];
    }

    /**
     * Add widgets to the widget catalogue.
     * @param Widget $widget Widget object for the catalogue.
     * @return \AcmeWidgetCo\Stock\Catalogue
     * @throws Exception
     */
    private function add(Widget $widget): Catalogue {
        // Contemplating making life easier by supplying Widget parameters to the Add method
        // therefore reducing lines of code.  But then if the widget class parameter changed, 
        // so too would this method have to be changed, which isn't SOLID of us.
        // Sanity check - do we already have this widget?
        if (in_array($widget->getCode(), $this->widgets)) {
            throw new Exception('Widget code already added to catalogue');
        }

        $this->widgets[$widget->getCode()] = $widget;

        return $this;
    }

}
