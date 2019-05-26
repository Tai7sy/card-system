import fetch from '~/utils/fetch';


export function login (email, password, remember = false) {
  return fetch({
    url: 'login',
    method: 'post',
    data: {
      email,
      password,
      remember
    }
  });
}

export function getVerify () {
  return fetch({
    url: 'login/verify',
    method: 'post'
  });
}
export function changePass (data) {
  return fetch({
    url: 'login/password',
    method: 'post',
    data
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



