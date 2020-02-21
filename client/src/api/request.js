import router from '@/router'
import app   from '@/main'
import axios from 'axios'
import qs from 'qs'

// let local = 'http://172.17.88.111/api';
// let local = 'http://mechfrog88.ddns.net/';
// let local = 'http://chkl1.ml/api';
let local = 'http://www2.chonghwakl.edu.my:8080/api';
// let local = 'http://10.20.95.34';

let service = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? '/api' : local,
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
  // console.log(JSON.parse(JSON.stringify(error)));
  if (error.message == 'Network Error') 
    app.$notify({
      type: 'error',
      title: '网络异常，请稍后再试。'
    })
  else if (error.response.status == 401 && router.app._route.fullPath != '/') {
    localStorage.clear();
    router.push('/');
  }
  return Promise.reject(error);
});

export default service;