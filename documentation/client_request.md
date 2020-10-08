#### Consuming with JavaScript
As discussed in the [Laravel Passport Documentation](https://laravel.com/docs/8.x/passport#consuming-your-api-with-javascript) "When building an API, it can be extremely useful to be able to consume your own API from your JavaScript application. This approach to API development allows your own application to consume the same API that you are sharing with the world. The same API may be consumed by your web application, mobile applications, third-party applications, and any SDKs that you may publish on various package managers."

##### Manual Approach
When consuming your API with JavaScript, you need to send the access token and tenant domain with each request to your application. The access token must be passed as an authorization header prefixed with "Bearer" and X-Forwarded-Host set to the tenant domain:

```js
    // Using Axios
    const instance = axios.create({
        ...
        headers: {'Authorization': `Bearer ${token}`}, {'X-Forwarded-Host': 'domain'}
    });
```
