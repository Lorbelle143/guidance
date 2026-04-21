-- Admin accounts seed
-- Default password for all accounts: admin123
-- Hash: $2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri
-- IMPORTANT: Each admin should change their password after first login.

INSERT INTO users (email, password, full_name, role, is_active)
VALUES
  ('lorbelleganzan@gmail.com', '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'Lorbelle Ganzan',       'admin', 1),
  ('gco@nbsc.edu.ph',          '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'GCO Admin',             'admin', 1),
  ('jacorpuz@nbsc.edu.ph',     '$2y$12$g4Iuar46ESQbSx2uIX3nwelvgoOPUR5yvzo4EcF.VASXhCHE0W0Ri', 'Jo Augustine Corpuz',   'admin', 1)
ON DUPLICATE KEY UPDATE
  full_name  = VALUES(full_name),
  is_active  = 1;
