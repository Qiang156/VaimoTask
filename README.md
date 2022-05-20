# VaimoTask
Since 2022-04-07

---
### Phase 1
- Create clean magento instance
- Import lots of products - write simple php script to generate ib xml and import, in chunks of 100
- Setup script with attributes (brand, description?, rating?, product_type, tags)
- Test import
- Image importing in transporter that generates a new IB import for images using Image Binder
- Use id as sku
- Image named 1048.jpg

> 
> ---
> Phase 1 [**Done**]
> - bin/magento product:xml:generate --filter="brand=maybelline"
> - bin/magento integration:job:run transport_product_import
> - bin/magento integration:job:run process_product_import
> - bin/magento imagebinder:run
> - bin/magento index:reindex
> ---
> - make colorname and color hex to show like swatches
> ---

### Phase 2
- Find out how to import configurable products
- Setup script to add color_name, color_hex attributes
- Create xml with configurable products and color simple products
- Use id + hex as sku (1048-B28378)
- Image named 1048-B28378.jpg with a color border/box https://www.php.net/manual/en/function.imagerectangle.php

>
> ---
> Phase 2 **Done**
> - Information from vaimo confluence
> > - Configurable products must come AFTER it's simple products in the import file
> > - The parent_sku attribute must include a configurable products SKU, in case it should be a child
> > - Alternatively, configurable product may contain comma-separated list of all child SKUs listed in the child_products node.
> - For child product, need to put them in front of their parent product with parent_sku tag with their parent's sku and type tag with virtual string.
> - For parent product, should write with type tag with configurable and configurable_attributes tag with specific options. 
> - bin/magento product:xml:generate -f brand=maybelline -t 100 configurable:color_hex
> ---


### Phase 3
- Minor frontend changes, like a banner, remove unwanted functions, add some colors etc

> ---
> Phase 3 [Done]
> - created Banner
> - modified Header block

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
> Done

### Phase 6
- Install Akeneo locally
- Import product data
- Create bridge between pim and Magento
