DROP PROCEDURE IF EXISTS add_sip;
CREATE PROCEDURE add_sip(
  IN pSipId VARCHAR(128)
  , IN pUsername VARCHAR(256)
  , IN pPassword VARCHAR(64)
  , IN pHost VARCHAR(256)
)
BEGIN
  INSERT INTO sip(
    _id
    , username
    , password
    , host
  ) VALUES (
    pSipId
    , pUsername
    , pPassword
    , pHost
  );

  SELECT LAST_INSERT_ID();
END;