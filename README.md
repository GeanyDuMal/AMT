# Site pour l'AEDI

Les objectifs de ce site vont etre biensur : 
* Permettra de tenir le bar du local, en maniere de stock, de paiment ou encore de facture
  - Cela devras etre sur stocké sur une Base de Données MySQL
  - Implementer une DAO pour anticiper un eventuel changement de systeme de persistance
  - Lorsque qu'un "ticket de caisse" est effectué, devoir valider le paiement et retirer les produits acheté du stock
  - Avoir un recapitulatif de l'état du Stock
* Permettre de proposer des produits à la vente comme des Sweats et des T-shirts
  - Visuel, prix, taille
  - Proposer uniquement le moyen de paiement si le temps le permet (API Paypal ? Faire a la toute fin)
* Permettre d'afficher les anciennes affiches proposé par le bureau
* Permettre d'afficher les futurs evenements avec les liens des formulaires pour s'y inscrire
  - Afficher aussi les evenements passé
* Avoir un design pratique à l'utilisation
  - Bouton pour l'enregistrement et le paiement d'une commande
  - Total de la commande toujours affiché
  - Photo des produits
  - Possibilité d'ajouter et de supprimer des produits d'une commande
* Avoir un calendrier ou l'on choisirait une date et où l'on afficherait le montant recolté ce jour
* Stat sur la semaine / 15 derniers jours / mois courant (si on est connecté)
  - Montant réalisé
  - pourcentage par rapport à la periode précédente
* Possibilité de visualiser l'équipe actuel avec les postes
* Bien penser a faire un systeme de connexion pour les membres du bureau afin de pouvoir editer une commande
* Pouvoir indiquer lors du paiement quel montant a été donné au vendeur, et indiqué a l'utilisateur quel montant il doit rendre au client
* Pouvoir indiquer au paiement si le paiement est fait en direct ou via une "ardoise"
* Pouvoir recharger l'"ardoise" de qqn
* Pouvoir creer l'"ardoise"

* Contraintes :  
  - Realiser le projet en Web avec Symfony
  - Utiliser une base de données MySQL
