<?php

namespace ETNA\Auth;

use Silex\Application;
use Silex\Api\BootableProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

use Exception;

class AuthServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    private $app           = null;
    private $authenticator = null;

    /**
     * @{inherit doc}
     */
    public function boot(Application $app)
    {
        $app->before([$this, "authBeforeFunction"], Application::EARLY_EVENT);

        $keys = [
            "auth.cookie_expiration",
        ];
        foreach ($keys as $key) {
            if (!isset($app[$key])) {
                throw new Exception("\$app['{$key}']: invalid key");
            }
        }

        $this->app = $app;
    }

    /**
     * @{inherit doc}
     */
    public function register(Container $app)
    {
        $app["auth"]                   = $this;
        $app["auth.cookie_expiration"] = "7day";

        $app->register(new AuthenticatorServiceProvider());
    }

    public function authBeforeFunction(Request $req)
    {
        // On autorise les OPTIONS sans auth puisqu'il n'y a pas le cookie
        if ('OPTIONS' === $req->getMethod()) {
            return;
        }

        try {
            if ($req->cookies->has("authenticator")) {
                $this->app->abort(401, "authenticator: missing cookie");
            }

            // On refait un try / catch ici car les exceptions de $authenticator ne sont pas des HttpException
            // au passage, on met un vrai message et un vrai code HTTP
            try {
                $req->user = $this->app["authenticator"]->extract($req->cookies->get("authenticator"));
            } catch (Exception $e) {
                $this->app->abort(401, "authenticator: cookie signature mismatch");
            }

            // Je suis authentifié depuis trop longtemps
            if ($this->app["auth.cookie_expiration"] && strtotime("{$req->user->login_date}{$this->app["auth.cookie_expiration"]}") < strtotime("now")) {
                $this->app->abort(401, "authenticator: cookie signature expired");
            }
        } catch (HttpException $e) {
            // on supprime le cookie, histoire de forcer l'utilisateur à se re-authentifier.
            $response = new Response($error->getMessage(), $error->getStatusCode());
            $response->headers->clearCookie("authenticator", "/", $req->domain);

            return $response;
        }
    }
}
