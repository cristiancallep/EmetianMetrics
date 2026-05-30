USE emetian_metrics;

ALTER TABLE items
    ADD COLUMN crypto_symbol VARCHAR(20) DEFAULT NULL AFTER title;
