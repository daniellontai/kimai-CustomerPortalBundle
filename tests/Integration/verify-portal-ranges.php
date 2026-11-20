<?php

declare(strict_types=1);

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

/**
 * Black-box acceptance checks for the disposable Customer Portal stack.
 *
 * The fixture creates the named entries below. These literal assertions catch
 * an accidental range widening, a broken inclusive boundary, or exposing a
 * frozen portal through query-string tampering.
 */
function environment(string $name): string
{
    $value = getenv($name);
    if (!is_string($value) || $value === '') {
        throw new RuntimeException(sprintf('Missing required environment variable: %s', $name));
    }

    return $value;
}

function get(string $url): string
{
    $context = stream_context_create([
        'http' => [
            'header' => "Host: kimai.test\r\n",
            'ignore_errors' => true,
            'timeout' => 20,
        ],
    ]);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        throw new RuntimeException(sprintf('Request failed: %s', $url));
    }

    return $response;
}

function expectContains(string $needle, string $response): void
{
    if (!str_contains($response, $needle)) {
        throw new RuntimeException(sprintf('Expected response to contain "%s"', $needle));
    }
}

function expectNotContains(string $needle, string $response): void
{
    if (str_contains($response, $needle)) {
        throw new RuntimeException(sprintf('Expected response not to contain "%s"', $needle));
    }
}

function expectSame(string $expected, string $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message);
    }
}

$baseUrl = rtrim(environment('KIMAI_URL'), '/') . '/en';
$batchA = $baseUrl . '/auth/customer-portal/' . environment('PORTAL_BATCH_A_KEY');
$batchB = $baseUrl . '/auth/customer-portal/' . environment('PORTAL_BATCH_B_KEY');
$editable = $baseUrl . '/auth/customer-portal/' . environment('PORTAL_EDITABLE_KEY');

$batchAResponse = get($batchA);
expectContains('A first boundary', $batchAResponse);
expectContains('A last boundary', $batchAResponse);
expectNotContains('Before A', $batchAResponse);
expectNotContains('After A', $batchAResponse);
expectNotContains('B first boundary', $batchAResponse);
expectNotContains('B last boundary', $batchAResponse);

$batchBResponse = get($batchB);
expectContains('B first boundary', $batchBResponse);
expectContains('B last boundary', $batchBResponse);
expectNotContains('A first boundary', $batchBResponse);
expectNotContains('A last boundary', $batchBResponse);

expectSame(
    $batchAResponse,
    get($batchA . '?from=2020-01-01&to=2030-12-31&year=2030&month=12'),
    'Frozen portal response changed after range URL tampering'
);

$editableResponse = get($editable . '?from=2026-09-16&to=2026-10-09');
expectContains('B first boundary', $editableResponse);
expectContains('B last boundary', $editableResponse);
expectNotContains('A first boundary', $editableResponse);

$editableBoundaryResponse = get($editable . '?from=2026-08-11&to=2026-10-10');
expectContains('value="2026-10-10"', $editableBoundaryResponse);

fwrite(STDOUT, "Customer Portal date-range acceptance checks passed\n");
