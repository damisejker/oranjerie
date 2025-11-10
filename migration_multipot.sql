-- ============================================================
-- МИГРАЦИЯ: Поддержка нескольких горшков (MULTIPOT)
-- Дата: 2025-11-10
-- Описание: Добавляет поддержку нескольких горшков для игры "Оранжерея"
-- ============================================================

-- 1. Создаём новую таблицу горшков
-- Заменяет старую таблицу pot_positions, добавляет поддержку нескольких горшков на пользователя
CREATE TABLE IF NOT EXISTS `pots` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `login` VARCHAR(64) NOT NULL,
  `name` VARCHAR(64) DEFAULT 'Горшок',
  `pot_left` VARCHAR(16) DEFAULT '50%',
  `pot_top` VARCHAR(16) DEFAULT '80%',
  `unlocked_at` DATETIME DEFAULT NOW(),
  INDEX `idx_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Добавляем колонку pot_id в таблицу oranjerie
-- Это свяжет каждое растение с конкретным горшком
ALTER TABLE `oranjerie`
  ADD COLUMN `pot_id` INT NULL AFTER `login`,
  ADD INDEX `idx_pot_id` (`pot_id`);

-- 3. Миграция данных из старой таблицы pot_positions в новую таблицу pots
-- Создаём дефолтный горшок для каждого пользователя, у которого есть позиция горшка
INSERT INTO `pots` (`login`, `name`, `pot_left`, `pot_top`, `unlocked_at`)
SELECT
  `username` as `login`,
  'Горшок' as `name`,
  COALESCE(`pot_left`, '50%') as `pot_left`,
  COALESCE(`pot_top`, '80%') as `pot_top`,
  NOW() as `unlocked_at`
FROM `pot_positions`
WHERE NOT EXISTS (
  SELECT 1 FROM `pots` WHERE `pots`.`login` = `pot_positions`.`username`
);

-- 4. Привязываем существующие растения к дефолтному горшку их владельца
UPDATE `oranjerie` o
INNER JOIN `pots` p ON o.`login` = p.`login`
SET o.`pot_id` = p.`id`
WHERE o.`pot_id` IS NULL
AND p.`id` = (
  SELECT MIN(`id`) FROM `pots` WHERE `login` = o.`login`
);

-- 5. ОПЦИОНАЛЬНО: Удаление старой таблицы pot_positions
-- ВАЖНО: Раскомментируйте следующую строку только после проверки, что всё работает корректно!
-- DROP TABLE IF EXISTS `pot_positions`;

-- ============================================================
-- КОНЕЦ МИГРАЦИИ
-- ============================================================
