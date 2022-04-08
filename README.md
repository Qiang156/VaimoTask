# VaimoTask
since 2022-04-07

---
### Phase 1
- Create clean magento instance
- Import lots of products - write simple php script to generate ib xml and import, in chunks of 100
- Setup script with attributes (brand, description?, rating?, product_type, tags)
- Test import
- Image importing in transporter that generates a new IB import for images using Image Binder
- Use id as sku
- Image named 1048.jpg

### Phase 2
- Find out how to import configurable products
- Setup script to add color_name, color_hex attributes
- Create xml with configurable products and color simple products
- Use id + hex as sku (1048-B28378)
- Image named 1048-B28378.jpg with a color border/box https://www.php.net/manual/en/function.imagerectangle.php

### Phase 3
- Minor frontend changes, like a banner, remove unwanted functions, add some colors etc

---

### Phase 4
- Install Multi option filer module (ajax if its easy)
- Have a look at pagination/lazy loading
- Multiple frontends, create second frontend with only Maybelline, create website with setup script, add Maybelline products to a second 
- Create Maybelline theme with minor changes to make them look different
- Create some content for the different pages, use page builder and perhaps create a Carousel

### Phase 5
- Laravel mock price api
- Magento FE asks Magento BE for price, if its not cached then it will ask Laravel and cache it.
- When adding to cart ask for the price again (make sure it can load from cache), and update the db table quote_item
- Stock api if you want

### Phase 6
- Install Akeneo locally
- Import product data
- Create bridge between pim and Magento
