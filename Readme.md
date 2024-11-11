# Building Steps 

## Step 1: Preparing Services [ Mysql , PhpMyAdmin , Redis ]
```bash
docker compose up -d --build
```

```bash
##### MYSQL & PHPMYADMIN #####

DB_DATABASE=food_ordering
DB_USERNAME=root
DB_PASSWORD=pass_@123
DB_ROOT_PASSWORD=pass_@123
DB_PORT=3306

PHP_MYADMIN_MY_PORT=8002
PHPMYADMIN_DB_USERNAME=root
PHPMYADMIN_DB_PASSWORD=pass_@123
PHPMYADMIN_PORT=8365

NETWORK_NAME=food_network

DB_UPLOAD_MAX_FILESIZE=100M


##### REDIS #####
REDIS_PORT=6372
```

## Step 2: Which Service You Want To Test [ Monolithic , MicroServices ]

### Monolithic [ Foodics ] [ With Test Cases ]
```bash
===== Monolithic =====
cd monolithic/foodics
docker compose up -d --build
docker exec -it -uroot foodics-service php artisan test
```

###  MicroServices [ Notification , Order , Stock ] [ Without Test Cases , sorry :( ]
```bash
===== Notification =====
```bash
cd micro-service/src/Notification
docker compose up -d --build

===== Order =====
cd micro-service/src/Order
docker compose up -d --build

===== Stock =====
cd micro-service/src/Stock
docker compose up -d --build
```

## Step 3: Testing Services [ Monolithic ]
```bash
docker exec -it -uroot foodics-service php artisan test

   PASS  Tests\Feature\PlaceOrderTest
  ✓ it places an order successfully                                      1.14s  
  ✓ it dispatches PlaceNewOrderJob when placing an order                 0.03s  
  ✓ it stores the order with "Processing" status                         0.05s  
  ✓ it attaches products to the order                                    0.05s  
  ✓ it reserves product ingredients for the order                        0.05s  
  ✓ it deducts ingredient stock based on order quantity                  0.05s  
  ✓ it triggers a low stock alert when stock is low                      0.07s  
  ✓ it cancels the order when stock is not enough                        0.05s  
  ✓ it dispatches SendLowStockAlertJob when an ingredient is low in sto… 0.04s  
  ✓ it dispatches SendLowStockAlertJob only once when multiple orders a… 0.04s  

  Tests:    10 passed (13 assertions)
  Duration: 1.65s

```

