<?php
declare(strict_types=1);

namespace EMA\App\Account\Command\AddAccount;

use EMA\Domain\Foundation\Command\Command;

class AddAccount implements Command
{
    /** @var  string */
    private $social_provider_name;
    /** @var  string */
    private $social_provider_id;
    
    /**
     * AddAccount constructor.
     *
     * @param string $social_provider_name
     * @param string $social_provider_id
     */
    public function __construct($social_provider_name, $social_provider_id)
    {
        $this->social_provider_name = $social_provider_name;
        $this->social_provider_id   = $social_provider_id;
    }
    
    /**
     * @return string
     */
    public function getSocialProviderName(): string
    {
        return $this->social_provider_name;
    }
    
    /**
     * @return string
     */
    public function getSocialProviderId(): string
    {
        return $this->social_provider_id;
    }
    
    
}
