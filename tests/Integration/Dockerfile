ARG KIMAI_IMAGE
FROM ${KIMAI_IMAGE}

COPY . /opt/kimai/var/plugins/CustomerPortalBundle
COPY tests/Integration/seed-portal-range-fixtures.php /fixtures/seed.php
RUN chown -R www-data:www-data /opt/kimai/var/plugins/CustomerPortalBundle /fixtures
