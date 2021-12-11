# Liste des commandes Symfony utiles

## General

### Setup du projet avec des fichiers sources

     composer instal

### Creer un projet symfony
      
      composer create-project symfony/website-skeleton NOMDUPROJET "X.X.*" //Correspond a la version de Symfony

### Lancer le serveur pour pouvoir acceder a la page web
      
      php -S 127.0.0.1:8000 -t public  

### Creer un controller avec sa template
<br>Penser a renommer la route de cette maniere : 
     @Route("/xxx", name="xxx")

      php bin/console make:controller


## Database 
### Creer la base de données (penser a remplir le .env auparavant)
            
      php bin/console doctrine:database:create
            
### Creer une classe php (qui creera une table dans la bd)
<br>Pour la creation des champs, suivre ce qui est ecrit dans l'invite de commande
            
      php bin/console make:entity


### Transformer les classes PHP en migration pour du SQL
            
      php bin/console make:migration

### Executer toute les migrations vers la BdD

      php bin/console doctrine:migration:migrate

### Telecharger le bundle pour generer un jeu de données
            
      composer require orm-fixtures --dev

### Creer une classe pour generer le jeu de données
<br>NOM DE LA classe TonObjetFixtures

      php bin/console make:fixtures

### Executer l'importation du jeu de données

      php bin/console doctrine:fixture:load