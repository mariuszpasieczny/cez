Instrukcja instalacji
1. Wgrać zawartość paczki na serwer WWW; 
ze względów bezpieczeństwa w katalogu dostępnym publicznie (zależy od konfiguracji serwera ale zwykle jest to katalog public_html) powinna być dostępna jedynie zawartość katalogu public z paczki, pozostałe katalogi powinny znajdować się "równolegle" do katalogu publicznego
2. w pliku /public/_htaccess ustawić parametr APPLICATION_ENV na wartość production 
3. skonfigurować połączenie do bazy danych (w pliku /application/config/application.ini - parametry :
resources.db.params.host 
resources.db.params.dbname 
resources.db.params.username 
resources.db.params.password 
w sekcji [production] (linie 199 - 203)
4. zaimportować do bazy danych pliki (zachowując kolejność):
esoz tables.sql (podstawowa struktura tabel)
esoz views.sql (dodatkowe widoki)
esoz data.sql (podstawowe dane - podstawowy użytkownik administracyjny aplikacji, podstawowe dane słownikowe niezbędne do działania aplikacji)
5. zalogować się do aplikacji na użytkownika admin@localhost.pl z pustym hasłem i ustawić hasło dla administratora.