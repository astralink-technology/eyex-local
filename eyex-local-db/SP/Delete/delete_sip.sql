DROP PROCEDURE IF EXISTS delete_sip;
CREATE PROCEDURE delete_sip(
  IN pSipId VARCHAR(128)
)
BEGIN
  IF
    pSipId IS NOT NULL
  THEN
    DELETE FROM sip WHERE
    ((pSipId IS NULL) OR (sip._id = pSipId));
  END IF;
END;