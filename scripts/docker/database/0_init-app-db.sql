CREATE TABLE entity_meta (
  id       SERIAL PRIMARY KEY,
  name     VARCHAR(100) UNIQUE NOT NULL,
  `schema` JSON                NOT NULL
);

CREATE TABLE entity (
  id      INT AUTO_INCREMENT,
  meta_id INT REFERENCES entity_meta (id),
  value   JSON NOT NULL,
  PRIMARY KEY (id, meta_id)
) PARTITION BY KEY (meta_id) PARTITIONS 100;
