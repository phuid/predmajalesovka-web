## todo
- all pages
- admin actions on pages
- store password on prihlaseni page to cookie and just check it when they try to do something

## db

```sql
CREATE TABLE emails (
    id int NOT NULL,
    email varchar(255) NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE teams (
    id int NOT NULL,
    name varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE proofs (
    id int NOT NULL,
    round_id int NOT NULL,
    team_id int NOT NULL,
    time TIMESTAMP NOT NULL,
    img_url varchar(255) NOT NULL,
    verified boolean,
    PRIMARY KEY (id)
);
CREATE TABLE rounds (
    id int NOT NULL,
    nickname varchar(255) NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP DEFAULT '2024-05-01 00:00:00',
    hint_folder varchar(255) NOT NULL,
    PRIMARY KEY (id)
);
```

## vision

### users

- header - 3D BLUE RED EFFECT Viz https://developer.mozilla.org/en-US/docs/Web/CSS/text-shadow
- email subscribe form
- google calendar style feed

#### nahravani

- select class - login
- check, jestli uz neni nahrany
  - ? "tvoje trida uz dukaz pro toto kolo nahrala, pokud nahrajete nový obrázek, započítá se vám čas nahrání nového obrázku"
  - : "nahrajte obrázek, běží vám čas; čas od začátku kola: hh:mm:ss"
- form na nahrani obrazku
- vysledkyyyy

### admins

- when a new image is inserted an email is sent to everyone who registered for them (users can do that)
  - predmet: "predmajalesovka"
  - obsah:
    - "byl nahrán nový obrázek do předmajálesové hry ke stanovišti \<\> \<nickname\>"
    - obrazek as remote content (\<img src=""\>)
    - in that email must be an unfollow link

https://github.com/vielhuber/simpleauth
