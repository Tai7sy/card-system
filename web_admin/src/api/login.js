import fetch from './../utils/fetch';


export function login (params) {
  return fetch({
    url: 'login',
    method: 'post',
    data: params
  });
}

export function getVerify () {
  return fetch({
    url: 'login/verify',
    method: 'post'
  });
}
export function changePass (params) {
  return fetch({
    url: 'login/password',
    method: 'post',
    data: params
  });
}


export function getInfo () {
  return fetch({
    url: 'login/info',
    method: 'post'
  });
}

export function logout () {
  return fetch({
    url: 'login/logout',
    method: 'post'
  });
}



