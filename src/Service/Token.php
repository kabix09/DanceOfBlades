<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

class Token
{
    private string $token;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(string $token='', LoggerInterface $tokenLogger)
    {
        $this->token = $token;
        $this->logger = $tokenLogger;
    }

    public function generate(string $key): self
    {
        try {
            $this->token = sodium_crypto_generichash(
                (new \DateTime('now'))->getTimestamp() . substr(str_shuffle("1234567890abcdefghijklmnoprstuwxyz()/$"), 32),
                $key
            );
        } catch (\SodiumException $e) {
            $this->logger->alert('Sodium token generate fail');
        }

        return $this;
    }

    public function convertToHex(): self
    {
        try {
            $this->token = sodium_bin2hex($this->token);
        } catch (\SodiumException $e) {
            $this->logger->alert('Sodium token convert to hex fail');
        }

        return $this;
    }

    public function convertToBin(): self
    {
        try {
            $this->token = sodium_hex2bin($this->token);
        } catch (\SodiumException $e) {
            $this->logger->alert('Sodium token convert to bin fail');
        }

        return $this;
    }

    public function getValue(): string
    {
        return $this->token;
    }

}