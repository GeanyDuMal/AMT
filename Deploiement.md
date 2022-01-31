## Toute les manipulations indiqué ne seront pas faites dans le repository mais seront à faire une fois le projet téléchargé

# Pour le deploiement il y a plusieurs choses à faire : 
Nous, du fait de notre hebergeur, nous avons du modifier le codage des caractere :
- Dans Webiste/config/packages/doctrine.yaml, rajouter cette ligne en dessous de 'url'
  ```YAML
    charset: UTF8
  ```
- Il faudra créer une base de donnée (ou utiliser celle proposé par l'hebergeur) et importer le script Website/db_aedi.sql
- Il faudra, dans le fichier Website/.env, modifier 2 choses : 
  - Le APP_ENV qu'il faudra passer à prod (enleve le mode debug)
  - Le DATABASE_URL qu'il faudra configurer en fonction de ce que vous utilisez. Il y a plusieurs exemple selon la bases que vous utilisez
- Il faudra créer un fichier .htaccess a la racine de Website (le notre est ci dessous) :
  ```Apache
  
  ```