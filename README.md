# Viddyoze Code Test: Acme Widget Co.
A (probably well over-engineered) proof of concept of a sales system, showcasing configurable and instantly applied:
* delivery charges
* offers
* widgets!

The Basket class is initialised with new classes Catalogue, Delivery and Offers.  The basket is filled with the 'add' method, passing a code string value that looks up for sanity checking against the Catalogue.  The 'total' Basket method allows for optionally including Delivery output and Offers output.

The UI uses a hidden field of JSON for basket contents. JSON is sent back to the page when the basket changes, and then all further interactions with the system classes are handled by the FormHandler class (except for index.php's Catalogue use for generating the shop front), which in turn makes use of the entire basket, delivery, catalogue and offers classes. JS updates the UI with the JSON returned from FormHandler via index.php.

The classes have been designed with SOLID principles in mind (balanced with keeping time spent as minimal as reasonably possible) and for readability, maintainability, flexibility and possible database sources later. Examples of the latter: Offers and Delivery rules.

Bcmath functions handle all money calculations as floating point variables are inherently not reliable for accuracy.

A lot of this could have been short-cut, e.g.

* The configurable 'offers' classes and how they work with the basket content.  I just would not let myself do it in any way that was less SOLID than it is at the moment.  I've built this as if coding the basics of a true deployable extendable system.
* The user interface involves no form submissions for speed and ease of updates.  I didn't want to hold any session data nor handle POST variables. 
* The UI has had more layout/styling than necessary for this task... I can't help a _small_ amount of aesthetics.
* I've provided a working 'empty basket' feature for quicker testing.

I've commented the code a lot; despite time, this should help follow the logic of how things work.

Contrary to convention, there are no unit tests.  This was to keep the footprint for a coding challenge as light as possible, and for time.


## Installation

Clone this repo and spin up in a PHP 7.4 web environment. Ensure static .js and .css file types can be serviced.  Set the root as /public.

## Usage

Navigate to the web root, or /index.php.

## Contributing
None.

## License
None.