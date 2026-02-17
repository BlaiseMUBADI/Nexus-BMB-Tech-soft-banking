-- Structure complète RH et utilisateurs

CREATE TABLE tb_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(191) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE tb_postes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    nom VARCHAR(191) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (service_id) REFERENCES tb_services(id)
);

CREATE TABLE tb_agents (
    matricule VARCHAR(50) PRIMARY KEY,
    nom VARCHAR(191) NOT NULL,
    postnom VARCHAR(191),
    prenom VARCHAR(191),
    sexe ENUM('M','F'),
    date_naissance DATE,
    telephone VARCHAR(50),
    email VARCHAR(191),
    adresse VARCHAR(191),
    date_embauche DATE,
    statut ENUM('actif','inactif') DEFAULT 'actif',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE tb_affectations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agent_matricule VARCHAR(50) NOT NULL,
    poste_id BIGINT UNSIGNED NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (agent_matricule) REFERENCES tb_agents(matricule),
    FOREIGN KEY (poste_id) REFERENCES tb_postes(id)
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(191) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(191) PRIMARY KEY,
    token VARCHAR(191) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE sessions (
    id VARCHAR(191) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX (user_id),
    INDEX (last_activity)
);
