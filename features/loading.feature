# language: fr

Fonctionnalité: Vérification des paramètres
  Afin de pouvoir fonctionner
  L'utilisateur doit passer un certain nombre de paramètres

Contexte:
  Etant donné que j'instancie un nouvel objet

Scénario: Il manque tous les paramètres
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il manque "auth.authenticator_url"
  Quand j'injecte "" dans "auth.force_guest"
  Et j'injecte "" dans "auth.cookie_expiration"
  Et j'injecte "" dans "auth.public_key.tmp_path"
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il manque "auth.force_guest"
  Quand j'injecte "" dans "auth.authenticator_url"
  Et j'injecte "" dans "auth.cookie_expiration"
  Et j'injecte "" dans "auth.public_key.tmp_path"
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il manque "auth.cookie_expiration"
  Quand j'injecte "" dans "auth.authenticator_url"
  Et j'injecte "" dans "auth.force_guest"
  Et j'injecte "" dans "auth.public_key.tmp_path"
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il manque "auth.public_key.tmp_path"
  Quand j'injecte "" dans "auth.authenticator_url"
  Et j'injecte "" dans "auth.force_guest"
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il ne manque aucun paramètres mais l'URL n'est pas bonne
  Quand j'injecte "" dans "auth.authenticator_url"
  Et j'injecte "" dans "auth.force_guest"
  Et j'injecte "" dans "auth.cookie_expiration"
  Et j'injecte "" dans "auth.public_key.tmp_path"
  Quand Silex boot mon provider
  Alors je dois avoir une exception


Scénario: Il ne manque aucun paramètres et l'URL est bonne
  Quand j'injecte "file://__DIR__/../../tmp/keys/" dans "auth.authenticator_url"
  Et j'injecte "" dans "auth.force_guest"
  Et j'injecte "" dans "auth.cookie_expiration"
  Et j'injecte "" dans "auth.public_key.tmp_path"
  Quand Silex boot mon provider
  Alors je ne dois pas avoir d'exception

