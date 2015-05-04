DROP PROCEDURE IF EXISTS add_announcement;
CREATE PROCEDURE add_announcement(
  IN pAnnouncementId VARCHAR(128)
  , IN pMessage TEXT
)
BEGIN
  INSERT INTO announcement(
    _id
    , message
  ) VALUES (
    pAnnouncementId
    , pMessage
  );

  SELECT LAST_INSERT_ID();
END;