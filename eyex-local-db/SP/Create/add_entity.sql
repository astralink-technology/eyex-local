DROP PROCEDURE IF EXISTS add_entity;

CREATE PROCEDURE add_entity(
  pEntityId VARCHAR(128)
  , pAuthenticationString VARCHAR(256)
  , pAuthenticationStringLower VARCHAR(256)
  , pFirstName VARCHAR(256)
  , pLastName VARCHAR(256)
  , pName VARCHAR(512)
  , pExtension VARCHAR(128)
  , pCard VARCHAR(256)
  , pPin VARCHAR(16)
)
BEGIN
  INSERT INTO entity(
    _id
    , authentication_string
    , authentication_string_lower
    , first_name
    , last_name
    , name
    , extension
    , card
    , pin
  ) VALUES (
    pEntityId
    , pAuthenticationString
    , pAuthenticationStringLower
    , pFirstName
    , pLastName
    , pName
    , pExtension
    , pCard
    , pPin
  );

  SELECT LAST_INSERT_ID();
END;