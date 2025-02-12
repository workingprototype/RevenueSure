<?php
function getBaseCurrency(){
    return "USD";
}
/**
 * Fetches the exchange rate for a given currency from the database.
 *
 * @param string $currencyCode The currency code (e.g., USD, EUR).
 * @param PDO    $conn         The database connection object.
 *
 * @return float The exchange rate for the currency, or 1.0 if not found.
 */
function getExchangeRate(string $currencyCode, PDO $conn): float {
    $stmt = $conn->prepare("SELECT exchange_rate FROM currency_rates WHERE currency_code = :currency_code");
    $stmt->bindParam(':currency_code', $currencyCode);
    $stmt->execute();
    $rate = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($rate && isset($rate['exchange_rate'])) ? (float)$rate['exchange_rate'] : 1.0; // Default to 1.0 for base currency
}

function convertCurrency(float $amount, string $fromCurrency, string $toCurrency, PDO $conn): float {
    if ($fromCurrency === $toCurrency) {
        return $amount;
    }

    $fromRate = getExchangeRate($fromCurrency, $conn);
    $toRate = getExchangeRate($toCurrency, $conn);

    return round(($amount / $fromRate) * $toRate, 2);
}
?>