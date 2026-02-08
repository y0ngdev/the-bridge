- if an alumnus logs in to ict portal, it would take them to the bridge. 

- Allow subunit alumni coordinatoer to access the-bridge and manage their alumnus data.






sudo chown -R www-data:www-data /var/www/html/the-bridge
sudo chmod -R 755 /var/www/html/the-bridge
sudo chmod -R 775 /var/www/html/the-bridge/storage
sudo chmod -R 775 /var/www/html/the-bridge/bootstrap/cache


| Stateless | Source | IP Protocol | Source Port Range | Destination Port Range | Type and Code | Allows | Description |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| No | 0.0.0.0/0 | TCP | All | 22 | | | TCP traffic for ports: 22 SSH Remote Login Protocol |
| No | 0.0.0.0/0 | ICMP | | | 3, 4 | | ICMP traffic for: 3, 4 Destination Unreachable: Fragmentation Needed and Don't Fragment was Set |
| No | 10.0.0.0/16 | ICMP | | | 3 | | ICMP traffic for: 3 Destination Unreachable |
| No | 0.0.0.0/0 | TCP |All  | 80 | | | TCP traffic for ports: All |
| No | 0.0.0.0/0 | TCP | All | 443 | | | TCP traffic for ports: All |