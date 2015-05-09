DROP PROCEDURE IF EXISTS delete_phone;
CREATE PROCEDURE delete_phone(
  IN pPhoneId VARCHAR(128)
)
BEGIN
  IF
    pPhoneId IS NOT NULL
  THEN
    DELETE FROM phone WHERE
    ((pPhoneId IS NULL) OR (phone._id = pPhoneId));
  END IF;
END;