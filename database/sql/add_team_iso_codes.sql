-- Script pour ajouter les codes ISO aux équipes
-- À exécuter après avoir importé les équipes depuis le CSV

-- Sénégal
UPDATE teams SET iso_code = 'sn' WHERE UPPER(name) = 'SENEGAL' AND iso_code IS NULL;

-- Botswana
UPDATE teams SET iso_code = 'bw' WHERE UPPER(name) = 'BOTSWANA' AND iso_code IS NULL;

-- Afrique du Sud
UPDATE teams SET iso_code = 'za' WHERE UPPER(name) = 'AFRIQUE DU SUD' AND iso_code IS NULL;

-- Égypte
UPDATE teams SET iso_code = 'eg' WHERE UPPER(name) = 'EGYPTE' AND iso_code IS NULL;

-- RD Congo
UPDATE teams SET iso_code = 'cd' WHERE UPPER(name) = 'RD CONGO' AND iso_code IS NULL;

-- Côte d'Ivoire
UPDATE teams SET iso_code = 'ci' WHERE UPPER(name) LIKE '%COTE%IVOIRE%' AND iso_code IS NULL;

-- Cameroun
UPDATE teams SET iso_code = 'cm' WHERE UPPER(name) = 'CAMEROUN' AND iso_code IS NULL;

-- Bénin
UPDATE teams SET iso_code = 'bj' WHERE UPPER(name) = 'BENIN' AND iso_code IS NULL;

-- Vérifier les résultats
SELECT name, iso_code FROM teams ORDER BY name;
