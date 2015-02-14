# Laboration 2
Kurs: Webbteknik II, 1DV449  
Ulrika Falk, uf222ba  
Körbar version finns på: [http://www.falkebo.com/1DV449/Lab2/index.php](http://www.falkebo.com/1DV449/Lab2/index.php)

### Del 1 - Säkerhetsproblem
##### 1. Session Hijacking
Åtgärder: Använd SSL eller TLS (dock ej tillämpbart i detta fall).  
Spara IP-adressen och webbläsaren i sessionsvariabler vid inloggningen och jämför användarens mot sessionsvariablerna när sidorna hämtas. Ta bort sessionsvariabler och cookies vid utloggning.
##### 2. XSS - Cross Site Scripting
Åtgärd: En funktion som tvättar all text som är inmatad av användarna.
##### 3. CSRF - Cross Site Request Forgery
Åtgärd: Har implementerat "Synchronizer Token Pattern". Se referens.
##### 4. SQL Injections
Åtgärder: Parametriserade frågor och tvättning av användarinmatade strängar.
##### 5. Inloggning
Lösenord sparas i klartext i databasen.  
Åtgärd: Hasha lösenorden. Använder funktionen password_hash() för ändamålet.  
Funktionen isUser returnerar alltid en sträng bara några värden skrivs in i användarnamn- och lösenordstextfälten. Ingen verifiering mot tabellen i databasen där användardnamn och lösenord finns, genomförs.  
Åtgärd: Verifiering av användarnamn och lösenord mot databasen. Funktionen returnerar false om uppgifterna är felaktiga eller inte finns i databasen.  
Ingen säkerhet i applikationen. Det går exampelvis bra att gå direkt till mess.php och titta på innehållet även om man inte är inloggad.  
Åtgärd: Anropa en funktion i början av varje fil som kontrollerar att användaren är inloggad.

##### Referenser
Kod för att ta bort sessionen: [http://php.net/manual/en/function.session-destroy.php](http://php.net/manual/en/function.session-destroy.php)  
Koden i följande video har använts för att implementera "Synchronizer Token Pattern": [https://www.youtube.com/watch?v=VflbINBabc4](https://www.youtube.com/watch?v=VflbINBabc4 )  
OWASP Testing Guide 4.0 [https://www.owasp.org/images/5/52/OWASP_Testing_Guide_v4.pdf](https://www.owasp.org/images/5/52/OWASP_Testing_Guide_v4.pdf)  
W3Schoools - [http://www.w3schools.com/](http://www.w3schools.com/)  
PHP manualen - [http://php.net/manual/en/](http://php.net/manual/en/)

### Del 2 - Optimering
Den optimering som gjorts är att ta bort länkar till icke existerande filer, slå ihop kod och minifiera den. 
Mätningarna är gjorda i Chrome mot www.falkebo.com som hostas av one.com. Mätningarna har gjorts mot mess.php. Fem stycken mätningar för de tre olika situationerna och i tabellerna nedan ses de beräknade genomsnittsvärdena för laddningstiderna. Gzip används för komprimering och det kan sannolikt inte ändras, såvida test inte sker på annat webbhotell, vilket inte gjorts.

Intressant att den sista optimeringen med CDN för jquery-filen inte "lönade" sig. Det är dock så små datamängder att resultaten inte kan anses vara så tillförlitliga. Det hade sannolikt gjort skillnad om det varit stora datamängder där CDN använts.
##### Före någon form av optimering
| Requests |Transferred (KB)|Total time (ms)|
|:---------:|---------------:|--------------:|
|     12    |            315 |         463,8 |

##### Efter optimering, minifiering och ihopslagning av js-filer
| Requests |Transferred (KB)|Total time (ms)|
|:---------:|---------------:|--------------:|
|     9     |            253 |         368,6 |

##### Vid användning av CDN för jquery-filen
| Requests |Transferred (KB)|Total time (ms)|
|:---------:|---------------:|--------------:|
|      9    |            250 |         381,2 |

##### Referenser
För minifiering av JavaScript har följande verktyg använts: [http://jscompress.com/](http://jscompress.com/)  
Kul ställe där kan kan testa prestandan: [http://gtmetrix.com](http://gtmetrix.com)  
For the love of SEO [http://fortheloveofseo.com/blog/performance/leverage-browser-caching-how-to-add-expires-headers/](http://fortheloveofseo.com/blog/performance/leverage-browser-caching-how-to-add-expires-headers/)  
Caching Tutorial: [https://www.mnot.net/cache_docs/](https://www.mnot.net/cache_docs/)  
Boken [High Performance Web Sites Essential Knowledge for Front-End Engineers](http://shop.oreilly.com/product/9780596529307.do) skriven av Steve Souders

### Del 3 - Longpolling
Http-protokollet är ju stateless, vilket innebär att "riktiga" realtidsapplikationer är omöjliga att implementera. Därav det nya protokollet websocket i HTML5. Poängen med longpolling är att skapa en illusion av att datat uppdateras i realtid. En longpollinglösning består av två delar. Frontend och backend. Det går ut på att det görs en request från klienten, servern svarar inte direkt (såvida den inte har ny data), utan den väntar tills den har ny data eller tiden gått ut.  När klienten fått någon av svaren så upprepas proceduren och en ny request skickas till serversidan.  
  
Den lösning som implementerats i denna laboration berör filerna MessageBoard.js (frontend), functions.php och get.php (backend). 
De viktigaste förändringarna som gjordes på klientsidan var att i MessageBoard.getMessages() sätta ett värde för timeout och använda funktionen always för att alltid anropa MessageBoard.getMessages() igen. Det är det som initierar http-förfrågningarna. För att söka efter nya meddelanden, modifierades funktionen getMessages på serversidan att ta det högsta id:t av de redan hämtade meddelandena. En while-loop har lagts till som pollar databasen efter ny data. Loopen körs tills ny data hittats eller maxtiden är uppnådd. Efter att varje databasfråga har körts får loopen "vila" i en sekund, för inte belasta SQL-servern med alldeles för många förfrågningar.

##### Referenser longpolling
[http://webcooker.net/ajax-polling-requests-php-jquery/](http://webcooker.net/ajax-polling-requests-php-jquery/)  
[http://techoctave.com/c7/posts/60-simple-long-polling-example-with-javascript-and-jquery](http://techoctave.com/c7/posts/60-simple-long-polling-example-with-javascript-and-jquery)  
[http://codertalks.com/long-polling-implementation/](http://codertalks.com/long-polling-implementation/)  
[http://lxcblog.com/2010/10/17/jquery-long-polling-ajax-php-chat-messaging-example/](http://lxcblog.com/2010/10/17/jquery-long-polling-ajax-php-chat-messaging-example/)  
Film - [https://www.screenr.com/SNH](https://www.screenr.com/SNH)  
jQuery AJAX example - [http://tutorials.jenkov.com/jquery/ajax.html](http://tutorials.jenkov.com/jquery/ajax.html)  
jQuery API documentation - [http://api.jquery.com/](http://api.jquery.com/)

