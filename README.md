# REST API (php SQLite)
Simple php project about currencies and exchanges
## Задание
Третий проект из RoadMap Сергея Жукова (Спасибо большое за тз):
https://zhukovsd.github.io/java-backend-learning-course/projects/currency-exchange/

Скопировано из источника:

REST API #
Методы REST API реализуют CRUD интерфейс над базой данных - позволяют создавать (C - create), читать (R - read), редактировать (U - update). В целях упрощения, опустим удаление (D - delete).

Валюты #

GET /currencies #

Получение списка валют. Пример ответа:

```json
[
  {
    "id": 0,
    "name": "United States dollar",
    "code": "USD",
    "sign": "$"
  },
  {
    "id": 0,
    "name": "Euro",
    "code": "EUR",
    "sign": "€"
  }
]
```

HTTP коды ответов:

Успех - 200
Ошибка (например, база данных недоступна) - 500
GET /currency/EUR #
Получение конкретной валюты. Пример ответа:

```json
{
  "id": 0,
  "name": "Euro",
  "code": "EUR",
  "sign": "€"
}
```


HTTP коды ответов:

Успех - 200
Код валюты отсутствует в адресе - 400
Валюта не найдена - 404
Ошибка (например, база данных недоступна) - 500

POST /currencies #

Добавление новой валюты в базу. Данные передаются в теле запроса в виде полей формы (x-www-form-urlencoded). Поля формы - name, code, sign. Пример ответа - JSON представление вставленной в базу записи, включая её ID:

```json
{
  "id": 0,
  "name": "Euro",
  "code": "EUR",
  "sign": "€"
}
```

HTTP коды ответов:

Успех - 201
Отсутствует нужное поле формы - 400
Валюта с таким кодом уже существует - 409
Ошибка (например, база данных недоступна) - 500

Обменные курсы #

GET /exchangeRates #

Получение списка всех обменных курсов. Пример ответа:

```json
[
  {
    "id": 0,
    "baseCurrency": {
      "id": 0,
      "name": "United States dollar",
      "code": "USD",
      "sign": "$"
    },
    "targetCurrency": {
      "id": 1,
      "name": "Euro",
      "code": "EUR",
      "sign": "€"
    },
    "rate": 0.99
  }
]
```

HTTP коды ответов:

Успех - 200
Ошибка (например, база данных недоступна) - 500

GET /exchangeRate/USDRUB #

Получение конкретного обменного курса. Валютная пара задаётся идущими подряд кодами валют в адресе запроса. Пример ответа:

```json
{
  "id": 0,
  "baseCurrency": {
    "id": 0,
    "name": "United States dollar",
    "code": "USD",
    "sign": "$"
  },
  "targetCurrency": {
    "id": 1,
    "name": "Euro",
    "code": "EUR",
    "sign": "€"
  },
  "rate": 0.99
}
```

HTTP коды ответов:

Успех - 200
Коды валют пары отсутствуют в адресе - 400
Обменный курс для пары не найден - 404
Ошибка (например, база данных недоступна) - 500

POST /exchangeRates #

Добавление нового обменного курса в базу. Данные передаются в теле запроса в виде полей формы (x-www-form-urlencoded). Поля формы - baseCurrencyCode, targetCurrencyCode, rate. Пример полей формы:

baseCurrencyCode - USD
targetCurrencyCode - EUR
rate - 0.99
Пример ответа - JSON представление вставленной в базу записи, включая её ID:

```json
{
  "id": 0,
  "baseCurrency": {
    "id": 0,
    "name": "United States dollar",
    "code": "USD",
    "sign": "$"
  },
  "targetCurrency": {
    "id": 1,
    "name": "Euro",
    "code": "EUR",
    "sign": "€"
  },
  "rate": 0.99
}
```

HTTP коды ответов:

Успех - 201
Отсутствует нужное поле формы - 400
Валютная пара с таким кодом уже существует - 409
Одна (или обе) валюта из валютной пары не существует в БД - 404
Ошибка (например, база данных недоступна) - 500

PATCH /exchangeRate/USDRUB #

Обновление существующего в базе обменного курса. Валютная пара задаётся идущими подряд кодами валют в адресе запроса. Данные передаются в теле запроса в виде полей формы (x-www-form-urlencoded). Единственное поле формы - rate.

Пример ответа - JSON представление обновлённой записи в базе данных, включая её ID:

```json
{
  "id": 0,
  "baseCurrency": {
    "id": 0,
    "name": "United States dollar",
    "code": "USD",
    "sign": "$"
  },
  "targetCurrency": {
    "id": 1,
    "name": "Euro",
    "code": "EUR",
    "sign": "€"
  },
  "rate": 0.99
}
```

HTTP коды ответов:

Успех - 200
Отсутствует нужное поле формы - 400
Валютная пара отсутствует в базе данных - 404
Ошибка (например, база данных недоступна) - 500

Обмен валюты #

GET /exchange?from=BASE_CURRENCY_CODE&to=TARGET_CURRENCY_CODE&amount=$AMOUNT #
Расчёт перевода определённого количества средств из одной валюты в другую. Пример запроса - GET /exchange?from=USD&to=AUD&amount=10.

Пример ответа:

```json
{
  "baseCurrency": {
    "id": 0,
    "name": "United States dollar",
    "code": "USD",
    "sign": "$"
  },
  "targetCurrency": {
    "id": 1,
    "name": "Australian dollar",
    "code": "AUD",
    "sign": "A€"
  },
  "rate": 1.45,
  "amount": 10.00,
  "convertedAmount": 14.50
}
```

Получение курса для обмена может пройти по одному из трёх сценариев. Допустим, совершаем перевод из валюты A в валюту B:

В таблице ExchangeRates существует валютная пара AB - берём её курс
В таблице ExchangeRates существует валютная пара BA - берем её курс, и считаем обратный, чтобы получить AB
В таблице ExchangeRates существуют валютные пары USD-A и USD-B - вычисляем из этих курсов курс AB
Остальные возможные сценарии, для упрощения, опустим.

Для всех запросов, в случае ошибки, ответ может выглядеть так:

```json
{
  "message": "Валюта не найдена"
}
```

Значение message зависит от того, какая именно ошибка произошла.
## Local run
1) Install docker.
2) run command: "docker-compose up -d" in directory with project.
3) Install composer in this running container using official DOCs.
4) Project will be available with this URL: "http://localhost:8876/".