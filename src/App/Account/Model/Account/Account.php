<?php
declare(strict_types=1);

namespace EMA\App\Account\Model\Account;

use Assert\Assert;
use EMA\Domain\Foundation\VO\Identity;

final class Account
{
    /** @var  Identity */
    private $id;
    /** @var  string */
    private $social_provider_name;
    /** @var  string */
    private $social_provider_id;
    
    /**
     * Account constructor.
     *
     * @param Identity $id
     * @param string   $social_provider_name
     * @param string   $social_provider_id
     */
    public function __construct(Identity $id, $social_provider_name, $social_provider_id)
    {
        $this->id                   = $id;
        $this->social_provider_name = $social_provider_name;
        $this->social_provider_id   = $social_provider_id;
        
        Assert::that($social_provider_name)->inArray(['google']);
        Assert::that($social_provider_id)->string()->minLength(1);
    }
    
    /**
     * @return Identity
     */
    public function getId(): Identity
    {
        return $this->id;
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