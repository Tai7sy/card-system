import fetch from '~/utils/fetch';

export function login (data) {
  return fetch({
    url: 'auth/login',
    method: 'post',
    data
  });
}

export function logout () {
  return fetch({
    url: 'auth/token',
    method: 'delete'
  });
}

export function register (data) {
  return fetch({
    url: 'auth/register',
    method: 'put',
    data
  });
}

export function sendResetLink(data) {
  return fetch({
    url: 'auth/send-reset-link',
    method: 'post',
    data
  });
}
export function reset(data) {
  return fetch({
    url: 'auth/reset-password',
    method: 'post',
    data
  });
}

export function getInfo () {
  return fetch({
    url: 'info',
    method: 'get'
  });
}

export function changePass (data) {
  return fetch({
    url: 'info/password',
    method: 'post',
    data
  });
}

