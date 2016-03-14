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
    public function register(Container $app)
    {
        $app["auth"]                   = $this;
        $app["auth.cookie_expiration"] = "7day";

        $app->register(new AuthenticatorServiceProvider());
    }

    /**
     * @{inherit doc}
     */
    public function boot(Application $app)
    {
        $app->before([$this, "authentication"], Application::EARLY_EVENT);

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

    public function authentication(Request $req)
    {
        // On autorise les OPTIONS sans authn puisqu'il n'y a pas le cookie
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
                // $this->app["logs"]->debug("");
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

    /**
     * Vérifie si l'utilisateur courant a ce role
     *
     * @param string $roles : "adm"
     * @return true
     * @throws HttpException
     */
    public function hasRole($role)
    {
        return $this->hasRoles([$role]);
    }

    /**
     * Vérifie si l'utilisateur courant a tous les roles
     *
     * @param array $roles : ["adm", "terminator"]
     * @return true
     * @throws HttpException
     */
    public function hasRoles(array $roles)
    {
        return function (Request $req, Response $res) {
            foreach ($roles as $role) {
                if (!in_array($role, $req->user->roles))) {
                    $this->app->abort(403, "Not Authorised");
                }
            }

            return true;
        };
    }

    /**
     * Vérifie si l'utilisateur courant a un ou plusieurs roles
     *
     * Dans l'example ci-dessous, ça passe si je suis "adm" OU ("profs" ET "paris")
     *
     * @param array $roles : ["adm", ["profs", "paris"]]
     * @return true
     * @throws HttpException
     */
    public function hasSomeRoles(array $roles)
    {
        return function (Request $req, Response $res) use ($roles) {
            foreach ($roles as $role) {
                try {
                    if (is_array($role)) {
                        return $this->hasRoles($role)
                    } else {
                        return $this->hasRole($role)
                    }
                } catch (\Exception $e) {
                    // On fait rien puisqu'on est dans une boucle,
                    // donc on va tenter les autres possibilités
                }
            }

            // Et au pire, si on arrive ici, c'est que toutes les possibilités
            // ont échoué, donc on peut dire que l'acces est interdit
            $this->app->abort(403, "Not Authorised");
        };
    }

    public function noLogas()
    {
        return function (Request $req, Response $res) {
            if (!empty($req->user->logas)) {
                $this->app->abort(403, "Not Authorised");
            }
        }
    }
}
