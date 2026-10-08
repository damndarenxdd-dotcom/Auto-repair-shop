# Scoring Rubric

| Criteria | Description | Points |
|---|---|---:|
| External Weather API Integration | Successfully integrates a third-party Weather API into the existing Laravel project. | 15 |
| Laravel HTTP Client | Correctly uses Laravel's HTTP Client to send requests to the external Weather API. | 15 |
| Weather Dashboard | Functional weather section/card is implemented and integrated into the existing Admin Dashboard. | 15 |
| Display Weather Information | Correctly displays the location, temperature, weather condition/description, humidity, and wind speed. | 15 |
| JSON Data Mapping | Correctly extracts and maps data from the API's JSON response into the application view. | 15 |
| API Error Handling | Properly handles API errors, invalid requests, connection failures, and unavailable weather data. | 10 |
| Timeout & Fallback Handling | Properly handles API timeouts and displays an appropriate fallback message when the service is unavailable. | 5 |
| API Key / Configuration | Properly stores and manages the API key using `.env` or application configuration when required. | 5 |
| Functionality & Integration | Features work properly and are integrated into the existing project without major errors. | 5 |
| **Total** |  | **100** |

## Expected Demonstration

The student should be able to demonstrate the following flow:

- Admin Dashboard → Laravel HTTP Client → External Weather API → JSON Response → Data Mapping → Weather Display → Error/Timeout Handling

Ensure the Admin Dashboard shows:

- Location / City
- Temperature
- Weather condition / description
- Humidity
- Wind speed

Notes:

- API key must be stored in `.env` (see `WEATHER_API_KEY` in `.env.example`).
- The implementation should include timeout handling and a visible fallback message when weather data is unavailable.
- Real-time updates (polling or push) are acceptable if they meet the requirements above.
