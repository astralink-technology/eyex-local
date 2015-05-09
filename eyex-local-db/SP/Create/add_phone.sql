DROP PROCEDURE IF EXISTS add_phone;
CREATE PROCEDURE add_phone(
  IN pPhoneId VARCHAR(128)
  , IN pPhoneDigits VARCHAR(32)
  , IN pCountryCode VARCHAR(6)
  , IN pCode VARCHAR(6)
  , IN pType VARCHAR(6)
  , IN pEntity VARCHAR(128)
)
BEGIN
  INSERT INTO announcement(
    _id
    , phone_digits
    , country_code
    , code
    , type
    , entity
  ) VALUES (
    pPhoneId
    , pPhoneDigits
    , pCountryCode
    , pCode
    , pType
    , pEntityId
  );

  SELECT LAST_INSERT_ID();
END;