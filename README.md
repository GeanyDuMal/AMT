# Site pour l'AEDI

Les objectifs de ce site vont être biensur : 
* Permettre de tenir le bar du local, en manière de stock, de paiements ou encore de factures
  - Cela devras être stocké sur une Base de Données MySQL
  - Implementer une DAO pour anticiper un eventuel changement de systeme de persistance
  - Lorsque qu'un "ticket de caisse" est effectué, devoir valider le paiement et retirer les produits achetés du stock
  - Avoir un recapitulatif de l'état du Stock
* Permettre de proposer des produits à la vente comme des Sweats et des T-shirts
  - Visuel, prix, taille
  - Proposer uniquement le moyen de paiement si le temps le permet (API Paypal ? Faire à la toute fin)
* Permettre d'afficher les anciennes affiches proposés par le bureau
* Permettre d'afficher les futurs evenements avec les liens des formulaires pour s'y inscrire
  - Afficher aussi les evenements passés
* Avoir un design pratique à l'utilisation
  - Bouton pour l'enregistrement et le paiement d'une commande
  - Total de la commande toujours affiché
  - Photo des produits
  - Possibilité d'ajouter et de supprimer des produits d'une commande
* Avoir un calendrier où l'on choisirait une date et où l'on afficherait le montant recolté ce jour
* Stat sur la semaine / 15 derniers jours / mois courant (si on est connecté)
  - Montant réalisé
  - pourcentage par rapport à la periode précédente
* Possibilité de visualiser l'équipe actuelle avec les postes
* Bien penser à faire un systeme de connexion pour les membres du bureau afin de pouvoir editer une commande
* Pouvoir indiquer lors du paiement quel montant a été donné au vendeur, et indiquer à l'utilisateur quel montant il doit rendre au client
* Pouvoir indiquer au moment du paiement si le paiement est fait en direct ou via une "ardoise"
* Pouvoir recharger l'"ardoise" de qqn
* Pouvoir créer l'"ardoise"

* Contraintes :  
  - Realiser le projet en Web avec Symfony
  - Utiliser une base de données MySQL


Lien poster : 
https://www.canva.com/design/DAE1nZKEhL8/kkicE3HD_o1dCTlX45ydZg/watch?utm_content=DAE1nZKEhL8&utm_campaign=designshare&utm_medium=link&utm_source=sharebutton
