<?php
// Configure these for your one.com MySQL credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'c2s4tpyp0_globbin_se');
define('DB_USER', 'c2s4tpyp0_globbin_se');
define('DB_PASS', 'sftpglobbin1313');
define('DB_CHARSET', 'utf8mb4');

function getDb(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        initTables($pdo);
    }
    return $pdo;
}

function initTables(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gangs (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(100) NOT NULL,
            type       VARCHAR(50)  NOT NULL,
            credits    INT          NOT NULL DEFAULT 1000,
            reputation INT          NOT NULL DEFAULT 0,
            created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighters (
            id                INT AUTO_INCREMENT PRIMARY KEY,
            gang_id           INT          NOT NULL,
            name              VARCHAR(100) NOT NULL,
            type              VARCHAR(50)  NOT NULL,
            cost              INT          NOT NULL DEFAULT 0,
            experience        INT          NOT NULL DEFAULT 0,
            kills             INT          NOT NULL DEFAULT 0,
            advancement_count INT          NOT NULL DEFAULT 0,
            in_recovery       TINYINT(1)   NOT NULL DEFAULT 0,
            dead              TINYINT(1)   NOT NULL DEFAULT 0,
            m        INT NOT NULL DEFAULT 5,
            ws       INT NOT NULL DEFAULT 4,
            bs       INT NOT NULL DEFAULT 4,
            s        INT NOT NULL DEFAULT 3,
            t        INT NOT NULL DEFAULT 3,
            w        INT NOT NULL DEFAULT 1,
            i        INT NOT NULL DEFAULT 4,
            a        INT NOT NULL DEFAULT 1,
            ld       INT NOT NULL DEFAULT 6,
            cl       INT NOT NULL DEFAULT 7,
            wil      INT NOT NULL DEFAULT 7,
            int_stat INT NOT NULL DEFAULT 7,
            FOREIGN KEY (gang_id) REFERENCES gangs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_skills (
            id             INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id     INT          NOT NULL,
            skill_name     VARCHAR(100) NOT NULL,
            skill_category VARCHAR(100) NOT NULL DEFAULT '',
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_injuries (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id  INT          NOT NULL,
            injury_name VARCHAR(100) NOT NULL,
            permanent   TINYINT(1)  NOT NULL DEFAULT 0,
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS equipment (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id INT          NOT NULL,
            name       VARCHAR(100) NOT NULL,
            type       VARCHAR(20)  NOT NULL DEFAULT 'equipment',
            cost       INT          NOT NULL DEFAULT 0,
            traits     JSON,
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_weapons (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id INT          NOT NULL,
            name       VARCHAR(100) NOT NULL,
            cost       INT          NOT NULL DEFAULT 0,
            notes      VARCHAR(255) NOT NULL DEFAULT '',
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_armour (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id INT          NOT NULL,
            name       VARCHAR(100) NOT NULL,
            cost       INT          NOT NULL DEFAULT 0,
            notes      VARCHAR(255) NOT NULL DEFAULT '',
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_wargear (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id INT          NOT NULL,
            name       VARCHAR(100) NOT NULL,
            cost       INT          NOT NULL DEFAULT 0,
            notes      VARCHAR(255) NOT NULL DEFAULT '',
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_special_rules (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            fighter_id  INT          NOT NULL,
            rule_name   VARCHAR(100) NOT NULL,
            description VARCHAR(255) NOT NULL DEFAULT '',
            FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS weapon_library (
            id        INT AUTO_INCREMENT PRIMARY KEY,
            gang_type VARCHAR(50)  NOT NULL,
            name      VARCHAR(100) NOT NULL,
            cost      INT          NOT NULL DEFAULT 0,
            range_s   VARCHAR(10)  NOT NULL DEFAULT '-',
            range_l   VARCHAR(10)  NOT NULL DEFAULT '-',
            hit_s     VARCHAR(10)  NOT NULL DEFAULT '-',
            hit_l     VARCHAR(10)  NOT NULL DEFAULT '-',
            str       VARCHAR(10)  NOT NULL DEFAULT '-',
            ap        VARCHAR(10)  NOT NULL DEFAULT '-',
            dmg       VARCHAR(10)  NOT NULL DEFAULT '1',
            ammo      VARCHAR(10)  NOT NULL DEFAULT '-',
            traits    VARCHAR(255) NOT NULL DEFAULT '',
            sort_order INT         NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS skill_library (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(100) NOT NULL,
            category   VARCHAR(50)  NOT NULL DEFAULT '',
            factions   TEXT         NOT NULL DEFAULT '',
            roles      TEXT         NOT NULL DEFAULT '',
            sort_order INT          NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS injury_library (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(100) NOT NULL,
            category    VARCHAR(50)  NOT NULL DEFAULT '',
            description VARCHAR(255) NOT NULL DEFAULT '',
            sort_order  INT          NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS fighter_templates (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            gang_type  VARCHAR(50)  NOT NULL,
            name       VARCHAR(100) NOT NULL,
            cost       INT          NOT NULL DEFAULT 0,
            m          INT          NOT NULL DEFAULT 5,
            ws         INT          NOT NULL DEFAULT 4,
            bs         INT          NOT NULL DEFAULT 4,
            s          INT          NOT NULL DEFAULT 3,
            t          INT          NOT NULL DEFAULT 3,
            w          INT          NOT NULL DEFAULT 1,
            i          INT          NOT NULL DEFAULT 4,
            a          INT          NOT NULL DEFAULT 1,
            ld         INT          NOT NULL DEFAULT 6,
            cl         INT          NOT NULL DEFAULT 7,
            wil        INT          NOT NULL DEFAULT 7,
            int_stat   INT          NOT NULL DEFAULT 7,
            sort_order INT          NOT NULL DEFAULT 0,
            notes      TEXT         NOT NULL DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}
