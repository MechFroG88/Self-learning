import router from '@/router'
import app   from '@/main'
import axios from 'axios'
import qs from 'qs'

let local = 'http://10.20.95.34';
let prod = '';

let service = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? prod : local,
  withCredentials: true,
  transformRequest: [function (data, headers) {
    if(headers['Content-Type'] == "multipart/form-data"){
      return data;
    }
    return qs.stringify(data);
  }],
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
  // if (error.response.status == 401) {
  //   router.push('/');
  // }
  return Promise.reject(error);
});

export default service;