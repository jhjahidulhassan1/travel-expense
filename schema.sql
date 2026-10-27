-- Import via phpMyAdmin (cPanel) into your database.
-- Already installed the first version? Run only this line instead:
--   ALTER TABLE trips ADD COLUMN members TEXT NULL AFTER days;
CREATE TABLE IF NOT EXISTS trips (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_uid  VARCHAR(64)  NOT NULL UNIQUE,      -- generated in the browser; makes offline sync idempotent
  name        VARCHAR(120) NOT NULL,
  route_from  VARCHAR(80)  NOT NULL DEFAULT '',
  route_to    VARCHAR(80)  NOT NULL DEFAULT '',
  travelers   SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  days        SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  members     TEXT NULL,                         -- JSON array of team member names
  total       DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trip_items (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  trip_id     INT UNSIGNED NOT NULL,
  category    VARCHAR(40)  NOT NULL,
  description VARCHAR(160) NOT NULL DEFAULT '',
  qty         DECIMAL(10,2) NOT NULL DEFAULT 1,
  unit_cost   DECIMAL(12,2) NOT NULL DEFAULT 0,
  amount      DECIMAL(12,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
