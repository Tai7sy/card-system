import fetch from '../utils/fetch';



export function getList (params) {
  return fetch({
    url: 'log',
    method: 'post',
    data: params
  });
}

