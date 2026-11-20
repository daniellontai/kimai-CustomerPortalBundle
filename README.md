# Customer Portal - Kimai Plugin 

You can share your customer and project report (budgets, timesheets) with anyone by a public URL and optional password.

Features, documentation and installation instructions at:   
https://www.kimai.org/store/customer-portal.html

## Changelog

All changes and compatible versions can be found in the [CHANGELOG](CHANGELOG.md).

## Upgrade

Back up the Kimai database before deploying an updated bundle. After replacing
the plugin directory, run the plugin migration inside the Kimai PHP container:

```bash
docker compose exec kimai /opt/kimai/bin/console kimai:bundle:customer-portal:install
docker compose exec kimai /opt/kimai/bin/console kimai:reload --env=prod
```

Existing portals keep their month/year navigation until an administrator sets
a start or end date. Frozen ranges require both dates and cannot be changed by
editing portal URLs.

## Why this fork?

- The original author has abandoned the plugin and is not responding
- It was forked to support Kimai > 2.0
- Many new features were added

Credits go out to [Fabian Vetter](https://vettersolutions.de/i), who was the [original author](https://github.com/dexterity42/SharedProjectTimesheetsBundle) of this plugin.
