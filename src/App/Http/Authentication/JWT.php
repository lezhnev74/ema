<?php
declare(strict_types=1);

namespace EMA\App\Http\Authentication;

use Carbon\Carbon;
use EMA\Domain\Foundation\VO\Identity;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\BaseSigner;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

final class JWT
{
    public function parseToken(string $token): Identity
    {
        try {
            $token = (new Parser())->parse((string)$token);
        } catch (\Exception $e) {
            throw new BadToken("Invalid JWT format");
        }
        
        $data = new ValidationData();
        $data->setIssuer('ema.app.test');
        $data->setAudience('anyone');
        $data->setCurrentTime(Carbon::now()->timestamp);
        
        //dump($data);
        //dump($token);
        
        if (!$token->validate($data)) {
            throw new BadToken("Invalid token");
        }
        
        if (!$token->verify($this->getSigner(), config('app.key'))) {
            throw new BadToken("Invalid signature");
        }
        
        if (!$token->hasClaim('sub')) {
            throw new BadToken("No subject in the token");
        }
        
        return new Identity($token->getClaim('sub'));
    }
    
    public function makeToken(Identity $id): string
    {
        $token = (new Builder())
            ->setIssuer('ema.app.test')// Configures the issuer (iss claim)
            ->setAudience('anyone')// Configures the audience (aud claim)
            ->set('sub', $id->getAsString())
            ->setIssuedAt(Carbon::now()->timestamp)// Configures the time that the token was issue (iat claim)
            ->setNotBefore(Carbon::now()->timestamp)// Configures the time that the token can be used (nbf claim)
            ->setExpiration(Carbon::now()->addDays(30)->timestamp)// Configures the expiration time of the token (nbf claim)
            ->sign($this->getSigner(), config('app.key'))// creates a signature using "testing" as key
            ->getToken(); // Retrieves the generated token
        
        return (string)$token;
    }
    
    private function getSigner(): BaseSigner
    {
        return new Sha256();
    }
}