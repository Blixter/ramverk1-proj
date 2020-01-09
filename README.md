# ramverk1-proj

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Blixter/ramverk1-proj/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Blixter/ramverk1-proj/?branch=master)
[![Build Status](https://travis-ci.org/Blixter/ramverk1-proj.svg?branch=master)](https://travis-ci.org/Blixter/ramverk1-proj)

This is my take on Stackoverflow. On this site the user can ask questions, which can be answered by others users. All questions and answers can be commented and voted. The user can mark an answer as an accepted answer.

## Install your own copy

Clone the repo with the following command:

```
git clone git@github.com:Blixter/ramverk1-proj.git
```

## Install and update packages

Start with updating composer packages:

```
composer update
```

Get all the tools required by the framework:

```
make install
```

## Database

This project is using SQLite3, in order to set up the database run the bash file `init-database.bash` with the following command:

```
bash init-database.bash
```

## License

This software carries a MIT license. See [LICENSE.txt](LICENSE.txt) for details.

```
 .
..:  Copyright (c) 2020 Robin Blixter (r.blixter89@gmail.com)
```
