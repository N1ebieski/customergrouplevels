# Customer Group Levels

Prestashop module for assigning group levels to users depending on product categories. It allows you to set different prices for products to various users depending on their groups in specific categories.

# Tests

Integration tests:

1. Uninstall all custom modules (make sure the file override/classes/SpecificPrice.php is deleted)
2. Create symlink tests/Resources/modules/customergrouplevels to modules/customergrouplevels
3. Add "customergrouplevels" to src/PrestashopBundle/Install::getModulesList
4. Clear var/cache/test
5. Run ```composer create-test-db```
6. Run ```composer phpunit```
7. Run ```composer restore-test-db```

## Copyright and License

https://intelekt.net.pl/pages/regulamin