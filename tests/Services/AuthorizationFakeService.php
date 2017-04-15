<?php
declare(strict_types=1);

namespace EMA\Tests\Services;

use Prooph\ServiceBus\Plugin\Guard\AuthorizationService;

final class AuthorizationFakeService implements AuthorizationService
{
    private $is_granted = true;
    
    /**
     * AuthorizationFakeService constructor.
     *
     * @param bool $is_granted
     */
    public function __construct(bool $is_granted) { $this->is_granted = $is_granted; }
    
    
    public function isGranted(string $messageName, $context = null): bool
    {
        return $this->is_granted;
    }
    
}