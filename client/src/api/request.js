const axios = require('axios');
const app = require('../main');

let local = 'https://mechfrog88.ddns.net:8080';
let prod = '';

let service = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? prod : local,
  timeout: 3000,
  withCredentials: true,
  
})

service.interceptors.request.use(function (config) {
  app.$Progress.start();
  return config;
}, function (error) {
  // Do something with request error
  return Promise.reject(error);
});

service.interceptors.response.use(function (response) {
  app.$Progress.finish();
  return response;
}, function (error) {
  app.$Progress.fail();
  return Promise.reject(error);
});

export default service;