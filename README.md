[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/lezhnev74/ema/master/LICENSE)
[![Build Status](https://travis-ci.org/lezhnev74/ema.svg?branch=master)](https://travis-ci.org/lezhnev74/ema)
[![Coverage Status](https://coveralls.io/repos/github/lezhnev74/ema/badge.svg?branch=master)](https://coveralls.io/github/lezhnev74/ema?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lezhnev74/ema/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lezhnev74/ema/?branch=master)

# Overview
External memory app - allows one to quickly post and search text notes

* Blog post: -
* Demo: https://ema.lessthan12ms.com
* Client repo: https://github.com/lezhnev74/ema-web-client

## Why?


* I needed something better than a todo app to practice Clean Architecture inspired by Uncle Bob's talks
* Evernote, disk files (like Quiver app) and other tools disappointed me. I needed a light, fast post-n-search app for storing code snippets and linux commands. 
* Secondly I needed a simple app to play with the DDD concepts along with Service Bus oriented architecture. All these things found its places in this project.  

  
## Installation

```
#requires php7.1-apcu module (for DI-container caching), see function `container()` in helpers file
cd <project root>
# sqlite database will be located in there
mkdir storage 
cp .env.example .env
# then edit .env file
composer update
# then run database migrations
php migrations/doctrine.php migrations:migrate
```










