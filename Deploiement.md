## Toute les manipulations indiqué ne seront pas faites dans le repository mais seront à faire une fois le projet téléchargé

# Pour le deploiement il y a plusieurs choses à faire : 
Nous, du fait de notre hebergeur, nous avons du modifier le codage des caractere :
- Dans Webiste/config/packages/doctrine.yaml, rajouter cette ligne en dessous de 'url'
  ```YAML
    charset: UTF8
  ```
- Ensuite il faudra créer un fichier .htaccess a la racine de Website (le notre est ci dessous) :
  ```Apache
  
  ```