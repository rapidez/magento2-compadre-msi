# Magento Rapidez Compadre

This **Magento Module** will allow Rapidez to support more functionalities.

## Current functionality

Currently this module adds the ProductStockItem type to the ProductInterface in the graphql definition.
Allowing you to retrieve the in_stock, qty, min_sale_qty, max_sale_qty, qty_increments

And it extends Magento functionality in order to facilitate file upload product options via GraphQL

## Installation

In your Magento installation run
```bash
composer require rapidez/magento2-compadre
bin/magento module:enable Rapidez_Compadre
```

## Configuration

Configuration options are available under `Stores > Configuration > Rapidez > Config`

Here you can configure what extra fields should be exposed in GraphQL, fields not exposed will be `null`.

## Release instructions

If GraphQl changes have been made src/etc/module.xml must be updated with the new release version number.
This way we can easily detect which fields should be available in GraphQl for use. As Introspection is disabled outside developer mode.

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
