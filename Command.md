# Liste des commandes Symfony utiles

## General

### Setup du projet avec des fichiers sources

     composer install

### Creer un projet symfony

#### Avec composer
      
      composer create-project symfony/website-skeleton NOMDUPROJET "X.X.*" //Correspond a la version de Symfony

#### Avec le CLI Symfony
     
     symfony new {nom du projet} --version={version} --full

### Lancer le serveur pour pouvoir acceder a la page web
     
#### Avec PHP      
      php -S localhost:8000 -t public

### Lancer le serveur pour pouvoir acceder a la page web en HTTPS avec Symfony

##### Pré-requis:
Installation du certificat HTTPS:

````shell
symfony server:ca:install
````

Ensuite il faut pour lancer :

````shell
symfony server -d
````

Pour l'arrêter il suffit d'écrire:

````shell
symfony server:stop
````

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

      php bin/console make:fixtures

ex: ClientFixture

### Executer l'importation du jeu de données

      php bin/console doctrine:fixture:load

## Executer les tests

     php bin/phpunit

## Pour Setup le projet complet avec le jeu d'essai : 
Lancer le fichier [initialize.sh](initialize.sh)

     ./initialize.sh