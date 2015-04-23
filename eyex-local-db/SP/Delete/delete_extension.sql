DROP PROCEDURE IF EXISTS delete_extension;
CREATE PROCEDURE delete_extension(
  IN pExtensionId VARCHAR(128)
)
BEGIN
  IF
    pExtensionId IS NOT NULL
  THEN
    DELETE FROM extension WHERE
    ((pExtensionId IS NULL) OR (extension._id = pExtensionId));
  END IF;
END;