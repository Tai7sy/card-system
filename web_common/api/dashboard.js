import fetch from '~/utils/fetch';


export function dashboard (params) {
  return fetch({
    url: 'dashboard',
    method: 'get',
    params
  });
}
