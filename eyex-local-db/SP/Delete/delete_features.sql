DROP PROCEDURE IF EXISTS delete_features;
CREATE PROCEDURE delete_features(
  IN pFeaturesId VARCHAR(128)
)
BEGIN
  IF
    pFeaturesId IS NOT NULL
  THEN
    DELETE FROM features WHERE
    ((pFeaturesId IS NULL) OR (features._id = pFeaturesId));
  END IF;
END;