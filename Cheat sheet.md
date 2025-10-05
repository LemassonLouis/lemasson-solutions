# Cheat sheet (Sulu 2.6)

## Afficher la version de Sulu
```Bash
docker compose exec php composer show sulu/sulu
```



## Vider le cache
```Bash
# Pour l'admin
docker compose exec php bin/adminconsole cache:clear

# Pour le site public
docker compose exec php bin/websiteconsole cache:clear

# Console standard Symfony
docker compose exec php bin/console cache:clear
```



## Initialiser un webspace

1. Créer un nouveau fichier XML de webspace dans `config/webspaces`, veiller à ce que le nom du fichier soit le même que ce qui est dans la balise `<key>`
2. Vider le cache
    ```Bash
    docker compose exec php bin/console cache:clear
    ```
3. Initialiser le webspace
    ```Bash
    docker compose exec php bin/adminconsole sulu:document:initialize
    ```
4. Dans le menu, aller dans Réglages > Rôles et utilisateurs, modifier le rôle User, dans la séléction des webspaces choisir le webspace créer et lui appliquer toutes les permissions.
5. Rafraichir le navigateur en vidant le cache au besoin _(Ctrl + Maj + R)_
