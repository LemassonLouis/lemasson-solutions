# Cheat sheet (Sulu 2.6)

## Vider le cache
```Bash
docker compose exec php bin/console cache:clear
```



## Afficher la version de Sulu
```Bash
docker compose exec php composer show sulu/sulu
```



## Initialiser un webspace

```Bash
docker compose exec php bin/console sulu:webspace:init {webspace key}
```



## Initialiser une localisation/traduction

```Bash
docker compose php bin/adminconsole sulu:document:initialize
```
