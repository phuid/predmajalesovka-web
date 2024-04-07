## todo

- round filtering on index
- admin remove hint
- admin remove round

- create round mkdir fail but status 200


## check

- is it possible to read gps coordinates from round hint photo metadata?

## db

```sql
CREATE DATABASE predmajalesova_hra;
USE predmajalesova_hra;

CREATE TABLE emails (
    id int NOT NULL AUTO_INCREMENT,
    email varchar(255) NOT NULL,
    category int NOT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE teams (
    id int NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    password varchar(255) NOT NULL,
    category int NOT NULL,
    PRIMARY KEY (id)
);

-- https://www.random.org/strings/?num=12&len=10&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new
INSERT INTO teams (name, password, category) VALUES
('admin', 'xxxxxxxxxx', 0),
('primaA', 'xxxxxxxxxx', 1),
('primaB', 'xxxxxxxxxx', 1),
('sekundaA', 'xxxxxxxxxx', 1),
('sekundaB', 'xxxxxxxxxx', 1),
('tercieA', 'xxxxxxxxxx', 1),
('tercieB', 'xxxxxxxxxx', 1),
('kvartaA', 'xxxxxxxxxx', 2),
('kvartaB', 'xxxxxxxxxx', 2),
('kvintaA', 'xxxxxxxxxx', 2),
('kvintaB', 'xxxxxxxxxx', 2),
('sextaA', 'xxxxxxxxxx', 2),
('sextaB', 'xxxxxxxxxx', 2),
('septimaA', 'xxxxxxxxxx', 3),
('septimaB', 'xxxxxxxxxx', 3),
('oktavaA', 'xxxxxxxxxx', 3),
('oktavaB', 'xxxxxxxxxx', 3);


CREATE TABLE proofs (
    id int NOT NULL AUTO_INCREMENT,
    round_id int NOT NULL,
    team_id int NOT NULL,
    time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    img_url varchar(255) NOT NULL,
    verified boolean,
    deleted boolean DEFAULT false,
    PRIMARY KEY (id)
);

CREATE TABLE rounds (
    id int NOT NULL AUTO_INCREMENT,
    nickname varchar(255) NOT NULL,
    category int NOT NULL,
    start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP DEFAULT '2024-05-01 00:00:00',
    hint_folder varchar(255) NOT NULL,
    PRIMARY KEY (id)
);
```

## vision

### users

- email subscribe form
- google calendar style feed

#### nahravani

- select team - login
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
