## todo
- all pages + email unsub
- admin actions on pages
- store password on prihlaseni page to cookie and just check it when they try to do something

## db

- zadani-imgs
  - kolo_id (number not null)
  - url
- zadani-kola
  - kolo_id (number not null)
  - name
  - start-time
- tridy-casy
  - kolo_id (number not null)
  - trida
  - cas
  - img_url
  - verified (bool)
- tridy
  - password (string)
- emails
  - email (string)

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
