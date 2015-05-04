DROP PROCEDURE IF EXISTS get_announcement;
CREATE PROCEDURE get_announcement()
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS announcementTemp AS (
    SELECT
      announcement._id
      , announcement.message
    FROM announcement
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    announcementTemp;

  PREPARE stmt FROM "SELECT *, ? AS total_rows from announcementTemp;";
  EXECUTE stmt USING @pTotalRows;
  DEALLOCATE PREPARE stmt;

END;
