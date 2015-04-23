DROP PROCEDURE IF EXISTS add_extension;
CREATE PROCEDURE add_extension(
  pExtensionId VARCHAR(128)
  , pNumber INTEGER
  , pExtensionPassword VARCHAR(256)
  , pEntity VARCHAR(128)
)
BEGIN
  INSERT INTO extension(
    _id
    , number
    , extension_password
    , entity
  ) VALUES (
    pExtensionId
    , pNumber
    , pExtensionPassword
    , pEntity
  );
  SELECT LAST_INSERT_ID();
END;