# Country API Contract

## Web Admin

- `country.index`

## API Endpoints

- `GET /api/countries`
- `POST /api/countries`
- `GET /api/countries/{country}`
- `PUT/PATCH /api/countries/{country}`
- `DELETE /api/countries/{country}`

## Authentication

- API endpoint `country` memakai `Sanctum`
- gunakan bearer token atau authenticated Sanctum session

## Query Parameters

- `filter[iso]=ID`
- `filter[name]=indo`
- `filter[nice_name]=indo`
- `filter[iso3]=IDN`
- `filter[numcode]=360`
- `filter[phonecode]=62`
- `filter[status]=1`
- `sort=name`
- `sort=-id`
