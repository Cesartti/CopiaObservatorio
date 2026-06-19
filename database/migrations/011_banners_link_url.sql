-- 011: Add link_url to banner tables for "Ver más" hyperlinks
ALTER TABLE cms_home_banners
  ADD COLUMN link_url VARCHAR(512) NULL AFTER image_url;

ALTER TABLE cms_microsite_hero_slides
  ADD COLUMN link_url VARCHAR(512) NULL AFTER image_url;
