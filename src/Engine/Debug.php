<?php

declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcmeWidgetCo\Engine;

/**
 * Debug output of exceptions.  In the real world, this would respond to an easy
 * debug mode switch (as the output must never be seen in the real world).
 * 
 * Would also have different levels of error.
 *
 * @author Matt
 */
class Debug {

    static function show(String $message, \Exception $e) {
        echo '<p>Exception:</p><pre>'
        . $message . ': ' . $e->getMessage() . PHP_EOL
        . 'Stack trace:' . PHP_EOL
        . $e->getTraceAsString()
        . '</pre>';
    }

}
