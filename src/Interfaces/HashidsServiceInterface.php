<?php declare(strict_types=1);

namespace VipinBose\HashidsBundle\Interfaces;

interface HashidsServiceInterface
{
	public function encode(mixed ...$numbers): string;
	public function decode(string $hash): array;
	public function encodeHex(string $str): string;
	public function decodeHex(string $hash): string;
}
