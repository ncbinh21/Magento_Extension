nohup php bin/magento forix:import --entity=catalog_product --behavior=delete --file=old_products.csv > var/deleteOldProducts.log &

php bin/magento forix:import --entity=melfred_product_relations --behavior=append --file=sample_relations_1_22.csv
php bin/magento forix:import --entity=melfred_product_attributes --behavior=append --file=sample_products_1_22.csv
bin/magento forix:import --entity=melfred_catalog_product --based=catalog_product --behavior=append --file=sample_products_1_22.csv