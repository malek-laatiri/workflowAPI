# Workflow

La gestion d’un workflow consiste à suivre l’état d’évolution d’une tâche (un projet) bien définie au
sein d’une entreprise d’un acteur à l’autre. Chaque acteur responsable d’une étape de réalisation
d’une tâche aura une notification englobant toutes les informations nécessaires.
## Premiers pas

Ces instructions vous permettront d'obtenir une copie du projet sur votre ordinateur local à des fins de développement .


### Prérequis

De quoi vous avez besoin pour installer le logiciel et comment les installer
```
Composer
```
```
PHP version: ^7.1.3
```
```
MySQL version: 5.5, 10.0, 10.1, 10.2
```

### Installation

Une série étape par étape qui  explique comment lancer un env de développement

```
composer install
```

les paramètres nécessaires à l'établissement de la connexion d'une base de données.
 Cette dernière étant propre à l'environnement d'exécution de notre code, nous allons définir les paramètres dans le fichier .env.
```
DATABASE_URL=mysql://root:root@127.0.0.1:3306/workflow?serverVersion=5.7
```
Initialiser la base de données.
```
php bin/console doctrine:database:create
```
Créer la structure des tables
```
php bin/console doctrine:schema:update --force
```
### Bundles

* Installation FOSRestBundle
```
composer require friendsofsymfony/rest-bundle
```
Configuration de base dans le fichier app/config/packages/fos_rest.yml
```
#app/config/packages/fos_rest.yml

fos_rest:
    body_converter:
        enabled: true
    view:
        formats: { json: true, xml: false, rss: false }
    serializer:
        serialize_null: true
```
Affichez toute la configuration du FOSRestBundle
```
bin/console config:dump-reference fos_rest
```
* Installation JWT(JSON Web Token)
```
composer require lexik/jwt-authentication-bundle
```
la création de clés publique et privée pour signer et valider les jetons. l'utilisation du l'utilitaire OpenSSL pour générer ces deux clefs.

```
###> lexik/jwt-authentication-bundle ###

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem

JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem

JWT_PASSPHRASE= *ICI*

###< lexik/jwt-authentication-bundle ###
```
Le fichier config/packages/security.yaml permettre de configurer toutes les règles de sécurité de notre application et quelles méthodes d’authentification nous allons utiliser.
```
#config/packages/security.yaml
login:
      pattern:  ^/api/login
      stateless: true
      anonymous: true
      json_login:
        check_path:               /api/login_check
        success_handler:          lexik_jwt_authentication.handler.authentication_success
        failure_handler:          lexik_jwt_authentication.handler.authentication_failure

    api:
      pattern:   ^/api
      stateless: true
      guard:
        authenticators:
          - lexik_jwt_authentication.jwt_token_authenticator
```
on saisit l’URL de login, http://localhost/api/login_check,
et on envoie dans le corps de la requête notre objet JSON qui contient la clé username avec pour valeur l’email de user et la clé password avec son mot de passe.
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1ODI4ODU5ODUsImV4cCI6MTU4Mjg4OTU4NSwicm9sZXMiOlsiUk9MRV9ERVZFTE9QRVIiLCJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJtYWxlayJ9.TrWiaaeBkiMeNzay-9CUNdc2nQN77xBjUYr7rxHVz08pgzzKEoRxR5gqpCs7SwCorlKnk0d7m9iOYYK07P-REglExrXeT3KXbYloIiyT_pk02jEJd1gdugdsVlhSun1JWbCNvRb_HTu9dWWnM6mb4DzfjGSZZbVecNFyr2aVA114-gGMO9ezCJ25HYnDFjzFQX7FV6-2jYPrnrXsOpj5QfTn0Z4X5olg9iQglpa-BO0dDaDvcDI1sUtEuWR6afSUcs6E-qwqjbixGVHnfJugvoSxtir5ePFjL19Oyg4zm5V7vExTbK6mloLExjZf5f11O9CBP0Nafs083PCdqQdOdYum_AVuPoDSmLUMlcBLeofa6vejXy-myj8i4ubfHoHm5ZCfHcyWy40pjZml8nu3ngbpUTRZz2IauaWn1Sk4ualARGC9yTAOoVbqrtUC6Vf1GWd0tPLP7C8xFG5BG20sPAw39-zW3dXZ9OPljc0Nmv61P_DD49AUxkLJE3G299WyUdLLvVseOw00XPgN4UK7GzZrsolkX7Re4w_l-erVuHxbUfSuqqyU-7HlyIX4iCV2kIW3gbgcK36ubu6AVg0wtOQg4rPZNxiw8uKEXl7QeIOXH5vsfIFcSAftxSIQQsZvhl24Zt_raotDs04O-8LMFNeT2bo6uW4jxagryHI3w7M

```
le token decodé contient le nom d’utilisateur, les rôles et les dates de création et d’expiration .
```
HEADER:ALGORITHM & TOKEN TYPE

{
  "typ": "JWT",
  "alg": "RS256"
}
PAYLOAD:DATA

{
  "iat": 1582885985,
  "exp": 1582889585,
  "roles": [
    "ROLE_DEVELOPER",
    "ROLE_USER"
  ],
  "username": "malek"
}
```

* Installation NelmioApiDocBundle
```
composer require nelmio/api-doc-bundle
```
Pour parcourir la documentation avec Swagger UI:
```
# config/routes.yaml
app.swagger_ui:
    path: /doc
    methods: GET
    defaults: { _controller: nelmio_api_doc.controller.swagger_ui }
```
* Installation JMSSerializerBundle
```
composer require jms/serializer-bundle
```
* Installation Swift Mailer

```
composer require symfony/swiftmailer-bundle
```
Ces paramètres sont définis dans la MAILER_URL variable d'environnement du .env:
```
# .env
MAILER_URL=null://localhost
# use this to configure a traditional SMTP server
MAILER_URL=smtp://localhost:465?encryption=ssl&auth_mode=login&username=&password=
```
## Vérification(Ubuntu)

Avant d'installer Symfony, il est nécessaire de vérifier la configuration de l'ordinateur.

Vérification MySQL:
```
mysql -V
```
la version s'affiche
```
mysql  Ver 15.1 Distrib 10.1.44-MariaDB, for debian-linux-gnu (x86_64) using readline 5.2
```
Vérification PHP:

```
php -V
```
la version s'affiche

```
PHP 7.2.24-0ubuntu0.18.04.3 (cli) (built: Feb 11 2020 15:55:52) ( NTS )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.2.0, Copyright (c) 1998-2018 Zend Technologies
    with Zend OPcache v7.2.24-0ubuntu0.18.04.3, Copyright (c) 1999-2018, by Zend Technologies
```
Vérification Composer:

```
composer
```
la version s'affiche
```
Composer version 1.8.6 2019-06-11 15:03:05
```

## développé par

* [Symfony 4.3](https://symfony.com/doc/current/index.html#gsc.tab=0) - Un framework Web écrit en PHP.
* [MySQL](https://www.mysql.com/) - Un système de gestion de bases de données

## Auteur

* **Malek Laatiri** - *Initial work* - [Workflow](https://github.com/malek-laatiri/workflowAPI)




