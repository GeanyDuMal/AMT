-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 31 jan. 2022 à 22:07
-- Version du serveur : 10.4.22-MariaDB
-- Version de PHP : 7.4.26

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

--
-- Structure de la table `association`
--

CREATE TABLE `association` (
  `member_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `association_role`
--

CREATE TABLE `association_role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `association_role`
--

INSERT INTO `association_role` (`id`, `name`) VALUES
(1, 'President'),
(2, 'Tresorier'),
(3, 'Vice President'),
(4, 'Secretaire'),
(5, 'Membre');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `client_type_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(5,2) DEFAULT 0.00,
  `fidelity_point` int(11) DEFAULT 0,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client_type`
--

CREATE TABLE `client_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client_type`
--

INSERT INTO `client_type` (`id`, `name`) VALUES
(1, 'Association'),
(2, 'Etudiant');

-- --------------------------------------------------------

--
-- Structure de la table `ordered`
--

CREATE TABLE `ordered` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `payment_type_id` int(11) NOT NULL,
  `ordered_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payment_type`
--

CREATE TABLE `payment_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `payment_type`
--

INSERT INTO `payment_type` (`id`, `name`) VALUES
(1, 'Carte Bancaire'),
(2, 'Espece'),
(3, 'Solde');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `post_type_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://a2mo-197c6.kxcdn.com/wp-content/uploads/2021/10/placeholder1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `post_type`
--

CREATE TABLE `post_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `post_type`
--

INSERT INTO `post_type` (`id`, `name`) VALUES
(1, 'Event'),
(2, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `price`
--

CREATE TABLE `price` (
  `product_id` int(11) NOT NULL,
  `client_type_id` int(11) NOT NULL,
  `price` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_type_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_stock` int(11) NOT NULL,
  `image_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product_type`
--

CREATE TABLE `product_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product_type`
--

INSERT INTO `product_type` (`id`, `name`) VALUES
(1, 'Snack'),
(2, 'Boisson');

-- --------------------------------------------------------

--
-- Structure de la table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ordered_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `association`
--
ALTER TABLE `association`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `IDX_FD8521CCD60322AC` (`role_id`);

--
-- Index pour la table `association_role`
--
ALTER TABLE `association_role`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C7440455AA08CB10` (`login`),
  ADD KEY `IDX_C74404559771C8EE` (`client_type_id`);

--
-- Index pour la table `client_type`
--
ALTER TABLE `client_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ordered`
--
ALTER TABLE `ordered`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C3121F9919EB6921` (`client_id`),
  ADD KEY `IDX_C3121F99DC058279` (`payment_type_id`);

--
-- Index pour la table `payment_type`
--
ALTER TABLE `payment_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8DF8A43BA0` (`post_type_id`);

--
-- Index pour la table `post_type`
--
ALTER TABLE `post_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `price`
--
ALTER TABLE `price`
  ADD PRIMARY KEY (`product_id`,`client_type_id`),
  ADD KEY `IDX_CAC822D94584665A` (`product_id`),
  ADD KEY `IDX_CAC822D99771C8EE` (`client_type_id`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_D34A04AD14959723` (`product_type_id`);

--
-- Index pour la table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6117D13B4584665A` (`product_id`),
  ADD KEY `IDX_6117D13BAA60395A` (`ordered_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `association_role`
--
ALTER TABLE `association_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `client_type`
--
ALTER TABLE `client_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ordered`
--
ALTER TABLE `ordered`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payment_type`
--
ALTER TABLE `payment_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `post_type`
--
ALTER TABLE `post_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `association`
--
ALTER TABLE `association`
  ADD CONSTRAINT `FK_FD8521CC7597D3FE` FOREIGN KEY (`member_id`) REFERENCES `client` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FD8521CCD60322AC` FOREIGN KEY (`role_id`) REFERENCES `association_role` (`id`);

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C74404559771C8EE` FOREIGN KEY (`client_type_id`) REFERENCES `client_type` (`id`);

--
-- Contraintes pour la table `ordered`
--
ALTER TABLE `ordered`
  ADD CONSTRAINT `FK_F529939819EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_F5299398DC058279` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_type` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DF8A43BA0` FOREIGN KEY (`post_type_id`) REFERENCES `post_type` (`id`);

--
-- Contraintes pour la table `price`
--
ALTER TABLE `price`
  ADD CONSTRAINT `FK_CAC822D94584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CAC822D99771C8EE` FOREIGN KEY (`client_type_id`) REFERENCES `client_type` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD14959723` FOREIGN KEY (`product_type_id`) REFERENCES `product_type` (`id`);

--
-- Contraintes pour la table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `FK_6117D13B4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_6117D13B8D9F6D38` FOREIGN KEY (`ordered_id`) REFERENCES `ordered` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
