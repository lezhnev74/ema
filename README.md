# Overview
External memory app - allows one to quickly post and search text notes

## Why?

* Evernote, disk files and other tools dissappointed me. I needed a light, fast post-n-search app for storing code snippets and linux commands.
 
* Secondly I needed a simple app to play with the DDD concepts along with Service Bus oriented architecture. All these things found its places in this project.  
 
## Usage
 
The app is intended to be accessible on x.lessthan12ms.com. X subdomain was chosen for quick and unambiguous access through chrome address bar.

## Keyboard control

After opening the app page, one have few key options:
 * `s` - focus on **s**earch panel 
 * `a` - **a**dd new note
 
## Installation
First of all configure your database connection within `config/database.php`
Then fill the database up:
```
cd <project root>
php migrations/doctrine.php migrations:migrate
```

This app is supposed to work with databases that support FullText search. For example, MariaDB.
You need to explicitly set a filed `notes.note_text` as `FULLTEXT(note_text)` before using the app.









