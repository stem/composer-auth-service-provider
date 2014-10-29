# language: fr

Fonctionnalité: Vérification du cookie
  Afin de pouvoir fonctionner
  L'utilisateur doit passer un certain nombre de paramètres

Contexte:
  Etant donné que j'instancie un nouvel objet
  Et j'injecte "file://__DIR__/tmp/key/" dans "auth.authenticator_url"
  Et j'injecte true dans "auth.force_guest"
  Et j'injecte false dans "auth.cookie_expiration"
  Et j'injecte "__DIR__/tmp/public.key" dans "auth.public_key.tmp_path"
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


Scénario: Vérification pour un mec authentifié
  Et mon identité est 
  """
  {
    "login":  "crivis_s",
    "logas":  false,
    "groups": ["prof","adm","auth_adm"],
    "login_date":"2013-08-01 14:18:55"
  }
  """
  Alors je suis authentifié en tant que "crivis_s" depuis "2013-08-01 14:18:55"
  Et j'ai les roles "prof,adm,auth_adm"


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

