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

export function getLessonsList() {
  return request({
    url: '/lessons',
    method: 'GET'
  })
}

export function getLessonUsers(id) {
  return request({
    url: `/lesson/${id}`,
    method: 'GET'
  })
}

// add array of students to a forced lesson
export function addForcedLesson(id, data) {
  return request({
    url: `/lesson/force/add/${id}`,
    method: 'POST',
    data
  })  
}

// remove array of students from forced lesson
export function removeForcedLesson(id, data) {
  return request({
    url: `/lesson/force/remove/${id}`,
    method: 'POST',
    data
  })  
}

export function editLesson(id, data) {
  return request({
    url: `/lesson/edit/${id}`,
    method: 'POST',
    data
  })
}

export function deleteLesson(id) {
  return request({
    url: `/lesson/${id}`,
    method: 'DELETE'
  })
}
