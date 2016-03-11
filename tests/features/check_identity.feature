# language: fr

Fonctionnalité: Vérification du cookie
  Afin de pouvoir fonctionner
  L'utilisateur doit passer un certain nombre de paramètres

Contexte:
  Etant donné que j'instancie un nouvel objet
  Et j'injecte "file://__DIR__/tmp/key/" dans "authenticator.url"
  Et j'injecte "30seconds" dans "auth.cookie_expiration"
  Et j'injecte "__DIR__/tmp/public.key" dans "authenticator.cache.file"
  Quand Silex boot mon provider

Scénario: Vérification pour un guest
  Alors je ne suis pas authentifié


Scénario: Vérification pour un mec qui ne connait pas son mot de passe
  Et mon identité est
  """
  {
    "login": "crivis_s",
    "logas":  false,
    "groups": null,
    "login_date":null
  }
  """
  Alors je ne suis pas authentifié


Scénario: Vérification pour un mec qui ne connait pas son mot de passe
  Et j'injecte false dans "auth.force_guest"
  Et mon identité est
  """
  {
    "login": "crivis_s",
    "logas":  false,
    "groups": null,
    "login_date":null
  }
  """
  Alors je ne suis pas authentifié


Scénario: Vérification pour un mec qui tente d'autosigner
  Et ma fausse identité est
  """
  {
    "login": "crivis_s",
    "logas":  false,
    "groups": ["prof","adm","auth_adm"],
    "login_date":"2013-08-01 14:18:55"
  }
  """
  Alors je ne suis pas authentifié


Scénario: Vérification pour un mec authentifié depuis trop longtemps
  Et mon identité est
  """
  {
    "login":  "crivis_s",
    "logas":  false,
    "groups": ["prof","adm","auth_adm"],
    "login_date":"2013-08-01 14:18:55"
  }
  """
  Alors je ne suis pas authentifié


Scénario: Vérification pour un mec logas
  Et mon identité est
  """
  {
    "login":  "lequer_r",
    "logas":    {
      "login":  "crivis_s",
      "logas":  false,
      "groups": ["prof","adm","auth_adm"],
      "login_date":"2013-08-01 14:18:55"
    },
    "groups": ["prof","adm"],
    "login_date":"2013-08-01 14:20:42"
  }
  """
  Alors je suis logas en tant que "lequer_r" depuis "2013-08-01 14:20:42"
  Et en vrai, je suis "crivis_s" depuis "2013-08-01 14:18:55"
  Et j'ai les roles "prof,adm"

