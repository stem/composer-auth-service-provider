<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use ETNA\Silex\Provider\Auth\AuthServiceProvider;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @BeforeSuite
     */
    public static function createKeys()
    {
        passthru("bash -c '[ -d tmp/keys ] || mkdir -p tmp/keys'");
        passthru("bash -c '[ -f tmp/keys/private.key ] || openssl genrsa  -out tmp/keys/private.key 2048'");
        passthru("bash -c '[ -f tmp/keys/public.key ]  || openssl rsa -in tmp/keys/private.key -pubout -out tmp/keys/public.key'");
    }

    /**
     * @Given /^que j\'instancie un nouvel objet$/
     */
    public function queJInstancieUnNouvelObjet()
    {
        $this->e   = null;
        $this->app = new Silex\Application();
        $this->app->register(new AuthServiceProvider());
    }

    /**
     * @Given /^Silex boot mon provider$/
     */
    public function silexBootMonProvider()
    {
        try {
            $this->app->boot();
        } catch (Exception $e) {
            $this->e = $e;
        }
    }

    /**
     * @Given /^je dois avoir une exception$/
     */
    public function jeDoisAvoirUneException()
    {
        if (!$this->e) {
            throw new Exception("No exception catched");
        }
    }

    /**
     * @Given /^j\'injecte "([^"]*)" dans "([^"]*)"$/
     */
    public function jInjecteDans($value, $key)
    {
        $this->app[$key] = str_replace("__DIR__", __DIR__, $value);
    }

    /**
     * @Given /^je ne dois pas avoir d\'exception$/
     */
    public function jeNeDoisPasAvoirDException()
    {
        if ($this->e) {
            throw new Exception("Exception catched : {$e->getMessage()}");
        }
    }
}
