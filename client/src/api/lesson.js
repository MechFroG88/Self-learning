import request from './request';

export function createLesson(data) {
  return request({
    url: '/lesson',
    method: 'POST',
    data
  })
}

export function getAllLessons() {
  return request({
    url: '/lesson',
    method: 'GET'
  })
}

export function getLessonUsers(id) {
  return request({
    url: `/lesson/${id}`,
    method: 'GET'
  })
}

export function editLesson(id, data) {
  return request({
    url: `/lesson/${id}`,
    method: 'PUT',
    data
  })
}

export function deleteLesson(id) {
  return request({
    url: `/lesson/${id}`,
    method: 'DELETE'
  })
}