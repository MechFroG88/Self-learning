import request from './request';

export function createClass(data) {
  return request({
    url: '/class',
    method: 'POST',
    data
  })
}

export function getAllClasses() {
  return request({
    url: '/class',
    method: 'GET'
  })
}

export function editClass(id, data) {
  return request({
    url: `/class/${id}`,
    method: 'PUT',
    data
  })
}

export function deleteClass(id) {
  return request({
    url: `/user/${id}`,
    method: 'DELETE'
  })
}