# Country API Contract

## Endpoints

- `GET /countries`: paginated country list
- `POST /countries`: create country
- `GET /countries/{country}`: show country detail
- `PUT /countries/{country}`: update country
- `DELETE /countries/{country}`: delete country

## Success Response Shape

All successful responses use `CountryResource`.

```json
{
  "data": {
    "id": 1,
    "iso": "ID",
    "name": "Indonesia",
    "nice_name": "Indonesia",
    "iso3": "IDN",
    "numcode": 360,
    "phonecode": 62,
    "status": true,
    "created_at": "2026-05-13T00:00:00.000000Z",
    "updated_at": "2026-05-13T00:00:00.000000Z"
  }
}
```

`GET /countries` returns the same resource items under `data` with Laravel pagination `links` and `meta`.

## Error Contract

- `422 Unprocessable Entity`: validation errors, including duplicate values and blocked delete when a country is still referenced by province data
- `404 Not Found`: country does not exist

## Delete Policy

- A country cannot be deleted when it is already referenced by province data
- This guard prevents unintended cascading deletion of region hierarchy data
