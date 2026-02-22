-- data.sql : Données d'insertion pour toutes les tables

-- Données pour la table tb_clients
INSERT INTO `tb_clients` (`matricule`, `nom`, `postnom`, `prenom`, `email`, `telephone`, `sexe`, `date_naissance`, `lieu_naissance`, `adresse`, `etat_civil`, `nom_conjoint`, `zone`, `type_piece_identite`, `lieu_delivrance_piece`, `date_delivrance_piece`, `numero_piece_identite`, `photo`, `secteur_activite`, `type_activite`, `nom_entreprise`, `adresse_entreprise`, `telephone_entreprise`, `statut_entreprise`, `nombre_annees_experience`, `revenu_mensuel`, `revenu_mensuel_devise`, `autres_details_activite`, `created_at`, `updated_at`) VALUES
('CL-EBENKGA-26-00001', 'Blaise', 'MUBADI', 'Bakajika', 'exemple@email.com', '0123456789', 'M', '1995-08-17', 'Kananga', 'Nganza  N° 12', 'Marié', 'Nice', 'Urbain', 'Carte nationale d''identité', 'Kananga', '2020-12-28', '08888025', 'clients/1771152283_IMG_8147.jpeg', 'Enseignement', 'Commerce', 'IPKO', 'Kanowa Pepiniere, N''SELE, NGANZA , KANANGA', '+243 55555', 'Agréee', '2', 50000.00, NULL, 'Ras', '2026-02-13 21:26:13', '2026-02-15 08:53:39'),
('CL-EBENKGA-26-00002', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Divorcé', NULL, 'Urbain', 'Carte d''électeur', 'Kananga', '2021-02-19', '000847', 'clients/Nice.jpg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N''SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 12:47:42', '2026-02-14 12:47:42'),
('CL-EBENKGA-26-00003', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Marié', 'Nice MPUTU', 'Urbain', 'Carte d''électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771151216_IMG_8304.jpeg', 'Sorry', 'Commerce', 'KANKO', 'Kanowa Pepiniere, N''SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 14:06:40', '2026-02-15 08:27:42'),
('CL-EBENKGA-26-00004', 'MPUTU', 'TUDIKOLELE', 'Clémence', 'exemple@email.com', '0123456789', 'M', '1994-02-04', 'Kinshasa', 'Ndesha', 'Célibataire', NULL, 'Urbain', 'Carte d''électeur', 'Kananga', '2021-02-19', '000847', 'clients/1771085969_Blaise_1.jpeg', 'Sorry', 'Agriculture', 'KANKO', 'Kanowa Pepiniere, N''SELE, NGANZA , KANANGA', '0992463511', 'Agréee', '5', 50000.00, NULL, 'Ras', '2026-02-14 14:19:29', '2026-02-14 14:19:29');

-- Données pour la table tb_agents
INSERT INTO `tb_agents` (`matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `photo`, `date_embauche`, `statut`, `created_at`, `updated_at`) VALUES
('AG-EBENKGA-26-00002', 'KABUE', 'NTUMBA', 'Joel', 'F', '1995-01-31', '+21', 'christophetshibangu117@gmail.com', 'Kanowa Pepiniere, N''SELE, NGANZA , KANANGA', 'agents/1771284216_1767056067186jpg', '2025-06-05', 'actif', '2026-02-16 21:23:36', '2026-02-16 21:23:36');

-- Données pour la table tb_services
INSERT INTO `tb_services` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Caisse', NULL, '2026-02-17 11:57:22', '2026-02-17 11:57:22'),
(2, 'Comptabilité', NULL, '2026-02-17 12:00:40', '2026-02-17 12:00:40'),
(3, 'Service manquant', NULL, '2026-02-20 07:00:00', '2026-02-20 07:00:00'),
(4, 'Ressources Humaines', NULL, '2026-02-17 12:03:08', '2026-02-17 12:03:08'),
(5, 'faculté', NULL, '2026-02-17 12:04:22', '2026-02-17 12:04:22'),
(6, 'Cafétariat', NULL, '2026-02-17 12:04:39', '2026-02-17 12:04:39'),
(7, 'Polyclinique', NULL, '2026-02-17 12:05:55', '2026-02-17 12:05:55');

-- Données pour la table tb_postes
INSERT INTO `tb_postes` (`id`, `service_id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(2, 3, 'Caisse', 'Central', '2026-02-20 07:40:25', '2026-02-20 07:40:25'),
(3, 7, 'Nourritue', NULL, '2026-02-20 07:40:39', '2026-02-20 07:40:39'),
(4, 3, 'Autres', NULL, '2026-02-20 08:41:29', '2026-02-20 08:41:29');

-- Données pour la table sessions
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4B0jAT18TagJUpYcK9beqtDoOG5ctfrvYIXVmU7Z', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUdLcU94ZFVTcFBkMmlBM1ZsQmxPQWlTQ0FRVWdCQkJ5Umo5REZEOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9yaC9zZXJ2aWNlcyI7czo1OiJyb3V0ZSI7czoxNDoic2VydmljZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771587599),
('eqNNsP5Zdmbn3bTCHDekCi6rPoYroMVuMK2EJtl1', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT016eUVUSEhjOWVxclNvUHlTRnVmbzFGTFBxRlVsQ3dxTFdaZGtqTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3QvTmV4dXMtQk1CLVRlY2gtc29mdC1iYW5raW5nL3B1YmxpYy9yaC9zZXJ2aWNlcyI7czo1OiJyb3V0ZSI7czoxNDoic2VydmljZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1771581801);
