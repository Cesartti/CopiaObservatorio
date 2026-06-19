-- 010: Make title optional in hero slides and fix charset
-- Title can now be empty (image-only slides)

ALTER TABLE cms_microsite_hero_slides
  MODIFY COLUMN title VARCHAR(255) NOT NULL DEFAULT '';

-- Ensure table uses utf8mb4 to handle tildes and special chars
ALTER TABLE cms_microsite_hero_slides
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE cms_home_banners
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
