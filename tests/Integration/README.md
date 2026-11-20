# Disposable integration test

The test stack uses only a fresh MySQL volume and synthetic fixture data. It
does not read, connect to, or import production data.

Run it with:

```bash
docker compose -f tests/Integration/docker-compose.yml -p customer-portal-range-test up --build --abort-on-container-exit --exit-code-from verifier
```

For manual browser checks, while the stack is running open
`http://localhost:8001`. The port binds to loopback only; it is not exposed
to the network. The seeded admin login is `admin@kimai.test` with password
`integration-test-password`.

Remove only its scoped containers, network, and volume with:

```bash
docker compose -f tests/Integration/docker-compose.yml -p customer-portal-range-test down -v
```
