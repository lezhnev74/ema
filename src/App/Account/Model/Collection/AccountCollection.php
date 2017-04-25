<?php
declare(strict_types=1);


namespace EMA\App\Account\Model\Collection;


use EMA\App\Account\Model\Account\Account;

interface AccountCollection
{
    public function save(Account $model): void;
}