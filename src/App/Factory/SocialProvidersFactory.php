<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Assert\InvalidArgumentException;
use Doctrine\Common\Collections\Collection;
use EMA\App\Http\Authentication\AuthenticationMiddleware;
use EMA\App\Note\Query\AllNotes\AllNotes;
use EMA\App\Note\Query\SearchNotes\SearchNotes;
use EMA\Domain\Foundation\Exception\DomainProblem;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNote;
use EMA\Domain\Note\Commands\ModifyNote\ModifyNote;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Model\VO\NoteText;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

final class SocialProvidersFactory
{
    
    public function google(ContainerInterface $container): \Google_Client
    {
        $client = new \Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        
        return $client;
    }
}