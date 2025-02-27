Template LAMP base per codespaces di github, piattaforma che facilita la creazione di un container di tipo https://www.docker.com/, orientato allo sviluppo e connesso a uno o più repository gitub

Contiene anche un esempio embrionale di autenticazione in PHP

GUIDA per i primi passi. ATTENZIONE la versione PDF non è piu aggiornata e viene mantenuta solo per le illustrazioni.

1) Creazione codespace a partire dal "template”

Create il vostro account Github www.github.com e loggate.

Collegatevi a https://github.com/profPane/lampbase

Premete il pulsante verde “Use this template” in alto da destra

Scegliete  “Open in a Codespaces” 

Si avvierà la versione WEB di VSCode e comincerà a costruire il vostro “codespace”, in basso a destra ci sarà una barra di progresso.

Finita la creazione del codespace avrete l’ambiente VSCode attivo dentro al browser con tutti i files del progetto.

Avviate il Server Web Apache dal terminale linux in basso a destra, potete sempre aprire un terminale dal menu in alto a destra, le tre linee sovrapposte, Terminale -> Nuovo Terminale

Nel terminale date il comando: 

apachectl start

Viene avviato il servizio server web e, dopo qualche secondo, si aprirà una finestra che vi avviserà che il server è attivo e che potete aprire una pagina web per visitarlo, cliccate “Open in Browser”

Se per caso non dovesse apparire o lo chiudete voi potete sempre aprirlo andando sulla linguetta porte, nella riga della porta 80, passando col mouse sull’indirizzo (https:/….) vi apparirà un simbolo di “mondo” da cliccare

Il simbolo accanto al mondo vi permette di aprire un browser dentro VSCode che condivide lo spazio al centro con la finestra del sorgente, senza browser esterno.

Se tutto è andato liscio dovreste star visitando la pagina index.hmtl inclusa nel template, è un rozzo menu e il link più interessante è "registrati".

Verrà visualizzata la pagina "registra_utente" che permetterà di registrarsi SOLO come ospite, infatti l'apposito selettore sarà bloccato su "ospite"
Se avete prima fatto login con un account amministratore (user:admin, pass:admin2025) potrete creare utenti con accessi privilegiati.
L'accesso privilegiato viene salvato nel database con un numero da 0 a 9 (livello), il meccanismo serve per poter implementare un controllo di accesso secondo il livello per le pagine nelle quali è necessario.

OVVIAMENTE ancora nulla funziona perchè non avete creato il DB usato dal codice PHP dell'esempio


2) Ripristino database di esempio

Nel template è incluso un esempio base di connessione al DB con PHP per la realizzazione di pagine dinamiche.

Se volete provarlo dovete ripristinare il DB dell'applicazione, nel file db/database.php trovate le credenziali di accesso al motore DB

Dal terminale date il comando: 

mysql -u root -p -h 127.0.0.1

usate la password “mariadb”, si avvia la console del database, è fondamentale distinguere tra il terminale (linux) e la console (DB) 

La console del database vi permette di eseguire le query e sarebbe già sufficiente per provare la validità delle vostre query 

Dalla console DB create il database “esempio”: 

create database esempio;

Verrà creato il database VUOTO, per usarlo dovete avvertire il motore database con il comando: 

use esempio; 

da questo momento in poi tutte le query dalla console del database verranno effettuate in quel database, alternativamente potete far precedere ai nomi delle tabelle quelle del DB, esempio SELECT * FROM iscritti; diventa SELECT * FRM esempio.iscritti;

Per uscire dal motore DB usate il comando: 

quit;

Se volete provare l'esempio di autenticazione ripristinate il backup dalle TABELLE terminale, ATTENZIONE questo elimina ogni eventuale tabella preesistente:

mysql -p -u root esempio -h 127.0.0.1 < db/dump_database/esempio.sql

In qualsiasi momento potete fare un backup con il comando:

mysqldump -u root -p -h 127.0.0.1 esempio > db/dump_database/backup_nome_scelto.sql

attenzione al percorso del file, l’esempio vale se vi trovate con il terminale nella “root” del sito.


3) Gestore grafico del server database

Se volete gestire il DB in modalità grafica dentro VSCode potete installare l’estensione di VSCode: 

https://marketplace.visualstudio.com/items?itemName=cweijan.vscode-mysql-client2

Cercando "mysql" nella sezione Estensioni di VSCode, troverete diverse scelte, io preferisco quella di Weijan Chen che aggiunge icone nella barra per un acceso rapido al DB

Nella colonna a SX vi apparirà un simbolo Cilindrico, l’icona standard dei DB, cliccando potete creare una connessione al vostro DB, con i seguenti parametri: 
Connessione: mioDB, Host: 127.0.0.1, Nome (utente): root, Password: mariadb, Porta: 3306

Alternativamente potete installare phpMyAdmin:
sudo apt install phpmyadmin


4) Pubblicare il vostro codice in un repository (deposito del codice) di GitHub

Oltre a tenerlo sulla macchina virtuale di codespaces potete salvare il vostro progetto su GitHub, cliccando su “Controllo del codice” cioè l’icona con i pallini collegati da linee.

Vi verrà chiesto un “Messaggio” sintetico che descrive i cambiamenti fatti nel codice in questa sessione di programmazione, es: “sistemato errore di accesso al database”.

La prima volta vi verrà chiesto se volete che VSCode si occupi di preparare il codice, rispondete “sempre”  

Sempre la prima volta vi viene chiesto se volete creare un nuovo "BRANCH" cioè un repository su Github.
Si aprirà un browser per loggare su github se non siete già loggati con un pulsante per collegare GitHub al vostro codespace
Poi vi verrà chiesto se volete rendere pubblico o privato il vostro deposito ed un nome, alla fine premete i pulsati Commit ed Sincronizza che appariranno
Da questo momento in poi

Da adesso in poi, ogni volta volta che fate un buon numero di modifiche, potete sincronizzarle con il vostro "deposito" github, creando un nuovo commit. 

Buon lavoro