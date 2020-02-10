import request from './request.js';

export function createUser(data) {
  return request({
    url: '/user',
    method: 'POST',
    data
  })
}

export function getUser() {
  return request({
    url: '/user',
    method: 'GET'
  })
}

export function getAllUsers() {
  return request({
    url: '/users',
    method: 'GET'
  })
}

export function getUserLessons() {
  return request({
    url: '/user/lesson',
    method: 'GET'
  })
}

export function userLogin(data) {
  return request({
    url: '/user/login',
    method: 'POST',
    data
  })
}

export function userLogout() {
  return request({
    url: '/user/logout',
    method: 'POST'
  })
}

export function submitUser(data) {
  return request({
    url: '/user/submit',
    method: 'POST',
    data
  })
}

export function editUser(id, data) {
  return request({
    url: `/user/${id}`,
    method: 'PUT',
    data
  })
}

export function deleteUser(id) {
  return request({
    url: `/user/${id}`,
    method: 'DELETE'
  })
}