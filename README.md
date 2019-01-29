
# Telegram-MFCoinCourse
Исходники бота для Telegram с курсом MFCoin.
Данные берутся с [CoinlibAPI](https://coinlib.io/apidocs).

## Установка и настройка
1. Создайте нового бота в BotFather;
2. Установите webhook для бота, выполнив запрос:
```
https://api.telegram.org/bot%token%/setWebhook?url=%webhook%
```
Где %token% - ваш токен доступа, %webhook% - url скрипта, который будет перехватывать запросы к боту.

3. Переименуйте config_example.php в config.php;
4. Внести в файл настроек свои данные - токен, ссылку на бота, CoinlibAPI ключ;
