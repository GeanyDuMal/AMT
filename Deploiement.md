## Toute les manipulations indiqué ne seront pas faites dans le repository mais seront à faire une fois le projet téléchargé

# Pour le deploiement il y a plusieurs choses à faire : 
Nous, du fait de notre hebergeur, nous avons du modifier l'encodage des caracteres :
- Dans config/packages/doctrine.yaml, rajouter cette ligne en dessous de 'url'
  ```YAML
    charset: UTF8
  ```
- Il faudra supprimer le fichier config/packages/doctrine_migration.yaml 
- Il faudra créer une base de données (ou utiliser celle proposé par l'hebergeur) et importer le script db_empty.sql
- Il faudra, dans le fichier .env, modifier 2 choses : (vous pourrez également créer un .env.local)
  - Le APP_ENV qu'il faudra passer à prod (enleve le mode debug)
  - Le DATABASE_URL qu'il faudra configurer en fonction de ce que vous utilisez. Il y a plusieurs exemple selon la bases que vous utilisez
- Il faudra créer un fichier .htaccess
  - A la racine (le notre est ci dessous) :
    ```Apache
    DirectoryIndex index.php

    RewriteEngine on
  
    RewriteBase /
  
    RewriteCond %{HTTP_HOST} ^(www.)?aedi.lescigales.org/$
    RewriteCond %{REQUEST_URI} !^/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !.(?:css|js|jpe?g|gif|png)$ [NC]
    RewriteRule ^(.*)$ public/index.php?/$1 [QSA,L]
    RewriteCond %{REQUEST_URI} !.(?:css|js|jpe?g|gif|png)$ [NC]
    RewriteRule ^ public/index.php [L]
  
    RewriteCond %{REQUEST_URI} .(?:css|js|jpe?g|gif|png)$ [NC]
    RewriteRule ^(.*)$ public/$1 [QSA,L]
    ```
  - Le second qui permet de gerer les route avec Apache, est present dans public/.htaccess

#### Anciennement, plus obligatoire aujourd'hui
- Afin de faire fonctionner la partie connexion en HTTPS, il faudra rajouter ce code entre la ligne 7 et 8 dans le fichier public/index.php :
  ```PHP
  if ($context['APP_ENV'] === "prod") {
        if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') 
        && (!isset($_SERVER['REDIRECT_HTTPS']) || $_SERVER['REDIRECT_HTTPS'] != 'on')
        && (!isset($_SERVER['REDIRECT_REDIRECT_HTTPS']) || $_SERVER['REDIRECT_REDIRECT_HTTPS'] != 'on') // this one is the one working....
        ){
            $url = sprintf('https://%s%s', $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
            die(header("Location: $url"));
        }
    }
  ```
  Pour cette partie dans le index.php, ce n'est pas la plus propre, mais elle est fonctionnelle sur notre hebergeur (comparée aux autres)

### Migrations dans la base de données

Actuellement, l'hebergeur ne permet pas de faire de migration via les lignes de commandes comme le permet Symfony.
Il faudra prendre la migration puis conserver unqiuement le SQL et le saisir ligne par ligne afin de s'assurer qu'il ne plante pas lors de la saisie
Il faudra également recuperer la ligne inseré en base de données de test pour la table doctrine-migration dans le cas d'un changement d'hebergeur un jour.
