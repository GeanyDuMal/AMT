-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 29 nov. 2021 à 22:27
-- Version du serveur : 10.4.21-MariaDB
-- Version de PHP : 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_aedi`
--

-- --------------------------------------------------------
-- DROP TABLE
DROP TABLE IF EXISTS `achat`;
DROP TABLE IF EXISTS `bureau`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `post`;
DROP TABLE IF EXISTS `role_bureau`;
DROP TABLE IF EXISTS `tarif`;
DROP TABLE IF EXISTS `produit`;
DROP TABLE IF EXISTS `type_commande`;
DROP TABLE IF EXISTS `type_paiement`;
DROP TABLE IF EXISTS `type_post`;
DROP TABLE IF EXISTS `type_produit`;

-- --------------------------------------------------------
--
-- Structure de la table `achat`
--
CREATE TABLE `achat` (
  `id_achat` int(11) NOT NULL,
  `produit` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `commande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `bureau`
--

CREATE TABLE `bureau` (
  `id_client` int(11) NOT NULL,
  `id_role_bureau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `solde` decimal(4,1) NOT NULL,
  `nb_achat` int(11) NOT NULL DEFAULT 0,
  `fidelite` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `command`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_client` int(11) DEFAULT NULL,
  `type_paiement` int(11) NOT NULL,
  `type_commande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id_post` int(11) NOT NULL,
  `titre` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `type_post` int(11) NOT NULL,
  `image` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(30) NOT NULL,
  `type_produit` int(11) NOT NULL,
  `qte_stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `role_bureau`
--

CREATE TABLE `role_bureau` (
  `id` int(11) NOT NULL,
  `designation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `role_bureau`
--

INSERT INTO `role_bureau` (`id`, `designation`) VALUES
(1, 'President'),
(2, 'Tresorier'),
(3, 'Membre');

-- --------------------------------------------------------

--
-- Structure de la table `tarif`
--

CREATE TABLE `tarif` (
  `id_produit` int(11) NOT NULL,
  `type_commande` int(11) NOT NULL,
  `tarif` decimal(4,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `type_commande`
--

CREATE TABLE `type_commande` (
  `id` int(11) NOT NULL,
  `designation` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `type_commande`
--

INSERT INTO `type_commande` (`id`, `designation`) VALUES
(1, 'Classique'),
(2, 'AEDI');

-- --------------------------------------------------------

--
-- Structure de la table `type_paiement`
--

CREATE TABLE `type_paiement` (
  `id_type_paiement` int(11) NOT NULL,
  `designation` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `type_paiement`
--

INSERT INTO `type_paiement` (`id_type_paiement`, `designation`) VALUES
(1, 'Espece'),
(2, 'Solde');

-- --------------------------------------------------------

--
-- Structure de la table `type_post`
--

CREATE TABLE `type_post` (
  `id_type_post` int(11) NOT NULL,
  `designation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `type_post`
--

INSERT INTO `type_post` (`id_type_post`, `designation`) VALUES
(1, ''),
(2, 'Event'),
(3, ''),
(4, 'Event');

-- --------------------------------------------------------

--
-- Structure de la table `type_produit`
--

CREATE TABLE `type_produit` (
  `id_type_produit` int(11) NOT NULL,
  `nom_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `type_produit`
--

INSERT INTO `type_produit` (`id_type_produit`, `nom_type`) VALUES
(1, 'Snack'),
(2, 'Boisson'),
(3, 'Autre');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achat`
--
ALTER TABLE `achat`
  ADD KEY `produit` (`produit`),
  ADD KEY `commande` (`commande`);

--
-- Index pour la table `bureau`
--
ALTER TABLE `bureau`
  ADD PRIMARY KEY (`id_client`),
  ADD KEY `id_role_bureau` (`id_role_bureau`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `command`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`) USING BTREE,
  ADD KEY `client` (`id_client`),
  ADD KEY `type_paiement` (`type_paiement`) USING BTREE,
  ADD KEY `type_commande` (`type_commande`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `type_post` (`type_post`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `qteStock` (`qte_stock`),
  ADD KEY `typeProduit` (`type_produit`);

--
-- Index pour la table `role_bureau`
--
ALTER TABLE `role_bureau`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tarif`
--
ALTER TABLE `tarif`
  ADD PRIMARY KEY (`id_produit`,`type_commande`) USING BTREE,
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `type_commande` (`type_commande`);

--
-- Index pour la table `type_commande`
--
ALTER TABLE `type_commande`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `type_paiement`
--
ALTER TABLE `type_paiement`
  ADD PRIMARY KEY (`id_type_paiement`);

--
-- Index pour la table `type_post`
--
ALTER TABLE `type_post`
  ADD PRIMARY KEY (`id_type_post`);

--
-- Index pour la table `type_produit`
--
ALTER TABLE `type_produit`
  ADD PRIMARY KEY (`id_type_produit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role_bureau`
--
ALTER TABLE `role_bureau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `type_commande`
--
ALTER TABLE `type_commande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `type_paiement`
--
ALTER TABLE `type_paiement`
  MODIFY `id_type_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `type_post`
--
ALTER TABLE `type_post`
  MODIFY `id_type_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `type_produit`
--
ALTER TABLE `type_produit`
  MODIFY `id_type_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achat`
--
ALTER TABLE `achat`
  ADD CONSTRAINT `achat_ibfk_1` FOREIGN KEY (`commande`) REFERENCES `commande` (`id_commande`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `achat_ibfk_2` FOREIGN KEY (`produit`) REFERENCES `produit` (`id_produit`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `bureau`
--
ALTER TABLE `bureau`
  ADD CONSTRAINT `bureau_ibfk_1` FOREIGN KEY (`id_role_bureau`) REFERENCES `role_bureau` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `bureau_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `command`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`type_paiement`) REFERENCES `type_paiement` (`id_type_paiement`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`type_commande`) REFERENCES `type_commande` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`type_post`) REFERENCES `type_post` (`id_type_post`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`type_produit`) REFERENCES `type_produit` (`id_type_produit`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Contraintes pour la table `tarif`
--
ALTER TABLE `tarif`
  ADD CONSTRAINT `tarif_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tarif_ibfk_2` FOREIGN KEY (`type_commande`) REFERENCES `type_commande` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;