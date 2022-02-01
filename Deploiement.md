## Toute les manipulations indiqué ne seront pas faites dans le repository mais seront à faire une fois le projet téléchargé

# Pour le deploiement il y a plusieurs choses à faire : 
Nous, du fait de notre hebergeur, nous avons du modifier le codage des caractere :
- Dans Webiste/config/packages/doctrine.yaml, rajouter cette ligne en dessous de 'url'
  ```YAML
    charset: UTF8
  ```
- Il faudra créer une base de donnée (ou utiliser celle proposé par l'hebergeur) et importer le script Website/db_aedi.sql
- Il faudra, dans le fichier Website/.env, modifier 2 choses : (vous pourrez également créer un .env.local)
  - Le APP_ENV qu'il faudra passer à prod (enleve le mode debug)
  - Le DATABASE_URL qu'il faudra configurer en fonction de ce que vous utilisez. Il y a plusieurs exemple selon la bases que vous utilisez
- Il faudra créer un fichier .htaccess
  - A la racine de Website (le notre est ci dessous) :
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
  - Le second qui permet de gerer les route avec Apache, il est present dans Website/public/.htaccess