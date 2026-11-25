-- 038_boletines_portadas_titulos.sql
-- 1) Cada boletin lleva de imagen de entrada su portada real (primera pagina
--    del PDF, en assets/img/boletines/). Antes varios no tenian imagen y los
--    dos de violencia compartian la misma.
-- 2) Los titulos pierden el «—» (y el «Dimension Social», que ya no se usa).
-- Idempotente: se apoya en pdf_url.
SET NAMES utf8mb4;

UPDATE cms_bulletins SET cover_url = CASE pdf_url
  WHEN 'assets/pdf/Boletin 1-04 julio-2023.pdf'                THEN 'assets/img/boletines/general-01-metricas.jpg'
  WHEN 'assets/pdf/Boletin 2-04 julio 2023.pdf'                THEN 'assets/img/boletines/general-02-pobreza.jpg'
  WHEN 'assets/pdf/Boletin 3-17 de julio 2023.pdf'             THEN 'assets/img/boletines/general-03-ods.jpg'
  WHEN 'assets/pdf/Boletin 4-06 julio 2023.pdf'                THEN 'assets/img/boletines/general-04-agricola.jpg'
  WHEN 'assets/pdf/Boletin 5 - 11 julio 2023.pdf'              THEN 'assets/img/boletines/general-05-calidad-aire.jpg'
  WHEN 'assets/pdf/Boletin 6-13 julio 2023.pdf'                THEN 'assets/img/boletines/general-06-ctei.jpg'
  WHEN 'assets/pdf/Boletin01_MDM.pdf'                          THEN 'assets/img/boletines/economico-01-mdm.jpg'
  WHEN 'assets/pdf/BOLETIN 1 de Violencia 10 dic 2024.pdf'     THEN 'assets/img/boletines/social-violencia-01-2024.jpg'
  WHEN 'assets/pdf/BOLETIN_VIOLENCIAS_NOVIEMBRE_2025.pdf'      THEN 'assets/img/boletines/social-violencia-02-2025.jpg'
  WHEN 'assets/pdf/BOLETIN_SALUD_NOVIEMBRE_2025.pdf'           THEN 'assets/img/boletines/social-salud-2025.jpg'
  WHEN 'assets/pdf/BOLETIN_VIOLENCIAS_2026.pdf'                THEN 'assets/img/boletines/social-violencia-03-2026.jpg'
  ELSE cover_url END;

UPDATE cms_bulletins SET title = 'Boletín 1 de Medición de Desempeño Municipal'
 WHERE pdf_url = 'assets/pdf/Boletin01_MDM.pdf';
UPDATE cms_bulletins SET title = TRIM(REPLACE(title, ' — Dimensión Social', '')) WHERE title LIKE '% — Dimensión Social';
UPDATE cms_bulletins SET title = TRIM(REPLACE(title, ' — ', ' ')) WHERE title LIKE '% — %';
