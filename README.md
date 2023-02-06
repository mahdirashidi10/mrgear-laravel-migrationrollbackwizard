## Laravel Migration Rollback Wizard ##

---
More usability for Laravel migration rollback. 

### Installation ###

---
```
composer require mrgear/laravel-migrationrollbackwizard  
```

### Brief Explaination ###

---

This package is using Laravel's functionality for adding extra actions on migrations rollback.
[Visit documentation](https://laravel.com/docs/docs/master/migrations).

Framework is running migrations using database table (migrations) which has two key columns for that purpose: file and batch.

Batch column handles steps of migration and File column holds the name of migration file.


Migration Rollback Wizard uses the table to handle the purpose.

### Usage ###

---

Migration Rollback Wizard is a console command that provides more usability for rolling back migrations.

<br>

###### Command:  ######

```
php artisan migrate:rollback:wizard {--limit=} {--file=} {--verb=} {--include} {--exclude} 
```
<br>

Consider we have these migration files:


1. ``` 2021_10_12_104234_create_users_table ``` 
2. ``` 2021_10_12_125321_create_wallet_table ``` 
3. ``` 2021_10_12_134000_create_payments_table ``` 
4. ``` 2022_1_14_001120_add_phone_number_column_to_users_table ``` 
5. ``` 2022_1_15_001120_remove_refund_amount_column_from_wallet_table ``` 
6. ``` 2022_1_22_201200_add_status_column_to_payment_table ``` 
7. ``` 2022_3_10_115601_create_transactions_table ```  
8. ``` 2022_4_14_001120_change_phone_number_column_in_users_table ``` 
9. ``` 2022_5_12_155522_add_last_balance_to_transactions_table ```  
10. ``` 2022_10_12_133322_remove_transactions_table ```

<br>

######  ``` --file ```  option:  ######

---
Using ``` --file ``` option you can directly target a migration to rollback.

e.g. 
``` php artisan migrate:rollback:wizard --file=2022_1_15_001120_remove_refund_amount_column_from_wallet_table ```
will run down method of ``` 2022_1_15_001120_remove_refund_amount_column_from_wallet_table ``` file.

<br>

######  ``` --verb ```  option:  ######

---

 ``` --verb ``` option will roll back files containing the verb.

e.g.
``` php artisan migrate:rollback:wizard --verb=users ``` runs down method of 2022_4_14_001120_change_phone_number_column_in_users_table, 2022_1_14_001120_add_phone_number_column_to_users_table and 2021_10_12_104234_create_users_table files.

<br>

######  ``` --limit ```  option:  ######

---
Using ``` --limit ``` option you can set limit number. The migration rollback will be executed from last migration file to the number you specified.

For example ``` php artisan migrate:rollback:wizard --limit=3 ``` will run rollback command for 2022_10_12_133322_remove_transactions_table, 2022_5_12_155522_add_last_balance_to_transactions_table, 2022_4_14_001120_change_phone_number_column_in_users_table
files in order.

**Note:** If you run ``` php artisan migrate:rollback:wizard --limit=5 ```, two remaining migrations (2022_3_10_115601_create_transactions_table, 2022_1_22_201200_add_status_column_to_payment_table) will be executed

<br>

######  ``` --include ``` option:  ######

---

In ``` mrw.php ``` config file an include has been specified. 
``` php artisan migrate:rollback:wizard --include ``` will run migration files added in include array.
