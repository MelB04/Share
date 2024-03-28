# A faire : 
- composer install
- Copier .env et créer .env.local
- Réaliser une migration vers BDD

# .env.local
- Il faut changer les lignes suivantes : 

```
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
DATABASE_URL="mysql://loginXXXX:mdp@127.0.0.1:3306/nomDB?serverVersion=10.3.31-MariaDB&charset=utf8mb4"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
###< doctrine/doctrine-bundle ###
```

- Ne pas oublier de copier ces lignes dans du .env vers le .env.local pour ceux qui n'avaient pas réalisé l'API Plateform avant de créer le .env.local
```
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/xxxxxxxxxxxxx
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/xxxxxxxxxxxxx
JWT_PASSPHRASE=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
###< lexik/jwt-authentication-bundle ### 
```
